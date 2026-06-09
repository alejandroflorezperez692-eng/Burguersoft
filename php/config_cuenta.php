<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/conexion.php';
requerirLogin();
$navActivo = 'config_cuenta';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = limpiar($_POST['nombre']   ?? '');
    $apellido = limpiar($_POST['apellido'] ?? '');
    $correo   = limpiar($_POST['correo']   ?? '');
    $telefono = limpiar($_POST['telefono'] ?? '');
    $actual   = $_POST['passActual'] ?? '';
    $nueva    = $_POST['passNueva']  ?? '';
    $pdo      = getPDO();

    $stmt = $pdo->prepare("SELECT contrasena, telefono, nombre, apellido, correo FROM usuario WHERE id = ?");
    $stmt->execute([$_SESSION['id_usuario']]);
    $ususarioActual = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = $ususarioActual['contrasena'];
    $_SESSION['telefono'] = $ususarioActual['telefono'] ?? '';
    $_SESSION['nombre']   = $ususarioActual['nombre']   ?? '';
    $_SESSION['apellido'] = $ususarioActual['apellido'] ?? '';
    $_SESSION['correo']   = $ususarioActual['correo']   ?? '';

    if ($actual && !password_verify($actual, $hash)) {
        $msg = ['type' => 'err', 'text' => 'La contraseña actual es incorrecta.'];
    } else {
        $nuevoHash = $nueva ? password_hash($nueva, PASSWORD_DEFAULT) : $hash;
        $pdo->prepare("UPDATE usuario SET nombre=?, apellido=?, correo=?, telefono=?, contrasena=? WHERE id=?")
            ->execute([$nombre, $apellido, $correo, $telefono, $nuevoHash, $_SESSION['id_usuario']]);
        $_SESSION['nombre']   = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['correo']   = $correo;
        $_SESSION['telefono'] = $telefono;
        $msg = ['type' => 'ok', 'text' => '✅ Cambios guardados correctamente.'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft — Configuración de Cuenta</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .config-page { padding: 36px 40px 60px; }

        .config-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 28px;
            max-width: 900px;
        }

        .profile-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 32px 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            text-align: center;
            height: fit-content;
        }

        .avatar-ring {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 900;
            color: #fff;
            box-shadow: 0 8px 28px rgba(232,130,26,0.4);
        }

        .profile-name {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
        }

        .profile-email {
            font-size: 13px;
            color: var(--text-400);
            word-break: break-all;
        }

        .profile-role {
            display: inline-flex;
            align-items: center;
            padding: 4px 14px;
            border-radius: 20px;
            background: rgba(232,130,26,0.12);
            border: 1px solid rgba(232,130,26,0.3);
            font-size: 11px;
            font-weight: 700;
            color: var(--brand);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .profile-logout {
            width: 100%;
            padding: 11px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--danger);
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 8px;
        }

        .profile-logout:hover { background: rgba(200,56,42,0.07); border-color: var(--danger); }

        .form-panel {
            margin-left: -28px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .form-section {
            padding-bottom: 28px;
            margin-bottom: 28px;
            border-bottom: 1px solid var(--border);
        }

        .form-section:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }

        .form-section-title {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 20px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 16px;
        }

        .field:last-child { margin-bottom: 0; }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .field input {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-glow); }
        .field input::placeholder { color: var(--text-400); }

        .pass-wrap { position: relative; }

        .pass-wrap input { width: 100%; padding-right: 80px; box-sizing: border-box; }

        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            box-shadow: none;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-400);
            cursor: pointer;
            padding: 0;
            transition: color 0.15s;
        }

        .pass-toggle:hover { color: var(--brand); transform: translateY(-50%); box-shadow: none; }

        .strength-bar-wrap { height: 5px; background: var(--surface-3); border-radius: 3px; overflow: hidden; margin-top: 8px; }
        .strength-bar { height: 100%; width: 0%; border-radius: 3px; transition: all 0.3s; }

        .reqs { list-style: none; padding: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 4px 16px; margin-top: 10px; }
        .reqs li { font-size: 12px; color: var(--text-400); }
        .reqs li.met { color: var(--success); }

        .msg-box {
            padding: 14px 18px;
            border-radius: var(--r-sm);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .msg-ok  { background: #d5f5e3; color: #1a7a42; border-left: 4px solid var(--success); }
        .msg-err { background: #fde8e8; color: #922; border-left: 4px solid var(--danger); }

        .btn-submit {
            width: 100%;
            padding: 11px;
            background: var(--brand);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232,130,26,0.35);
            transition: all 0.2s var(--ease);
        }

        .btn-submit:hover { background: var(--brand-deep); transform: translateY(-2px); }

        @media (max-width: 768px) {
            .config-layout { grid-template-columns: 1fr; }
            .form-row-2 { grid-template-columns: 1fr; }
            .reqs { grid-template-columns: 1fr; }
        }

        body.dark-mode .profile-panel { background: var(--surface); }
        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .field input { background: var(--surface-2); color: var(--text-900); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="config-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Configuración de Cuenta</h1>
            <div class="subtitulo">Gestiona tu información personal y seguridad</div>
        </div>
    </div>

    <div class="config-layout">

        <div>
        </div>

        <div class="form-panel">

            <?php if ($msg): ?>
            <div class="msg-box msg-<?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
            <?php endif; ?>

            <form method="POST" action="config_cuenta.php">

                <div class="form-section">
                    <div class="form-section-title">Información Personal</div>

                    <div class="form-row-2">
                        <div class="field">
                            <label>Nombre</label>
                            <input type="text" name="nombre"
                                value="<?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>"
                                pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}$"
                                maxlength="15"
                                oninput="this.value=this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g,'');if(this.value.length>0)this.value=this.value.charAt(0).toUpperCase()+this.value.slice(1);"
                                placeholder="Tu nombre"
                                required>
                        </div>
                        <div class="field">
                            <label>Apellido</label>
                            <input type="text" name="apellido"
                                value="<?= htmlspecialchars($_SESSION['apellido'] ?? '') ?>"
                                pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}$"
                                maxlength="15"
                                oninput="this.value=this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g,'');if(this.value.length>0)this.value=this.value.charAt(0).toUpperCase()+this.value.slice(1);"
                                placeholder="Tu apellido"
                                required>
                        </div>
                    </div>

                    <div class="field">
                        <label>Correo electrónico</label>
                        <input type="email" name="correo"
                            value="<?= htmlspecialchars($_SESSION['correo'] ?? '') ?>"
                            placeholder="correo@ejemplo.com"
                            required>
                    </div>

                    <div class="field">
                        <label>Teléfono</label>
                        <input type="tel" name="telefono"
                            value="<?= htmlspecialchars($_SESSION['telefono'] ?? '') ?>"
                            pattern="^\d{10}$"
                            maxlength="10"
                            oninput="filtrarTelefono(this)"
                            placeholder="3001234567"
                            required>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Seguridad (opcional)</div>

                    <div class="field">
                        <label>Contraseña actual</label>
                        <div class="pass-wrap">
                            <input type="password" name="passActual" id="passActual" placeholder="Tu contraseña actual">
                            <button type="button" class="pass-toggle" onclick="togglePass('passActual', this)">Mostrar</button>
                        </div>
                    </div>

                    <div class="field">
                        <label>Nueva contraseña</label>
                        <div class="pass-wrap">
                            <input type="password" name="passNueva" id="passNueva"
                                placeholder="Déjala vacía para no cambiarla"
                                oninput="evaluarPassword(this.value)">
                            <button type="button" class="pass-toggle" onclick="togglePass('passNueva', this)">Mostrar</button>
                        </div>
                        <div class="strength-bar-wrap">
                            <div class="strength-bar" id="progreso"></div>
                        </div>
                        <ul class="reqs">
                            <li id="req-len">❌ Mínimo 8 caracteres</li>
                            <li id="req-may">❌ Una mayúscula</li>
                            <li id="req-num">❌ Un número</li>
                            <li id="req-esp">❌ Un símbolo</li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Guardar cambios</button>
                <a href="/burguersoft/logout.php" class="profile-logout">Cerrar sesión</a>
            </form>

        </div>
    </div>

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
    <img style="width:22px;height:22px;filter:invert(1);pointer-events:none;" src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>

<script>
function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function togglePass(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? 'Mostrar' : 'Ocultar';
}

function evaluarPassword(valor) {
    const longitud  = valor.length >= 8;
    const mayuscula = /[A-Z]/.test(valor);
    const numero    = /[0-9]/.test(valor);
    const especial  = /[@#$%^&*!]/.test(valor);
    const checks    = [longitud, mayuscula, numero, especial];

    const ids = ['req-len','req-may','req-num','req-esp'];
    const labels = ['Mínimo 8 caracteres','Una mayúscula','Un número','Un símbolo'];
    checks.forEach((c, i) => {
        const el = document.getElementById(ids[i]);
        el.textContent = (c ? '✅' : '❌') + ' ' + labels[i];
        el.className = c ? 'met' : '';
    });

    const cumplidos = checks.filter(Boolean).length;
    const colores   = ['#e0e0e0','#C8382A','#f39c12','#fdd835','#2ecc71'];
    const barra     = document.getElementById('progreso');
    barra.style.width      = (cumplidos * 25) + '%';
    barra.style.background = colores[cumplidos];
}

function filtrarTelefono(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 0 && input.value[0] !== '3') input.value = input.value.substring(1);
}
</script>
</body>
</html>