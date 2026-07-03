<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') jsonResponse(['error' => 'Método no permitido'], 405);

iniciarSesionSegura();
$usuario_id = (int)($_SESSION['id_usuario'] ?? 0);

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$metodo = limpiar($body['metodo_pago'] ?? 'Efectivo');
$items  = $body['items'] ?? [];

if (empty($items)) jsonResponse(['error' => 'Carrito vacío'], 400);

$total = 0;
foreach ($items as $item) {
    $total += (float)($item['precio_unitario'] ?? 0) * (int)($item['cantidad'] ?? 1);
}

$pdo->beginTransaction();
try {
    $pdo->prepare("INSERT INTO venta (valor_total, metodo_pago, estado, usuario_id) VALUES (?,?,?,?)")
        ->execute([$total, $metodo, 'Pagado', $usuario_id]);
    $venta_id = (int)$pdo->lastInsertId();

    foreach ($items as $item) {
        $producto_id     = (int)($item['producto_id'] ?? $item['id_producto'] ?? 0);
        $cantidad        = (int)($item['cantidad']        ?? 1);
        $precio_unitario = (float)($item['precio_unitario'] ?? 0);
        $subtotal        = $cantidad * $precio_unitario;
        if (!$producto_id) continue;
        $pdo->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal, estado) VALUES (?,?,?,?,?,?)")
            ->execute([$venta_id, $producto_id, $cantidad, $precio_unitario, $subtotal, 'En proceso']);
    }
    $pdo->commit();
    jsonResponse(['success' => true, 'venta_id' => $venta_id]);
} catch (Throwable $e) {
    $pdo->rollBack();
    jsonResponse(['error' => 'Error al guardar pedido'], 500);
}
?>