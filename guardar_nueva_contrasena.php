<?php
session_start();
require_once 'includes/conexion.php';
require_once 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('php/recuperar_contrasena.php');
}

// Seguridad: solo accesible si pasó por verificar_codigo.php
if (empty($_SESSION['correo_recuperacion']) || empty($_SESSION['codigo_verificado'])) {
    redirigir('php/recuperar_contrasena.php');
}

$correo           = $_SESSION['correo_recuperacion'];
$nueva_contrasena = $_POST['nueva_contrasena']    ?? '';
$confirmar        = $_POST['confirmar_contrasena'] ?? '';

if (!$nueva_contrasena || !$confirmar) {
    redirigir('restablecer_contrasena.php');
}

if ($nueva_contrasena !== $confirmar) {
    redirigir('restablecer_contrasena.php');
}

if (strlen($nueva_contrasena) < 8) {
    redirigir('restablecer_contrasena.php');
}

// Hashear y guardar contraseña, limpiar token
$hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE usuario
     SET contrasena = ?, token_recuperacion = NULL, expiracion_token = NULL
     WHERE correo = ?"
);
$stmt->bind_param('ss', $hash, $correo);
$stmt->execute();
$stmt->close();

// Limpiar sesión de recuperación
unset($_SESSION['correo_recuperacion'], $_SESSION['codigo_verificado']);

$_SESSION['mensaje']      = '✅ Contraseña actualizada. Ya puedes iniciar sesión.';
$_SESSION['tipo_mensaje'] = 'exito';
redirigir('php/login.php');
?>
