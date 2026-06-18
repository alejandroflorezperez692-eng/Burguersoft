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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Nueva Contraseña</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
    <style>

        body, input, button, select, textarea {
            font-family: 'Lato', sans-serif;
            color: #2c1810;
        }

        .header-bar, h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        .campo {
            margin-bottom: 12px;
            text-align: left;
        }

        .campo label {
            display: block;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 4px; 
            color: #2c1810;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .input-wrapper { 
            position: relative; 
            display: flex; 
            align-items: center; 
            width: 100%;
        }

        .input-wrapper input { 
            width: 100%;
            height: 42px; 
            padding: 8px 85px 8px 14px; 
            box-sizing: border-box;
            border: 1px solid transparent;
            border-radius: 10px;
            color: #2c1810;
            font-size: 14px;
            font-weight: 400;
            outline: none;
            transition: all .2s ease;
        }
      
        #password {
            background: #e8f0fe; 
        }
        #confirmar-password {
            background: #faf6f0;
            border: 1px solid #eadecc;
        }
        
        .input-wrapper input:focus {
            border-color: #2c1810;
            background: #fff !important;
            box-shadow: 0 0 0 3px rgba(44, 24, 16, 0.06);
        }

        .btn-toggle-pass {
            position: absolute; 
            right: 14px;
            background: none; 
            border: none; 
            cursor: pointer; 
            font-size: 13px;
            font-weight: 700; 
            color: #E8821A;
            user-select: none;
            transition: color .2s;
        }
        
        #requisitos {
            list-style: none;
            padding: 0;
            font-size: 12px;
            margin: 8px 0 4px 4px; 
            color: #666;
            line-height: 1.4;
        }
        #requisitos li {
            margin-bottom: 4px; 
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #requisitos li.valido {
            color: #27ae60;
            font-weight: 700;
        }
        #requisitos li.invalido {
            color: #e74c3c;
        }
        
        #contenedor-barra {
            height: 4px;
            width: 100%;
            background: #e0e0e0;
            margin-top: 6px;
            border-radius: 4px;
            overflow: hidden;
        }

        .btn-primario {
            margin-top: 9px; 
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="estilos/img/icono.png" class="logo" alt="Logo">
        <a href="php/login.php" class="btn-regresar">[ Regresar ]</a>
    </div>

    <div class="header-bar">NUEVA CONTRASEÑA</div>

    <div class="card" style="padding: 20px;"> <div class="icono" style="margin-bottom: 10px;">
            <img src="estilos/img/bloquear.png" alt="Candado" style="width: 50px; height: auto;">
        </div>

        <p class="descripcion" style="margin-bottom: 15px; font-size: 14px;">Ingresa y confirma tu nueva contraseña.</p>

        <form method="POST" action="guardar_nueva_contrasena.php" id="formNuevaPass">

            <div class="campo">
                <label for="password">NUEVA CONTRASEÑA*</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required
                           placeholder="Mínimo 8 caracteres">
                    <button type="button" class="btn-toggle-pass" onclick="togglePassword('password', this)"
                            onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#E8821A'">
                        Mostrar
                    </button>
                </div>
                
                <div id="contenedor-barra">
                    <div id="progreso" style="height:100%; width:0%; background:#ddd; transition:0.3s;"></div>
                </div>

                <ul id="requisitos">
                    <li id="longitud" class="invalido">❌ Mínimo 8 caracteres</li>
                    <li id="mayuscula" class="invalido">❌ Al menos una mayúscula</li>
                    <li id="numero" class="invalido">❌ Al menos un número</li>
                    <li id="especial" class="invalido">❌ Al menos un símbolo (@, #, $, etc.)</li>
                </ul>
            </div>

            <div class="campo">
                <label for="confirmar-password">CONFIRMAR CONTRASEÑA*</label>
                <div class="input-wrapper">
                    <input type="password" id="confirmar-password" required
                           placeholder="Repite tu contraseña">
                    <button type="button" class="btn-toggle-pass" onclick="togglePassword('confirmar-password', this)"
                            onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#E8821A'">
                        Mostrar
                    </button>
                </div>
                <p id="msg-confirmar" style="font-size:12px; margin-top:4px; min-height:14px; font-weight: bold; text-align: left;"></p>
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

    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmar-password');
    const progreso = document.getElementById('progreso');
    const msgConfirmar = document.getElementById('msg-confirmar');

    const requisitos = {
        longitud: { re: /.{8,}/, el: document.getElementById('longitud'), text: 'Mínimo 8 caracteres' },
        mayuscula: { re: /[A-Z]/, el: document.getElementById('mayuscula'), text: 'Al menos una mayúscula' },
        numero: { re: /[0-9]/, el: document.getElementById('numero'), text: 'Al menos un número' },
        especial: { re: /[@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?!]/, el: document.getElementById('especial'), text: 'Al menos un símbolo (@, #, $, etc.)' }
    };

    function evaluarPassword() {
        const val = passwordInput.value;
        let cumplidos = 0;

        for (let key in requisitos) {
            if (requisitos[key].re.test(val)) {
                requisitos[key].el.innerHTML = '✅' + requisitos[key].text;
                requisitos[key].el.className = 'valido';
                cumplidos++;
            } else {
                requisitos[key].el.innerHTML = '❌ ' + requisitos[key].text;
                requisitos[key].el.className = 'invalido';
            }
        }

        const porcentaje = (cumplidos / 4) * 100;
        progreso.style.width = porcentaje + '%';

        if (cumplidos === 0) {
            progreso.style.background = '#ddd'; 
        } else if (cumplidos <= 1) {
            progreso.style.background = '#e74c3c'; 
        } else if (cumplidos <= 3) {
            progreso.style.background = '#f39c12'; 
        } else {
            progreso.style.background = '#27ae60'; 
        }
    }

    function verificarCoincidencia() {
        if (!confirmInput.value) {
            msgConfirmar.innerHTML = '';
            confirmInput.style.background = '#faf6f0';
            confirmInput.style.borderColor = '#eadecc';
            return;
        }
        if (passwordInput.value === confirmInput.value) {
            msgConfirmar.innerHTML = '✅ Las contraseñas coinciden';
            msgConfirmar.style.color = '#27ae60';
            confirmInput.style.background = '#fff';
            confirmInput.style.borderColor = '#27ae60';
        } else {
            msgConfirmar.innerHTML = '❌ Las contraseñas no coinciden';
            msgConfirmar.style.color = '#e74c3c';
            confirmInput.style.background = '#fff';
            confirmInput.style.borderColor = '#e74c3c';
        }
    }

    passwordInput.addEventListener('input', () => {
        evaluarPassword();
        verificarCoincidencia();
    });
    
    confirmInput.addEventListener('input', verificarCoincidencia);

    document.getElementById('formNuevaPass').addEventListener('submit', function (e) {
        let cumplidos = 0;
        for (let key in requisitos) {
            if (requisitos[key].re.test(passwordInput.value)) cumplidos++;
        }

        if (cumplidos < 4) {
            e.preventDefault();
            alert('Por favor, cumple con todos los requisitos de seguridad.');
            return;
        }

        if (passwordInput.value !== confirmInput.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
        }
    });
    </script>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-text">
                    <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
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