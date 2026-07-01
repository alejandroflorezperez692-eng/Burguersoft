<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    $marcas = $pdo->query("SELECT * FROM marca ORDER BY nombre")->fetchAll();
    jsonResponse($marcas);
}

if ($method === 'POST') {
    $body     = json_decode(file_get_contents('php://input'), true) ?? [];
    $nombre   = limpiar($body['nombre']   ?? '');
    $imagen   = limpiar($body['img']      ?? '');
    $telefono = limpiar($body['telefono'] ?? '');
    $correo   = limpiar($body['correo']   ?? '');
    $nit      = limpiar($body['nit']      ?? '');
    $estado   = limpiar($body['estado']   ?? '');
    if (!$nombre || !$nit) jsonResponse(['error' => 'Nombre y NIT son requeridos'], 400);
    $pdo->prepare("INSERT INTO marca (nombre, img, telefono, correo, nit, estado) VALUES (?,?,?,?,?,?)")
        ->execute([$nombre, $imagen, $telefono, $correo, $nit, $estado]);
    registrarBitacora($pdo, (int)$_SESSION['id_usuario'], 'Marcas', "Registró la marca: $nombre");
    jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body     = json_decode(file_get_contents('php://input'), true) ?? [];
    $nombre   = limpiar($body['nombre']   ?? '');
    $imagen   = limpiar($body['img']      ?? '');
    $telefono = limpiar($body['telefono'] ?? '');
    $correo   = limpiar($body['correo']   ?? '');
    $nit      = limpiar($body['nit']      ?? '');
    $estado   = limpiar($body['estado']   ?? '');
    $pdo->prepare("UPDATE marca SET nombre=?, img=?, telefono=?, correo=?, nit=?, estado=? WHERE id=?")
        ->execute([$nombre, $imagen, $telefono, $correo, $nit, $estado, $id]);
    registrarBitacora($pdo, (int)$_SESSION['id_usuario'], 'Marcas', "Editó la marca: $nombre");
    jsonResponse(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    try {
        $sNombre = $pdo->prepare("SELECT nombre FROM marca WHERE id = ?");
        $sNombre->execute([$id]);
        $nombre = $sNombre->fetchColumn() ?: "#$id";

        $pdo->prepare("DELETE FROM marca WHERE id=?")->execute([$id]);
        registrarBitacora($pdo, (int)$_SESSION['id_usuario'], 'Marcas', "Eliminó la marca: $nombre");
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'No se puede eliminar: marca en uso'], 409);
    }
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>