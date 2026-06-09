<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'usuarios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft — Gestión de Usuarios</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .usuarios-page { padding: 36px 40px 60px; }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .search-input {
            flex: 1;
            max-width: 360px;
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

        .search-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .search-input::placeholder { color: var(--text-400); }

        .count-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-600);
            white-space: nowrap;
        }

        .count-chip strong { color: var(--brand); }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .users-table thead { background: var(--text-900); }

        .users-table thead th {
            padding: 14px 18px;
            color: rgba(255,255,255,0.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .users-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,0.06);
            transition: background 0.15s;
        }

        .users-table tbody tr:last-child { border-bottom: none; }
        .users-table tbody tr:hover { background: rgba(232,130,26,0.04); }

        .users-table td {
            padding: 14px 18px;
            font-size: 13.5px;
            color: var(--text-900);
            border: none;
            vertical-align: middle;
        }

        .user-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name-text { font-weight: 600; }
        .user-email { font-size: 12px; color: var(--text-400); margin-top: 1px; }

        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-administrador { background: #ffd700; color: #5a4200; }
        .badge-cajero        { background: #dbeafe; color: #1a3f7e; }
        .badge-mesero        { background: #d5f5e3; color: #1a5c32; }
        .badge-cliente       { background: #f3e8ff; color: #6b21a8; }
        .badge-estado-activo    { background: #d5f5e3; color: #1a7a42; }
        .badge-estado-inactivo  { background: #fde8e8; color: #922; }
        .badge-estado-suspendido{ background: #fef3cd; color: #8a6200; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(22,8,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.activo { display: flex; animation: fadeM 0.2s var(--ease); }

        @keyframes fadeM { from { opacity:0; } to { opacity:1; } }

        .modal-box {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
            animation: slideM 0.25s var(--ease);
        }

        @keyframes slideM { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }

        .modal-box h3 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 900;
            color: var(--text-900);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
        }

        .modal-campo { margin-bottom: 18px; }

        .modal-campo label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
            margin-bottom: 7px;
        }

        .modal-campo select {
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

        .modal-campo select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .modal-acciones { display: flex; gap: 12px; margin-top: 28px; }

        .btn-guardar-modal {
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

        .btn-guardar-modal:hover { background: var(--brand-deep); transform: translateY(-1px); }

        .btn-cancelar-modal {
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

        .btn-cancelar-modal:hover { background: var(--surface-3); }

        body.dark-mode .users-table { background: var(--surface); }
        body.dark-mode .users-table thead { background: #0e0500; }
        body.dark-mode .modal-box { background: var(--surface); }
        body.dark-mode .modal-campo select { background: var(--surface-2); color: var(--text-900); }
        body.dark-mode .search-input { background: var(--surface); color: var(--text-900); }
        body.dark-mode .count-chip { background: var(--surface); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="usuarios-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Gestión de Usuarios</h1>
            <div class="subtitulo">Administra roles, estados y accesos del sistema</div>
        </div>
    </div>

    <div class="stat-grid" style="margin-bottom:28px;">
        <div class="stat-card" style="--accent:#E8821A;">
            <div class="stat-label">Total usuarios</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="total-usuarios">—</div>
        </div>
        <div class="stat-card" style="--accent:#ffd700;">
            <div class="stat-label">Administradores</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="count-admin">—</div>
        </div>
        <div class="stat-card" style="--accent:#2ecc71;">
            <div class="stat-label">Activos</div>
            <div class="stat-val" style="font-family: var(--font-sans); " id="count-activos">—</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-left">
            <input type="text" id="inputBuscar" class="search-input" placeholder="Buscar por nombre, apellido o correo...">
            <div class="count-chip">Total: <strong id="count-mostrados">0</strong></div>
        </div>
    </div>

    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Teléfono</th>
                <th>N° Documento</th>
                <th>Estado</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-usuarios-body">
            <tr><td colspan="7" style="text-align:center;color:var(--text-400);padding:40px;">Cargando...</td></tr>
        </tbody>
    </table>

</div>
</div>

<div class="modal-overlay" id="modalEditar">
    <div class="modal-box">
        <h3> Editar Usuario</h3>
        <input type="hidden" id="modal-id">
        <div class="modal-campo">
            <label>Rol del sistema</label>
            <select id="modal-rol">
                <option value="Administrador">Administrador</option>
                <option value="Cajero">Cajero</option>
                <option value="Mesero">Mesero</option>
                <option value="Cliente">Cliente</option>
            </select>
        </div>
        <div class="modal-campo">
            <label>Estado</label>
            <select id="modal-estado">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="Suspendido">Suspendido</option>
            </select>
        </div>
        <div class="modal-acciones">
            <button class="btn-cancelar-modal" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-guardar-modal" onclick="guardarCambios()">Guardar cambios</button>
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
const API = '../controllers/Gestion-usuarios.php';
let listaUsuarios = [];

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

window.onload = () => fetchUsuarios();
document.getElementById('inputBuscar').addEventListener('input', filtrarUsuarios);

async function fetchUsuarios() {
    try {
        const res = await fetch(API);
        if (!res.ok) throw new Error();
        listaUsuarios = await res.json();
        renderizarTabla(listaUsuarios);
        actualizarKPIs(listaUsuarios);
    } catch (err) {
        document.getElementById('tabla-usuarios-body').innerHTML =
            `<tr><td colspan="7" style="text-align:center;color:var(--danger);padding:30px;">Error al cargar usuarios.</td></tr>`;
    }
}

function actualizarKPIs(datos) {
    document.getElementById('total-usuarios').textContent = datos.length;
    document.getElementById('count-admin').textContent = datos.filter(u => u.rol === 'Administrador').length;
    document.getElementById('count-activos').textContent = datos.filter(u => u.estado === 'Activo').length;
}

function iniciales(nombre, apellido) {
    return ((nombre || '').charAt(0) + (apellido || '').charAt(0)).toUpperCase();
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('tabla-usuarios-body');
    document.getElementById('count-mostrados').textContent = datos.length;

    if (!datos.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-400);padding:40px;">No se encontraron usuarios.</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(u => {
        const rol    = (u.rol || 'sin-rol').toLowerCase();
        const estado = (u.estado || 'inactivo').toLowerCase().replace(' ', '-');
        return `
        <tr>
            <td style="color:var(--text-400);font-size:12px;">#${u.id}</td>
            <td>
                <div class="user-name-cell">
                    <div class="user-avatar">${iniciales(u.nombre, u.apellido)}</div>
                    <div>
                        <div class="user-name-text">${u.nombre} ${u.apellido}</div>
                        <div class="user-email">${u.correo}</div>
                    </div>
                </div>
            </td>
            <td style="color:var(--text-600);">${u.telefono || '—'}</td>
            <td style="color:var(--text-600);">${u.Ndocumento || '—'}</td>
            <td><span class="badge badge-estado-${estado}">${u.estado || 'Inactivo'}</span></td>
            <td><span class="badge badge-${rol}">${u.rol || 'Sin rol'}</span></td>
            <td>
                <button class="btn-icon btn-icon-edit" onclick="abrirModal(${u.id},'${u.rol}','${u.estado}')" title="Editar">✏️</button>
                <button class="btn-icon btn-icon-del" onclick="eliminarUsuario(${u.id})" title="Eliminar" style="margin-left:6px;">🗑️</button>
            </td>
        </tr>`;
    }).join('');
}

function filtrarUsuarios() {
    const term = document.getElementById('inputBuscar').value.toLowerCase();
    renderizarTabla(listaUsuarios.filter(u => {
        const nombre = `${u.nombre} ${u.apellido}`.toLowerCase();
        const correo = (u.correo || '').toLowerCase();
        return nombre.includes(term) || correo.includes(term);
    }));
}

function abrirModal(id, rol, estado) {
    document.getElementById('modal-id').value     = id;
    document.getElementById('modal-rol').value    = rol;
    document.getElementById('modal-estado').value = estado;
    document.getElementById('modalEditar').classList.add('activo');
}

function cerrarModal() { document.getElementById('modalEditar').classList.remove('activo'); }

async function guardarCambios() {
    const id     = document.getElementById('modal-id').value;
    const rol    = document.getElementById('modal-rol').value;
    const estado = document.getElementById('modal-estado').value;
    try {
        const res  = await fetch(`${API}?id=${id}`, { method: 'PUT',
            headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ rol, estado }) });
        const data = await res.json();
        if (data.success) { cerrarModal(); fetchUsuarios(); }
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de red'); }
}

async function eliminarUsuario(id) {
    if (!confirm('¿Eliminar este usuario permanentemente?')) return;
    try {
        const res  = await fetch(`${API}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) fetchUsuarios();
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de red'); }
}

document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
</body>
</html>