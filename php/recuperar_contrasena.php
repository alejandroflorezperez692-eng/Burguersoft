<?php

session_start();

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo    = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap" media="print" onload="this.media='all'">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Recuperar Contraseña</title>
    <link rel="stylesheet" href="../estilos/estilos-login.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
</head>
<body>
    <div class="navbar">
        <img src="../estilos/img/icono.png" class="logo">
        <a href="login.php" class="btn-regresar">[ Regresar ]</a>
    </div>

    <div class="header-bar">¿TIENES PROBLEMAS?</div>

    <div class="card">
        <div class="icono">
            <img src="../estilos/img/bloquear.png" alt="Imagen Bloqueo">
        </div>

        <p class="descripcion">
            Ingresa tu correo electrónico registrado para recibir el enlace de recuperación.
        </p>

        <?php if ($mensaje): ?>
            <p style="color:<?= $tipo === 'error' ? 'red' : 'green' ?>;
                       text-align:center;margin-bottom:10px;">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        <?php endif; ?>

        
        <form method="POST" action="../procesar_recuperacion.php">
            <input id="rec-email" name="correo" type="email" class="input"
                   placeholder="Correo electrónico (obligatorio)" required>
            <button type="submit" class="btn-primario">Enviar código de recuperación</button>
        </form>

        <p style="color: #2c1810;">¿No puedes cambiar la contraseña?</p>

        <div class="separador-contenedor">
            <div class="linea"></div>
            <span class="circulo">Entonces...</span>
            <div class="linea"></div>
        </div>

        <a href="/burguersoft/php/registro.php" class="btn-secundario">Crear cuenta nueva</a>
    </div>

<div class="acc-panel" id="accPanel">
    <div class="acc-panel-title"> Accesibilidad</div>
    <div class="acc-row">
        <div class="acc-row-label">Tema</div>
        <div class="acc-row-btns">
            <button class="acc_tema" onclick="setTema('claro')">Claro</button>
            <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tamano de letra</div>
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

<button class="acc-fab" id="accFab" onclick="togglePanel()"> <img style="width: 24px; height: 24px; filter: invert(1); pointer-events: none;"  onclick="togglePanel()" src="../estilos/img/accesibilidad.png" alt="Accesibilidad"></button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>

    <footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style =" display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
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