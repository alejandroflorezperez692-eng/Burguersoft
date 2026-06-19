<?php
session_start();

require_once __DIR__ . '/../includes/conexion.php';
global $pdo;

$stmtProd = $pdo->query(
    "SELECT id, nombre, valor, descripcion, img, categoria
     FROM producto
     WHERE estado IN ('Disponible','Por agotarse')
     ORDER BY categoria, nombre"
);
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// Promociones activas
$hoy = date('Y-m-d');
$stmtPromo = $pdo->prepare(
    "SELECT id, nombre, descripcion, precio, imagen
     FROM promocion
     WHERE estado = 'Activa'
       AND (fecha_inicio IS NULL OR fecha_inicio <= ?)
       AND (fecha_fin   IS NULL OR fecha_fin   >= ?)"
);
$stmtPromo->execute([$hoy, $hoy]);
$promociones = $stmtPromo->fetchAll(PDO::FETCH_ASSOC);

// Agrupar productos por categoría
$grupos = [];
foreach ($productos as $p) {
    $grupos[$p['categoria']][] = $p;
}
ksort($grupos);

if (!function_exists('hv')) {
    function hv($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('formatCOP')) {
    function formatCOP($v) { return '$' . number_format((float)$v, 0, ',', '.'); }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT – Menú</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <link rel="stylesheet" href="../estilos/factura-estilos.css">
    <style>
        :root {
            --primario:   #3d2111;
            --secundario: #F18921;
            --alerta:     #C3402A;
            --fondo:      #f6f5e4;
        }
        body { background: var(--fondo); min-height: 100vh; }

        .titulo-seccion {
            text-align: center;
            font-size: 32px;
            padding: 36px 20px 10px;
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
            letter-spacing: 1px;
        }

        #productos-container {
            padding: 10px 30px 60px;
            max-width: 1300px;
            margin: 0 auto;
        }

        .promos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 10px;
        }
        .promo-card {
            background: linear-gradient(135deg, #fff8ee 0%, #fff3de 100%);
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #EF9F27;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
            position: relative;
        }
        .promo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(239,159,39,.25);
        }
        .promo-badge {
            position: absolute;
            top: 10px; left: 10px;
            background: #E8821A;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .promo-card-img {
            width: 100%; height: 160px;
            object-fit: cover;
            background: #e8e0d4;
        }
        .promo-card-body {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .promo-card-nombre {
            font-weight: bold;
            font-size: 15px;
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
        }
        .promo-card-desc {
            font-size: 12px;
            color: #777;
            line-height: 1.4;
            flex: 1;
        }
        .promo-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px 12px;
            border-top: 1px solid #f0d8b0;
        }
        .promo-card-precio {
            font-size: 16px;
            font-weight: bold;
            color: #E8821A;
            font-family: 'Lucida Sans', sans-serif;
        }

        .cat-titulo {
            font-family: 'Lucida Sans', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: var(--primario);
            margin: 36px 0 16px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--secundario);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cat-titulo::before {
            content: '';
            display: inline-block;
            width: 12px; height: 12px;
            background: var(--secundario);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .prod-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #e8e0d4;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            display: flex;
            flex-direction: column;
        }
        .prod-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(61,33,17,.15);
            border-color: var(--secundario);
        }
        .prod-card-img {
            width: 100%; height: 160px;
            object-fit: cover;
            background: #e8e0d4;
        }
        .prod-card-body {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .prod-card-nombre {
            font-weight: bold;
            font-size: 15px;
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
        }
        .prod-card-desc {
            font-size: 12px;
            color: #777;
            line-height: 1.4;
            flex: 1;
        }
        .prod-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px 12px;
            border-top: 1px solid #f0ebe3;
        }
        .prod-card-precio {
            font-size: 16px;
            font-weight: bold;
            color: var(--alerta);
            font-family: 'Lucida Sans', sans-serif;
        }
        .btn-add {
            background: var(--secundario);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-add:hover { background: var(--primario); }

        .menu-vacio {
            text-align: center;
            padding: 60px 20px;
            color: #888;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php
$paginaActiva = 'menu';
include __DIR__ . '/../includes/header_publico.php';
?>


<h2 class="titulo-seccion">NUESTROS PRODUCTOS</h2>

<div id="productos-container">

    <?php if (empty($grupos)): ?>
        <div class="menu-vacio">
            El menú está vacío por ahora. ¡Vuelve pronto!
        </div>

    <?php else: ?>

        <?php foreach ($grupos as $categoria => $items): ?>
            <h3 class="cat-titulo"><?= hv($categoria) ?></h3>
            <div class="productos-grid">
                <?php foreach ($items as $p): ?>
                <div class="prod-card">
                    <img class="prod-card-img"
                         src="<?= hv($p['img']) ?>"
                         alt="<?= hv($p['nombre']) ?>"
                         onerror="this.src='/burguersoft/estilos/img/placeholder.png'">
                    <div class="prod-card-body">
                        <div class="prod-card-nombre"><?= hv($p['nombre']) ?></div>
                        <div class="prod-card-desc"><?= hv($p['descripcion']) ?></div>
                    </div>
                    <div class="prod-card-footer">
                        <span class="prod-card-precio"><?= formatCOP($p['valor']) ?></span>
                        <?php if (isset($_SESSION['id_usuario'])): ?>
                        <button type="button" class="btn-add" title="Agregar al carrito"
                            onclick="agregarAlCarrito(
                                <?= (int)$p['id'] ?>,
                                '<?= hv($p['nombre']) ?>',
                                <?= (float)$p['valor'] ?>,
                                '<?= hv($p['img']) ?>',
                                'producto',
                                this
                            )">+</button>
                        <?php else: ?>
                        <a href="/burguersoft/php/login.php">
                            <button type="button" class="btn-add" title="Inicia sesión para agregar">🔒</button>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php
$checkout_modal = __DIR__ . '/../php/checkout_modal.php';
if (file_exists($checkout_modal)) include $checkout_modal;
?>

<script src="/burguersoft/js/Menu.js"></script>

<!-- Accesibilidad -->
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
                <li><span>Sábado:</span>           <span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span>           <span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>