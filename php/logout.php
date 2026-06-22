<?php
require_once __DIR__ . '/../includes/funciones.php';
iniciarSesionSegura();
$_SESSION = [];
session_destroy();
session_start();
redirigir('/burguersoft/php/Burguersoft.php?toast=logout_ok');
?>

