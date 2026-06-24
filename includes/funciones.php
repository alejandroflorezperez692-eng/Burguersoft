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
    // ✅ Corregido: ruta correcta con /php/
    if (empty($_SESSION['id_usuario'])) redirigir('/burguersoft/php/login.php');
}

function requerirAdmin(): void {
    requerirLogin();
    // ✅ Corregido: redirige al cliente, no al admin (evita bucle infinito)
    if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador') {
        redirigir('/burguersoft/php/Menu.php');
    }
}

function jsonResponse(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function calcularEstadoStock(int $cantidad): string {
    if ($cantidad <= 0) return 'Agotado';
    if ($cantidad <= 5)  return 'Por agotarse';
    return 'Disponible';
}

function actualizarEstadoProducto(PDO $pdo, int $producto_id): void {
    $s = $pdo->prepare("SELECT cantidad FROM producto WHERE id = ?");
    $s->execute([$producto_id]);
    $cantidad = (int)$s->fetchColumn();
    $estado   = calcularEstadoStock($cantidad);
    $pdo->prepare("UPDATE producto SET estado = ? WHERE id = ?")
        ->execute([$estado, $producto_id]);
}
?>