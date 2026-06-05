<?php
$db_host     = 'localhost';
$db_usuario  = 'root';
$db_password = '';
$db_nombre   = 'burguersoft';

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