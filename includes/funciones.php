<?php

function redirigir(string $url): void
{
    header("Location: $url");
    exit;
}

function limpiar(string $valor): string
{
    return htmlspecialchars(strip_tags(trim($valor)));
}

function generarToken(): string
{
    return bin2hex(random_bytes(32));
}

function urlBase(): string
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $carpeta   = '/burguersoft';
    return "$protocolo://$host$carpeta";
}

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=burguersoft;charset=utf8mb4',
            'root',
            '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function iniciarSesionSegura(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function requerirLogin(): void {
    iniciarSesionSegura();
    if (empty($_SESSION['id_usuario'])) redirigir('/burguersoft/login.php');
}

function requerirAdmin(): void {
    requerirLogin();
    if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador') {
        redirigir('/burguersoft/php/inicio_admin.php');
    }
}

function jsonResponse(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function estadoProductoPorCantidad(int $cantidad): string {
    if ($cantidad <= 0) return 'Agotado';
    if ($cantidad <= 5) return 'Por agotarse';
    return 'Disponible';
}

function registrarBitacora(PDO $pdo, int $usuario_id, string $modulo, string $descripcion): void {
    if (!$usuario_id) return;
    try {
        $pdo->prepare("INSERT INTO historial (usuario_id, modulo, descripcion) VALUES (?,?,?)")
            ->execute([$usuario_id, $modulo, $descripcion]);
    } catch (Throwable $e) {
    }
}
?>