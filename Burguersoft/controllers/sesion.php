<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

if (empty($_SESSION['id_usuario'])) {
    jsonResponse(['error' => 'No autorizado'], 401);
}


jsonResponse([
    'id'       => (int)$_SESSION['id_usuario'],
    'nombre'   => $_SESSION['nombre']      ?? '',
    'apellido' => $_SESSION['apellido']    ?? '',
    'correo'   => $_SESSION['correo']      ?? '',
    'rol'      => $_SESSION['rol_usuario'] ?? '',
]);
