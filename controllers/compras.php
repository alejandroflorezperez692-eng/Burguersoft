<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

$metodos_validos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Nequi', 'Daviplata'];

function requerirAdminApi(): void {
    if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador')
        jsonResponse(['error' => 'No autorizado'], 403);
}

if ($method === 'GET') {
    if ($id) {
        $s = $pdo->prepare("
            SELECT dc.*, mp.nombre AS nombre_materia, mp.unidad_medida, m.nombre AS nombre_marca
            FROM detalle_compra dc
            JOIN materia_prima mp ON mp.id = dc.materia_prima_id
            LEFT JOIN marca m ON m.id = dc.marca_id
            WHERE dc.compra_id = ?
        ");
        $s->execute([$id]);
        jsonResponse(['items' => $s->fetchAll()]);
    }

    jsonResponse($pdo->query("
        SELECT c.id, c.fecha, c.valor_total, c.metodo_pago,
               (SELECT COUNT(*) FROM detalle_compra dc WHERE dc.compra_id = c.id) AS num_items
        FROM compra c
        ORDER BY c.fecha DESC, c.id DESC
    ")->fetchAll());
}

if ($method === 'POST') {
    requerirAdminApi();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $metodo = limpiar($body['metodo_pago'] ?? '');
    $items  = $body['items'] ?? [];
    $usuario_id = (int)($_SESSION['id_usuario'] ?? 0);

    if (!$metodo || empty($items))
        jsonResponse(['error' => 'Faltan datos de la compra'], 400);

    $metodo = ucfirst(strtolower($metodo));
    if (!in_array($metodo, $metodos_validos))
        jsonResponse(['error' => 'Método de pago inválido'], 400);

    $total = 0;
    foreach ($items as $item) {
        $cant = (float)($item['cantidad'] ?? 0);
        $pu   = (float)($item['precio_unitario'] ?? 0);
        if ($cant <= 0 || $pu <= 0)
            jsonResponse(['error' => 'Cantidad y precio deben ser mayores a cero'], 400);
        $total += $cant * $pu;
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO compra (fecha, valor_total, metodo_pago) VALUES (CURDATE(), ?, ?)")
            ->execute([$total, $metodo]);
        $compra_id = (int)$pdo->lastInsertId();

        $insDetalle = $pdo->prepare("
            INSERT INTO detalle_compra
                (cantidad, precio_unitario, subtotal, compra_id, materia_prima_id, usuario_id, marca_id)
            VALUES (?,?,?,?,?,?,?)
        ");

        foreach ($items as $item) {
            $materia_id = (int)($item['materia_prima_id'] ?? 0);
            $cant       = (float)($item['cantidad'] ?? 0);
            $pu         = (float)($item['precio_unitario'] ?? 0);
            $marca_id   = !empty($item['marca_id']) ? (int)$item['marca_id'] : null;
            $subtotal   = $cant * $pu;
            if (!$materia_id) continue;

            $sMateria = $pdo->prepare("SELECT id FROM materia_prima WHERE id = ?");
            $sMateria->execute([$materia_id]);
            if (!$sMateria->fetchColumn())
                throw new RuntimeException("La materia prima seleccionada ya no existe");

            $insDetalle->execute([$cant, $pu, $subtotal, $compra_id, $materia_id, $usuario_id, $marca_id]);

            $pdo->prepare("
                UPDATE materia_prima
                SET cantidad = cantidad + ?
                WHERE id = ?
            ")->execute([$cant, $materia_id]);
        }

        $pdo->commit();
        jsonResponse(['success' => true, 'compra_id' => $compra_id]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}

if ($method === 'DELETE') {
    requerirAdminApi();
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);

    $pdo->beginTransaction();
    try {
        $sDetalle = $pdo->prepare("SELECT materia_prima_id, cantidad FROM detalle_compra WHERE compra_id = ?");
        $sDetalle->execute([$id]);
        $detalles = $sDetalle->fetchAll();

        foreach ($detalles as $d) {
            $pdo->prepare("
                UPDATE materia_prima
                SET cantidad = GREATEST(0, cantidad - ?)
                WHERE id = ?
            ")->execute([(float)$d['cantidad'], (int)$d['materia_prima_id']]);
        }

        $pdo->prepare("DELETE FROM detalle_compra WHERE compra_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM compra WHERE id = ?")->execute([$id]);

        $pdo->commit();
        jsonResponse(['success' => true]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>
