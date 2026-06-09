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
    <title>Burgersoft — Gestión de Marcas</title>
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
            cursor: default;
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

        .marca-hover {
            position: absolute;
            inset: 0;
            background: rgba(22,8,0,0.88);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 22px;
            opacity: 0;
            transition: opacity 0.28s var(--ease);
            border-radius: var(--r-lg);
        }

        .marca-card:hover .marca-hover { opacity: 1; }

        .marca-hover-info {
            text-align: center;
        }

        .marca-hover-info p {
            font-size: 12.5px;
            color: rgba(240,235,227,0.75);
            line-height: 1.6;
            margin: 2px 0;
            word-break: break-all;
        }

        .marca-hover-info p strong { color: #fff; font-weight: 700; }

        .hover-actions { display: flex; gap: 10px; margin-top: 10px; }

        .btn-editar-m {
            flex: 1;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            background: var(--info);
            color: #fff;
            transition: filter 0.2s, transform 0.15s;
        }

        .btn-eliminar-m {
            flex: 1;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            background: var(--danger);
            color: #fff;
            transition: filter 0.2s, transform 0.15s;
        }

        .btn-editar-m:hover,
        .btn-eliminar-m:hover { filter: brightness(0.85); transform: translateY(-1px); }

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

        body.dark-mode .marca-card { background: var(--surface); }
        body.dark-mode .marca-img-wrap { background: var(--surface-2); }
        body.dark-mode .modal-box { background: var(--surface); }
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
            <input type="text" id="m-nombre" placeholder="Nombre de la marca">
        </div>

        <div class="form-group">
            <label>NIT *</label>
            <input type="text" id="m-nit" placeholder="Número de identificación">
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
            <label>Teléfono</label>
            <input type="text" id="m-telefono" placeholder="Número de contacto">
        </div>

        <div class="form-group">
            <label>Correo</label>
            <input type="email" id="m-correo" placeholder="correo@empresa.com">
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-cancel-m" onclick="cerrarModal()">Cancelar</button>
            <button type="button" id="btn-guardar-marca" class="btn-save-m" onclick="guardarMarca()">Guardar</button>
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
const API_M   = '/burguersoft/controllers/marcas.php';
let allMarcas = [];
let editIdMarca = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function estadoClass(e) {
    if (e === 'Activo')    return 'dot-activo';
    if (e === 'Inactivo')  return 'dot-inactivo';
    return 'dot-suspendido';
}

function renderMarcas(lista) {
    const cont = document.getElementById('listaGaseosas');
    const ctr  = document.getElementById('contador-marcas');
    ctr.textContent = lista.length === 1 ? '1 marca' : `${lista.length} marcas`;

    if (!lista.length) {
        cont.innerHTML = '<div class="empty-state">No hay marcas registradas. ¡Agrega la primera!</div>';
        return;
    }

    cont.innerHTML = lista.map(m => `
        <div class="marca-card">
            <div class="marca-img-wrap">
                <img src="${m.img}" alt="${m.nombre}" onerror="this.src='../estilos/img/default.jpg'">
            </div>
            <div class="marca-body">
                <div class="marca-nombre">${m.nombre}</div>
                <span class="estado-dot ${estadoClass(m.estado)}">${m.estado}</span>
            </div>
            <div class="marca-hover">
                <div class="marca-hover-info">
                    <p><strong>${m.nombre}</strong></p>
                    <p>NIT: ${m.nit || '—'}</p>
                    <p>Tel: ${m.telefono || '—'}</p>
                    <p>Email: ${m.correo || '—'}</p>
                </div>
                <div class="hover-actions">
                    <button class="btn-editar-m" onclick="prepararEdicion(${m.id},'${encodeURIComponent(m.nombre)}','${encodeURIComponent(m.img)}','${encodeURIComponent(m.telefono||'')}','${encodeURIComponent(m.correo||'')}','${encodeURIComponent(m.nit||'')}','${encodeURIComponent(m.estado||'')}')">Editar</button>
                    <button class="btn-eliminar-m" onclick="eliminarMarca(${m.id})">Eliminar</button>
                </div>
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

async function guardarMarca() {
    const nombre   = document.getElementById('m-nombre').value.trim();
    const imagen   = document.getElementById('m-imagen').value.trim();
    const telefono = document.getElementById('m-telefono').value.trim();
    const correo   = document.getElementById('m-correo').value.trim();
    const nit      = document.getElementById('m-nit').value.trim();
    const estado   = document.getElementById('m-estado').value.trim();
    if (!nombre || !imagen || !nit) return alert('Nombre, imagen y NIT son obligatorios.');
    const datos  = { nombre, img: imagen, telefono, correo, nit, estado };
    const url    = editIdMarca ? `${API_M}?id=${editIdMarca}` : API_M;
    const method = editIdMarca ? 'PUT' : 'POST';
    const res = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(datos) });
    if (res.ok) { cerrarModal(); mostrarMarcas(); }
    else alert(editIdMarca ? 'Error al actualizar.' : 'Error al agregar.');
}

async function eliminarMarca(id) {
    if (!confirm('¿Eliminar esta marca?')) return;
    const res = await fetch(`${API_M}?id=${id}`, { method: 'DELETE' });
    if (res.ok) mostrarMarcas();
    else alert('Error: puede estar en uso.');
}

document.addEventListener('DOMContentLoaded', mostrarMarcas);
</script>
</body>
</html>