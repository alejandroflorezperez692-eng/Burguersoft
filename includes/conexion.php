<?php
require_once __DIR__ . '/env.php';
cargarEnv(__DIR__ . '/../.env');

$db_host     = env('DB_HOST', 'localhost');
$db_usuario  = env('DB_USUARIO', 'root');
$db_password = env('DB_PASSWORD', '');
$db_nombre   = env('DB_NOMBRE', 'burguersoft');

$conn = new mysqli($db_host, $db_usuario, $db_password, $db_nombre);

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_nombre;charset=utf8mb4",
        $db_usuario,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log('Error de conexión a BD: ' . $e->getMessage());
    if (env('APP_ENV', 'local') === 'local') {
        die("Error de conexión: " . $e->getMessage());
    }
    die("Error interno del servidor. Intenta más tarde.");
}
?>
