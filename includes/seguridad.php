<?php
/*  1) CABECERAS DE SEGURIDAD HTTP */
function enviarCabecerasSeguridad(): void
{
    static $enviadas = false;
    if ($enviadas || headers_sent()) return;
    $enviadas = true;
 
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; "
         . "img-src 'self' data: https:; "
         . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
         . "font-src 'self' https://fonts.gstatic.com; "
         . "script-src 'self' 'unsafe-inline'; "
         . "frame-ancestors 'none'");
 
    // Solo tiene efecto real cuando el sitio corre bajo HTTPS
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/*  2) SESIÓN SEGURA  */
function configurarCookieSesion(): void
{
    if (session_status() !== PHP_SESSION_NONE) return;
 
    $esHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
 
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $esHttps,   
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('BSOFT_SESSID');
}

/* Regenera el ID de sesión conservando los datos.
Debe llamarse justo después de un login exitoso para evitar ataques de fijación de sesión (session fixation). */
function regenerarSesionTrasLogin(): void
{
    session_regenerate_id(true);
}

/* 3) TOKEN CSRF */
function generarCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/* Valida el token CSRF recibido por header (X-CSRF-Token) o por campo de formulario (csrf_token). 
Corta la ejecución con 403 si falta o no coincide. Solo aplica a métodos que modifican datos.*/

function requerirCSRF(): void
{
    $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo === 'POST' && !empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
        $metodo = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) return;
 
    $tokenEnviado = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    $tokenSesion  = $_SESSION['csrf_token'] ?? '';
 
    if (!$tokenEnviado || !$tokenSesion || !hash_equals($tokenSesion, $tokenEnviado)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Token de seguridad (CSRF) inválido o ausente. Recarga la página.']);
        exit;
    }
}
/**
 * Igual que requerirCSRF() pero pensado para formularios HTML
 * tradicionales (redirige con mensaje en vez de responder JSON).
 */
function requerirCSRFFormulario(string $urlRedirectSiFalla): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
 
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSesion  = $_SESSION['csrf_token'] ?? '';
 
    if (!$tokenEnviado || !$tokenSesion || !hash_equals($tokenSesion, $tokenEnviado)) {
        $_SESSION['mensaje']      = 'Tu sesión expiró o la solicitud no es válida. Intenta de nuevo.';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: $urlRedirectSiFalla");
        exit;
    }
}

/*  VALIDACIÓN SEGURA DE ARCHIVOS SUBIDOS IMÁGENES */
function validarImagenSubida(array $archivo, int $maxBytes = 5 * 1024 * 1024): ?array
{
    if (empty($archivo['tmp_name']) || !is_uploaded_file($archivo['tmp_name'])) {
        return null;
    }
 
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
 
    if ($archivo['size'] > $maxBytes) {
        return null;
    }
 
    $mimesPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];
 
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($archivo['tmp_name']);
 
    if (!isset($mimesPermitidos[$mime])) {
        return null;
    }
 
    $extension     = $mimesPermitidos[$mime];
    $nombreSeguro  = bin2hex(random_bytes(16)) . '.' . $extension;
 
    return ['nombre_archivo' => $nombreSeguro, 'mime' => $mime];
}
 
/* Mueve un archivo ya validado con validarImagenSubida() a $dirDestino. Devuelve true/false. */
function guardarImagenValidada(array $archivo, string $dirDestino, string $nombreArchivo): bool
{
    if (!is_dir($dirDestino)) mkdir($dirDestino, 0755, true);
    return move_uploaded_file($archivo['tmp_name'], rtrim($dirDestino, '/') . '/' . $nombreArchivo);
}
 
/*  5) RATE LIMITING POR IP + IDENTIFICADOR login, códigos, etc.
   Usa la tabla `intentos_seguridad` (ver sql/migracion_seguridad.sql) */
function obtenerIP(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
 
/* Revisa si el ip + identificador + accion está bloqueado. 
  Devuelve segundos restantes de bloqueo (0 si no está bloqueado). */
function segundosBloqueoRestante(PDO $pdo, string $accion, string $identificador, int $limiteIntentos, int $ventanaSegundos): int
{
    $ip = obtenerIP();

    $s = $pdo->prepare(
        "SELECT COUNT(*) AS intentos,
                GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), MAX(creado_en) + INTERVAL ? SECOND)) AS restante
         FROM intentos_seguridad
         WHERE accion = ? AND identificador = ? AND ip = ?
           AND creado_en > (NOW() - INTERVAL ? SECOND)"
    );
    $s->execute([$ventanaSegundos, $accion, $identificador, $ip, $ventanaSegundos]);
    $fila = $s->fetch();
 
    if (!$fila || (int)$fila['intentos'] < $limiteIntentos) return 0;
 
    return (int) $fila['restante'];
}
 
function registrarIntentoFallido(PDO $pdo, string $accion, string $identificador): void
{
    $pdo->prepare(
        "INSERT INTO intentos_seguridad (accion, identificador, ip, creado_en) VALUES (?, ?, ?, NOW())"
    )->execute([$accion, $identificador, obtenerIP()]);
}
 
function limpiarIntentos(PDO $pdo, string $accion, string $identificador): void
{
    $pdo->prepare(
        "DELETE FROM intentos_seguridad WHERE accion = ? AND identificador = ? AND ip = ?"
    )->execute([$accion, $identificador, obtenerIP()]);
}
?>