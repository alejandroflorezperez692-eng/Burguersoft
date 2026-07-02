<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

$ESTADOS_VALIDOS = ['Pagado', 'Cancelado', 'Reembolsada', 'Rechazada'];

function actualizarEstadoProducto(PDO $pdo, int $producto_id): void {
    $s = $pdo->prepare("SELECT cantidad FROM producto WHERE id = ?");
    $s->execute([$producto_id]);
    $cantidad = (int)$s->fetchColumn();
    $estado   = estadoProductoPorCantidad($cantidad);
    $pdo->prepare("UPDATE producto SET estado = ? WHERE id = ?")->execute([$estado, $producto_id]);
}

function restaurarStockVenta(PDO $pdo, int $venta_id): void {
    $s = $pdo->prepare("SELECT producto_id, cantidad FROM detalle_venta WHERE venta_id = ?");
    $s->execute([$venta_id]);
    $items = $s->fetchAll();

    foreach ($items as $item) {
        $prod_id = (int)$item['producto_id'];
        $cant    = (int)$item['cantidad'];
        if (!$prod_id || $cant <= 0) continue;

        $pdo->prepare("
            UPDATE producto
            SET cantidad = CAST(cantidad AS SIGNED) + ?
            WHERE id = ?
        ")->execute([$cant, $prod_id]);

        $pdo->prepare("
            UPDATE materia_prima mp
            JOIN receta r ON r.materia_id = mp.id
            SET mp.cantidad = mp.cantidad + (r.cantidad_usada * ?)
            WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
        ")->execute([$cant, $prod_id]);

        actualizarEstadoProducto($pdo, $prod_id);
    }
}

function descontarStockVenta(PDO $pdo, int $venta_id): void {
    $s = $pdo->prepare("SELECT producto_id, cantidad FROM detalle_venta WHERE venta_id = ?");
    $s->execute([$venta_id]);
    $items = $s->fetchAll();

    foreach ($items as $item) {
        $prod_id = (int)$item['producto_id'];
        $cant    = (int)$item['cantidad'];
        if (!$prod_id || $cant <= 0) continue;

        $sProd = $pdo->prepare("SELECT cantidad, nombre FROM producto WHERE id = ?");
        $sProd->execute([$prod_id]);
        $prod = $sProd->fetch();
        if (!$prod || (int)$prod['cantidad'] < $cant)
            throw new RuntimeException("Stock insuficiente para reactivar: " . ($prod['nombre'] ?? "producto #$prod_id"));

        $sMateria = $pdo->prepare("
            SELECT r.cantidad_usada, mp.cantidad AS stock_mp, mp.nombre AS nombre_mp
            FROM receta r
            JOIN materia_prima mp ON mp.id = r.materia_id
            WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
        ");
        $sMateria->execute([$prod_id]);
        foreach ($sMateria->fetchAll() as $ing) {
            $necesario = (float)$ing['cantidad_usada'] * $cant;
            if ((float)$ing['stock_mp'] < $necesario)
                throw new RuntimeException("Materia prima insuficiente para reactivar: " . $ing['nombre_mp']);
        }
    }

    foreach ($items as $item) {
        $prod_id = (int)$item['producto_id'];
        $cant    = (int)$item['cantidad'];
        if (!$prod_id || $cant <= 0) continue;

        $pdo->prepare("
            UPDATE producto
            SET cantidad = GREATEST(0, CAST(cantidad AS SIGNED) - ?)
            WHERE id = ?
        ")->execute([$cant, $prod_id]);

        $pdo->prepare("
            UPDATE materia_prima mp
            JOIN receta r ON r.materia_id = mp.id
            SET mp.cantidad = GREATEST(0, mp.cantidad - (r.cantidad_usada * ?))
            WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
        ")->execute([$cant, $prod_id]);

        actualizarEstadoProducto($pdo, $prod_id);
    }
}

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
        SELECT v.id, v.fecha, v.valor_total, v.metodo_pago, v.estado, v.tipo_entrega,
               u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
               v.usuario_id
        FROM venta v
        LEFT JOIN usuario u ON u.id = v.usuario_id
        ORDER BY v.id DESC  
    ")->fetchAll());
}

if ($method === 'POST') {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $metodo     = limpiar($body['metodo_pago']  ?? '');
    $items      = $body['items']      ?? [];
    $promos     = $body['promociones'] ?? [];
    $usuario_id = (int)($_SESSION['id_usuario'] ?? 0);
    $tipo_entrega  = limpiar($body['tipo_entrega'] ?? 'Recoger');
    $tipos_validos = ['Domicilio', 'Recoger', 'Consumir'];
    if (!in_array($tipo_entrega, $tipos_validos)) {
        $tipo_entrega = 'Recoger';
    }

  if (!$metodo || (empty($items) && empty($promos)))
        jsonResponse(['error' => 'Faltan datos de la venta'], 400);

    $metodos_validos = ['Efectivo','Tarjeta','Transferencia','Nequi','Daviplata'];
    $metodo = ucfirst(strtolower($metodo));
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

            $sStock = $pdo->prepare("SELECT cantidad, estado, nombre FROM producto WHERE id = ?");
            $sStock->execute([$prod_id]);
            $prod = $sStock->fetch();
            if (!$prod) throw new RuntimeException("Producto no encontrado");

            $stock = (int)$prod['cantidad'];
            if ($prod['estado'] === 'Agotado' || $stock < $cant)
                throw new RuntimeException("Stock insuficiente para el producto: " . $prod['nombre']);

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
                SELECT pp.id_producto, p.cantidad, p.estado, p.nombre
                FROM promocion_producto pp
                JOIN producto p ON p.id = pp.id_producto
                WHERE pp.id_promocion = ?
            ");
            $sp->execute([$promo_id]);
            $prods_promo = $sp->fetchAll();
            foreach ($prods_promo as $pp) {
                if ($pp['estado'] === 'Agotado' || (int)$pp['cantidad'] < 1)
                    throw new RuntimeException("Stock insuficiente para: " . $pp['nombre']);

                $sMateriaPromo = $pdo->prepare("
                    SELECT r.cantidad_usada, mp.cantidad AS stock_mp, mp.nombre AS nombre_mp
                    FROM receta r
                    JOIN materia_prima mp ON mp.id = r.materia_id
                    WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
                ");
                $sMateriaPromo->execute([(int)$pp['id_producto']]);
                foreach ($sMateriaPromo->fetchAll() as $ing) {
                    if ((float)$ing['stock_mp'] < (float)$ing['cantidad_usada'])
                        throw new RuntimeException("Materia prima insuficiente: " . $ing['nombre_mp']);
                }
            }
        }

        $pdo->prepare("INSERT INTO venta (valor_total, metodo_pago, estado, usuario_id, tipo_entrega) VALUES (?,?,?,?,?)")
            ->execute([$total, $metodo, 'En cocina', $usuario_id, $tipo_entrega]);
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
                SET cantidad = GREATEST(0, CAST(cantidad AS SIGNED) - ?)
                WHERE id = ?
            ")->execute([$cant, $prod_id]);

            $pdo->prepare("
                UPDATE materia_prima mp
                JOIN receta r ON r.materia_id = mp.id
                SET mp.cantidad = GREATEST(0, mp.cantidad - (r.cantidad_usada * ?))
                WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
            ")->execute([$cant, $prod_id]);

            actualizarEstadoProducto($pdo, $prod_id);
        }

        $insPromo = $pdo->prepare("INSERT INTO venta_promocion (venta_id, promocion_id) VALUES (?,?)");
        foreach ($promos as $promo) {
            $promo_id = (int)($promo['promocion_id'] ?? 0);
            if (!$promo_id) continue;

            $insPromo->execute([$venta_id, $promo_id]);

            $sp = $pdo->prepare("
                SELECT id_producto FROM promocion_producto WHERE id_promocion = ?
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
                    SET cantidad = GREATEST(0, CAST(cantidad AS SIGNED) - 1)
                    WHERE id = ?
                ")->execute([$pid]);

                $pdo->prepare("
                    UPDATE materia_prima mp
                    JOIN receta r ON r.materia_id = mp.id
                    SET mp.cantidad = GREATEST(0, mp.cantidad - r.cantidad_usada)
                    WHERE r.producto_id = ? AND mp.estado != 'Inactivo'
                ")->execute([$pid]);

                actualizarEstadoProducto($pdo, $pid);
            }
        }

        $pdo->commit();
        registrarBitacora($pdo, $usuario_id, 'Ventas', "Registró la venta #$venta_id por valor de $" . number_format($total, 0, ',', '.'));
        jsonResponse(['success' => true, 'venta_id' => $venta_id]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $estado = limpiar($body['estado']      ?? '');
    $metodo = limpiar($body['metodo_pago'] ?? '');

    if (!$estado && !$metodo) jsonResponse(['error' => 'Sin datos para actualizar'], 400);

  $estados_validos = [
    'En cocina','En barra','Pendiente de pago','Entregado','Pagado','Cancelado',
    'Listo','En camino',
    'Listo para recoger'
];
    if ($estado && !in_array($estado, $estados_validos))
        jsonResponse(['error' => 'Estado inválido'], 400);

    $sVenta = $pdo->prepare("SELECT estado, usuario_id FROM venta WHERE id = ?");
    $sVenta->execute([$id]);
    $venta = $sVenta->fetch();
    if (!$venta) jsonResponse(['error' => 'Venta no encontrada'], 404);
    
    $estadoActual = $venta['estado'];

$esAdmin = ($_SESSION['rol_usuario'] ?? '') === 'Administrador';


$esAdmin = ($_SESSION['rol_usuario'] ?? '') === 'Administrador';
    if (!$esAdmin) {
        if ((int)$venta['usuario_id'] !== (int)$_SESSION['id_usuario'])
            jsonResponse(['error' => 'No autorizado'], 403);
        if ($estado !== 'Cancelado' || $metodo)
            jsonResponse(['error' => 'Solo puedes cancelar tu pedido'], 403);
        if (!in_array($estadoActual, ['En cocina','En barra','Pendiente de pago']))
            jsonResponse(['error' => 'Este pedido ya no se puede cancelar'], 409);
    }

    $sActual = $pdo->prepare("SELECT estado FROM venta WHERE id = ?");
    $sActual->execute([$id]);
    $estadoActual = $sActual->fetchColumn();
    if ($estadoActual === false) jsonResponse(['error' => 'Venta no encontrada'], 404);

    $campos = [];
    $vals   = [];
    if ($estado) { $campos[] = 'estado = ?';      $vals[] = $estado; }
    if ($metodo) { $campos[] = 'metodo_pago = ?'; $vals[] = $metodo; }
    if (!$campos) jsonResponse(['error' => 'Sin datos para actualizar'], 400);

    $pdo->beginTransaction();
    try {
        if ($estado && $estado !== $estadoActual) {
            $eraActiva  = $estadoActual === 'Pagado';
            $seraActiva = $estado === 'Pagado';

            if ($eraActiva && !$seraActiva) {
                restaurarStockVenta($pdo, $id);
            } elseif (!$eraActiva && $seraActiva) {
                descontarStockVenta($pdo, $id);
            }
        }

        $vals[] = $id;
        $pdo->prepare("UPDATE venta SET " . implode(', ', $campos) . " WHERE id = ?")->execute($vals);

        $pdo->commit();

        $usuario_id = (int)($_SESSION['id_usuario'] ?? 0);
        if ($estado === 'Cancelado') {
            registrarBitacora($pdo, $usuario_id, 'Ventas', "Canceló la venta #$id");
        } elseif ($estado && $estado !== $estadoActual) {
            registrarBitacora($pdo, $usuario_id, 'Ventas', "Cambió la venta #$id al estado: $estado");
        } elseif ($metodo) {
            registrarBitacora($pdo, $usuario_id, 'Ventas', "Actualizó el método de pago de la venta #$id");
        }

        jsonResponse(['success' => true]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);

    $pdo->beginTransaction();
    try {
        $sActual = $pdo->prepare("SELECT estado FROM venta WHERE id = ?");
        $sActual->execute([$id]);
        $estadoActual = $sActual->fetchColumn();
        if ($estadoActual === false) {
            $pdo->rollBack();
            jsonResponse(['error' => 'Venta no encontrada'], 404);
        }

        if ($estadoActual === 'Pagado') {
            restaurarStockVenta($pdo, $id);
        }

        $pdo->prepare("DELETE FROM venta WHERE id=?")->execute([$id]);
        $pdo->commit();
        registrarBitacora($pdo, (int)($_SESSION['id_usuario'] ?? 0), 'Ventas', "Eliminó la venta #$id");
        jsonResponse(['success' => true]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 422);
    }
}
 
jsonResponse(['error' => 'Método no permitido'], 405);
?>