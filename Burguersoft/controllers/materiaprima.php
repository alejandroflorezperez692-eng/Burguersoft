<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

function requerirAdminApi(): void {
    if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador')
        jsonResponse(['error' => 'No autorizado'], 403);
}

if ($method === 'GET') {
    try {
        $datos = $pdo->query("
            SELECT mp.*, m.nombre AS nombre_marca
            FROM materia_prima mp
            LEFT JOIN marca m ON m.id = mp.marca_id
            WHERE mp.estado != 'Inactivo'
            ORDER BY mp.nombre
        ")->fetchAll();
        jsonResponse($datos);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error al consultar: ' . $e->getMessage()], 500);
    }
}

if ($method === 'POST') {
    jsonResponse(['error' => 'Los insumos nuevos se registran desde Compras, no desde este módulo'], 405);
}

if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    requerirAdminApi();
    $body     = json_decode(file_get_contents('php://input'), true) ?? [];
    $nombre   = limpiar($body['nombre']        ?? '');
    $tipo     = limpiar($body['tipo']          ?? '');
    $unidad   = limpiar($body['unidad_medida'] ?? '');
    $valor    = (float)($body['valor']         ?? 0);
    $marca_id = !empty($body['marca_id']) ? (int)$body['marca_id'] : null;

    if (!$nombre || !$tipo) jsonResponse(['error' => 'Faltan campos'], 400);

    try {
        $pdo->prepare("UPDATE materia_prima SET nombre=?, tipo=?, unidad_medida=?, valor=?, marca_id=? WHERE id=?")
            ->execute([$nombre, $tipo, $unidad, $valor, $marca_id, $id]);
        registrarBitacora($pdo, (int)($_SESSION['id_usuario'] ?? 0), 'Materia Prima', "Editó el insumo: $nombre");
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error de Base de Datos: ' . $e->getMessage()], 500);
    }
}

if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    requerirAdminApi();
    try {
        $sNombre = $pdo->prepare("SELECT nombre FROM materia_prima WHERE id = ?");
        $sNombre->execute([$id]);
        $nombre = $sNombre->fetchColumn() ?: "#$id";

        $pdo->prepare("UPDATE materia_prima SET estado = 'Inactivo' WHERE id=?")->execute([$id]);
        registrarBitacora($pdo, (int)($_SESSION['id_usuario'] ?? 0), 'Materia Prima', "Dio de baja el insumo: $nombre");
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'No se pudo dar de baja la materia prima'], 409);
    }
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>