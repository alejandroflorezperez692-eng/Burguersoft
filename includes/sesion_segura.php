<?php
/**
 * Incluir esto en lugar de llamar session_start() directamente.
 * Configura cookies HttpOnly/Secure/SameSite ANTES de abrir la sesión
 * y agrega las cabeceras de seguridad HTTP a la respuesta.
 */
require_once __DIR__ . '/seguridad.php';

if (session_status() === PHP_SESSION_NONE) {
    configurarCookieSesion();
    session_start();
}
enviarCabecerasSeguridad();
?>
