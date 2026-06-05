<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/conexion.php';
requerirLogin();
$navActivo = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = limpiar($_POST['nombre']   ?? '');
    $apellido = limpiar($_POST['apellido'] ?? '');
    $correo   = limpiar($_POST['correo']   ?? '');
    $telefono  = limpiar($_POST['telefono']  ?? '');
    $actual   = $_POST['passActual'] ?? '';
    $nueva    = $_POST['passNueva']  ?? '';
    $pdo      = getPDO();

    $stmt = $pdo->prepare("SELECT contrasena, telefono, nombre, apellido, correo FROM usuario WHERE id = ?");
    $stmt->execute([$_SESSION['id_usuario']]);  // sesión: id_usuario
    $ususarioActual = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = $ususarioActual['contrasena'];
    $_SESSION['telefono'] = $ususarioActual['telefono'] ?? '';
    $_SESSION['nombre']   = $ususarioActual['nombre']   ?? '';
    $_SESSION['apellido'] = $ususarioActual['apellido'] ?? '';
    $_SESSION['correo']   = $ususarioActual['correo']   ?? '';
    if ($actual && !password_verify($actual, $hash)) {
        $msg = 'La contraseña actual es incorrecta.';
    } else {
        $nuevoHash = $nueva ? password_hash($nueva, PASSWORD_DEFAULT) : $hash;
        $pdo->prepare("UPDATE usuario SET nombre=?, apellido=?, correo=?, telefono=?, contrasena=? WHERE id=?")
            ->execute([$nombre, $apellido, $correo, $telefono, $nuevoHash, $_SESSION['id_usuario']]);
        $_SESSION['nombre']   = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['correo']   = $correo;
        $_SESSION['telefono'] = $telefono;
        $msg = '✅ Cambios guardados correctamente.';
    }
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Cuenta</title>
    <link rel="stylesheet" href="../estilos/configuracion.css">
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href=".. /estilos/img/icono.png" type="image/x-icon">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>
<div class="main-content">
    <div class="container">
        <h1>Configuración de Cuenta</h1>
        <form class="config-form" method="POST" action="config_cuenta.php">
            <!-- INFORMACIÓN PERSONAL -->
<h2>Información Personal</h2>
<?php if ($msg): ?>
    <p style="text-align:center; color:<?= str_starts_with($msg,'✅')?'green':'red' ?>;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>
<label>Nombre</label>
<input type="text" name="nombre"
    value="<?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>"
    pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}$"
    maxlength="15"
    oninput="this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g, '');
             if(this.value.length > 0) this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);"
    required>

<label>Apellido</label>
<input type="text" name="apellido"
    value="<?= htmlspecialchars($_SESSION['apellido'] ?? '') ?>"
    pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}$"
    maxlength="15"
    oninput="this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g, '');
             if(this.value.length > 0) this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);"
    required>

<label>Correo electrónico</label>
<input type="email" name="correo"
    value="<?= htmlspecialchars($_SESSION['correo'] ?? '') ?>" required>

<label>teléfono</label>
<input type="tel" name="telefono" style="width:100%; padding-right:90px; box-sizing:border-box; color:var(--color-input-text); background:var(--color-input-bg); border:1px solid #ccc; border-radius:var(--border-radius); transition:var(--transition-speed);"
    value="<?= htmlspecialchars($_SESSION['telefono'] ?? '') ?>"
    pattern="^\d{10}$"
    maxlength="10"
    oninput="filtrarTelefono(this)"
    placeholder="Ej: 3001234567"
    required>
<!-- SEGURIDAD -->
<h2>Seguridad (opcional)</h2>

<!-- Contraseña actual -->
<label>Contraseña actual</label>
<div style="position:relative;">
    <input type="password" name="passActual" id="passActual"
        placeholder="Ingresa tu contraseña actual"
        style="width:100%; padding-right:90px; box-sizing:border-box;">
    <span onclick="togglePassword('passActual', this)"
        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
            cursor:pointer; color:#888; font-size:13px; user-select:none;">Mostrar</span>
</div>

<!-- Nueva contraseña -->
<label>Nueva contraseña</label>
<div style="position:relative;">
    <input type="password" name="passNueva" id="passNueva"
        placeholder="Deja en blanco para no cambiarla"
        oninput="evaluarPassword(this.value)"
        style="width:100%; padding-right:90px; box-sizing:border-box;">
    <span onclick="togglePassword('passNueva', this)"
        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
        cursor:pointer; color:#888; font-size:13px; user-select:none;">Mostrar</span>
</div>
<!-- Barra de seguridad -->
<div id="contenedor-barra" style="height:6px;width:100%;background:#e0e0e0;margin-top:5px;border-radius:4px;overflow:hidden;">
    <div id="progreso" style="height:100%;width:0%;transition:0.3s;"></div>
</div>
<ul id="requisitos" style="list-style:none;padding:0;font-size:12px;margin-top:8px;color:#666;">
    <li id="longitud">❌ Mínimo 8 caracteres</li>
    <li id="mayuscula">❌ Al menos una mayúscula</li>
    <li id="numero">❌ Al menos un número</li>
    <li id="especial">❌ Al menos un símbolo (@, #, $, etc.)</li>
</ul>
            <button type="submit" class="btn-guardar">Guardar cambios</button>
            <a href="/burguersoft/logout.php"><button class="btn-guardar-CS">Cerrar sesión</button></a>
    </form>
</div>
    <link rel="stylesheet" href="../estilos/Accesibilidad.css">
    <script src="../js/Accesibilidad.js"></script>
    <script>
function togglePassword(inputId, span) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        span.textContent = 'Ocultar';
    } else {
        input.type = 'password';
        span.textContent = 'Mostrar';
    }
}
function evaluarPassword(valor) {
    const longitud  = valor.length >= 8;
    const mayuscula = /[A-Z]/.test(valor);
    const numero    = /[0-9]/.test(valor);
    const especial  = /[@#$%^&*!]/.test(valor);

    document.getElementById('longitud').textContent  = (longitud  ? '✅' : '❌') + ' Mínimo 8 caracteres';
    document.getElementById('mayuscula').textContent = (mayuscula ? '✅' : '❌') + ' Al menos una mayúscula';
    document.getElementById('numero').textContent    = (numero    ? '✅' : '❌') + ' Al menos un número';
    document.getElementById('especial').textContent  = (especial  ? '✅' : '❌') + ' Al menos un símbolo (@, #, $, etc.)';

    const cumplidos = [longitud, mayuscula, numero, especial].filter(Boolean).length;
    const colores   = ['#e0e0e0', '#e53935', '#fb8c00', '#fdd835', '#43a047'];
    const barra     = document.getElementById('progreso');
    barra.style.width      = (cumplidos * 25) + '%';
    barra.style.background = colores[cumplidos];
}
function filtrarTelefono(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 0 && input.value[0] !== '3') {
        input.value = input.value.substring(1);
    }
}
</script>   
<?php include __DIR__ . '/../includes/accesibilidad.php'; ?>
</body></html>