<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
$paginaActiva = 'oriente';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Mis Pedidos</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <script src="../js/Hero-Carrusel.js" defer></script>
</head>
<body>
<h1>Mis pedidos</h1>
<br><br>
<h2>No tienes pedidos...</h2>
<div class="navbar">
        <img src="../estilos/img/icono.png" class="logo">
        <a href="../php/Burguersoft.php" class="btn-regresar"> [ Regresar ]</a>
</div>
<style>
   h1{
    text-align: center;
    color: #E8821A;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 40px;
    margin-top: 75px;
    }

    h2{
    text-align: center;
    color: #908983;
    font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    font-size: 18px;
    margin-top: 80px;
    }

    html, body {
    height: 100%;
    margin: 0;
    }

    body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    }

    .contenido {
    flex: 1;
    }
    footer{
    margin-top: 300px !important;
    }

    .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 32px;
    }

    .logo{
    width: 85px;
    height: auto;
    }

    .btn-regresar {
    background: var(--color-primario);
    color: #fff;
    padding: 9px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    border: 1px solid rgba(255, 255, 255, 0.35);
    top: auto;
    right: auto;
    }

    .btn-regresar:hover {
    background: #4b3a27 !important;
    color: #fff !important;
    cursor: pointer;
    }
</style>

<div class="acc-panel" id="accPanel">
    <div class="acc-panel-title">Accesibilidad</div>
    <div class="acc-row">
        <div class="acc-row-label">Tema</div>
        <div class="acc-row-btns">
            <button class="acc_tema" onclick="setTema('claro')">Claro</button>
            <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tamaño de letra</div>
        <div class="acc-row-btns">
            <button class="acc-btn-option" onclick="cambiarFuente(-1)">A-</button>
            <button class="acc-btn-option" onclick="cambiarFuente(1)">A+</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tipo de letra</div>
        <div class="acc-row-btns">
            <button class="acc-btn-option" onclick="aplicarFuente('Georgia, serif')">Serif</button>
            <button class="acc-btn-option" onclick="aplicarFuente('Arial, sans-serif')">Sans</button>
        </div>
    </div>
    <button class="acc-btn-reset" onclick="restablecer()">Restablecer</button>
</div>

<button class="acc-fab" id="accFab" onclick="togglePanel()">
    <img style="width: 24px; height: 24px; filter: invert(1); pointer-events: none;"
         src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>


<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                    <img src="../estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
                    <hr>
                    <h3 style="margin: 6px;">El Oriente</h3>
                </div>
                <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
            </div>
        </div>

        <div class="footer-section">
            <h4>Horarios de atención</h4>
            <ul class="footer-horarios">
                <li><span>Lunes – Viernes:</span> <span>3:30 PM – 10:00 PM</span></li>
                <li><span>Sábado:</span> <span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span> <span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>