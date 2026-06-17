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
    <title>Burgersoft — Promociones</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .promos-page { padding: 36px 40px 60px; }

        .topbar {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .topbar input {
            flex: 1;
            min-width: 220px;
            padding: 11px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .topbar input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-glow); }
        .topbar input::placeholder { color: var(--text-400); }

        .meta-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .meta-bar span { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-400); }
        .meta-bar::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        .promos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 22px;
        }

        .promo-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: transform 0.25s var(--ease), box-shadow 0.25s, border-color 0.25s;
        }

        .promo-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }

        .promo-img-wrap {
            height: 190px;
            background: var(--surface-3);
            position: relative;
            overflow: hidden;
        }

        .promo-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s var(--ease);
        }

        .promo-card:hover .promo-img-wrap img { transform: scale(1.06); }

        .promo-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--brand);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .promo-body {
            padding: 18px 20px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .promo-nombre {
            font-family: var(--font-display);
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-900);
            line-height: 1.3;
        }

        .promo-desc {
            font-size: 13px;
            color: var(--text-400);
            line-height: 1.5;
            flex: 1;
        }

        .promo-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 4px;
        }

        .tag {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            background: rgba(232,130,26,0.1);
            border: 1px solid rgba(232,130,26,0.25);
            border-radius: 20px;
            color: #8a4a10;
        }

        .tag-empty { background: rgba(22,8,0,0.05); border-color: var(--border); color: var(--text-400); font-weight: 400; font-style: italic; }

        .promo-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: rgba(245,237,216,0.4);
        }

        .promo-precio {
            font-family: var(--font-display);
            font-size: 1.3rem;
            font-weight: 900;
            color: var(--brand);
        }

        .promo-actions { display: flex; gap: 8px; }

        .btn-edit-p, .btn-del-p {
            padding: 6px 14px;
            border: none;
            border-radius: 20px;
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s, transform 0.15s;
            color: #fff;
        }

        .btn-edit-p { background: var(--info); }
        .btn-del-p  { background: var(--danger); }
        .btn-edit-p:hover, .btn-del-p:hover { filter: brightness(0.85); transform: translateY(-1px); }

        .empty-state { grid-column: 1/-1; text-align: center; padding: 80px 40px; color: var(--text-400); font-size: 16px; }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(22,8,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }

        .modal.show { display: flex; animation: fadeM 0.2s; }
        @keyframes fadeM { from { opacity:0; } to { opacity:1; } }

        .modal-content {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 36px;
            width: 90%;
            max-width: 500px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: slideM 0.25s;
        }

        .modal-content::-webkit-scrollbar { width: 4px; }
        .modal-content::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 2px; }

        @keyframes slideM { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }

        .modal-content h2 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 900;
            color: var(--text-900);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .form-group textarea { resize: vertical; min-height: 75px; }

        .prod-search-wrap input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 13px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            box-sizing: border-box;
            margin-bottom: 8px;
            transition: border-color 0.2s;
        }

        .prod-search-wrap input:focus { border-color: var(--brand); }

        .prod-lista {
            max-height: 200px;
            overflow-y: auto;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            background: var(--surface);
            margin-bottom: 10px;
            scrollbar-width: thin;
        }
        
        .prod-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .prod-item:last-child { border-bottom: none; }
        .prod-item:hover { background: rgba(232,130,26,0.06); }
        .prod-item.selected { background: rgba(232,130,26,0.11); }

        .prod-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--brand); flex-shrink: 0; cursor: pointer; }
        .prod-item img { width: 36px; height: 36px; border-radius: var(--r-sm); object-fit: cover; background: var(--surface-3); flex-shrink: 0; }

        .prod-item-nombre { font-size: 13px; font-weight: 700; color: var(--text-900); }
        .prod-item-precio { font-size: 11.5px; color: var(--brand); font-weight: 600; }

        .prod-seleccionados { display: flex; flex-wrap: wrap; gap: 6px; min-height: 30px; align-items: center; }
        .prod-sel-label { font-size: 12px; color: var(--text-400); font-style: italic; }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: rgba(232,130,26,0.12);
            border: 1px solid rgba(232,130,26,0.35);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #8a4a10;
        }

        .chip button { background: none; border: none; cursor: pointer; color: #b05010; font-size: 14px; line-height: 1; padding: 0; transition: color 0.15s; }
        .chip button:hover { color: var(--danger); }

        .preview-img { width: 100%; max-height: 170px; margin-top: 10px; border-radius: var(--r-sm); object-fit: cover; display: none; border: 1px solid var(--border); }
        .preview-img.show { display: block; }

        .modal-actions { display: flex; gap: 12px; margin-top: 24px; }

        .btn-cancel-p {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-600);
            cursor: pointer;
            transition: all 0.18s;
        }

        .btn-cancel-p:hover { background: var(--surface-3); }

        .btn-save-p {
            flex: 2;
            padding: 12px;
            background: var(--brand);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232,130,26,0.35);
            transition: all 0.2s;
        }

        .btn-save-p:hover { background: var(--brand-deep); transform: translateY(-1px); }

        body.dark-mode .promo-card { background: var(--surface); }
        body.dark-mode .promo-img-wrap { background: var(--surface-2); }
        body.dark-mode .promo-footer { background: rgba(255,255,255,0.03); }
        body.dark-mode .modal-content { background: var(--surface); }
        body.dark-mode .prod-lista { background: var(--surface-2); }
        body.dark-mode .prod-item { border-bottom-color: rgba(255,255,255,0.05); }
        body.dark-mode .topbar input { background: var(--surface); color: var(--text-900); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="promos-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Combos y Promociones</h1>
            <div class="subtitulo">Gestiona las ofertas activas del local</div>
        </div>
        <button class="btn-primary" onclick="abrirModal()">+ Añadir promoción</button>
    </div>

    <div class="topbar">
        <input type="text" id="buscar" placeholder="Buscar promoción...">
    </div>

    <div class="meta-bar">
        <span id="contador-promos">0 promociones</span>
    </div>

    <div class="promos-grid" id="listaPromos">
        <div class="empty-state">Cargando promociones...</div>
    </div>

</div>
</div>

<div id="formulario" class="modal">
    <div class="modal-content">
        <h2 id="tituloForm">Añadir Promoción</h2>

        <div class="form-group">
            <label>Nombre *</label>
            <input type="text" id="nombre" onkeypress="sololetras(event)"  placeholder="Ej: Combo Burger Deluxe">
        </div>

        <div class="form-group">
            <label>Descripción</label>
            <textarea id="descripcion" placeholder="Ej: Hamburguesa con queso, papas y bebida"></textarea>
        </div>

        <div class="form-group">
            <label>Precio *</label>
            <input type="number" id="precio" onkeypress="solonumeros(event)" placeholder="Ej: 15000" step="100" min="0">
        </div>

       <div class="form-group">
            <label>Fecha de inicio *</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio">
        </div>

        <div class="form-group">
            <label>Fecha de fin *</label>
            <input type="date" id="fecha_fin" name="fecha_fin">
        </div>

        <script>
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_inicio').min = hoy;
            document.getElementById('fecha_fin').min = hoy;

            // Ademas, cuando cambie fecha_inicio, fecha_fin no puede ser menor
            document.getElementById('fecha_inicio').addEventListener('change', function() {
                document.getElementById('fecha_fin').min = this.value;
            });
        </script>

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
            <label>Imagen</label>
            <input type="file" id="imagen" accept="image/*">
            <span id="imagen-hint" style="font-size:12px;color:var(--text-400);display:none;">
                Si no eliges una nueva imagen se conserva la actual.
            </span>
            <img id="preview" class="preview-img">
        </div>

        <div class="modal-actions">
            <button class="btn-cancel-p" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-save-p" onclick="guardarPromo()">Guardar</button>
        </div>
    </div>
</div>

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
const CTRL = '../controllers/promociones.php';
let promociones    = [];
let todosProductos = [];
let seleccionados  = new Set();
let editandoId     = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

async function init() { await Promise.all([cargarPromos(), cargarProductos()]); }

async function cargarPromos() {
    try {
        const res = await fetch(CTRL);
        promociones = await res.json();
        renderPromos();
    } catch (e) {
        document.getElementById('listaPromos').innerHTML = '<div class="empty-state">No se pudo conectar al servidor.</div>';
    }
}

async function cargarProductos() {
    try {
        const res = await fetch(`${CTRL}?accion=productos`);
        todosProductos = await res.json();
    } catch (e) { todosProductos = []; }
}

function renderPromos() {
    const lista    = document.getElementById('listaPromos');
    const busqueda = document.getElementById('buscar').value.toLowerCase().trim();
    const filtradas = promociones.filter(p =>
        (p.nombre || '').toLowerCase().includes(busqueda)
    );

    document.getElementById('contador-promos').textContent =
        filtradas.length === 1 ? '1 promoción' : `${filtradas.length} promociones`;

    if (!filtradas.length) {
        lista.innerHTML = '<div class="empty-state">No hay promociones. ¡Agrega la primera!</div>';
        return;
    }

    lista.innerHTML = filtradas.map(p => {
        const imgSrc = p.imagen || '../estilos/img/promocion.png';
        const prods  = p.productos || [];
        const tagsHtml = prods.length
            ? prods.map(pr => `<span class="tag">${pr.nombre}</span>`).join('')
            : '<span class="tag tag-empty">Sin productos</span>';

        return `
        <div class="promo-card">
            <div class="promo-img-wrap">
                <img src="${imgSrc}" alt="${p.nombre}"
                     onerror="this.onerror=null;this.src='../estilos/img/promocion.png'">
                <span class="promo-badge">Promo</span>
            </div>
            <div class="promo-body">
                <div class="promo-nombre">${p.nombre}</div>
                ${p.descripcion ? `<div class="promo-desc">${p.descripcion}</div>` : ''}
                <div class="promo-tags">${tagsHtml}</div>
            </div>
            <div class="promo-footer">
                <div class="promo-precio">$${Number(p.precio).toLocaleString('es-CO')}</div>
                <div class="promo-actions">
                    <button class="btn-edit-p" onclick="editarPromo(${p.id})">Editar</button>
                    <button class="btn-del-p" onclick="eliminarPromo(${p.id})">Eliminar</button>
                </div>
            </div>
        </div>`;
    }).join('');
}

function filtrarProductos() {
    const q = document.getElementById('prod-buscar').value.toLowerCase().trim();
    const lista = q ? todosProductos.filter(p => p.nombre.toLowerCase().includes(q)) : todosProductos;
    renderListaProductos(lista);
}

function renderListaProductos(lista) {
    const cont = document.getElementById('prod-lista');
    if (!lista.length) { cont.innerHTML = '<div style="padding:14px;text-align:center;font-size:13px;color:var(--text-400);">Sin resultados</div>'; return; }
    cont.innerHTML = lista.map(p => `
        <label class="prod-item ${seleccionados.has(p.id) ? 'selected' : ''}">
            <input type="checkbox" value="${p.id}" ${seleccionados.has(p.id) ? 'checked' : ''}
                   onchange="toggleProducto(${p.id}, this.checked)">
            <img src="${p.img || '../estilos/img/promocion.png'}" onerror="this.src='../estilos/img/promocion.png'" alt="">
            <div>
                <div class="prod-item-nombre">${p.nombre}</div>
                <div class="prod-item-precio">$${Number(p.valor).toLocaleString('es-CO')}</div>
            </div>
        </label>
    `).join('');
}

function toggleProducto(id, checked) {
    if (checked) seleccionados.add(id); else seleccionados.delete(id);
    actualizarChips(); filtrarProductos();
}

function actualizarChips() {
    const cont = document.getElementById('prod-seleccionados');
    if (!seleccionados.size) { cont.innerHTML = '<span class="prod-sel-label">Ningún producto seleccionado</span>'; return; }
    cont.innerHTML = [...seleccionados].map(id => {
        const p = todosProductos.find(x => x.id === id);
        if (!p) return '';
        return `<span class="chip">${p.nombre}<button onclick="toggleProducto(${id},false)" title="Quitar">×</button></span>`;
    }).join('');
}

function abrirModal() {
    editandoId = null; seleccionados.clear();
    document.getElementById('tituloForm').textContent = 'Añadir Promoción';
    ['nombre','descripcion','precio','prod-buscar'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('imagen').value = '';
    document.getElementById('preview').classList.remove('show');
    renderListaProductos(todosProductos);
    actualizarChips();
    document.getElementById('formulario').classList.add('show');
    document.getElementById('imagen-hint').style.display = 'none';
}

function cerrarModal() { document.getElementById('formulario').classList.remove('show'); editandoId = null; seleccionados.clear(); }

async function editarPromo(id) {
    const promo = promociones.find(p => p.id === id);
    if (!promo) return;
    editandoId = id; seleccionados.clear();
    try {
        const res = await fetch(`${CTRL}?accion=productos_promo&id=${id}`);
        const ids = await res.json();
        ids.forEach(pid => seleccionados.add(Number(pid)));
    } catch(e) {}
    document.getElementById('tituloForm').textContent = 'Editar Promoción';
    document.getElementById('nombre').value      = promo.nombre;
    document.getElementById('descripcion').value = promo.descripcion || '';
    document.getElementById('precio').value      = promo.precio;
    document.getElementById('imagen').value      = '';
    document.getElementById('prod-buscar').value = '';
    const prev = document.getElementById('preview');
    if (promo.imagen) {
        prev.src = promo.imagen;
        prev.classList.add('show');
    } else {
        prev.classList.remove('show');
    }
    renderListaProductos(todosProductos); actualizarChips();
    document.getElementById('formulario').classList.add('show');
}

async function eliminarPromo(id) {
    if (!confirm('¿Eliminar esta promoción?')) return;
    try {
        const res  = await fetch(`${CTRL}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) cargarPromos(); else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

async function guardarPromo() {
    const nombre      = document.getElementById('nombre').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const precio      = document.getElementById('precio').value;
    const fecha_inicio = document.getElementById('fecha_inicio').value;
    const fecha_fin    = document.getElementById('fecha_fin').value;
    const imagenFile  = document.getElementById('imagen').files[0];

    if (!nombre || !precio) { alert('Nombre y precio son obligatorios'); return; }

    const fd = new FormData();
    fd.append('nombre', nombre);
    fd.append('descripcion', descripcion);
    fd.append('precio', precio);
    fd.append('estado', 'Activa');
    fd.append('fecha_inicio', fecha_inicio);
    fd.append('fecha_fin', fecha_fin);
    fd.append('productos_ids', JSON.stringify([...seleccionados]));
    if (imagenFile) fd.append('imagen', imagenFile);

    if (editandoId) {
        fd.append('_method', 'PUT');
    }

    const url    = editandoId ? `${CTRL}?id=${editandoId}` : CTRL;
    const method = 'POST';

    try {
        const res  = await fetch(url, { method, body: fd });
        const data = await res.json();
        if (data.success || data.id) { cerrarModal(); cargarPromos(); }
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

document.getElementById('imagen').addEventListener('change', e => {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => { document.getElementById('preview').src = ev.target.result; document.getElementById('preview').classList.add('show'); };
    reader.readAsDataURL(file);
});

document.getElementById('buscar').addEventListener('input', renderPromos);
document.addEventListener('DOMContentLoaded', init);

function sololetras(e) {
    const char = String.fromCharCode(e.keyCode);
    if (!/^[a-zA-Z\s]+$/.test(char)) e.preventDefault();
}

function solonumeros(e) {
    const char = String.fromCharCode(e.keyCode);
    if (!/^[0-9.]$/.test(char)) e.preventDefault();
}
</script>
</body>
</html>