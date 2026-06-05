<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';

// ── GET ──────────────────────────────────────────────────────────────────────
if ($method === 'GET') {

    // Lista de productos para el selector del modal
    if ($accion === 'productos') {
        $rows = $pdo->query(
            "SELECT id, nombre, valor, img FROM producto ORDER BY nombre"
        )->fetchAll();
        jsonResponse($rows);
    }

    // IDs de productos vinculados a una promoción concreta
    if ($accion === 'productos_promo' && $id) {
        $s = $pdo->prepare(
            "SELECT producto_id FROM promocion_producto WHERE promocion_id = ?"
        );
        $s->execute([$id]);
        jsonResponse($s->fetchAll(PDO::FETCH_COLUMN));
    }

    // Todas las promociones con sus productos embebidos
    $promos = $pdo->query(
        "SELECT id, nombre_promocion, descripcion, precio, imagen, estado,
                fecha_inicio, fecha_fin
         FROM promocion ORDER BY id DESC"
    )->fetchAll();

    foreach ($promos as &$promo) {
        $s = $pdo->prepare(
            "SELECT p.id, p.nombre, p.valor, p.img
             FROM promocion_producto pp
             JOIN producto p ON p.id = pp.producto_id
             WHERE pp.promocion_id = ?"
        );
        $s->execute([$promo['id']]);
        $promo['productos'] = $s->fetchAll();
    }
    unset($promo);

    jsonResponse($promos);
}

// A partir de aquí se requiere sesión
if (empty($_SESSION['id_usuario'])) jsonResponse(['error' => 'No autorizado'], 401);

// ── POST ─────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $nombre        = limpiar($_POST['nombre_promocion'] ?? '');
    $descripcion   = limpiar($_POST['descripcion']      ?? '');
    $precio        = (float)($_POST['precio']           ?? 0);
    $estado        = limpiar($_POST['estado']           ?? 'Activa');
    $fecha_inicio  = limpiar($_POST['fecha_inicio']     ?? '');
    $fecha_fin     = limpiar($_POST['fecha_fin']        ?? '');
    $productos_ids = json_decode($_POST['productos_ids'] ?? '[]', true) ?: [];

    if (!$nombre || $precio <= 0) jsonResponse(['error' => 'Nombre y precio requeridos'], 400);

    $imagen = '../estilos/img/promocion.png';
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $dir = __DIR__ . '/../uploads/promociones/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext            = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $nombre_archivo);
        $imagen = '../uploads/promociones/' . $nombre_archivo;
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare(
            "INSERT INTO promocion (nombre_promocion, descripcion, precio, imagen, estado, fecha_inicio, fecha_fin)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([$nombre, $descripcion, $precio, $imagen, $estado,
                    $fecha_inicio ?: null, $fecha_fin ?: null]);
        $promo_id = (int)$pdo->lastInsertId();

        $ins = $pdo->prepare(
            "INSERT INTO promocion_producto (promocion_id, producto_id) VALUES (?, ?)"
        );
        foreach ($productos_ids as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) $ins->execute([$promo_id, $pid]);
        }

        $pdo->commit();
        jsonResponse(['success' => true, 'id' => $promo_id]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// ── PUT (enviado como POST con ?id=X) ────────────────────────────────────────
if ($method === 'PUT') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);

    $nombre        = limpiar($_POST['nombre_promocion'] ?? '');
    $descripcion   = limpiar($_POST['descripcion']      ?? '');
    $precio        = (float)($_POST['precio']           ?? 0);
    $estado        = limpiar($_POST['estado']           ?? 'Activa');
    $fecha_inicio  = limpiar($_POST['fecha_inicio']     ?? '');
    $fecha_fin     = limpiar($_POST['fecha_fin']        ?? '');
    $productos_ids = json_decode($_POST['productos_ids'] ?? '[]', true) ?: [];

    if (!$nombre || $precio <= 0) jsonResponse(['error' => 'Nombre y precio requeridos'], 400);

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $dir = __DIR__ . '/../uploads/promociones/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext            = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $nombre_archivo);
        $imagen = '../uploads/promociones/' . $nombre_archivo;
    } else {
        $s = $pdo->prepare("SELECT imagen FROM promocion WHERE id = ?");
        $s->execute([$id]);
        $imagen = $s->fetchColumn() ?: '../estilos/img/promocion.png';
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare(
            "UPDATE promocion
             SET nombre_promocion=?, descripcion=?, precio=?, imagen=?,
                 estado=?, fecha_inicio=?, fecha_fin=?
             WHERE id=?"
        )->execute([$nombre, $descripcion, $precio, $imagen, $estado,
                    $fecha_inicio ?: null, $fecha_fin ?: null, $id]);

        $pdo->prepare("DELETE FROM promocion_producto WHERE promocion_id = ?")->execute([$id]);
        $ins = $pdo->prepare(
            "INSERT INTO promocion_producto (promocion_id, producto_id) VALUES (?, ?)"
        );
        foreach ($productos_ids as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) $ins->execute([$id, $pid]);
        }

        $pdo->commit();
        jsonResponse(['success' => true]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// ── DELETE ───────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) jsonResponse(['error' => 'ID requerido'], 400);
    $pdo->beginTransaction();
    try {
        $pdo->prepare("DELETE FROM promocion_producto WHERE promocion_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM promocion WHERE id = ?")->execute([$id]);
        $pdo->commit();
        jsonResponse(['success' => true]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

jsonResponse(['error' => 'Método no permitido'], 405);
?>
