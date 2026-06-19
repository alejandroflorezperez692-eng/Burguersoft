<?php
require_once __DIR__ . '/../includes/funciones.php';
iniciarSesionSegura();
$_SESSION = [];
session_destroy();
session_start();
$_SESSION['logout_exitoso'] = true;
redirigir('/burguersoft/php/Burguersoft.php');
?>