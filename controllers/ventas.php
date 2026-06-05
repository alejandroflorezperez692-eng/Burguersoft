<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

// ── GET ───────────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    if ($id) {
        // Detalle de una venta: productos normales + promociones
        $s = $pdo->prepare("
            SELECT dv.*, p.nombre AS nombre_producto
            FROM detalle_venta dv
            JOIN producto p ON p.id = dv.producto_id
            WHERE dv.venta_id = ?
        ");
        $s->execute([$id]);
        $items = $s->fetchAll();

        $sp = $pdo->prepare("
            SELECT vp.id AS vp_id, pr.id AS promocion_id, pr.nombre_promocion,
                   pr.precio, pr.imagen
            FROM venta_promocion vp
            JOIN promocion pr ON pr.id = vp.promocion_id
            WHERE vp.venta_id = ?
        ");
        $sp->execute([$id]);
        $promos = $sp->fetchAll();

        jsonResponse(['items' => $items, 'promociones' => $promos]);
    }

    // Lista todas las ventas con nombre de usuario
    jsonResponse($pdo->query("
        SELECT v.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario
        FROM venta v
        LEFT JOIN usuario u ON u.id = v.usuario_id
        ORDER BY v.fecha DESC
    ")->fetchAll());
}

// ── POST (crear venta) ────────────────────────────────────────────────────────
if ($method === 'POST') {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $metodo     = limpiar($body['metodo_pago']  ?? '');
    $items      = $body['items']      ?? [];   // [{producto_id, cantidad, precio_unitario}]
    $promos     = $body['promociones'] ?? [];  // [{promocion_id, precio, productos:[{id,cantidad}]}]
    $usuario_id = (int)($_SESSION['id_usuario'] ?? 0);

    if (!$metodo || (empty($items) && empty($promos)))
        jsonResponse(['error' => 'Faltan datos de la venta'], 400);

    $total = 0;

    // Sumar productos individuales
    foreach ($items as $item) {
        $total += (float)($item['precio_unitario'] ?? 0) * (int)($item['cantidad'] ?? 1);
    }
    // Sumar promociones
    foreach ($promos as $promo) {
        $total += (float)($promo['precio'] ?? 0);
    }

    $pdo->beginTransaction();
    try {
        // 1) Insertar cabecera de venta
        $pdo->prepare("INSERT INTO venta (valor_total, metodo_pago, usuario_id) VALUES (?,?,?)")
            ->execute([$total, $metodo, $usuario_id]);
        $venta_id = (int)$pdo->lastInsertId();

        // 2) Insertar detalle_venta para productos individuales
        $insDetalle = $pdo->prepare("
            INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal, estado)
            VALUES (?,?,?,?,?,?)
        ");
        foreach ($items as $item) {
            $prod_id  = (int)($item['producto_id']     ?? 0);
            $cant     = (int)($item['cantidad']        ?? 1);
            $pu       = (float)($item['precio_unitario'] ?? 0);
            $subtotal = $cant * $pu;
            if (!$prod_id) continue;
            $insDetalle->execute([$venta_id, $prod_id, $cant, $pu, $subtotal, 'En proceso']);

            // Descontar stock del producto
            $pdo->prepare("
                UPDATE producto
                SET catidad = GREATEST(0, CAST(catidad AS SIGNED) - ?)
                WHERE id = ?
            ")->execute([$cant, $prod_id]);
        }

        // 3) Insertar venta_promocion y descontar stock de cada producto de la promoción
        $insPromo = $pdo->prepare("INSERT INTO venta_promocion (venta_id, promocion_id) VALUES (?,?)");
        foreach ($promos as $promo) {
            $promo_id = (int)($promo['promocion_id'] ?? 0);
            if (!$promo_id) continue;
            $insPromo->execute([$venta_id, $promo_id]);

            // Obtener los productos de esta promoción y descontar 1 unidad de cada uno
            $sp = $pdo->prepare("
                SELECT producto_id FROM promocion_producto WHERE promocion_id = ?
            ");
            $sp->execute([$promo_id]);
            $prod_promo = $sp->fetchAll(PDO::FETCH_COLUMN);

            foreach ($prod_promo as $pid) {
                // Insertar en detalle_venta como si fuera venta individual (precio 0, ya pagado en promo)
                $pdo->prepare("
                    INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal, estado)
                    VALUES (?,?,?,?,?,?)
                ")->execute([$venta_id, (int)$pid, 1, 0, 0, 'En proceso']);

                // Descontar stock
                $pdo->prepare("
                    UPDATE producto
                    SET catidad = GREATEST(0, CAST(catidad AS SIGNED) - 1)
                    WHERE id = ?
                ")->execute([$pid]);
            }
        }

        $pdo->commit();
        jsonResponse(['success' => true, 'venta_id' => $venta_id]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Error al guardar venta: ' . $e->getMessage()], 500);
    }
}

// ── PUT (actualizar estado de detalle) ────────────────────────────────────────
if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $metodo = limpiar($body['metodo_pago'] ?? '');
    $total  = (float)($body['valor_total'] ?? 0);
    $pdo->prepare("UPDATE venta SET metodo_pago=?, valor_total=? WHERE id=?")
        ->execute([$metodo, $total, $id]);
    jsonResponse(['success' => true]);
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    // detalle_venta y venta_promocion se eliminan en cascada por FK
    $pdo->prepare("DELETE FROM venta WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>
