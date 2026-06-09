<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $nombre          = limpiar($_POST['nombre']          ?? '');
    $apellido        = limpiar($_POST['apellido']        ?? '');
    $correo          = limpiar($_POST['correo']          ?? '');
    $tipo_documento  = limpiar($_POST['Tdocumento']  ?? '');
    $numero_documento= limpiar($_POST['numero_documento']?? '');
    $password        = $_POST['password'] ?? '';

    // Validar campos obligatorios
    if (!$nombre || !$apellido || !$correo || !$tipo_documento || !$numero_documento || !$password) {
        echo json_encode(['error' => 'Todos los campos son obligatorios']);
        exit;
    }

    // Validar que el número de documento sea numérico
    if (!ctype_digit($numero_documento)) {
        echo json_encode(['error' => 'El número de documento solo puede contener dígitos']);
        exit;
    }

    $pdo = getPDO();

    // Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE correo = ?");
    $stmt->execute([$correo]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'Este correo ya está registrado']);
        exit;
    }

    // Verificar si el número de documento ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE Ndocumento = ?");
    $stmt->execute([$numero_documento]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'Este número de documento ya está registrado']);
        exit;
    }

    // Obtener id del rol 'Mesero' (rol por defecto para nuevos registros)
    $stmtRol = $pdo->prepare("SELECT id FROM rol WHERE nombre = ?");
    $stmtRol->execute(['Cliente']);
    $rol = $stmtRol->fetch();

    if (!$rol) {
        echo json_encode(['error' => 'Rol por defecto no existe en la base de datos. Inserta el rol "Mesero" primero.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->prepare("
        INSERT INTO usuario (nombre, apellido, correo, Tdocumento, Ndocumento, contrasena, estado, rol_id)
        VALUES (?, ?, ?, ?, ?, ?, 'Activo', ?)
    ")->execute([$nombre, $apellido, $correo, $tipo_documento, $numero_documento, $hash, $rol['id']]);

    echo json_encode(['success' => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>  
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Lato:wght@300;400;700;900&display=swap" media="print" onload="this.media='all'">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Crear cuenta</title>
    <link rel="stylesheet" href="../estilos/estilos-registro.css">
    <LINK REL="stylesheet" HREF="../estilos/estilos-login.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <script src="../js/Registro.js" defer></script>
</head>
<body>

    <div class="navbar">
        <img src="../estilos/img/icono.png" class="logon">
        <button class="boton-regresar" onclick="history.back()">Regresar</button>
    </div>

    <form id="registroForm">
        <div class="contenedor">
            <div class="encabezado">
                <h2 style="text-align: center;">CREAR UNA CUENTA</h2>
                <p>Crea tu cuenta en el Sistema.</p>
            </div>

            <div class="descripcion" style="text-align:center; margin:20px auto 10px; max-width:600px;">
                <p style="margin:0;">Llena cada uno de los siguientes campos para tu registro</p>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>NOMBRE*</label>
                    <input type="text" id="nombre" placeholder="Digite su nombre"
                        pattern="[A-Za-z0-9]+"
                        maxlength="15"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, ''); 
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
                        required>
                </div>
                <div class="campo">
                    <label>APELLIDO*</label>
                    <input type="text" id="apellido" placeholder="Digite su apellido"
                        pattern="[A-Za-záéíóúÁÉÍÓÚñÑ]+"
                        maxlength="15"
                        oninput="this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g, '');
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
                        required>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>Tipo Documento*</label>
                    <select id="tipo-documento" required>
                        <option value="">Seleccione...</option>
                        <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                        <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="numero-documento">Número Documento*</label>
                    <input type="text" id="numero-documento" placeholder="Digite su número de documento"
                        pattern="\d{6,15}"
                        maxlength="10"
                        oninput="this.value = this.value.replace(/\D/g, '');"
                        required>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label for="correo">CORREO*</label>
                    <input type="email" id="correo" name="correo" 
                        placeholder="ejemplo@gmail.com" 
                        autocomplete="email"
                        required>
                </div>
            </div>
            <br>
            <div class="campo">
                <label for="password">CONTRASEÑA*</label>
                <div style="position:relative;">
                    <input type="password" id="password" required
                        placeholder="Mínimo 8 caracteres"
                        oninput="evaluarPassword(this.value); verificarCoincidencia();"
                        style="padding-right:80px; width:100%;">
                    <button type="button" onclick="togglePassword('password', this)"
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
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                               background:none; border:none; cursor:pointer; font-size:13px;
                               font-weight:700; color:#E8821A;">
                        Mostrar
                    </button>
                </div>
                <p id="msg-confirmar" style="font-size:12px;margin-top:5px;min-height:16px;"></p>
            </div>

            <button type="submit" class="boton">Registrarse</button>
        </div>
    </form>

<div class="acc-panel" id="accPanel">
    <div class="acc-panel-title"> Accesibilidad</div>
    <div class="acc-row">
        <div class="acc-row-label">Tema</div>
        <div class="acc-row-btns">
            <button class="acc_tema" onclick="setTema('claro')">Claro</button>
            <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tamano de letra</div>
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

<button class="acc-fab" id="accFab" onclick="togglePanel()"> <img style="width: 24px; height: 24px; filter: invert(1); pointer-events: none;"  onclick="togglePanel()" src="../estilos/img/accesibilidad.png" alt="Accesibilidad"></button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>

   <footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style =" display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                    <img src="../estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
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