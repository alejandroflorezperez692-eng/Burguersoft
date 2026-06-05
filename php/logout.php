<?php
require_once __DIR__ . '/../includes/funciones.php';
iniciarSesionSegura();
session_destroy();
redirigir('/burguersoft/php/Burguersoft.php');
?>
