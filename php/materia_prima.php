<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirLogin();
$navActivo = 'materia';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT —  Materia Prima</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .mp-page { padding: 36px 40px 60px; }

        .form-panel {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 28px 32px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
        }

        .form-panel-title {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--border);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .field input {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface);
        }

        .field input::placeholder { color: var(--text-400); }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn-guardar-mp {
            padding: 11px 28px;
            background: var(--brand);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232,130,26,0.35);
            transition: all 0.2s var(--ease);
        }

        .btn-guardar-mp:hover { background: var(--brand-deep); transform: translateY(-2px); }

        .btn-limpiar-mp {
            padding: 11px 22px;
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

        .btn-limpiar-mp:hover { background: var(--surface-3); }

        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-toolbar h3 {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
        }

        .search-input {
            padding: 10px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 13px;
            background: var(--surface);
            color: var(--text-900);
            outline: none;
            width: 280px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .search-input::placeholder { color: var(--text-400); }

        .mp-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .mp-table thead { background: var(--text-900); }

        .mp-table thead th {
            padding: 14px 18px;
            color: rgba(255,255,255,0.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .mp-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,0.06);
            transition: background 0.15s;
        }

        .mp-table tbody tr:last-child { border-bottom: none; }
        .mp-table tbody tr:hover { background: rgba(232,130,26,0.04); }

        .mp-table td {
            padding: 14px 18px;
            font-size: 13.5px;
            color: var(--text-900);
            border: none;
            vertical-align: middle;
        }

        .valor-cell { font-weight: 700; color: var(--brand); }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .btn-edit-mp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-size: 14px;
            background: #dbeafe;
            color: #1a3f7e;
            transition: all 0.18s;
        }

        .btn-edit-mp:hover { background: var(--info); color: #fff; transform: scale(1.1); }

        .btn-del-mp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-size: 14px;
            background: #fde8e8;
            color: #922;
            transition: all 0.18s;
            margin-left: 6px;
        }

        .btn-del-mp:hover { background: var(--danger); color: #fff; transform: scale(1.1); }

        body.dark-mode .mp-table { background: var(--surface); }
        body.dark-mode .mp-table thead { background: #0e0500; }
        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .field input { background: var(--surface-2); color: var(--text-900); }
        body.dark-mode .search-input { background: var(--surface); color: var(--text-900); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="mp-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Materia Prima</h1>
            <div class="subtitulo">Inventario de insumos y materiales</div>
        </div>
    </div>

    <div class="stat-grid" style="margin-bottom:28px;">
        <div class="stat-card" style="--accent:#E8821A;">
            <div class="stat-label">Total Materia Prima</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-total">—</div>
        </div>
        <div class="stat-card" style="--accent:#2ecc71;">
            <div class="stat-label">Disponibles</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-disponibles">—</div>
        </div>
        <div class="stat-card" style="--accent:#E8821A;">
            <div class="stat-label">Valor promedio</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-promedio">—</div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-grid">
            <div class="field">
                <label>Nombre *</label>
                <input type="text" id="nombre" onkeypress="sololetras(event)" placeholder="Ej: Harina de trigo"
                required>
            </div>
            <div class="field">
                <label>Tipo *</label>
                <input type="text" id="tipo" onkeypress="sololetras(event)" placeholder="Ej: Cereal">
            </div >
            <div class="field">
                <label>Unidad de medida *</label>
                <input type="text" id="unidad_medida" onkeypress="sololetras(event)" placeholder="Ej: Kg, L, Unidades">
            </div>
            <div class="field">
                <label>Valor</label>
                <input type="number" id="valor" onkeypress="solonumeros(event)" placeholder="0">
            </div>
            <div class="field">
                <label>Cantidad *</label>
                <input type="number" id="cantidad" onkeypress="solonumeros(event)" placeholder="0">
            </div>
            <div class="field">
                <label>ID de marca</label>
                <input type="text" id="marca" onkeypress="solonumeros(event)" placeholder="Número">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn-guardar-mp" id="btn-guardar" onclick="guardar()">Guardar</button>
            <button class="btn-limpiar-mp" onclick="limpiar()">Limpiar</button>
        </div>
    </div>

    <div class="table-toolbar">
        <h3>Inventario</h3>
        <input type="text" id="buscar" class="search-input" placeholder="Buscar insumo...">
    </div>

    <table class="mp-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Unidad</th>
                <th>Valor</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Marca</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla"></tbody>
    </table>

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
const API_MP = '/burguersoft/controllers/materiaprima.php';
let editId = null;
let materiasGlobal = [];
const fmt = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }
document.getElementById('buscar').addEventListener('input', filtrar);
window.onload = listar;

async function listar() {
    try {
        const res = await fetch(API_MP);
        materiasGlobal = await res.json();
        mostrarTabla(materiasGlobal);
        actualizarKPIs(materiasGlobal);
    } catch (e) {}
}

function actualizarKPIs(datos) {
    document.getElementById('kpi-total').textContent = datos.length;
    document.getElementById('kpi-disponibles').textContent = datos.filter(m => m.cantidad > 0).length;
    const vals = datos.filter(m => parseFloat(m.valor) > 0).map(m => parseFloat(m.valor));
    const prom = vals.length ? vals.reduce((a,b) => a+b, 0) / vals.length : 0;
    document.getElementById('kpi-promedio').textContent = prom > 0 ? '$' + fmt.format(prom) : '—';
}

function estadoBadge(estado, cantidad) {
    if (!estado || estado === 'N/A') {
        const cls = cantidad > 0 ? 'background:#d5f5e3;color:#1a7a42' : 'background:#fde8e8;color:#922';
        return `<span class="estado-badge" style="${cls}">${cantidad > 0 ? 'Disponible' : 'Agotado'}</span>`;
    }
    return `<span class="estado-badge" style="background:#f5ede0;color:#6b4c38;">${estado}</span>`;
}

function mostrarTabla(datos) {
    const c = document.getElementById('cuerpoTabla');
    c.innerHTML = '';
    if (!Array.isArray(datos) || !datos.length) {
        c.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--text-400);padding:40px;">Sin registros.</td></tr>`;
        return;
    }
    datos.forEach(m => {
        c.innerHTML += `<tr>
            <td style="font-weight:600;">${m.nombre}</td>
            <td style="color:var(--text-600);">${m.tipo}</td>
            <td style="color:var(--text-400);">${m.unidad_medida}</td>
            <td class="valor-cell">$${fmt.format(parseFloat(m.valor) || 0)}</td>
            <td style="text-align:center;font-weight:600;">${m.cantidad}</td>
            <td>${estadoBadge(m.estado, m.cantidad)}</td>
            <td style="color:var(--text-400);">${m.nombre_marca || '—'}</td>
            <td>
                <button class="btn-edit-mp" onclick="editar(${m.id},'${encodeURIComponent(m.nombre)}','${encodeURIComponent(m.tipo)}','${encodeURIComponent(m.cantidad)}','${encodeURIComponent(m.valor)}','${encodeURIComponent(m.unidad_medida)}',${m.marca_id||0})" title="Editar">✏️</button>
                <button class="btn-del-mp" onclick="eliminar(${m.id})" title="Eliminar">🗑️</button>
            </td>
        </tr>`;
    });
}

function filtrar() {
    const t = document.getElementById('buscar').value.toLowerCase();
    mostrarTabla(materiasGlobal.filter(m =>
        m.nombre.toLowerCase().includes(t) || m.tipo.toLowerCase().includes(t)
    ));
}


function editar(id, nombre, tipo, stock, valor, unidad, marca) {
    editId = id;
    document.getElementById('nombre').value = decodeURIComponent(nombre);
    document.getElementById('tipo').value = decodeURIComponent(tipo);
    document.getElementById('unidad_medida').value = decodeURIComponent(unidad);
    document.getElementById('cantidad').value = decodeURIComponent(stock);
    document.getElementById('valor').value = decodeURIComponent(valor);
    document.getElementById('marca').value = marca === 0 ? '' : marca;
    document.getElementById('form-mp-title').textContent = '✏️ Editar insumo';
    document.getElementById('btn-guardar').textContent = 'Actualizar insumo';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

let _toastMpTimer = null;

function mostrarToastMp(mensaje, tipo = 'ok') {
    let toast = document.getElementById('toastMp');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toastMp';
        toast.style.cssText = `
            position: fixed; top: 20px; left: 50%;
            transform: translateX(-50%) translateY(-20px);
            padding: 14px 24px; border-radius: 10px;
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
        toast.style.color      = '#F2A93B';
        toast.style.border     = '1px solid #E8821A';
    } else {
        toast.style.background = '#2f1f1f';
        toast.style.color      = '#e63946';
        toast.style.border     = '1px solid #e63946';
    }

    toast.textContent = mensaje;
    toast.style.opacity   = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';

    if (_toastMpTimer) clearTimeout(_toastMpTimer);
    _toastMpTimer = setTimeout(() => {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(-50%) translateY(-20px)';
    }, 3500);
}

// ── Guardar / Actualizar ─────────────────────────────────
async function guardar() {
    const nombre   = document.getElementById('nombre').value.trim();
    const tipo     = document.getElementById('tipo').value.trim();
    const unidad   = document.getElementById('unidad_medida').value.trim();
    const valor    = document.getElementById('valor').value.trim();
    const cantidad = document.getElementById('cantidad').value.trim();
    const marca    = document.getElementById('marca').value.trim();

    if (!nombre || !tipo || !cantidad || !unidad) {
        mostrarToastMp('⚠ Nombre, tipo, cantidad y unidad son obligatorios.', 'error');
        return;
    }

    const data = { nombre, tipo, unidad_medida: unidad,
                   valor: valor === '' ? 0 : parseFloat(valor),
                   cantidad, marca_id: marca || null };
    const url    = editId ? `${API_MP}?id=${editId}` : API_MP;
    const method = editId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(data) });
        const resp = await res.json();
        if (res.ok) {
            const msg = editId
                ? ' Materia prima modificada correctamente.'
                : ' Materia prima agregada correctamente.';
            limpiar();
            listar();
            mostrarToastMp(msg, 'ok');
        } else {
            mostrarToastMp('⚠ Error: ' + (resp.error || 'Inténtalo de nuevo.'), 'error');
        }
    } catch (e) {
        mostrarToastMp('⚠ Error de conexión.', 'error');
    }
}

async function eliminar(id) {
    if (!confirm('¿Eliminar este insumo?')) return;
    try {
        const res = await fetch(`${API_MP}?id=${id}`, { method: 'DELETE' });
        if (res.ok) {
            listar();
            mostrarToastMp(' Materia prima eliminada correctamente.', 'ok');
        } else {
            mostrarToastMp('⚠ No se pudo eliminar el insumo.', 'error');
        }
    } catch (e) {
        mostrarToastMp('⚠ Error de conexión.', 'error');
    }
}

function limpiar() {
    editId = null;
    ['nombre','tipo','unidad_medida','valor','cantidad','marca'].forEach(id =>
        document.getElementById(id).value = '');
    document.getElementById('form-mp-title').textContent = '➕ Agregar insumo';
    document.getElementById('btn-guardar').textContent = 'Guardar insumo';
}

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