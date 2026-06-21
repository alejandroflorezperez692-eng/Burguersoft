<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

if (empty($_SESSION['id_usuario'])) {
    jsonResponse(['error' => 'No autorizado'], 401);
}

// Devuelve los datos de quien tiene la sesión iniciada en este momento.
// Si es un cliente comprando desde el carrito, es el cliente.
// Si es el administrador llenando el formulario de "Nueva Venta", es el administrador.
jsonResponse([
    'id'       => (int)$_SESSION['id_usuario'],
    'nombre'   => $_SESSION['nombre']      ?? '',
    'apellido' => $_SESSION['apellido']    ?? '',
    'correo'   => $_SESSION['correo']      ?? '',
    'rol'      => $_SESSION['rol_usuario'] ?? '',
]);
