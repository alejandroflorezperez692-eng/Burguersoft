<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

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

function formatCOP($valor) {
    return '$' . number_format((float)$valor, 0, ',', '.');
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
    <script src="../js/Hero-Carrusel.js" defer></script>
    <style>
        /* ── Grid de promociones ── */
        #grid-promociones {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 22px;
            padding: 10px 0;
        }

        .promo-card-pub {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid transparent;
            box-shadow: 0 4px 18px rgba(0,0,0,0.10);
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            display: flex;
            flex-direction: column;
        }
        .promo-card-pub:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-color: #e8821a;
        }
        .promo-img-pub {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #f5edd8;
        }
        .promo-img-pub img {
            width: 100%; height: 100%;
            object-fit: contain;
            object-position: center;
            transition: transform 0.4s;
        }
        .promo-card-pub:hover .promo-img-pub img { transform: scale(1.06); }
        .promo-badge-pub {
            position: absolute;
            top: 12px; right: 12px;
            background: #e8821a;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .promo-info-pub {
            padding: 18px 20px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .promo-info-pub h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e0a00;
            margin: 0;
            line-height: 1.3;
        }
        .promo-info-pub p {
            font-size: 13px;
            color: #888;
            margin: 0;
            line-height: 1.5;
        }
        .promo-fechas {
            font-size: 11px;
            color: #aaa;
        }
        .promo-footer-pub {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 10px;
        }
        .promo-precio-pub {
            font-size: 1.4rem;
            font-weight: 600;
            color: #d9480f;
        }
        .btn-circular-add {
            min-width: 64px;
            height: 44px;
            padding: 0 22px;
            border-radius: 12px;
            border: none;
            background: #e8821a;
            color: #fff;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-circular-add:hover { background: #c96d10; transform: scale(1.04); }
        .btn-circular-add.btn-login { background: #e8821a; font-size: 17px; }
        .btn-circular-add.btn-login:hover { background: #c96d10; }

        /* ── Carrito lateral (unificado, reemplaza cartPanel del header en esta página) ── */
        .carrito-panel {
            position: fixed;
            top: 0; right: -420px;
            width: 400px;
            height: 100vh;
            background: #fff;
            box-shadow: -4px 0 24px rgba(0,0,0,0.15);
            z-index: 1200;
            display: flex;
            flex-direction: column;
            transition: right 0.3s ease;
        }
        .carrito-panel.abierto { right: 0; }
        .carrito-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0e8df;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .carrito-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: #1e0a00;
            margin: 0;
        }
        .btn-cerrar-carrito {
            background: #f5edd8;
            border: none;
            border-radius: 50%;
            width: 32px; height: 32px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b4c38;
        }
        .carrito-items {
            flex: 1;
            overflow-y: auto;
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .carrito-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #faf7f3;
            border-radius: 10px;
        }
        .carrito-item-info { flex: 1; min-width: 0; }
        .carrito-item-nombre {
            font-size: 13px;
            font-weight: 700;
            color: #1e0a00;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .carrito-item-tipo {
            font-size: 10px;
            font-weight: 600;
            color: #e8821a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .carrito-item-precio {
            font-size: 12px;
            color: #e8821a;
            font-weight: 700;
        }
        .carrito-item-controles {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-qty {
            width: 26px; height: 26px;
            border: 1.5px solid #e8821a;
            background: #fff;
            color: #e8821a;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .btn-qty:hover { background: #e8821a; color: #fff; }
        .qty-num { font-size: 14px; font-weight: 700; color: #1e0a00; min-width: 20px; text-align: center; }
        .carrito-footer {
            padding: 20px 24px;
            border-top: 1px solid #f0e8df;
        }
        .carrito-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .carrito-total-label { font-size: 15px; font-weight: 700; color: #1e0a00; }
        .carrito-total-val   { font-size: 22px; font-weight: 900; color: #e8821a; }
        .select-metodo {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e8ddd3;
            border-radius: 10px;
            font-size: 14px;
            color: #1e0a00;
            background: #faf7f3;
            outline: none;
            margin-bottom: 12px;
        }
        .btn-finalizar {
            width: 100%;
            padding: 14px;
            background: #e8821a;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-finalizar:hover { background: #c96d10; transform: translateY(-1px); }
        .carrito-vacio {
            text-align: center;
            color: #bbb;
            padding: 40px 0;
            font-size: 14px;
        }
        .panel-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1100;
        }
        .panel-backdrop.activo { display: block; }

        /* ── Botón flotante del carrito ── */
        .carrito-flotante {
            position: fixed;
            bottom: 90px;
            right: 24px;
            background: #e8821a;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 56px; height: 56px;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(232,130,26,0.5);
            z-index: 500;
            display: none;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .carrito-flotante:hover { transform: scale(1.1); }
        .carrito-badge {
            position: absolute;
            top: -4px; right: -4px;
            background: #c8382a;
            color: #fff;
            border-radius: 50%;
            width: 20px; height: 20px;
            font-size: 11px;
            font-weight: 700;
            display: none;
            align-items: center;
            justify-content: center;
        }

        /* ── Toast ── */
        .toast-carrito {
            position: fixed;
            bottom: 160px;
            right: 24px;
            background: #1a7a42;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            z-index: 1500;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.25s, transform 0.25s;
            pointer-events: none;
        }
        .toast-carrito.show { opacity: 1; transform: translateY(0); }
        .toast-carrito.err  { background: #c8382a; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header_publico.php'; ?>

<!-- Hero carrusel -->
<section class="hero">
    <div class="carousel-container">
        <div class="carousel-slide active" style="background-image:url('https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg');"></div>
        <div class="carousel-slide" style="background-image:url('https://ranchera.com.co/wp-content/uploads/2022/11/perro-colombiano-1.jpg');"></div>
        <div class="carousel-slide" style="background-image:url('https://chefstv.net/wp-content/uploads/2024/03/0045-empanadas-saltenas-fritas-wide-web.webp');"></div>
        <div class="carousel-slide" style="background-image:url('https://www.elespectador.com/resizer/v2/4YMEEW2QBVGALOUC7LSPUFNKMU.jpg?auth=1913090d3e141e8a3ccce35509259201363e9dddf853024e2f30ac71ce6383a9&width=1110&height=739&smart=true&quality=60');"></div>
    </div>
</section>

<!-- Sección de promociones -->
<section class="promociones">
    <h2>Combos Diarios</h2>
    <p>Disfruta de nuestros combos exclusivos por tiempo limitado.</p>
    <div id="grid-promociones">

        <?php if (empty($grupos) && empty($promociones)): ?>
        <p style="padding:20px;color:#888;">Cargando promociones…</p>

        <?php else: ?>

        <?php if (!empty($promociones)): ?>
            <h3 class="cat-titulo"> Promociones del día</h3>
            <div class="promos-grid">
                <?php foreach ($promociones as $promo): ?>
                <div class="promo-card">
                    <span class="promo-badge">Promo</span>
                    <img class="promo-card-img"
                         src="<?= hv($promo['imagen']) ?>"
                         alt="<?= hv($promo['nombre']) ?>"
                         onerror="this.src='/burguersoft/estilos/img/placeholder.png'">
                    <div class="promo-card-body">
                        <div class="promo-card-nombre"><?= hv($promo['nombre']) ?></div>
                        <div class="promo-card-desc"><?= hv($promo['descripcion']) ?></div>
                    </div>
                    <div class="promo-card-footer">
                        <span class="promo-card-precio"><?= formatCOP($promo['precio']) ?></span>
                        <?php if (isset($_SESSION['id_usuario'])): ?>
                        <button type="button" class="btn-add" title="Agregar promo al carrito"
                            onclick="agregarAlCarrito(
                                <?= (int)$promo['id'] ?>,
                                '<?= hv($promo['nombre']) ?>',
                                <?= (float)$promo['precio'] ?>,
                                '<?= hv($promo['imagen']) ?>',
                                'promocion'
                            )">+</button>
                        <?php else: ?> 
                        <a href="/burguersoft/php/login.php">
                            <button type="button" class="btn-add" title="Inicia sesión para agregar">
                                <img src="../estilos/img/bloquear.png" style="filter:invert(1);">
                        </button>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>   
    </div>
    <br><br>
</section>


<!-- Panel lateral del carrito -->
<div class="carrito-panel" id="carrito-panel">
    <div class="carrito-header">
        <h3>🛒 Tu pedido</h3>
        <button class="btn-cerrar-carrito" onclick="cerrarCarrito()">✕</button>
    </div>
    <div class="carrito-items" id="carrito-items">
        <div class="carrito-vacio">El carrito está vacío.</div>
    </div>
    <div class="carrito-footer">
        <div class="carrito-total-row">
            <span class="carrito-total-label">Total</span>
            <span class="carrito-total-val" id="carrito-total">$0</span>
        </div>
        <select class="select-metodo" id="metodo-pago">
            <option value="">Seleccionar método de pago</option>
            <option value="Efectivo">Efectivo</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Nequi">Nequi</option>
            <option value="Daviplata">Daviplata</option>
        </select>
        <button class="btn-finalizar" onclick="finalizarPedido()">Confirmar pedido</button>
    </div>
</div>

<!-- Toast -->

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

<script>
// ── Estado de sesión (PHP → JS) ──────────────────────────────────────────────
const SESION_ACTIVA = <?= json_encode(!empty($_SESSION['id_usuario'])) ?>;

// ── Estado del carrito ───────────────────────────────────────────────────────
let carrito = [];

// ── Toast ────────────────────────────────────────────────────────────────────
function toast(msg, err = false) {
    const t = document.getElementById('toast-carrito');
    t.textContent = msg;
    t.className = 'toast-carrito show' + (err ? ' err' : '');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.className = 'toast-carrito', 2800);
}

// ── Accesibilidad toggle ─────────────────────────────────────────────────────
function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

// ── Carrito: abrir / cerrar ──────────────────────────────────────────────────
function abrirCarrito() {
    document.getElementById('carrito-panel').classList.add('abierto');
    document.getElementById('backdrop').classList.add('activo');
    // Ocultar el cartPanel del header si existe
    document.getElementById('cartPanel')?.classList.remove('active');
}

function cerrarCarrito() {
    document.getElementById('carrito-panel').classList.remove('abierto');
    document.getElementById('backdrop').classList.remove('activo');
}

// ── Agregar al carrito ───────────────────────────────────────────────────────
// tipo: 'producto' | 'promocion'
function agregarAlCarrito(tipo, id, nombre, precio) {
    if (!SESION_ACTIVA) {
        window.location.href = '/burguersoft/php/login.php';
        return;
    }
    const idx = carrito.findIndex(i => i.tipo === tipo && i.id === id);
    if (idx >= 0) {
        carrito[idx].cantidad++;
    } else {
        carrito.push({ tipo, id, nombre, precio: Number(precio), cantidad: 1 });
    }
    renderCarrito();
    sincronizarHeaderBadge();
    toast('✓ ' + nombre + ' agregado');
    abrirCarrito();
}

// ── Cambiar cantidad ─────────────────────────────────────────────────────────
function cambiarCantidad(idx, delta) {
    carrito[idx].cantidad += delta;
    if (carrito[idx].cantidad <= 0) carrito.splice(idx, 1);
    renderCarrito();
    sincronizarHeaderBadge();
}

// ── Renderizar carrito ───────────────────────────────────────────────────────
function renderCarrito() {
    const wrap  = document.getElementById('carrito-items');
    const badge = document.getElementById('carrito-badge');
    const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
    const count = carrito.reduce((s, i) => s + i.cantidad, 0);

    document.getElementById('carrito-total').textContent = '$' + total.toLocaleString('es-CO');

    // Botón flotante y badge
    const btnFlotante = document.getElementById('btn-carrito-flotante');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
        btnFlotante.style.display = 'flex';
    } else {
        badge.style.display = 'none';
        btnFlotante.style.display = SESION_ACTIVA ? 'flex' : 'none';
    }

    if (!carrito.length) {
        wrap.innerHTML = '<div class="carrito-vacio">El carrito está vacío.</div>';
        return;
    }

    wrap.innerHTML = '';
    carrito.forEach((item, idx) => {
        const tipoLabel = item.tipo === 'promocion' ? ' Promoción' : ' Producto';
        const div = document.createElement('div');
        div.className = 'carrito-item';
        div.innerHTML = `
            <div class="carrito-item-info">
                <div class="carrito-item-tipo">${tipoLabel}</div>
                <div class="carrito-item-nombre">${item.nombre}</div>
                <div class="carrito-item-precio">$${(item.precio * item.cantidad).toLocaleString('es-CO')}</div>
            </div>
            <div class="carrito-item-controles">
                <button class="btn-qty" onclick="cambiarCantidad(${idx}, -1)">−</button>
                <span class="qty-num">${item.cantidad}</span>
                <button class="btn-qty" onclick="cambiarCantidad(${idx}, 1)">+</button>
            </div>
        `;
        wrap.appendChild(div);
    });
}

// ── Sincronizar badge del carrito del header (si existe) ─────────────────────
function sincronizarHeaderBadge() {
    const count = carrito.reduce((s, i) => s + i.cantidad, 0);
    const hBadge = document.getElementById('badge-carrito');
    if (hBadge) hBadge.textContent = count;
}

// ── Finalizar pedido ─────────────────────────────────────────────────────────
async function finalizarPedido() {
    if (!SESION_ACTIVA) { window.location.href = '/burguersoft/php/login.php'; return; }
    if (!carrito.length) { toast('El carrito está vacío', true); return; }

    const metodo = document.getElementById('metodo-pago').value;
    if (!metodo) { toast('Selecciona un método de pago', true); return; }

    // Separar productos y promociones
    const items = carrito
        .filter(i => i.tipo === 'producto')
        .map(i => ({
            producto_id:     i.id,
            cantidad:        i.cantidad,
            precio_unitario: i.precio
        }));

    const promociones = carrito
        .filter(i => i.tipo === 'promocion')
        .map(i => ({
            promocion_id: i.id,
            cantidad:     i.cantidad,
            precio:       i.precio
        }));

    try {
        const res = await fetch('/burguersoft/controllers/ventas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ metodo_pago: metodo, items, promociones })
        });
        const data = await res.json();
        if (data.success) {
            carrito = [];
            renderCarrito();
            sincronizarHeaderBadge();
            cerrarCarrito();
            document.getElementById('metodo-pago').value = '';
            toast(' Pedido realizado con éxito');
        } else {
            toast(data.error || 'Error al procesar el pedido', true);
        }
    } catch (e) {
        toast('Error de conexión', true);
    }
}

// ── Redirigir toggleCart del header a este carrito ───────────────────────────
// (evita conflicto con el cartPanel del header_publico)
document.addEventListener('DOMContentLoaded', () => {

    // Mostrar botón flotante si está logueado
    if (SESION_ACTIVA) {
        document.getElementById('btn-carrito-flotante').style.display = 'flex';
    }

    // Sobrescribir el botón del carrito del header para que use ESTE panel
    const toggleCart = document.getElementById('toggleCart');
    if (toggleCart) {
        // Clonar para remover listeners anteriores
        const nuevoBtn = toggleCart.cloneNode(true);
        toggleCart.parentNode.replaceChild(nuevoBtn, toggleCart);
        nuevoBtn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            abrirCarrito();
        });
    }

    // Cargar promociones activas desde el controller
    cargarPromociones();
});

// ── Cargar y renderizar promociones ──────────────────────────────────────────
async function cargarPromociones() {
    const grid = document.getElementById('grid-promociones');
    try {
        const res   = await fetch('/burguersoft/controllers/promociones.php');
        const promos = await res.json();

        // Filtrar solo activas y vigentes
        const hoy    = new Date().toISOString().slice(0, 10);
        const activas = promos.filter(p => {
            if (p.estado !== 'Activa') return false;
            if (p.fecha_fin && p.fecha_fin < hoy) return false;
            if (p.fecha_inicio && p.fecha_inicio > hoy) return false;
            return true;
        });

        if (!activas.length) {
            grid.innerHTML = '<p style="padding:20px;color:#888;grid-column:1/-1">No hay promociones activas en este momento.</p>';
            return;
        }

        grid.innerHTML = activas.map(p => {
            const imgSrc   = p.imagen || '../estilos/img/promocion.png';
            const fechaHtml = (p.fecha_inicio || p.fecha_fin)
                ? `<div class="promo-fechas">
                       ${p.fecha_inicio ? 'Desde ' + p.fecha_inicio : ''}
                       ${p.fecha_fin    ? ' hasta ' + p.fecha_fin   : ''}
                   </div>`
                : '';

            if (SESION_ACTIVA) {
                // Usuario logueado → botón circular para agregar al carrito
                return `
                <div class="promo-card-pub">
                    <div class="promo-img-pub">
                        <img src="${imgSrc}" alt="${p.nombre}"
                             onerror="this.src='../estilos/img/promocion.png'">
                        <span class="promo-badge-pub"> PROMO</span>
                    </div>
                    <div class="promo-info-pub">
                        <h3>${p.nombre}</h3>
                        ${p.descripcion ? `<p>${p.descripcion}</p>` : ''}
                        ${fechaHtml}
                        <div class="promo-footer-pub">
                            <div class="promo-precio-pub">$${Number(p.precio).toLocaleString('es-CO')}</div>
                            <button class="btn-circular-add" title="Agregar al carrito"
                                onclick="agregarAlCarrito('promocion', ${p.id}, ${JSON.stringify(p.nombre)}, ${p.precio})">
                                +
                            </button>
                        </div>
                    </div>
                </div>`;
            } else {
                // No logueado → botón circular que invita a iniciar sesión
                return `
                <div class="promo-card-pub">
                    <div class="promo-img-pub">
                        <img src="${imgSrc}" alt="${p.nombre}"
                             onerror="this.src='../estilos/img/promocion.png'">
                        <span class="promo-badge-pub"> PROMO</span>
                    </div>
                    <div class="promo-info-pub">
                        <h3>${p.nombre}</h3>
                        ${p.descripcion ? `<p>${p.descripcion}</p>` : ''}
                        ${fechaHtml}
                        <div class="promo-footer-pub">
                            <div class="promo-precio-pub">$${Number(p.precio).toLocaleString('es-CO')}</div>
                            <button class="btn-circular-add btn-login" title="Inicia sesión para pedir"
                                onclick="window.location.href='/burguersoft/php/login.php'">
                                🔒
                            </button>
                        </div>
                    </div>
                </div>`;
            }
        }).join('');

    } catch (e) {
        grid.innerHTML = '<p style="padding:20px;color:#888;grid-column:1/-1">No se pudieron cargar las promociones.</p>';
    }
}
</script>

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