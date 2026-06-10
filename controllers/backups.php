<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador') {
    jsonResponse(['error' => 'Acceso denegado'], 403);
}

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';

$TABLAS = [
    'categoria', 'marca', 'materia_prima', 'producto',
    'promocion', 'promocion_producto', 'receta',
    'usuario', 'rol', 'venta', 'detalle_venta',
    'venta_promocion', 'compra', 'detalle_compra', 'copia_seguridad'
];

if ($method === 'GET') {

    if ($accion === 'historial') {
        $lista = $pdo->query("
            SELECT cs.id, cs.nombre_tabla, cs.fecha,
                   u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
            FROM copia_seguridad cs
            LEFT JOIN usuario u ON u.id = cs.usuario_id
            ORDER BY cs.fecha DESC
        ")->fetchAll();
        jsonResponse($lista);
    }

    if ($accion === 'exportar') {
        $snapshot = [];
        foreach ($TABLAS as $tabla) {
            try {
                $snapshot[$tabla] = $pdo->query("SELECT * FROM `$tabla`")->fetchAll();
            } catch (Throwable $e) {
                $snapshot[$tabla] = [];
            }
        }

        $pdo->prepare("
            INSERT INTO copia_seguridad (nombre_tabla, usuario_id)
            VALUES (?, ?)
        ")->execute(['EXPORTACION_COMPLETA', (int)$_SESSION['id_usuario']]);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="backup_burguersoft_' . date('Ymd_His') . '.json"');
        echo json_encode([
            'version'   => '1.0',
            'fecha'     => date('Y-m-d H:i:s'),
            'tablas'    => $snapshot
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    jsonResponse(['error' => 'Acción no reconocida'], 404);
}

if ($method === 'POST' && $accion === 'crear') {
    $tabla_objetivo = limpiar(
        json_decode(file_get_contents('php://input'), true)['tabla'] ?? 'TODAS'
    );

    $pdo->prepare("
        INSERT INTO copia_seguridad (nombre_tabla, usuario_id)
        VALUES (?, ?)
    ")->execute([$tabla_objetivo, (int)$_SESSION['id_usuario']]);

    jsonResponse(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
}

if ($method === 'POST' && $accion === 'restaurar') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (empty($body['tablas'])) jsonResponse(['error' => 'Datos inválidos'], 400);

    $orden_restauracion = [
        'rol', 'usuario', 'categoria', 'marca', 'materia_prima',
        'producto', 'promocion', 'promocion_producto', 'receta',
        'venta', 'detalle_venta', 'venta_promocion',
        'compra', 'detalle_compra', 'copia_seguridad'
    ];

    $pdo->beginTransaction();
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        foreach ($orden_restauracion as $tabla) {
            if (empty($body['tablas'][$tabla])) continue;
            $filas = $body['tablas'][$tabla];
            if (empty($filas)) continue;

            $columnas = array_keys($filas[0]);
            $cols_str = implode(',', array_map(fn($c) => "`$c`", $columnas));
            $vals_str = implode(',', array_fill(0, count($columnas), '?'));

            $stmt = $pdo->prepare("INSERT IGNORE INTO `$tabla` ($cols_str) VALUES ($vals_str)");
            foreach ($filas as $fila) {
                $stmt->execute(array_values($fila));
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        // Registrar la restauración
        $pdo->prepare("INSERT INTO copia_seguridad (nombre_tabla, usuario_id) VALUES (?,?)")
            ->execute(['RESTAURACION', (int)$_SESSION['id_usuario']]);

        $pdo->commit();
        jsonResponse(['success' => true, 'mensaje' => 'Base de datos restaurada correctamente']);
    } catch (Throwable $e) {
        $pdo->rollBack();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        jsonResponse(['error' => 'Error en restauración: ' . $e->getMessage()], 500);
    }
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $pdo->prepare("DELETE FROM copia_seguridad WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>
