<?php

session_start();
require_once 'includes/conexion.php';
require_once 'includes/funciones.php';

if (empty($_SESSION['correo_recuperacion'])) {
    redirigir('recuperar_contrasena.php');
}

$correo = $_SESSION['correo_recuperacion'];
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = '';
    for ($i = 1; $i <= 6; $i++) {
        $codigo .= preg_replace('/\D/', '', $_POST['d' . $i] ?? '');
    }

    if (strlen($codigo) !== 6) {
        $error = 'Ingresa los 6 dígitos del código.';
    } else {
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
