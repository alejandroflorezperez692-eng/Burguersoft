<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirLogin();
$navActivo = 'promociones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Burguersoft – Promociones</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@400;700;800&display=swap">
<link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
<link rel="stylesheet" href="../estilos/promociones.css">
<link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="container-promos">

    <div class="promos-header">
        <div>
            <h1>Combos y Promociones</h1>
            <p class="subtitulo">Gestiona las ofertas activas del local</p>
        </div>
    </div>

    <div class="top-bar">
        <input type="text" id="buscar" placeholder="Buscar promoción...">
        <button class="btn-agregar" onclick="abrirModal()">Añadir promoción</button>
    </div>

    <div class="promo-meta">
        <span id="contador-promos">0 promociones</span>
    </div>

    <div id="listaPromos" class="cards">
        <div class="empty-message">Cargando promociones...</div>
    </div>

</div>
</div>

<!-- MODAL -->
<div id="formulario" class="modal">
    <div class="modal-content">
        <h2 id="tituloForm">Añadir Promoción</h2>

        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" placeholder="Ej: Combo Burger Deluxe">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" placeholder="Ej: Hamburguesa con queso, papas y bebida"></textarea>
        </div>

        <div class="form-group">
            <label for="precio">Precio *</label>
            <input type="number" id="precio" placeholder="Ej: 15000" step="100" min="0">
        </div>

        <div class="form-group">
            <label>Productos del combo</label>
            <div class="prod-search-wrap">
                <input type="text" id="prod-buscar" placeholder="Buscar producto..." autocomplete="off" oninput="filtrarProductos()">
            </div>
            <div id="prod-lista" class="prod-lista"></div>
            <div id="prod-seleccionados" class="prod-seleccionados">
                <span class="prod-sel-label">Ningún producto seleccionado</span>
            </div>
        </div>

        <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" accept="image/*">
            <img id="preview" class="preview">
        </div>

        <div class="modal-buttons">
            <button class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-guardar" onclick="guardarPromo()">Guardar</button>
        </div>
    </div>
</div>

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

<script>
const CTRL = '../controllers/promociones.php';
let promociones    = [];
let todosProductos = [];
let seleccionados  = new Set();
let editandoId     = null;

async function init() {
    await Promise.all([cargarPromos(), cargarProductos()]);
}

// ── Carga de datos ─────────────────────────────────────────────────────────
async function cargarPromos() {
    try {
        const res = await fetch(CTRL);
        promociones = await res.json();
        renderPromos();
    } catch (e) {
        document.getElementById('listaPromos').innerHTML =
            '<div class="empty-message">No se pudo conectar al servidor.</div>';
    }
}

async function cargarProductos() {
    try {
        const res      = await fetch(`${CTRL}?accion=productos`);
        todosProductos = await res.json();
    } catch (e) {
        todosProductos = [];
    }
}

// ── Render tarjetas ────────────────────────────────────────────────────────
function renderPromos() {
    const lista     = document.getElementById('listaPromos');
    const busqueda  = document.getElementById('buscar').value.toLowerCase().trim();
    const filtradas = promociones.filter(p =>
        (p.nombre_promocion || '').toLowerCase().includes(busqueda)
    );

    document.getElementById('contador-promos').textContent =
        filtradas.length === 1 ? '1 promoción' : `${filtradas.length} promociones`;

    if (!filtradas.length) {
        lista.innerHTML = '<div class="empty-message">No hay promociones. ¡Agrega la primera!</div>';
        return;
    }

    lista.innerHTML = filtradas.map(p => {
        const imgSrc   = p.imagen || '../estilos/img/promocion.png';
        const prods    = p.productos || [];
        const prodsHtml = prods.length
            ? prods.map(pr => `<span class="tag-prod">${pr.nombre}</span>`).join('')
            : '<span class="tag-prod tag-vacio">Sin productos</span>';

        return `
        <div class="card">
            <div class="card-img-wrap">
                <img src="${imgSrc}" class="card-img" alt="${p.nombre_promocion}"
                     onerror="this.onerror=null;this.src='../estilos/img/promocion.png'">
                <span class="card-badge">Promo</span>
            </div>
            <div class="card-body">
                <div class="card-nombre">${p.nombre_promocion}</div>
                ${p.descripcion ? `<div class="card-descripcion">${p.descripcion}</div>` : ''}
                <div class="card-productos">${prodsHtml}</div>
            </div>
            <div class="card-footer">
                <div class="card-precio">$${Number(p.precio).toLocaleString('es-CO')}</div>
                <div class="card-actions">
                    <button class="btn-editar"   onclick="editarPromo(${p.id})">Editar</button>
                    <button class="btn-eliminar" onclick="eliminarPromo(${p.id})">Eliminar</button>
                </div>
            </div>
        </div>`;
    }).join('');
}

// ── Selector de productos ─────────────────────────────────────────────────
function filtrarProductos() {
    const q = document.getElementById('prod-buscar').value.toLowerCase().trim();
    const lista = q
        ? todosProductos.filter(p => p.nombre.toLowerCase().includes(q))
        : todosProductos;
    renderListaProductos(lista);
}

function renderListaProductos(lista) {
    const cont = document.getElementById('prod-lista');
    if (!lista.length) {
        cont.innerHTML = '<div class="prod-empty">No se encontraron productos</div>';
        return;
    }
    cont.innerHTML = lista.map(p => `
        <label class="prod-item ${seleccionados.has(p.id) ? 'selected' : ''}">
            <input type="checkbox" value="${p.id}"
                   ${seleccionados.has(p.id) ? 'checked' : ''}
                   onchange="toggleProducto(${p.id}, this.checked)">
            <img src="${p.img || '../estilos/img/promocion.png'}"
                 onerror="this.src='../estilos/img/promocion.png'" alt="">
            <div class="prod-item-info">
                <span class="prod-item-nombre">${p.nombre}</span>
                <span class="prod-item-precio">$${Number(p.valor).toLocaleString('es-CO')}</span>
            </div>
        </label>
    `).join('');
}

function toggleProducto(id, checked) {
    if (checked) seleccionados.add(id);
    else seleccionados.delete(id);
    actualizarChips();
    filtrarProductos();
}

function actualizarChips() {
    const cont = document.getElementById('prod-seleccionados');
    if (!seleccionados.size) {
        cont.innerHTML = '<span class="prod-sel-label">Ningún producto seleccionado</span>';
        return;
    }
    cont.innerHTML = [...seleccionados].map(id => {
        const p = todosProductos.find(x => x.id === id);
        if (!p) return '';
        return `<span class="chip">${p.nombre}
                    <button onclick="toggleProducto(${id}, false)" title="Quitar">×</button>
                </span>`;
    }).join('');
}

// ── Modal ──────────────────────────────────────────────────────────────────
function abrirModal() {
    editandoId = null;
    seleccionados.clear();
    document.getElementById('tituloForm').textContent = 'Añadir Promoción';
    ['nombre','descripcion','precio','imagen','prod-buscar'].forEach(id =>
        document.getElementById(id).value = ''
    );
    document.getElementById('preview').classList.remove('show');
    renderListaProductos(todosProductos);
    actualizarChips();
    document.getElementById('formulario').classList.add('show');
}

function cerrarModal() {
    document.getElementById('formulario').classList.remove('show');
    editandoId = null;
    seleccionados.clear();
}

async function editarPromo(id) {
    const promo = promociones.find(p => p.id === id);
    if (!promo) return;
    editandoId = id;

    seleccionados.clear();
    try {
        const res = await fetch(`${CTRL}?accion=productos_promo&id=${id}`);
        const ids = await res.json();
        ids.forEach(pid => seleccionados.add(Number(pid)));
    } catch(e) {}

    document.getElementById('tituloForm').textContent = 'Editar Promoción';
    document.getElementById('nombre').value           = promo.nombre_promocion;
    document.getElementById('descripcion').value      = promo.descripcion || '';
    document.getElementById('precio').value           = promo.precio;
    document.getElementById('imagen').value           = '';
    document.getElementById('prod-buscar').value      = '';

    const prev = document.getElementById('preview');
    if (promo.imagen) { prev.src = promo.imagen; prev.classList.add('show'); }
    else prev.classList.remove('show');

    renderListaProductos(todosProductos);
    actualizarChips();
    document.getElementById('formulario').classList.add('show');
}

async function eliminarPromo(id) {
    if (!confirm('¿Eliminar esta promoción?')) return;
    try {
        const res  = await fetch(`${CTRL}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) cargarPromos();
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

// ── Guardar ────────────────────────────────────────────────────────────────
async function guardarPromo() {
    const nombre      = document.getElementById('nombre').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const precio      = document.getElementById('precio').value;
    const imagenFile  = document.getElementById('imagen').files[0];

    if (!nombre || !precio) { alert('Nombre y precio son obligatorios'); return; }

    const fd = new FormData();
    fd.append('nombre_promocion', nombre);
    fd.append('descripcion',      descripcion);
    fd.append('precio',           precio);
    fd.append('productos_ids',    JSON.stringify([...seleccionados]));
    if (imagenFile) fd.append('imagen', imagenFile);

    const url    = editandoId ? `${CTRL}?id=${editandoId}` : CTRL;
    const method = editandoId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, { method, body: fd });
        const data = await res.json();
        if (data.success || data.id) { cerrarModal(); cargarPromos(); }
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

document.getElementById('imagen').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('preview').src = ev.target.result;
        document.getElementById('preview').classList.add('show');
    };
    reader.readAsDataURL(file);
});

document.getElementById('buscar').addEventListener('input', renderPromos);
document.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>
