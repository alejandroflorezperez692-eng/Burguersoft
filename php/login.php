<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

if (isset($_SESSION['id_usuario'])) {
    redirigir('/burguersoft/php/inicio_admin.php');
}

$error              = '';
$motivo             = $_GET['motivo'] ?? ''; 
$bloqueado          = false;
$segundos_restantes = 0;
$LIMITE_INTENTOS    = 3;
$TIEMPO_BLOQUEO     = 60;

if (isset($_SESSION['login_bloqueado_hasta'])) {
    $restante = $_SESSION['login_bloqueado_hasta'] - time();
    if ($restante > 0) {
        $bloqueado          = true;
        $segundos_restantes = $restante;
        $error = "Demasiados intentos fallidos. Espera <span id='countdown'>{$segundos_restantes}</span> segundo(s) para intentar de nuevo.";
    } else {
        unset($_SESSION['login_bloqueado_hasta'], $_SESSION['login_intentos']);
    }
}

if (!$bloqueado && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = limpiar($_POST['correo']     ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($correo && $contrasena) {
        $pdo  = getPDO();
        $stmt = $pdo->prepare(
            "SELECT u.id, u.nombre, u.apellido, u.correo, u.contrasena, u.telefono, r.nombre AS nombre_rol
             FROM usuario u
             LEFT JOIN rol r ON u.rol_id = r.id
             WHERE u.correo = ?"
        );
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);

            $_SESSION['id_usuario']  = $usuario['id'];
            $_SESSION['nombre']      = $usuario['nombre'];
            $_SESSION['apellido']    = $usuario['apellido'];
            $_SESSION['correo']      = $usuario['correo'];
            $_SESSION['rol_usuario'] = $usuario['nombre_rol'];
            $_SESSION['login_exitoso'] = true; 
            if ($usuario['nombre_rol'] === 'Administrador') {
                $_SESSION['es_admin'] = true;
                redirigir('/burguersoft/php/inicio_admin.php?toast=login_ok');
            }
            else {
                redirigir('/burguersoft/php/Burguersoft.php?toast=login_ok');
            };

        } else {
            $_SESSION['login_intentos'] = ($_SESSION['login_intentos'] ?? 0) + 1;
            $intentos_restantes = $LIMITE_INTENTOS - $_SESSION['login_intentos'];

            if ($_SESSION['login_intentos'] >= $LIMITE_INTENTOS) {
                $_SESSION['login_bloqueado_hasta'] = time() + $TIEMPO_BLOQUEO;
                $bloqueado          = true;
                $segundos_restantes = $TIEMPO_BLOQUEO;
                $error = "Demasiados intentos fallidos. Espera <span id='countdown'>{$segundos_restantes}</span> segundo(s) para intentar de nuevo.";
            } else {
                $error = 'Correo o contraseña incorrectos. Te quedan ' . $intentos_restantes . ' intento(s).';
            }
        }
    } else {
        $error = 'Por favor completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BURGUERSOFT - Iniciar Sesión</title>
    <link rel="stylesheet" href="../estilos/estilos-login.css">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .input-password-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input-password-wrapper .input {
            margin-bottom: 0;
            padding-right: 90px;
        }

        body.modo-oscuro-accesible .btn-toggle-password {
            color: var(--color-acento);
        }

        body.modo-oscuro-accesible .btn-toggle-password:hover {
            color: #fff;
        }

        .toast-bienvenida {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #2f2a1f;
            color: #f4f3f2;
            border: 2.5px solid #E8821A;
            padding: 18px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            opacity: 0;
            z-index: 9999;
            transition: opacity 0.4s ease, transform 0.4s ease;
            pointer-events: none;
            max-width: 90%;
            text-align: center;
        }

        .toast-bienvenida.mostrar {
            opacity: 1;
            transform: translateX(-50%) translateY(0);

        .contenedor-login {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding-bottom: 60px;
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

        footer {
            margin-top: 0 !important;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <img src="../estilos/img/icono.png" class="logo">
        <a href="../php/Burguersoft.php" class="btn-regresar"> [ Regresar ]</a>
    </div>

    <div class="contenedor-login">
        <div class="card">
            <div class="header-bar"> INICIAR SESIÓN </div>

    <div id="toastBienvenida" class="toast-bienvenida">¡Inicia sesión para continuar.</div>

    <div class="card">
        <?php if ($error): ?>
            <p style="color:red;text-align:center;margin-bottom:10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
            <?php if ($error): ?>
                <p class="<?= $bloqueado ? 'error-bloqueo' : 'error-normal' ?>">
                    <?= $error ?>
                </p>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="login.php">
                <h2>CORREO*</h2>
                <input type="email" name="correo" id="email" class="input"
                       placeholder="ejemplo@gmail.com" required
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                       <?= $bloqueado ? 'disabled' : '' ?>>

                <h2>CONTRASEÑA*</h2>
                <div class="input-password-wrapper">
                    <input type="password" name="contrasena" id="password" class="input"
                           placeholder="Ingresa tu contraseña" required
                           <?= $bloqueado ? 'disabled' : '' ?>>
                    <button type="button" id="btnToggle" onclick="togglePassword()"
                        onmouseover="this.style.color='#000000'"
                        onmouseout="this.style.color='#E8821A'"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                               background:none; border:none; cursor:pointer; font-size:13px;
                               font-weight:700; color:#E8821A;"
                        <?= $bloqueado ? 'disabled' : '' ?>>
                        Mostrar
                    </button>
                </div>

                <button type="submit" id="botonEntrar" class="btn-primario"
                    <?= $bloqueado ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                    INICIAR SESIÓN
                </button>

                <a href="recuperar_contrasena.php" class="link">¿Recuperar tu contraseña?</a>

                <div class="separador-contenedor">
                    <div class="linea"></div>
                    <span class="circulo">o</span>
                    <div class="linea"></div>
                </div>

                <div class="enlace-externo">
                    ¿No tienes una cuenta?
                    <a href="Registro.php">Crear cuenta</a>
                </div>
            </form>
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
        <img style="width:24px;height:24px;filter:invert(1);pointer-events:none;"
             src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
    </button>

    <script src="../js/accesibilidad.js"></script>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-text">
                    <div style="display:flex;align-items:center;gap:8px;justify-content:center;margin-bottom:10px;margin-top:-30px;">
                        <img src="../estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
                        <hr>
                        <h3 style="margin:6px;">El Oriente</h3>
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

<script>
    window.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toastBienvenida');

        <?php if ($motivo === 'promocion'): ?>          // ← NUEVO
            mostrarToastNaranja('Inicia sesión para comprar una promoción.');

        <?php elseif ($motivo=== 'producto'): ?>
            mostrarToastNaranja('Inicia sesión para comprar una producto del menu.');

        <?php elseif (!empty($_SESSION['mensaje']) && $_SESSION['tipo_mensaje'] === 'exito'): ?>
            mostrarToastExito('<?= htmlspecialchars($_SESSION['mensaje']) ?>');
            <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>

        <?php else: ?>
            if (toast && !sessionStorage.getItem('toastLoginMostrado')) {
                setTimeout(() => toast.classList.add('mostrar'), 100);
                setTimeout(() => toast.classList.remove('mostrar'), 3500);
                sessionStorage.setItem('toastLoginMostrado', '1');
            }
        <?php endif; ?>
    });

    function mostrarToastExito(mensaje) {
        const t = document.createElement('div');
        t.textContent = mensaje;
        t.style.cssText = `
            position: fixed; top: 20px; left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #1a3a1a; color: #4caf50;
            border: 2px solid #4caf50;
            padding: 14px 28px; border-radius: 10px;
            font-size: 14px; font-weight: 700;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            z-index: 99999; text-align: center;
            opacity: 0; transition: opacity 0.4s ease, transform 0.4s ease;
            pointer-events: none;
        `;
        document.body.appendChild(t);
        setTimeout(() => {
            t.style.opacity = '1';
            t.style.transform = 'translateX(-50%) translateY(0)';
        }, 100);
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(-20px)';
        }, 3600);
        setTimeout(() => t.remove(), 4100);
    }

    function mostrarToastNaranja(mensaje) {
    const t = document.createElement('div');
    t.textContent = mensaje;
    t.style.cssText = `
        position: fixed; top: 20px; left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background: #2f2a1f; color: #ffffff;
        border: 2.5px solid #E8821A;
        padding: 18px 28px; border-radius: 10px;
        font-size: 14px; font-weight: 600;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        z-index: 99999; text-align: center;
        opacity: 0; transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none; max-width: 90%;
    `;
    document.body.appendChild(t);
    setTimeout(() => {
        t.style.opacity = '1';
        t.style.transform = 'translateX(-50%) translateY(0)';
    }, 100);
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateX(-50%) translateY(-20px)';
    }, 3600);
    setTimeout(() => t.remove(), 4100);
}

    function togglePassword() {
        const input = document.getElementById('password');
        const btn   = document.getElementById('btnToggle');
        const visible = input.type === 'text';
        input.type      = visible ? 'password' : 'text';
        btn.textContent = visible ? 'Mostrar' : 'Ocultar';
    }
        function togglePassword() {
            const input   = document.getElementById('password');
            const btn     = document.getElementById('btnToggle');
            const visible = input.type === 'text';
            input.type      = visible ? 'password' : 'text';
            btn.textContent = visible ? 'Mostrar' : 'Ocultar';
        }

        <?php if ($bloqueado && $segundos_restantes > 0): ?>
        (function () {
            let segundos = <?= $segundos_restantes ?>;
            const el  = document.getElementById('countdown');
            const btn = document.getElementById('botonEntrar');

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
</body>
</html>