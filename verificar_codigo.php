<?php
session_start();
require_once 'includes/conexion.php';
require_once 'includes/funciones.php';

if (empty($_SESSION['correo_recuperacion'])) {
    redirigir('recuperar_contrasena.php');
}

$correo = $_SESSION['correo_recuperacion'];
$error  = '';
$bloqueado = false;
$segundos_restantes = 0;
$LIMITE_INTENTOS = 3;
$TIEMPO_BLOQUEO  = 60; 


if (isset($_SESSION['codigo_bloqueado_hasta'])) {
    $restante = $_SESSION['codigo_bloqueado_hasta'] - time();
    if ($restante > 0) {
        $bloqueado          = true;
        $segundos_restantes = $restante;
        $error = "Demasiados intentos fallidos. Espera <span id='countdown'>{$segundos_restantes}</span> segundo(s) para intentar de nuevo.";
    } else {
    
        unset($_SESSION['codigo_bloqueado_hasta'], $_SESSION['codigo_intentos']);
    }
}

if (!$bloqueado && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = '';
    for ($i = 1; $i <= 6; $i++) {
        $codigo .= preg_replace('/\D/', '', $_POST['d' . $i] ?? '');
    }

    if (strlen($codigo) !== 6) {
        $error = 'Ingresa los 6 dígitos del código.';
    } else {
        $stmt = $conn->prepare(
            "SELECT id FROM usuario
             WHERE correo              = ?
               AND token_recuperacion  = ?
               AND expiracion_token    > NOW()"
        );
        $stmt->bind_param('ss', $correo, $codigo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
     
            $_SESSION['codigo_intentos'] = ($_SESSION['codigo_intentos'] ?? 0) + 1;
            $intentos_restantes = $LIMITE_INTENTOS - $_SESSION['codigo_intentos'];

            if ($_SESSION['codigo_intentos'] >= $LIMITE_INTENTOS) {
                $_SESSION['codigo_bloqueado_hasta'] = time() + $TIEMPO_BLOQUEO;
                $bloqueado          = true;
                $segundos_restantes = $TIEMPO_BLOQUEO;
                $error = "Demasiados intentos fallidos. Espera <span id='countdown'>{$segundos_restantes}</span> segundo(s) para intentar de nuevo.";
            } else {
                $error = 'Código incorrecto o expirado. Te quedan ' . $intentos_restantes . ' intento(s).';
            }
        } else {
          
            unset($_SESSION['codigo_intentos'], $_SESSION['codigo_bloqueado_hasta']);
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
    <title>BURGUERSOFT - Verificar código</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <link rel="icon" href="estilos/img/icono.png" type="image/x-icon">
    <style>
        html {
            height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        footer {
            margin-top: 0 !important;
        }

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
        .error-bloqueo {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 10px 14px;
            color: #856404;
            text-align: center;
            margin-bottom: 14px;
            font-weight: 600;
        }
        .error-normal {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="estilos/img/icono1-oscuro.png" class="logo">
        <a href="php/login.php" class="btn-regresar">Regresar</a>
    </div>

    <div class="contenedor-login">
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
            <p class="<?= $bloqueado ? 'error-bloqueo' : 'error-normal' ?>">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="formCodigo">
            <div class="codigo-inputs">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" name="d<?= $i ?>" id="d<?= $i ?>"
                           maxlength="1" inputmode="numeric" pattern="[0-9]"
                           autocomplete="off" required
                           <?= $bloqueado ? 'disabled' : '' ?>>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn-primario"
                <?= $bloqueado ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                VERIFICAR CÓDIGO
            </button>
        </form>

        <p class="enlace-externo" style="margin-top:16px;font-size:14px;">
            ¿No recibiste el código?
            <a href="php/recuperar_contrasena.php">Enviar de nuevo</a>

        </p>
    </div>
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
    const inputs = document.querySelectorAll('.codigo-inputs input');

    inputs.forEach((input, i) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && i > 0) {
                inputs[i - 1].focus();
            }
        });
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
    

    if (!<?= $bloqueado ? 'true' : 'false' ?>) {
        inputs[0].focus();
    }


    <?php if ($bloqueado && $segundos_restantes > 0): ?>
    (function() {
        let segundos = <?= $segundos_restantes ?>;
        const el  = document.getElementById('countdown');

        const intervalo = setInterval(() => {
            segundos--;
            if (el) el.textContent = segundos;
            if (segundos <= 0) {
                clearInterval(intervalo);
                window.location.reload();
            }
        }, 1000);
    })();
    <?php endif; ?>
    </script>
    <script>
   
    window.addEventListener('DOMContentLoaded', () => {
        mostrarToastCodigo(' El codigo ha sido enviado a tu correo, insertalo para crear tu nueva contraseña');
    });

    let _toastTimer = null;

    function mostrarToastCodigo(mensaje) {
        let toast = document.getElementById('toastCodigo');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toastCodigo';
            toast.style.cssText = `
                position: fixed; top: 20px; left: 50%;
                transform: translateX(-50%) translateY(-20px);
                padding: 18px 28px; border-radius: 10px;
                font-size: 14px; font-weight: 600;
                box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                opacity: 0; z-index: 99999;
                transition: opacity 0.4s ease, transform 0.4s ease;
                pointer-events: none; max-width: 90%; text-align: center;
                background: #2f2a1f; color: #f6f5f2;
                border: 2.5px solid #E8821A;
            `;
            document.body.appendChild(toast);
        }

        toast.textContent = mensaje;
        toast.style.opacity   = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';

        if (_toastTimer) clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateX(-50%) translateY(-20px)';
        }, 3500);
    }
</script>

    <footer style="margin-top: 70px !important;">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-text">
                    <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                        <img src="estilos/img/icono1-oscuro.png" alt="Logo de El Oriente" class="footer-logo">
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