<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    try {
        $datos = $pdo->query("
            SELECT mp.*, m.nombre AS nombre_marca
            FROM materia_prima mp
            LEFT JOIN marca m ON m.id = mp.marca_id
            WHERE mp.estado != 'Inactivo'
            ORDER BY mp.nombre
        ")->fetchAll();
        jsonResponse($datos);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error al consultar: ' . $e->getMessage()], 500);
    }
}

if ($method === 'POST') {
    $body     = json_decode(file_get_contents('php://input'), true) ?? [];
    $nombre   = limpiar($body['nombre']        ?? '');
    $tipo     = limpiar($body['tipo']          ?? '');
    $valor    = (float)($body['valor']         ?? 0);
    $cantidad = limpiar($body['cantidad']      ?? '0');
    $unidad   = limpiar($body['unidad_medida'] ?? '');
    $marca_id = !empty($body['marca_id']) ? (int)$body['marca_id'] : null;
    
    $estado   = 'Disponible'; 

    if (!$nombre || !$tipo || !$unidad) jsonResponse(['error' => 'Faltan campos'], 400);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO materia_prima (nombre, tipo, valor, cantidad, unidad_medida, marca_id, estado) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$nombre, $tipo, $valor, $cantidad, $unidad, $marca_id, $estado]);
        jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error de Base de Datos: ' . $e->getMessage()], 500);
    }
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body     = json_decode(file_get_contents('php://input'), true) ?? [];
    $nombre   = limpiar($body['nombre']        ?? '');
    $tipo     = limpiar($body['tipo']          ?? '');
    $unidad   = limpiar($body['unidad_medida'] ?? '');
    $valor    = (float)($body['valor']         ?? 0);
    $cantidad = limpiar($body['cantidad']      ?? '0');
    $marca_id = !empty($body['marca_id']) ? (int)$body['marca_id'] : null;
    
    try {
        $pdo->prepare("UPDATE materia_prima SET nombre=?, tipo=?, unidad_medida=?, valor=?, cantidad=?, marca_id=? WHERE id=?")
            ->execute([$nombre, $tipo, $unidad, $valor, $cantidad, $marca_id, $id]);
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error de Base de Datos: ' . $e->getMessage()], 500);
    }
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    try {
        $pdo->prepare("UPDATE materia_prima SET estado = 'Inactivo' WHERE id=?")->execute([$id]);
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'No se pudo dar de baja la materia prima'], 409);
    }
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>