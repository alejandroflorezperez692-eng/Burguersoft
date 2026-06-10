<?php

session_start();
require_once 'includes/conexion.php';
require_once 'includes/funciones.php';
require_once 'includes/enviar_correo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('recuperar_contrasena.php');
}

$correo = limpiar($_POST['correo'] ?? '');

if (!$correo || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['mensaje']      = 'Por favor ingresa un correo válido.';
    $_SESSION['tipo_mensaje'] = 'error';
    redirigir('recuperar_contrasena.php');
}

$stmt = $conn->prepare("SELECT id FROM usuario WHERE correo = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['mensaje']      = 'Si el correo está registrado recibirás el código en breve.';
    $_SESSION['tipo_mensaje'] = 'exito';
    $stmt->close();
    redirigir('recuperar_contrasena.php');
}
$stmt->close();

$codigo     = strval(random_int(100000, 999999));
$expiracion = date('Y-m-d H:i:s', time() + 1800); 

$stmt = $conn->prepare(
    "UPDATE usuario SET token_recuperacion = ?, expiracion_token = ? WHERE correo = ?"
);
$stmt->bind_param('sss', $codigo, $expiracion, $correo);
$stmt->execute();
$stmt->close();

$enviado = enviarCodigoRecuperacion($correo, $codigo);

if ($enviado) {
    $_SESSION['correo_recuperacion'] = $correo;
    redirigir('verificar_codigo.php');
} else {
    $_SESSION['mensaje']      = 'Error al enviar el correo. Intenta nuevamente.';
    $_SESSION['tipo_mensaje'] = 'error';
    redirigir('recuperar_contrasena.php');
}
?>
