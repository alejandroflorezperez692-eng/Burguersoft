<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
global $pdo;
$stmtProd = $pdo->query(
    "SELECT id, nombre, valor, descripcion, img, categoria
     FROM producto
     WHERE estado IN ('Disponible','Por agotarse')
     ORDER BY categoria, nombre"
);
$paginaActiva = 'inicio';
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

function formatCOP($valor) {
    return '$' . number_format((float)$valor, 0, ',', '.');
    
}
if (isset($_SESSION['logout_exitoso'])) {
    echo "<script>alert('Sesión cerrada correctamente');</script>";
    unset($_SESSION['logout_exitoso']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Inicio</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <script src="../js/Hero-Carrusel.js" defer></script>
    <style>
    body { overflow-x: hidden; }
    .promociones { padding: 40px; max-width: 1200px; margin: 0 auto; box-sizing: border-box; width: 100%; }
    #grid-promociones { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px; padding: 10px 0; }
    .promo-card-pub { background: #fff; border-radius: 16px; overflow: hidden; border: 2px solid transparent; box-shadow: 0 4px 18px rgba(0,0,0,0.10); transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s; display: flex; flex-direction: column; }
    .promo-card-pub:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); border-color: #e8821a; }
    .promo-img-pub { position: relative; height: 200px; overflow: hidden; background: #f5edd8; }
    .promo-img-pub img { width: 100%; height: 100%; object-fit: contain; object-position: center; transition: transform 0.4s; }
    .promo-card-pub:hover .promo-img-pub img { transform: scale(1.06); }
    .promo-badge-pub { position: absolute; top: 12px; right: 12px; background: #e8821a; color: #fff; font-size: 10px; font-weight: 800; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; }
    .promo-info-pub { padding: 18px 20px 20px; flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .promo-info-pub h3 { font-size: 1.1rem; font-weight: 800; color: #1e0a00; margin: 0; line-height: 1.3; }
    .promo-info-pub p { font-size: 13px; color: #888; margin: 0; line-height: 1.5; }
    .promo-fechas { font-size: 11px; color: #aaa; }
    .promo-footer-pub { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 10px; }
    .promo-precio-pub { font-size: 1.4rem; font-weight: 600; color: #d9480f; }
    .btn-circular-add { min-width: 64px; height: 44px; padding: 0 22px; border-radius: 12px; border: none; background: #e8821a; color: #fff; font-size: 18px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: background 0.2s, transform 0.15s; }
    .btn-circular-add:hover { background: #c96d10; transform: scale(1.04); }
    .btn-circular-add.btn-login { background: #e8821a; font-size: 17px; }
    .btn-circular-add.btn-login:hover { background: #c96d10; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header_publico.php'; ?>

<section class="hero">
    <div class="carousel-container">
        <div class="carousel-slide active" style="background-image:url('https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg');"></div>
        <div class="carousel-slide" style="background-image:url('https://ranchera.com.co/wp-content/uploads/2022/11/perro-colombiano-1.jpg');"></div>
        <div class="carousel-slide" style="background-image:url('https://chefstv.net/wp-content/uploads/2024/03/0045-empanadas-saltenas-fritas-wide-web.webp');"></div>
        <div class="carousel-slide" style="background-image:url('https://www.elespectador.com/resizer/v2/4YMEEW2QBVGALOUC7LSPUFNKMU.jpg?auth=1913090d3e141e8a3ccce35509259201363e9dddf853024e2f30ac71ce6383a9&width=1110&height=739&smart=true&quality=60');"></div>
    </div>
</section>

<section class="promociones">
    <h2>Combos Diarios</h2>
    <p>Disfruta de nuestros combos exclusivos por tiempo limitado.</p>
    <div id="grid-promociones"></div>
    <br><br>
</section>

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

<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style="display:flex;align-items:center;gap:8px;justify-content:center;margin-bottom:10px;margin-top:-30px;">
                    <img src="../estilos/img/icono1-oscuro.png" alt="Logo de El Oriente" class="footer-logo">
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
                <li><span>Sábado:</span> <span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span> <span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>


<script src="../js/accesibilidad.js"></script>
<script src="/burguersoft/js/Menu.js"></script>
<script>
const SESION_ACTIVA = <?= json_encode(!empty($_SESSION['id_usuario'])) ?>;

function togglePanel() {
    document.getElementById('accPanel').classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', () => { cargarPromociones(); });

async function cargarPromociones() {
    const grid = document.getElementById('grid-promociones');
    try {
        const res    = await fetch('/burguersoft/controllers/promociones.php');
        const promos = await res.json();

        const hoy     = new Date().toISOString().slice(0, 10);
        const activas = promos.filter(p => {
            if (p.estado !== 'Activa') return false;
            if (p.fecha_fin    && p.fecha_fin    < hoy) return false;
            if (p.fecha_inicio && p.fecha_inicio > hoy) return false;
            return true;
        });

        if (!activas.length) {
            grid.innerHTML = '<p style="padding:20px;color:#888;grid-column:1/-1">No hay promociones activas en este momento.</p>';
            return;
        }

        grid.innerHTML = '';

        activas.forEach(p => {
            const imgSrc    = p.imagen || '../estilos/img/promocion.png';
            const fechaHtml = (p.fecha_inicio || p.fecha_fin)
                ? `<div class="promo-fechas">
                       ${p.fecha_inicio ? 'Desde ' + p.fecha_inicio : ''}
                       ${p.fecha_fin    ? ' hasta ' + p.fecha_fin   : ''}
                   </div>`
                : '';

            const card = document.createElement('div');
            card.className = 'promo-card-pub';
            card.innerHTML = `
                <div class="promo-img-pub">
                    <img src="${imgSrc}" alt="${p.nombre}"
                         onerror="this.src='../estilos/img/promocion.png'">
                    <span class="promo-badge-pub">PROMO</span>
                </div>
                <div class="promo-info-pub">
                    <h3>${p.nombre}</h3>
                    ${p.descripcion ? `<p>${p.descripcion}</p>` : ''}
                    ${fechaHtml}
                    <div class="promo-footer-pub">
                        <div class="promo-precio-pub">$${Number(p.precio).toLocaleString('es-CO')}</div>
                        ${SESION_ACTIVA
                            ? `<button class="btn-circular-add" title="Agregar al carrito">+</button>`
                            : `<button class="btn-circular-add btn-login" title="Inicia sesión para pedir"
                                   onclick="window.location.href='/burguersoft/php/login.php'">
                                   <img src="../estilos/img/bloquear.png"
                                        style="filter:invert(1);pointer-events:none;width:18px;height:18px;">
                               </button>`
                        }
                    </div>
                </div>`;

            if (SESION_ACTIVA) {
                card.querySelector('.btn-circular-add').addEventListener('click', function () {
                    agregarAlCarrito(p.id, p.nombre, p.precio, imgSrc, 'promocion', this);
                    this.textContent = '✓';
                    this.style.background = '#27ae60';
                    const btn = this;
                    setTimeout(() => { btn.textContent = '+'; btn.style.background = ''; }, 900);
                });
            }

            grid.appendChild(card);
        });

    } catch (e) {
        grid.innerHTML = '<p style="padding:20px;color:#888;grid-column:1/-1">No se pudieron cargar las promociones.</p>';
        console.error(e);
    }
}
</script>

</body>
</html>