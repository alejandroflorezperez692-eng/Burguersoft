<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo        = getPDO();
$method     = $_SERVER['REQUEST_METHOD'];
$accion     = $_GET['accion'] ?? 'reciente';
$usuario_id = (int)$_SESSION['id_usuario'];

if ($method !== 'GET') jsonResponse(['error' => 'Método no permitido'], 405);

$limite = $accion === 'todo' ? 300 : 6;

$s = $pdo->prepare("
    SELECT id, modulo, descripcion, fecha
    FROM historial
    WHERE usuario_id = ?
    ORDER BY fecha DESC, id DESC
    LIMIT $limite
");
$s->execute([$usuario_id]);
jsonResponse($s->fetchAll());
?>
