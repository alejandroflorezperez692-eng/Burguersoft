<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && !empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}
$accion = $_GET['accion'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

$categorias_enum = [
    'Hamburguesa','Perros Caliente','Salchipapa','Fritos',
    'Arepas','Picada','Bebidas Frias','Bebidas Calientes','Pizza'
];

function requerirAdminApi(): void {
    if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador')
        jsonResponse(['error' => 'No autorizado'], 403);
}

if ($accion === 'categorias' && $method === 'GET') {
    jsonResponse(array_map(fn($c) => ['id' => $c, 'nombre' => $c], $categorias_enum));
}

if ($accion === 'productos') {

    if ($method === 'GET') {
        $categoria = $_GET['categoria'] ?? '';
        if ($categoria) {
            $s = $pdo->prepare(
                "SELECT id, nombre, valor, descripcion, img, cantidad, categoria, estado
                 FROM producto WHERE categoria = ? ORDER BY nombre"
            );
            $s->execute([$categoria]);
            jsonResponse($s->fetchAll());
        } else {
            jsonResponse($pdo->query(
                "SELECT id, nombre, valor, descripcion, img, cantidad, categoria, estado
                 FROM producto ORDER BY categoria, nombre"
            )->fetchAll());
        }
    }

    if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) requerirAdminApi();

    if ($method === 'POST') {
        $nombre      = limpiar($_POST['nombre']      ?? '');
        $valor       = (float)($_POST['valor']       ?? 0);
        $descripcion = limpiar($_POST['descripcion'] ?? '');
        $categoria   = limpiar($_POST['categoria']   ?? '');
        $cantidad    = limpiar($_POST['cantidad']    ?? '0');
        $cantidad_int = (int)$cantidad;
        $estado      = calcularEstadoStock($cantidad_int);

        if (!$nombre || $valor <= 0 || !$categoria)
            jsonResponse(['error' => 'Nombre, precio y categoría son obligatorios'], 400);

        if (!in_array($categoria, $categorias_enum))
            jsonResponse(['error' => 'Categoría inválida'], 400);

        if (empty($_FILES['imagen']['tmp_name']))
            jsonResponse(['error' => 'La imagen es obligatoria'], 400);

        $dir = __DIR__ . '/../uploads/productos/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext  = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $file = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $file);
        $img = '../uploads/productos/' . $file;

        $pdo->prepare(
            "INSERT INTO producto (nombre, valor, descripcion, img, cantidad, categoria, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([$nombre, $valor, $descripcion, $img, $cantidad, $categoria, $estado]);

        jsonResponse(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
    }

    if ($method === 'PUT') {
        if (!$id) jsonResponse(['error' => 'ID requerido'], 400);

        $nombre      = limpiar($_POST['nombre']      ?? '');
        $valor       = (float)($_POST['valor']       ?? 0);
        $descripcion = limpiar($_POST['descripcion'] ?? '');
        $categoria   = limpiar($_POST['categoria']   ?? '');
        $cantidad    = limpiar($_POST['cantidad']    ?? '0');
        $cantidad_int = (int)$cantidad;
        $estado      = calcularEstadoStock($cantidad_int);

        if ($valor <= 0 || !$categoria || (int)$cantidad < 0)
            jsonResponse(['error' => 'Precio y categoría son obligatorios'], 400);

        if (!empty($_FILES['imagen']['tmp_name'])) {
            $dir = __DIR__ . '/../uploads/productos/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext  = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $file = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $file);
            $img = '../uploads/productos/' . $file;
        } else {
            $s = $pdo->prepare("SELECT img FROM producto WHERE id = ?");
            $s->execute([$id]);
            $img = $s->fetchColumn() ?: '';
        }

        $pdo->prepare(
            "UPDATE producto SET nombre=?, valor=?, descripcion=?, img=?, cantidad=?, categoria=?, estado=?
             WHERE id=?"
        )->execute([$nombre, $valor, $descripcion, $img, $cantidad, $categoria, $estado, $id]);

        jsonResponse(['success' => true]);
    }

    if ($method === 'DELETE') {
        if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
        try {
            $pdo->prepare("DELETE FROM receta  WHERE producto_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM producto WHERE id = ?")->execute([$id]);
            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'No se puede eliminar: tiene ventas asociadas'], 409);
        }
    }
}

if ($accion === 'receta') {

    if ($method === 'GET') {
        if (!$id) jsonResponse(['error' => 'ID producto requerido'], 400);
        $s = $pdo->prepare(
            "SELECT r.id, r.materia_id, r.cantidad_usada, mp.nombre AS nombre_materia
             FROM receta r
             JOIN materia_prima mp ON mp.id = r.materia_id
             WHERE r.producto_id = ?"
        );
        $s->execute([$id]);
        jsonResponse($s->fetchAll());
    }

    if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);
    if (in_array($method, ['POST', 'DELETE'])) requerirAdminApi();

    if ($method === 'POST') {
        $body        = json_decode(file_get_contents('php://input'), true);
        $producto_id = (int)($body['producto_id']    ?? 0);
        $materia_id  = (int)($body['materia_id']     ?? 0);
        $cantidad    = (float)($body['cantidad_usada'] ?? 0);
        if (!$producto_id || !$materia_id || $cantidad <= 0)
            jsonResponse(['error' => 'Datos inválidos'], 400);
        $pdo->prepare(
            "INSERT INTO receta (producto_id, materia_id,
             cantidad_usada, descripcion) VALUES (?,?,?,'')"
        )->execute([$producto_id, $materia_id, $cantidad]);
        jsonResponse(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
    }

    if ($method === 'DELETE') {
        if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
        $pdo->prepare("DELETE FROM receta WHERE id = ?")->execute([$id]);
        jsonResponse(['success' => true]);
    }
}

if ($accion === 'materias' && $method === 'GET') {
    jsonResponse($pdo->query(
        "SELECT id, nombre, cantidad, unidad_medida FROM materia_prima
         WHERE estado != 'Inactivo' ORDER BY nombre"
    )->fetchAll());
}    
jsonResponse(['error' => 'Acción no reconocida'], 404);
?>
