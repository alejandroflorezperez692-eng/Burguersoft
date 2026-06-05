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

// ── 1. Verificar si el correo existe ─────────────────────────
$stmt = $conn->prepare("SELECT id FROM usuario WHERE correo = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Mismo mensaje por seguridad (no revelar si existe o no)
    $_SESSION['mensaje']      = 'Si el correo está registrado recibirás el código en breve.';
    $_SESSION['tipo_mensaje'] = 'exito';
    $stmt->close();
    redirigir('recuperar_contrasena.php');
}
$stmt->close();

// ── 2. Generar código de 6 dígitos ───────────────────────────
$codigo     = strval(random_int(100000, 999999));
$expiracion = date('Y-m-d H:i:s', time() + 1800); // 30 minutos

// ── 3. Guardar código en la BD ───────────────────────────────
$stmt = $conn->prepare(
    "UPDATE usuario SET token_recuperacion = ?, expiracion_token = ? WHERE correo = ?"
);
$stmt->bind_param('sss', $codigo, $expiracion, $correo);
$stmt->execute();
$stmt->close();

// ── 4. Enviar el código por correo ───────────────────────────
$enviado = enviarCodigoRecuperacion($correo, $codigo);

if ($enviado) {
    // Guardar el correo en sesión para verificarlo en el siguiente paso
    $_SESSION['correo_recuperacion'] = $correo;
    redirigir('verificar_codigo.php');
} else {
    $_SESSION['mensaje']      = 'Error al enviar el correo. Intenta nuevamente.';
    $_SESSION['tipo_mensaje'] = 'error';
    redirigir('recuperar_contrasena.php');
}
?>
