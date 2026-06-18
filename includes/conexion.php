<?php
$db_host     = 'localhost';
$db_usuario  = 'root';
$db_password = '46374491';
$db_nombre   = 'burguersoft';

$conn = new mysqli($db_host, $db_usuario, $db_password, $db_nombre);

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_nombre;charset=utf8mb4",
        $db_usuario,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>