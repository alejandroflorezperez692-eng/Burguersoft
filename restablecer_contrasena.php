<?php
session_start();
require_once 'includes/funciones.php';

// Seguridad: solo entra si pasó por verificar_codigo.php
if (empty($_SESSION['correo_recuperacion']) || empty($_SESSION['codigo_verificado'])) {
    redirigir('recuperar_contrasena.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
    <style>
        .fuerza-bar { height:6px; border-radius:4px; margin:6px 0 14px; background:#ddd; transition:all .3s; }
        .fuerza-bar.debil  { background:#e74c3c; width:33%; }
        .fuerza-bar.media  { background:#f39c12; width:66%; }
        .fuerza-bar.fuerte { background:#27ae60; width:100%; }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="estilos/img/icono.png" class="logo">
        <a href="../burguersoft/php/login.php" class="btn-regresar">Ir al login</a>
    </div>

    <div class="header-bar">NUEVA CONTRASEÑA</div>

    <div class="card">
        <div class="icono">
            <img src="estilos/img/bloquear.png" alt="Candado">
        </div>

        <p class="descripcion">Ingresa y confirma tu nueva contraseña.</p>

        <form method="POST" action="guardar_nueva_contrasena.php" id="formNuevaPass">

            <h2>NUEVA CONTRASEÑA</h2>
            <input type="password" name="nueva_contrasena" id="nuevaPass" class="input"
                   placeholder="Mínimo 8 caracteres" required minlength="8">
            <div class="fuerza-bar" id="fuerzaBar"></div>

            <h2>CONFIRMAR CONTRASEÑA</h2>
            <input type="password" name="confirmar_contrasena" id="confirmaPass" class="input"
                   placeholder="Repite la contraseña" required>

            <p id="matchMsg" style="font-size:13px;text-align:center;color:red;display:none;">
                Las contraseñas no coinciden.
            </p>

            <button type="submit" class="btn-primario">Guardar contraseña</button>
        </form>
    </div>

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
            <button class="acc-btn-option" onclick="cambiarFuente(-1)">A−</button>
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
    <img style="width:22px;height:22px;filter:invert(1);pointer-events:none;" src="estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>
<link rel="stylesheet" href="estilos/accesibilidad.css">
<script src="js/accesibilidad.js"></script>

    <script>
    document.getElementById('nuevaPass').addEventListener('input', function () {
        const bar = document.getElementById('fuerzaBar');
        const v = this.value;
        if (v.length < 6) bar.className = 'fuerza-bar debil';
        else if (v.length < 10 || !/[A-Z]/.test(v) || !/[0-9]/.test(v)) bar.className = 'fuerza-bar media';
        else bar.className = 'fuerza-bar fuerte';
    });

    document.getElementById('formNuevaPass').addEventListener('submit', function (e) {
        const p1 = document.getElementById('nuevaPass').value;
        const p2 = document.getElementById('confirmaPass').value;
        if (p1 !== p2) {
            e.preventDefault();
            document.getElementById('matchMsg').style.display = 'block';
        }
    });
    </script>

    <footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style =" display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                    <img src="estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
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
