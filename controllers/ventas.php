<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    if ($id) {
        $s = $pdo->prepare("
            SELECT dv.*, p.nombre AS nombre
            FROM detalle_venta dv
            JOIN producto p ON p. id = dv.producto_id
            WHERE dv.venta_id = ?
        ");
        $s->execute([$id]);
        $items = $s->fetchAll();

        $sp = $pdo->prepare("
            SELECT vp.id AS vp_id, pr.id AS promocion_id, pr.nombre,
                   pr.precio, pr.imagen
            FROM venta_promocion vp
            JOIN promocion pr ON pr.id = vp.promocion_id
            WHERE vp.venta_id = ?
        ");
        $sp->execute([$id]);
        $promos = $sp->fetchAll();

        jsonResponse(['items' => $items, 'promociones' => $promos]);
    }

    jsonResponse($pdo->query("
        SELECT v.id, v.fecha, v.valor_total, v.metodo_pago, v.estado,
               u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
               v.usuario_id
        FROM venta v
        LEFT JOIN usuario u ON u.id = v.usuario_id
        ORDER BY v.fecha DESC
    ")->fetchAll());
}

if ($method === 'POST') {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $metodo     = limpiar($body['metodo_pago']  ?? '');
    $items      = $body['items']      ?? [];
    $promos     = $body['promociones'] ?? [];
    $usuario_id = (int)($_SESSION['id_usuario'] ?? 0);

    if (!$metodo || (empty($items) && empty($promos)))
        jsonResponse(['error' => 'Faltan datos de la venta'], 400);

    $metodos_validos = ['Efectivo','Tarjeta','Transferencia','Nequi','Daviplata'];
    if (!in_array($metodo, $metodos_validos))
        jsonResponse(['error' => 'Método de pago inválido'], 400);

    $total = 0;
    foreach ($items as $item) {
        $total += (float)($item['precio_unitario'] ?? 0) * (int)($item['cantidad'] ?? 1);
    }
    foreach ($promos as $promo) {
        $total += (float)($promo['precio'] ?? 0);
    }

    $pdo->beginTransaction();
    try {
        foreach ($items as $item) {
            $prod_id = (int)($item['producto_id'] ?? 0);
            $cant    = (int)($item['cantidad'] ?? 1);
            if (!$prod_id) continue;

            $sStock = $pdo->prepare("SELECT catidad FROM producto WHERE id = ?");
            $sStock->execute([$prod_id]);
            $stock = (int)$sStock->fetchColumn();
            if ($stock < $cant)
                throw new RuntimeException("Stock insuficiente para el producto ID $prod_id");

            $sMateria = $pdo->prepare("
                SELECT r.materia_id, r.cantidad_usada, mp.cantidad AS stock_mp, mp.nombre AS nombre_mp
                FROM receta r
                JOIN materia_prima mp ON mp.id = r.materia_id
                WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
            ");
            $sMateria->execute([$prod_id]);
            $ingredientes = $sMateria->fetchAll();

            foreach ($ingredientes as $ing) {
                $necesario = (float)$ing['cantidad_usada'] * $cant;
                if ((float)$ing['stock_mp'] < $necesario)
                    throw new RuntimeException("Materia prima insuficiente: " . $ing['nombre_mp']);
            }
        }

        foreach ($promos as $promo) {
            $promo_id = (int)($promo['promocion_id'] ?? 0);
            if (!$promo_id) continue;
            $sp = $pdo->prepare("
                SELECT pp.producto_id, p.catidad, p.nombre
                FROM promocion_producto pp
                JOIN producto p ON p.id = pp.producto_id
                WHERE pp.promocion_id = ?
            ");
            $sp->execute([$promo_id]);
            $prods_promo = $sp->fetchAll();
            foreach ($prods_promo as $pp) {
                if ((int)$pp['catidad'] < 1)
                    throw new RuntimeException("Stock insuficiente para: " . $pp['nombre']);
            }
        }

        $pdo->prepare("INSERT INTO venta (valor_total, metodo_pago, estado, usuario_id) VALUES (?,?,?,?)")
            ->execute([$total, $metodo, 'Pagado', $usuario_id]);
        $venta_id = (int)$pdo->lastInsertId();

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

            $insDetalle->execute([$venta_id, $prod_id, $cant, $pu, $subtotal, 'Pago']);

            $pdo->prepare("
                UPDATE producto
                SET catidad = GREATEST(0, CAST(catidad AS SIGNED) - ?)
                WHERE id = ?
            ")->execute([$cant, $prod_id]);

            $pdo->prepare("
                UPDATE materia_prima mp
                JOIN receta r ON r.materia_id = mp.id
                SET mp.cantidad = GREATEST(0, mp.cantidad - (r.cantidad_usada * ?))
                WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
            ")->execute([$cant, $prod_id]);
        }

        $insPromo = $pdo->prepare("INSERT INTO venta_promocion (venta_id, promocion_id) VALUES (?,?)");
        foreach ($promos as $promo) {
            $promo_id = (int)($promo['promocion_id'] ?? 0);
            if (!$promo_id) continue;

            $insPromo->execute([$venta_id, $promo_id]);

            $sp = $pdo->prepare("
                SELECT producto_id FROM promocion_producto WHERE promocion_id = ?
            ");
            $sp->execute([$promo_id]);
            $prods_promo = $sp->fetchAll(PDO::FETCH_COLUMN);

            foreach ($prods_promo as $pid) {
                $pid = (int)$pid;
                $sPrecio = $pdo->prepare("SELECT valor FROM producto WHERE id = ?");
                $sPrecio->execute([$pid]);
                $precio_prod = (float)$sPrecio->fetchColumn();

                $insDetalle->execute([$venta_id, $pid, 1, $precio_prod, $precio_prod, 'Pago']);

                $pdo->prepare("
                    UPDATE producto
                    SET catidad = GREATEST(0, CAST(catidad AS SIGNED) - 1)
                    WHERE id = ?
                ")->execute([$pid]);

                $pdo->prepare("
                    UPDATE materia_prima mp
                    JOIN receta r ON r.materia_id = mp.id
                    SET mp.cantidad = GREATEST(0, mp.cantidad - r.cantidad_usada)
                    WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
                ")->execute([$pid]);
            }
        }

        $pdo->commit();
        jsonResponse(['success' => true, 'venta_id' => $venta_id]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $estado = limpiar($body['estado'] ?? '');
    $metodo = limpiar($body['metodo_pago'] ?? '');

    $campos = [];
    $vals   = [];
    if ($estado) { $campos[] = 'estado = ?'; $vals[] = $estado; }
    if ($metodo) { $campos[] = 'metodo_pago = ?'; $vals[] = $metodo; }
    if (!$campos) jsonResponse(['error' => 'Sin datos para actualizar'], 400);
    $vals[] = $id;
    $pdo->prepare("UPDATE venta SET " . implode(', ', $campos) . " WHERE id = ?")->execute($vals);
    jsonResponse(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $pdo->prepare("DELETE FROM venta WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>
