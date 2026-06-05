<?php

session_start();
require_once 'includes/conexion.php';
require_once 'includes/funciones.php';

// Si no hay correo en sesión, mandar de vuelta al inicio
if (empty($_SESSION['correo_recuperacion'])) {
    redirigir('recuperar_contrasena.php');
}

$correo = $_SESSION['correo_recuperacion'];
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Unir los 6 inputs en un solo código
    $codigo = '';
    for ($i = 1; $i <= 6; $i++) {
        $codigo .= preg_replace('/\D/', '', $_POST['d' . $i] ?? '');
    }

    if (strlen($codigo) !== 6) {
        $error = 'Ingresa los 6 dígitos del código.';
    } else {
        // Verificar que el código exista y no haya expirado
        $stmt = $conn->prepare(
            "SELECT id FROM usuario
             WHERE correo     = ?
               AND token_recuperacion  = ?
               AND expiracion_token    > NOW()"
        );
        $stmt->bind_param('ss', $correo, $codigo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $error = 'Código incorrecto o expirado. Solicita uno nuevo.';
        } else {
            // Código válido → guardar en sesión y pasar al siguiente paso
            $_SESSION['codigo_verificado'] = true;
            $stmt->close();
            redirigir('restablecer_contrasena.php');
        }
        $stmt->close();
    }
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
    <title>Verificar código</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
    <style>
        .codigo-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 24px 0;
        }
        .codigo-inputs input {
            width: 52px;
            height: 60px;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            border: 2px solid #d4c5b5;
            border-radius: 8px;
            background: #faf8f6;
            color: #2c1810;
            outline: none;
            transition: border-color .2s;
        }
        .codigo-inputs input:focus {
            border-color: #2c1810;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="estilos/img/icono.png" class="logo">
        <a href="php/recuperar_contrasena.php" class="btn-regresar">Regresar</a>
    </div>

    <div class="header-bar">INGRESA TU CÓDIGO</div>

    <div class="card">
        <div class="icono">
            <img src="estilos/img/bloquear.png" alt="Candado">
        </div>

        <p class="descripcion">
            Te enviamos un código de 6 dígitos a<br>
            <strong><?= htmlspecialchars($correo) ?></strong>
        </p>

        <?php if ($error): ?>
            <p style="color:red;text-align:center;margin-bottom:10px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="verificar_codigo.php" id="formCodigo">
            <div class="codigo-inputs">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" name="d<?= $i ?>" id="d<?= $i ?>"
                           maxlength="1" inputmode="numeric" pattern="[0-9]"
                           autocomplete="off" required>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn-primario">Verificar código</button>
        </form>

        <p style="color:#2c1810;margin-top:16px;font-size:14px;text-align:center;">
            ¿No recibiste el código?
            <a href="php/recuperar_contrasena.php" style="color:#2c1810;font-weight:bold;">Enviar de nuevo</a>
        </p>
    </div>

    <div id="widget-accesibilidad">
        <button id="boton-accesibilidad" title="Opciones de Accesibilidad"></button>
        <div id="menu-accesibilidad">
            <h4>Panel de Accesibilidad</h4>
            <div class="opcion-acc">
                <label>Tamaño de letra: <span id="val-size">100%</span></label>
                <input type="range" id="slider-size" min="80" max="150" value="100">
            </div>
            <div class="opcion-acc">
                <label>Tipo de fuente:</label>
                <select id="select-font">
                    <option value="Arial, sans-serif">Predeterminada</option>
                    <option value="'Courier New', monospace">Monoespaciado</option>
                    <option value="'Georgia', serif">Elegante (Serif)</option>
                    <option value="'OpenDyslexic', sans-serif">Lectura Fácil</option>
                </select>
            </div>
            <div class="opcion-acc">
                <button id="btn-contraste" onclick="toggleContrast()">Activar Modo Oscuro</button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="estilos/Accesibilidad.css">
    <script src="js/Accesibilidad.js"></script>

    <script>
    // Auto-avance entre cajitas al escribir
    const inputs = document.querySelectorAll('.codigo-inputs input');

    inputs.forEach((input, i) => {
        input.addEventListener('input', function () {
            // Solo permitir números
            this.value = this.value.replace(/\D/g, '');
            if (this.value && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
        });
        input.addEventListener('keydown', function (e) {
            // Borrar retrocede al anterior
            if (e.key === 'Backspace' && !this.value && i > 0) {
                inputs[i - 1].focus();
            }
        });
        // Pegar el código completo de una vez
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pegado = (e.clipboardData || window.clipboardData)
                            .getData('text').replace(/\D/g, '').slice(0, 6);
            pegado.split('').forEach((c, idx) => {
                if (inputs[idx]) inputs[idx].value = c;
            });
            if (inputs[pegado.length - 1]) inputs[pegado.length - 1].focus();
        });
    });

    // Enfocar el primer input al cargar
    inputs[0].focus();
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
