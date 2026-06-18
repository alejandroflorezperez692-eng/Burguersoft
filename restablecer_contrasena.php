<?php
session_start();
require_once 'includes/funciones.php';

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
    <title>BURGUERSOFT - Nueva contraseña</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
    <style>
        .fuerza-bar { height:6px; border-radius:4px; margin:6px 0 14px; background:#ddd; transition:all .3s; }
        .fuerza-bar.debil  { background:#e74c3c; width:33%; }
        .fuerza-bar.media  { background:#f39c12; width:66%; }
        .fuerza-bar.fuerte { background:#27ae60; width:100%; }
        .input-wrapper { position:relative; display:flex; align-items:center; }
        .input-wrapper .input { flex:1; padding-right:90px; }
        .btn-toggle-pass {
            position:absolute; right:8px;
            background:#555; color:#fff;
            border:none; border-radius:5px;
            padding:5px 10px; font-size:12px;
            cursor:pointer; white-space:nowrap;
            transition:background .2s;
        }
        .btn-toggle-pass:hover { background:#333; }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="estilos/img/icono.png" class="logo">
        <a href="../burguersoft/php/login.php" class="btn-regresar">[ Regresar ]</a>
    </div>

    <div class="header-bar">NUEVA CONTRASEÑA</div>

    <div class="card">
        <div class="icono">
            <img src="estilos/img/bloquear.png" alt="Candado">
        </div>

        <p class="descripcion">Ingresa y confirma tu nueva contraseña.</p>

        <form method="POST" action="guardar_nueva_contrasena.php" id="formNuevaPass">

           <div class="campo">
                <label for="password">NUEVACONTRASEÑA*</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" required
                        placeholder="Mínimo 8 caracteres"
                        oninput="evaluarPassword(this.value); verificarCoincidencia();"
                        style="padding-right:80px; width:100%;">
                    <button type="button" onclick="togglePassword('password', this)"
                        onmouseover="this.style.color='#000000'"
                        onmouseout="this.style.color='#E8821A'"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                            background:none; border:none; cursor:pointer; font-size:13px;
                            font-weight:700; color:#E8821A;">
                        Mostrar
                    </button>
                </div>
                <div id="contenedor-barra" style="height:6px;width:100%;background:#e0e0e0;margin-top:5px;border-radius:4px;overflow:hidden;">
                    <div id="progreso" style="height:100%;width:0%;transition:0.3s;"></div>
                </div>
                <ul id="requisitos" style="list-style:none;padding:0;font-size:12px;margin-top:10px;color:#666;">
                    <li id="longitud">❌ Mínimo 8 caracteres</li>
                    <li id="mayuscula">❌ Al menos una mayúscula</li>
                    <li id="numero">❌ Al menos un número</li>
                    <li id="especial">❌ Al menos un símbolo (@, #, $, etc.)</li>
                </ul>
            </div>
            <br>

            <div class="campo">
                <label for="confirmar-password">CONFIRMAR CONTRASEÑA*</label>
                <div style="position:relative;">
                    <input type="password" id="confirmar-password" required
                        placeholder="Repite tu contraseña"
                        oninput="verificarCoincidencia()"
                        style="padding-right:80px; width:100%;">
                    <button type="button" onclick="togglePassword('confirmar-password', this)"
                        onmouseover="this.style.color='#000000'"
                        onmouseout="this.style.color='#E8821A'"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                            background:none; border:none; cursor:pointer; font-size:13px;
                            font-weight:700; color:#E8821A;">
                        Mostrar
                    </button>
                </div>
                <p id="msg-confirmar" style="font-size:12px;margin-top:5px;min-height:16px;"></p>
            </div>
            <button type="submit" class="btn-primario">Cambiar contraseña</button>
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
    function togglePassword(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn   = document.getElementById(btnId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Ocultar';
        } else {
            input.type = 'password';
            btn.textContent = 'Mostrar';
        }
    }

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