<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);
if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador') jsonResponse(['error' => 'Sin permisos'], 403);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    jsonResponse($pdo->query("
        SELECT u.id, u.nombre, u.apellido, u.correo, u.telefono,
               u.Tdocumento, u.Ndocumento, u.estado, r.nombre AS rol
        FROM usuario u
        LEFT JOIN rol r ON r.id = u.rol_id
        ORDER BY u.nombre
    ")->fetchAll());
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    if (isset($body['rol'])) {
        $s = $pdo->prepare("SELECT id FROM rol WHERE nombre = ?");
        $s->execute([$body['rol']]);
        $rol = $s->fetch();
        if (!$rol) jsonResponse(['error' => 'Rol no encontrado'], 400);
        $pdo->prepare("UPDATE usuario SET rol_id=? WHERE id=?")->execute([$rol['id'], $id]);
    }

    if (isset($body['estado'])) {
        $pdo->prepare("UPDATE usuario SET estado=? WHERE id=?")->execute([$body['estado'], $id]);
    }

    jsonResponse(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    if ($id === (int)$_SESSION['id_usuario']) jsonResponse(['error' => 'No puedes eliminarte a ti mismo'], 400);
    $pdo->prepare("DELETE FROM usuario WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>