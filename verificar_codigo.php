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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Nueva contraseña</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
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
                <label for="password">COMFIRMAR CONTRASEÑA*</label>
                <div class="campo-input-wrap">
                    <input type="password" id="password" name="password"
                        class="campo-input"
                        placeholder="Mínimo 8 caracteres"
                        oninput="evaluarPassword(this.value); verificarCoincidencia();">
                    <button type="button" class="campo-btn-mostrar"
                        onclick="togglePassword('password', this)">Mostrar</button>
                </div>
                <div class="campo-barra-wrap">
                    <div id="progreso"></div>
                </div>
                <ul class="campo-requisitos">
                    <li id="longitud">❌ Mínimo 8 caracteres</li>
                    <li id="mayuscula">❌ Al menos una mayúscula</li>
                    <li id="numero">❌ Al menos un número</li>
                    <li id="especial">❌ Al menos un símbolo (@, #, $, etc.)</li>
                </ul>
            </div>

            <!-- Campo confirmar contraseña -->
            <div class="campo">
                <label for="confirmar-password">CONFIRMAR CONTRASEÑA*</label>
                <div class="campo-input-wrap">
                    <input type="password" id="confirmar-password"
                        class="campo-input"
                        placeholder="Repite tu contraseña"
                        oninput="verificarCoincidencia()">
                    <button type="button" class="campo-btn-mostrar"
                        onclick="togglePassword('confirmar-password', this)">Mostrar</button>
                </div>
                <p id="msg-confirmar" class="campo-msg"></p>
            </div>

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
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Ocultar';
        } else {
            input.type = 'password';
            btn.textContent = 'Mostrar';
        }
    }

    function evaluarPassword(valor) {
        const longitud  = valor.length >= 8;
        const mayuscula = /[A-Z]/.test(valor);
        const numero    = /[0-9]/.test(valor);
        const especial  = /[@#$%^&*!]/.test(valor);

        document.getElementById('longitud').innerHTML  = (longitud  ? '✅' : '❌') + ' Mínimo 8 caracteres';
        document.getElementById('mayuscula').innerHTML = (mayuscula ? '✅' : '❌') + ' Al menos una mayúscula';
        document.getElementById('numero').innerHTML    = (numero    ? '✅' : '❌') + ' Al menos un número';
        document.getElementById('especial').innerHTML  = (especial  ? '✅' : '❌') + ' Al menos un símbolo (@, #, $, etc.)';

        const cumplidos = [longitud, mayuscula, numero, especial].filter(Boolean).length;
        const progreso  = document.getElementById('progreso');
        const colores   = ['#e0e0e0', '#e74c3c', '#f39c12', '#f39c12', '#27ae60'];
        progreso.style.width      = (cumplidos * 25) + '%';
        progreso.style.background = colores[cumplidos];
    }

    function verificarCoincidencia() {
        const p1  = document.getElementById('password').value;
        const p2  = document.getElementById('confirmar-password').value;
        const msg = document.getElementById('msg-confirmar');
        if (p2.length === 0) {
            msg.textContent = '';
        } else if (p1 === p2) {
            msg.textContent = '✅ Las contraseñas coinciden';
            msg.style.color = '#27ae60';
        } else {
            msg.textContent = '❌ Las contraseñas no coinciden';
            msg.style.color = '#e74c3c';
        }
    }

    document.getElementById('formNuevaPass').addEventListener('submit', function (e) {
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('confirmar-password').value;
        if (p1 !== p2) {
            e.preventDefault();
            document.getElementById('msg-confirmar').textContent = '❌ Las contraseñas no coinciden';
            document.getElementById('msg-confirmar').style.color = '#e74c3c';
        }
    });
    </script>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-text">
                    <div style="display:flex;align-items:center;gap:8px;justify-content:center;margin-bottom:10px;margin-top:-30px;">
                        <img src="estilos/img/icono.png" alt="Logo" class="footer-logo">
                        <hr>
                        <h3 style="margin:6px;">El Oriente</h3>
                    </div>
                    <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
                </div>
            </div>
            <div class="footer-section">
                <h4>Horarios de atención</h4>
                <ul class="footer-horarios">
                    <li><span>Lunes – Viernes:</span><span>3:30 PM – 10:00 PM</span></li>
                    <li><span>Sábado:</span><span>3:00 PM – 11:00 PM</span></li>
                    <li><span>Domingo:</span><span>3:00 PM – 10:00 PM</span></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>