<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

requerirAdmin();

$pdo = getPDO();
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID de la URL si existe: api_usuarios.php?id=5
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($method === 'GET' && !$id) {
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.correo,
            u.telefono,
            u.tipo_documento,
            u.Ndocumento,
            u.estado,
            r.nombre AS rol
        FROM usuario u
        LEFT JOIN rol r ON u.rol_id = r.id
        ORDER BY u.id ASC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($method === 'PUT' && $id) {
    $body = json_decode(file_get_contents('php://input'), true);

    // Obtener id del nuevo rol
    if (isset($body['rol'])) {
        $stmtRol = $pdo->prepare("SELECT id FROM rol WHERE nombre = ?");
        $stmtRol->execute([$body['rol']]);
        $rol = $stmtRol->fetch();
        if (!$rol) {
            echo json_encode(['error' => 'Rol no encontrado']);
            exit;
        }

        $pdo->prepare("UPDATE usuario SET rol_id = ? WHERE id = ?")
            ->execute([$rol['id'], $id]);
    }

    if (isset($body['estado'])) {
        $pdo->prepare("UPDATE usuario SET estado = ? WHERE id = ?")
            ->execute([$body['estado'], $id]);
    }

    echo json_encode(['success' => true]);
    exit;
}


if ($method === 'DELETE' && $id) {
    $pdo->prepare("DELETE FROM usuario WHERE id = ?")
        ->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Solicitud no válida']);