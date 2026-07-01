<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'marca';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT —  Gestión de Marcas</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .marcas-page { padding: 36px 40px 60px; }

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

        .topbar input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .topbar input::placeholder { color: var(--text-400); }

        .meta-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .meta-bar span {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-400);
        }

        .meta-bar::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        .marcas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 22px;
        }

        .marca-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            overflow: hidden;
            position: relative;
            transition: transform 0.25s var(--ease), box-shadow 0.25s var(--ease), border-color 0.25s;
            cursor: pointer;
        }

        .marca-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(22,8,0,0.55);
            opacity: 0;
            transition: opacity 0.25s;
            z-index: 4;
            border-radius: var(--r-lg);
        }

        .marca-card::after {
            content: 'Haga clic para ver la información';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -40%);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.1s, transform 0.25s;
            z-index: 5;
            text-shadow: 0 1px 6px rgba(0,0,0,0.5);
        }

        .marca-card:hover::before {
            opacity: 1;
        }

        .marca-card:hover::after {
            opacity: 1;
            transform: translate(-50%, -50%);
        }

        .marca-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }

        .marca-img-wrap {
            height: 160px;
            background: var(--surface-3);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            overflow: hidden;
        }

        .marca-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.35s var(--ease);
        }

        .marca-card:hover .marca-img-wrap img { transform: scale(1.06); }

        .marca-body {
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .marca-nombre {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .marca-id {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-400);
            margin-top: 2px;
        }

        .estado-dot {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .dot-activo    { background: #d5f5e3; color: #1a7a42; }
        .dot-inactivo  { background: #fde8e8; color: #922; }
        .dot-suspendido{ background: #fef3cd; color: #8a6200; }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 40px;
            color: var(--text-400);
            font-size: 16px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(22,8,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.show { display: flex; animation: fadeM 0.2s; }
        @keyframes fadeM { from { opacity:0; } to { opacity:1; } }

        .modal-box {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 36px;
            width: 90%;
            max-width: 480px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: slideM 0.25s;
        }

        @keyframes slideM { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }

        .modal-box h2 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 900;
            color: var(--text-900);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
        }

        .form-group { margin-bottom: 16px; }

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
        .form-group select {
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

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .logo-preview-wrap {
            background: var(--surface-3);
            border-radius: var(--r-sm);
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed var(--border-strong);
        }

        .logo-preview-wrap img { max-height: 90px; max-width: 100%; object-fit: contain; }

        .modal-actions { display: flex; gap: 12px; margin-top: 24px; }

        .btn-cancel-m {
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

        .btn-cancel-m:hover { background: var(--surface-3); }

        .btn-save-m {
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

        .btn-save-m:hover { background: var(--brand-deep); transform: translateY(-1px); }

        .detalle-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.82);
            backdrop-filter: blur(6px);
            z-index: 300;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .detalle-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .detalle-box {
            position: relative;
            width: 540px;
            max-width: 93vw;
            min-height: 200px;
            border-radius: 20px;
            padding: 38px 44px 34px;
            overflow: visible;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--surface);
            border: 1px solid var(--border-strong);
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
            transform: scale(0.1) translateY(40px);
            opacity: 0;
            transition: transform 0.42s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
        }

        .detalle-overlay.show .detalle-box {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .detalle-overlay.closing .detalle-box {
            transform: scale(0.1) translateY(40px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.25s ease;
        }

        .detalle-close {
            position: absolute;
            top: 14px;
            right: 16px;
            background: transparent;
            border: none;
            color: var(--text-600);
            font-size: 18px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
            z-index: 2;
        }

        .detalle-close:hover { color: var(--text-900); }

        .detalle-content {
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 2;
            max-width: 58%;
        }

        .detalle-nombre {
            font-size: 24px;
            font-weight: 900;
            color: var(--text-900);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-family: var(--font-sans);
        }

        .detalle-info {
            font-size: 13px;
            color: var(--text-600);
            line-height: 1.8;
            font-family: var(--font-sans, sans-serif);
            margin-top: 4px;
        }

        .detalle-info strong { color: var(--text-900); }

        .detalle-estado {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            width: fit-content;
        }

        .detalle-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-det-editar, .btn-det-eliminar {
            padding: 9px 22px;
            border: none;
            border-radius: 20px;
            font-family: var(--font-sans, sans-serif);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s, transform 0.15s;
            color: #fff;
        }

        .btn-det-editar   { background: var(--info, #2d89ef); }
        .btn-det-eliminar { background: var(--danger, #C8382A); }

        .btn-det-editar:hover,
        .btn-det-eliminar:hover { filter: brightness(0.85); transform: translateY(-1px); }

        .detalle-logo-bg {
            position: absolute;
            right: -40px;
            top: 50%;
            transform: translateY(-50%) scale(0) rotate(-20deg);
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: var(--surface-3);
            transition: transform 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.15s;
            z-index: 2;
        }

        .detalle-overlay.show .detalle-logo-bg {
            transform: translateY(-50%) scale(1) rotate(0deg);
        }

        .detalle-overlay.closing .detalle-logo-bg {
            transform: translateY(-50%) scale(0) rotate(20deg);
            transition: transform 0.25s ease;
        }

        .detalle-logo-flotante {
            position: absolute;
            border-radius: 50px;
            right: -24px;
            top: 50%;
            transform: translateY(-50%) scale(0) rotate(-20deg);
            width: 160px;
            height: 160px;
            object-fit: contain;
            filter: drop-shadow(0 10px 28px rgba(0,0,0,0.25));
            transition: transform 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.18s;
            z-index: 3;
        }

        .detalle-overlay.show .detalle-logo-flotante {
            transform: translateY(-50%) scale(1) rotate(0deg);
        }

        .detalle-overlay.closing .detalle-logo-flotante {
            transform: translateY(-50%) scale(0) rotate(20deg);
            transition: transform 0.25s ease;
        }

        body.dark-mode .marca-card { background: var(--surface); }
        body.dark-mode .marca-img-wrap { background: var(--surface-2); }
        body.dark-mode .modal-box { background: var(--surface); }
        body.dark-mode .detalle-box { background: var(--surface); }
        body.dark-mode .form-group input,
        body.dark-mode .form-group select { background: var(--surface-2); color: var(--text-900); }
        body.dark-mode .topbar input { background: var(--surface); color: var(--text-900); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="marcas-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Gestión de Marcas</h1>
            <div class="subtitulo">Proveedores y socios del negocio</div>
        </div>
        <button class="btn-primary" onclick="abrirModal()">+ Nueva marca</button>
    </div>

    <div class="topbar">
        <input type="text" id="buscarMarca" placeholder="Buscar marca..." oninput="filtrarMarcas()">
    </div>

    <div class="meta-bar">
        <span id="contador-marcas">0 marcas</span>
    </div>
    <div class="marcas-grid" id="listaGaseosas"></div>

</div>
</div>

<div class="modal-overlay" id="modalMarca">
    <div class="modal-box">
        <h2 id="modal-titulo">Nueva Marca</h2>

        <div class="form-group">
            <label>Nombre *</label>
            <input type="text" id="m-nombre" onkeypress="sololetras(event)" placeholder="Nombre de la marca">
        </div>

        <div class="form-group">
            <label>NIT *</label>
            <input type="text" id="m-nit" onkeypress="solonumeros(event)" placeholder="Número de identificación">
        </div>

        <div class="form-group">
            <label>URL Imagen *</label>
            <input type="text" id="m-imagen" oninput="previewLogo()" placeholder="https://...">
        </div>

        <div class="logo-preview-wrap">
            <img id="logo-preview" src="" alt="Vista previa" style="display:none;">
            <span id="logo-placeholder" style="font-size:12px;color:var(--text-400);">Vista previa del logo</span>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select id="m-estado">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="Suspendido">Suspendido</option>
            </select>
        </div>

        <div class="form-group">
            <label>Teléfono*</label>
            <input type="text" id="m-telefono" onkeypress="solonumeros(event)" placeholder="Número de contacto">
        </div>

        <div class="form-group">
            <label>Correo*</label>
            <input type="email" id="m-correo" placeholder="correo@empresa.com">
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-cancel-m" onclick="cerrarModal()">Cancelar</button>
            <button type="button" id="btn-guardar-marca" class="btn-save-m" onclick="guardarMarca()">Guardar</button>
        </div>
    </div>
</div>

<div class="detalle-overlay" id="detalleOverlay" onclick="cerrarDetalle(event)">
    <div class="detalle-box" id="detalleBox">
        <button class="detalle-close" onclick="cerrarDetalleBtn()">✕</button>
        <div class="detalle-content">
            <div class="detalle-nombre" id="det-nombre"></div>
            <div class="detalle-info" id="det-info"></div>
            <div class="detalle-estado" id="det-estado"></div>
            <div class="detalle-actions">
                <button class="btn-det-editar"   id="det-btn-editar">Editar</button>
                <button class="btn-det-eliminar" id="det-btn-eliminar">Eliminar</button>
            </div>
        </div>
        <div class="detalle-logo-bg" id="det-logo-bg"></div>
        <img class="detalle-logo-flotante" id="det-logo" src="" alt="">
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
const API_M   = '/burguersoft/controllers/marcas.php';
let allMarcas = [];
let editIdMarca = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function estadoClass(e) {
    if (e === 'Activo')   return 'dot-activo';
    if (e === 'Inactivo') return 'dot-inactivo';
    return 'dot-suspendido';
}

function renderMarcas(lista) {
    const cont = document.getElementById('listaGaseosas');
    const ctr  = document.getElementById('contador-marcas');
    ctr.textContent = lista.length === 1 ? '1 marca' : `${lista.length} marcas`;

    if (!lista.length) {
        cont.innerHTML = '<div class="empty-state">No hay marcas registradas. Agrega la primera.</div>';
        return;
    }

    cont.innerHTML = lista.map(m => `
        <div class="marca-card" onclick="abrirDetalle(${m.id})">
            <div class="marca-img-wrap">
                <img src="${m.img}" alt="${m.nombre}" onerror="this.src='../estilos/img/default.jpg'">
            </div>
            <div class="marca-body">
                <div>
                    <div class="marca-nombre">${m.nombre}</div>
                    <div class="marca-id">#${m.id}</div>
                </div>
                <span class="estado-dot ${estadoClass(m.estado)}">${m.estado}</span>
            </div>
        </div>
    `).join('');
}

async function mostrarMarcas() {
    try {
        allMarcas = await (await fetch(API_M)).json();
        renderMarcas(allMarcas);
    } catch (e) {
        document.getElementById('listaGaseosas').innerHTML = '<div class="empty-state">Error al cargar marcas.</div>';
    }
}

function filtrarMarcas() {
    const q = document.getElementById('buscarMarca').value.toLowerCase().trim();
    renderMarcas(allMarcas.filter(m => m.nombre.toLowerCase().includes(q)));
}

function abrirDetalle(id) {
    const m = allMarcas.find(x => x.id === id);
    if (!m) return;

    document.getElementById('det-nombre').textContent = m.nombre;
    document.getElementById('det-info').innerHTML =
        `<strong>NIT:</strong> ${m.nit || '—'}<br>
         <strong>Tel:</strong> ${m.telefono || '—'}<br>
         <strong>Email:</strong> ${m.correo || '—'}`;

    const estadoEl = document.getElementById('det-estado');
    estadoEl.textContent = m.estado;
    estadoEl.className   = 'detalle-estado estado-dot ' + estadoClass(m.estado);

    const logo = document.getElementById('det-logo');
    logo.src = m.img;
    logo.onerror = function(){ this.src='../estilos/img/default.jpg'; };

    document.getElementById('det-btn-editar').onclick = () => {
        cerrarDetalleBtn();
        setTimeout(() => prepararEdicion(m.id,
            encodeURIComponent(m.nombre), encodeURIComponent(m.img),
            encodeURIComponent(m.telefono||''), encodeURIComponent(m.correo||''),
            encodeURIComponent(m.nit||''), encodeURIComponent(m.estado||'')), 350);
    };

    document.getElementById('det-btn-eliminar').onclick = () => {
        cerrarDetalleBtn();
        setTimeout(() => eliminarMarca(m.id), 350);
    };

    const overlay = document.getElementById('detalleOverlay');
    overlay.classList.remove('closing');
    overlay.classList.add('show');
}

function cerrarDetalle(e) {
    if (e.target === document.getElementById('detalleOverlay')) cerrarDetalleBtn();
}

function cerrarDetalleBtn() {
    const overlay = document.getElementById('detalleOverlay');
    overlay.classList.add('closing');
    setTimeout(() => overlay.classList.remove('show', 'closing'), 380);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cerrarDetalleBtn();
});

function abrirModal() {
    editIdMarca = null;
    document.getElementById('modal-titulo').textContent = 'Nueva Marca';
    ['m-nombre','m-imagen','m-correo','m-telefono','m-nit'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('logo-preview').style.display = 'none';
    document.getElementById('logo-placeholder').style.display = 'block';
    document.getElementById('btn-guardar-marca').textContent = 'Guardar';
    document.getElementById('modalMarca').classList.add('show');
}

function cerrarModal() {
    document.getElementById('modalMarca').classList.remove('show');
    editIdMarca = null;
}

function previewLogo() {
    const url  = document.getElementById('m-imagen').value.trim();
    const prev = document.getElementById('logo-preview');
    const ph   = document.getElementById('logo-placeholder');
    if (url) { prev.src = url; prev.style.display = 'block'; ph.style.display = 'none'; }
    else { prev.style.display = 'none'; ph.style.display = 'block'; }
}

function prepararEdicion(id, nombre, imagen, tel, mail, nit, estado) {
    editIdMarca = id;
    document.getElementById('modal-titulo').textContent = 'Editar Marca';
    document.getElementById('m-nombre').value   = decodeURIComponent(nombre);
    document.getElementById('m-imagen').value   = decodeURIComponent(imagen);
    document.getElementById('m-correo').value   = decodeURIComponent(mail);
    document.getElementById('m-telefono').value = decodeURIComponent(tel);
    document.getElementById('m-nit').value      = decodeURIComponent(nit);
    document.getElementById('m-estado').value   = decodeURIComponent(estado);
    document.getElementById('btn-guardar-marca').textContent = 'Actualizar';
    previewLogo();
    document.getElementById('modalMarca').classList.add('show');
}
let _toastMarcaTimer = null;

function mostrarToastMarca(mensaje, tipo = 'ok') {
    let toast = document.getElementById('toastMarca');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toastMarca';
        toast.style.cssText = `
            position: fixed; top: 20px; left: 50%;
            transform: translateX(-50%) translateY(-20px);
            padding: 18px 28px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            opacity: 0; z-index: 99999;
            transition: opacity 0.4s ease, transform 0.4s ease;
            pointer-events: none; max-width: 90%; text-align: center;
        `;
        document.body.appendChild(toast);
    }

    if (tipo === 'ok') {
        toast.style.background = '#2f2a1f';
        toast.style.color      = '#ffffff';
        toast.style.border     = '2.5px solid #E8821A';
    } else {
        toast.style.background = '#2f1f1f';
        toast.style.color      = '#e63946';
        toast.style.border     = '1px solid #e63946';
    }

    toast.textContent = mensaje;
    toast.style.opacity   = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';

    if (_toastMarcaTimer) clearTimeout(_toastMarcaTimer);
    _toastMarcaTimer = setTimeout(() => {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(-50%) translateY(-20px)';
    }, 3500);
}


async function guardarMarca() {
    const nombre   = document.getElementById('m-nombre').value.trim();
    const imagen   = document.getElementById('m-imagen').value.trim();
    const telefono = document.getElementById('m-telefono').value.trim();
    const correo   = document.getElementById('m-correo').value.trim();
    const nit      = document.getElementById('m-nit').value.trim();
    const estado   = document.getElementById('m-estado').value.trim();

    if (!nombre || !imagen || !nit) {
        mostrarToastMarca('⚠ Nombre, imagen y NIT son obligatorios.', 'error');
        return;
    }

    const datos  = { nombre, img: imagen, telefono, correo, nit, estado };
    const url    = editIdMarca ? `${API_M}?id=${editIdMarca}` : API_M;
    const method = editIdMarca ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        if (res.ok) {
            const eraEdicion = editIdMarca !== null;
            cerrarModal();
            mostrarMarcas();
            const msg = eraEdicion
                ? ' Marca actualizada.'
                : ' Marca agregada.';
            mostrarToastMarca(msg, 'ok');
        } else {
            const msg = editIdMarca
                ? '⚠ Error al actualizar la marca.'
                : '⚠ Error al agregar la marca.';
            mostrarToastMarca(msg, 'error');
        }
    } catch (e) {
        mostrarToastMarca('⚠ Error de conexión.', 'error');
    }
}


async function eliminarMarca(id) {
    if (!confirm('¿Eliminar esta marca?')) return;

    try {
        const res = await fetch(`${API_M}?id=${id}`, { method: 'DELETE' });

        if (res.ok) {
            mostrarMarcas();
            mostrarToastMarca(' Marca eliminada.');
        } else {
            mostrarToastMarca('⚠ Error: la marca puede estar en uso.', 'error');
        }
    } catch (e) {
        mostrarToastMarca('⚠ Error de conexión.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', mostrarMarcas);
</script>
</body>
</html>