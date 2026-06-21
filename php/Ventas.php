<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'ventas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BURGUERSOFT —  Gestión de Ventas</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        .ventas-page { padding: 36px 40px 60px; }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .kpi-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 22px 24px 18px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--kpi-accent, var(--brand));
        }

        .kpi-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-400);
            margin-bottom: 10px;
        }

        .kpi-val {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-900);
            line-height: 1;
            margin-bottom: 6px;
        }

        .kpi-sub { font-size: 11.5px; color: var(--text-400); }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .chart-card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-400);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-title span {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--brand);
        }

        .chart-wrap { position: relative; height: 220px; }

        .form-panel {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
        }

        .form-panel-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--border);
        }

        .form-row-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .field-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--text-600);
            margin-bottom: 7px;
        }

        .field-group input,
        .field-group select {
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
            transition: border-color .2s, box-shadow .2s;
        }

        .field-group input:focus,
        .field-group select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface);
        }

        .field-group input::placeholder { color: var(--text-400); }
        .field-group input[readonly] {
            background: var(--surface-3);
            color: var(--text-400);
            cursor: not-allowed;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn-save-venta {
            padding: 11px 28px;
            background: var(--brand);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232,130,26,.35);
            transition: all .2s var(--ease);
        }

        .btn-save-venta:hover { background: var(--brand-deep); transform: translateY(-2px); }

        .btn-cancel-venta {
            padding: 11px 22px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-600);
            cursor: pointer;
            transition: all .18s;
        }

        .btn-cancel-venta:hover { background: var(--surface-3); }

        .table-section { position: relative; }

        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-toolbar h3 {
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
            transition: border-color .2s, box-shadow .2s;
        }

        .search-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .search-input::placeholder { color: var(--text-400); }

        .day-sep td {
            background: rgba(232,130,26,.07);
            padding: 9px 18px 8px;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--brand);
            border-bottom: 2px solid rgba(232,130,26,.18);
        }

        .ventas-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .ventas-table thead { background: var(--text-900); }

        .ventas-table thead th {
            padding: 14px 18px;
            color: rgba(255,255,255,.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .ventas-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,.06);
            transition: background .15s;
        }

        .ventas-table tbody tr:last-child { border-bottom: none; }
        .ventas-table tbody tr:hover:not(.day-sep) { background: rgba(232,130,26,.04); }

        .ventas-table td {
            padding: 13px 18px;
            font-size: 13.5px;
            color: var(--text-900);
            border: none;
            vertical-align: middle;
        }

        .metodo-chip {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .mc-efectivo   { background: #d5f5e3; color: #1a7a42; }
        .mc-tarjeta    { background: #dbeafe; color: #1a3f7e; }
        .mc-transfer   { background: #f3e8ff; color: #6b21a8; }
        .mc-nequi      { background: #e0f7e9; color: #065f46; }
        .mc-daviplata  { background: #fef3c7; color: #92400e; }

        .estado-chip {
            display: inline-flex;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .ec-pagado    { background: #d5f5e3; color: #1a7a42; }
        .ec-cancelado { background: #fee2e2; color: #991b1b; }
        .ec-otro      { background: #fef3c7; color: #92400e; }

        .items-list {
            margin: 0; padding: 0;
            list-style: none;
            font-size: 12.5px;
            line-height: 1.7;
        }

        .items-list li { color: var(--text-700); }
        .items-list li strong { color: var(--text-900); font-weight: 600; }

        .promo-tag {
            display: inline-block;
            background: #fce7f3;
            color: #9d174d;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 20px;
            margin-left: 4px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .valor-cell { font-weight: 700; color: var(--brand); }
        .usuario-cell { font-weight: 600; }

        body.dark-mode .kpi-card,
        body.dark-mode .form-panel,
        body.dark-mode .chart-card,
        body.dark-mode .ventas-table { background: var(--surface); }
        body.dark-mode .ventas-table thead { background: #0e0500; }
        body.dark-mode .mc-efectivo  { background: #064e2a; color: #6ee7b7; }
        body.dark-mode .mc-tarjeta   { background: #1e3a5f; color: #93c5fd; }
        body.dark-mode .mc-transfer  { background: #3b0764; color: #d8b4fe; }
        body.dark-mode .mc-nequi     { background: #064e2a; color: #6ee7b7; }
        body.dark-mode .promo-tag    { background: #500724; color: #fbcfe8; }
        body.dark-mode .day-sep td   { background: rgba(232,130,26,.12); }
        body.dark-mode .field-group input,
        body.dark-mode .field-group select { background: var(--surface-2); color: var(--text-900); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="ventas-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans) !important;">Gestión de Ventas</h1>
            <div class="subtitulo">Historial y registro de transacciones — <span id="fecha-hoy"></span></div>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card" style="--kpi-accent:#E8821A;">
            <div class="kpi-label">Ventas hoy</div>
            <div class="kpi-val" id="kpi-hoy">—</div>
            <div class="kpi-sub">Transacciones del día</div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#E8821A;">
            <div class="kpi-label">Ingresos hoy</div>
            <div class="kpi-val" id="kpi-ingresos-hoy">—</div>
            <div class="kpi-sub">Recaudo del día</div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#E8821A;">
            <div class="kpi-label">Promociones hoy</div>
            <div class="kpi-val" id="kpi-promos-hoy">—</div>
            <div class="kpi-sub">Promociones vendidas hoy</div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#E8821A;">
            <div class="kpi-label">Total ventas</div>
            <div class="kpi-val" id="kpi-total">—</div>
            <div class="kpi-sub">Histórico acumulado</div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#E8821A ;">
            <div class="kpi-label">Ingresos totales</div>
            <div class="kpi-val" id="kpi-ingresos">—</div>
            <div class="kpi-sub">Suma histórica</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-card-title"><span></span>Ventas por día (últimos 14 días)</div>
            <div class="chart-wrap"><canvas id="chartDias"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><span></span>Distribución por método de pago</div>
            <div class="chart-wrap"><canvas id="chartDonut"></canvas></div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-title" id="form-title" style="font-family: var(--font-sans);">Nueva Venta</div>
        <div class="form-row-grid">
            <div class="field-group">
                <label>Cliente</label>
                <input type="text" id="cliente" readonly placeholder="Se carga automáticamente">
            </div>
            <div class="field-group">
                <label>Producto</label>
                <select id="producto" onchange="autocompletarPrecio()">
                    <option value="">Seleccionar producto</option>
                </select>
            </div>
            <div class="field-group">
                <label>Método de pago</label>
                <select id="metodo_pago">
                    <option value="">Seleccionar método</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Nequi">Nequi</option>
                    <option value="Daviplata">Daviplata</option>
                </select>
            </div>
            <div class="field-group">
                <label>Cantidad</label>
                <input type="number" id="cantidad" onkeypress="solonumeros(event)" placeholder="1" min="1" value="1">
            </div>
            <div class="field-group">
                <label>Precio unitario</label>
                <input type="number" id="precio" onkeypress="solonumeros(event)" placeholder="0" step="500" min="1">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn-save-venta" onclick="guardar()" id="btn-guardar-v">Guardar venta</button>
            <button class="btn-cancel-venta" onclick="limpiarCampos()">Cancelar</button>
        </div>
    </div>

    <div class="table-section">
        <div class="table-toolbar">
            <h3 style="font-family: var(--font-sans);">Historial de ventas</h3>
            <input type="text" class="search-input" id="buscar" placeholder="Buscar por usuario, método, producto...">
        </div>
        <table class="ventas-table" id="tablaVentas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Productos / Promociones</th>
                    <th>Total</th>
                    <th>Hora</th>
                    <th>Método de pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
const API = '/burguersoft/controllers';
let ventasGlobal    = [];
let detallesGlobal  = {};
let productosGlobal = [];
let editIdVenta     = null;
let chartDias       = null;
let chartDonut      = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function hoyStr() {
    const n = new Date();
    return `${n.getFullYear()}-${String(n.getMonth()+1).padStart(2,'0')}-${String(n.getDate()).padStart(2,'0')}`;
}

function soloDia(ts) { return (ts || '').substring(0, 10); }

function formatDayTitle(iso) {
    const [y, m, d] = iso.split('-').map(Number);
    const f = new Date(y, m - 1, d);
    const dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const base  = `${dias[f.getDay()]} ${d} de ${meses[m-1]} de ${y}`;
    return iso === hoyStr() ? `HOY — ${base}` : base;
}

window.onload = async () => {
    document.getElementById('fecha-hoy').textContent =
        new Date().toLocaleDateString('es-CO', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    await cargarUsuarioActual();
    await cargarProductos();
    await listarVentas();
};

async function cargarUsuarioActual() {
    try {
        const res  = await fetch(`${API}/sesion.php`);
        const data = await res.json();
        if (data.nombre) {
            document.getElementById('cliente').value =
                `${data.nombre} ${data.apellido || ''}`.trim();
        }
    } catch (e) {}
}

async function cargarProductos() {
    try {
        const res = await fetch(`${API}/productos.php?accion=productos`);
        productosGlobal = await res.json();
        const sel = document.getElementById('producto');
        sel.innerHTML = '<option value="">Seleccionar producto</option>';
        if (Array.isArray(productosGlobal)) {
            productosGlobal.forEach(p => {
                sel.innerHTML += `<option value="${p.id}" data-precio="${p.valor}">${p.nombre}</option>`;
            });
        }
    } catch (e) {}
}

function autocompletarPrecio() {
    const sel = document.getElementById('producto');
    const opt = sel.options[sel.selectedIndex];
    const val = opt ? opt.dataset.precio : '';
    if (val) document.getElementById('precio').value = parseFloat(val).toFixed(0);
}

async function listarVentas() {
    try {
        const res  = await fetch(`${API}/ventas.php`);
        const json = await res.json();
        ventasGlobal = Array.isArray(json) ? json : [];

        detallesGlobal = {};
        await Promise.all(ventasGlobal.slice(0, 60).map(async v => {
            if (!v.id) return;
            try {
                const r = await fetch(`${API}/ventas.php?id=${v.id}`);
                if (!r.ok) return;
                const d = await r.json();
                detallesGlobal[v.id] = { items: d.items || [], promos: d.promociones || [] };
            } catch {}
        }));

        actualizarKPIs(ventasGlobal);
        renderCharts(ventasGlobal);
        mostrarTabla(ventasGlobal);
    } catch (e) {
        console.error('Error:', e);
    }
}

function setText(id, valor) {
    const el = document.getElementById(id);
    if (el) el.textContent = valor;
}

function actualizarKPIs(datos) {
    const hoy         = hoyStr();
    const hoyDatos    = datos.filter(v => soloDia(v.fecha) === hoy);
    const ingresosHoy = hoyDatos.reduce((s, v) => s + Number(v.valor_total || 0), 0);
    let promosHoy     = 0;
    hoyDatos.forEach(v => { promosHoy += (detallesGlobal[v.id]?.promos || []).length; });

    setText('kpi-hoy', hoyDatos.length);
    setText('kpi-ingresos-hoy', '$' + Math.round(ingresosHoy).toLocaleString('es-CO'));
    setText('kpi-promos-hoy', promosHoy);
    setText('kpi-total', datos.length);
    setText('kpi-ingresos', '$' + Math.round(datos.reduce((s,v) => s + Number(v.valor_total||0), 0)).toLocaleString('es-CO'));
}

function renderCharts(datos) {
    const hoy     = hoyStr();
    const diasMap = {};
    for (let i = 13; i >= 0; i--) {
        const d = new Date(); d.setDate(d.getDate() - i);
        diasMap[d.toISOString().substring(0, 10)] = 0;
    }
    datos.forEach(v => { const k = soloDia(v.fecha); if (k in diasMap) diasMap[k]++; });

    const dLabels = Object.keys(diasMap).map(k => { const [,m,d] = k.split('-'); return `${d}/${m}`; });
    const dValues = Object.values(diasMap);
    const dColors = Object.keys(diasMap).map(k => k === hoy ? '#E8821A' : 'rgba(232,130,26,.42)');

    if (chartDias) chartDias.destroy();
    chartDias = new Chart(document.getElementById('chartDias').getContext('2d'), {
        type: 'bar',
        data: { labels: dLabels, datasets: [{ data: dValues, backgroundColor: dColors, borderColor: dColors, borderWidth: 2, borderRadius: 6 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: {
                    title: (i) => { const k = Object.keys(diasMap)[i[0].dataIndex]; return k === hoy ? `Hoy (${k})` : k; },
                    label: (i) => ` ${i.raw} venta${i.raw !== 1 ? 's' : ''}`
                }}
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#6b4c38' } },
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 }, callback: v => Number.isInteger(v) ? v : '' }, grid: { color: 'rgba(0,0,0,0.05)' } }
            }
        }
    });

    const metodos = {};
    datos.forEach(v => { const m = v.metodo_pago || 'Otro'; metodos[m] = (metodos[m] || 0) + 1; });
    const mColors = ['#E8821A','#2d89ef','#9b59b6','#2ecc71','#f59e0b'];

    if (chartDonut) chartDonut.destroy();
    chartDonut = new Chart(document.getElementById('chartDonut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(metodos),
            datasets: [{ data: Object.values(metodos), backgroundColor: mColors.map(c => c+'DD'), borderColor: '#fff', borderWidth: 3, hoverOffset: 8 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6b4c38', padding: 16 } },
                tooltip: { callbacks: { label: (i) => ` ${i.label}: ${i.raw} venta${i.raw !== 1 ? 's' : ''}` } }
            }
        }
    });
}

function metodoBadge(m) {
    const cls = m === 'Efectivo' ? 'mc-efectivo'
              : m === 'Tarjeta'  ? 'mc-tarjeta'
              : m === 'Transferencia' ? 'mc-transfer'
              : m === 'Nequi'    ? 'mc-nequi'
              : m === 'Daviplata'? 'mc-daviplata'
              : 'mc-transfer';
    return `<span class="metodo-chip ${cls}">${m || '—'}</span>`;
}

function estadoBadge(e) {
    const cls = e === 'Pagado' ? 'ec-pagado' : e === 'Cancelado' ? 'ec-cancelado' : 'ec-otro';
    return `<span class="estado-chip ${cls}">${e || '—'}</span>`;
}

function renderItems(ventaId) {
    const det = detallesGlobal[ventaId];
    if (!det) return '<span style="color:var(--text-400);font-size:12px;">—</span>';

    const { items, promos } = det;
    if (!items.length && !promos.length) return '<span style="color:var(--text-400);font-size:12px;">Sin detalle</span>';

    let html = '<ul class="items-list">';

    const productosPorPromo = new Set();
    promos.forEach(pr => {
        html += `<li><strong>${escHtml(pr.nombre)}</strong><span class="promo-tag">Promo</span> <span style="color:var(--text-400);">$${Number(pr.precio).toLocaleString('es-CO')}</span></li>`;
        if (pr.vp_id) productosPorPromo.add(pr.vp_id);
    });

    items.forEach(it => {
        if (it.venta_promocion_id) return;
        html += `<li><strong>${escHtml(it.nombre)}</strong> x${it.cantidad} <span style="color:var(--text-400);">$${Number(it.subtotal).toLocaleString('es-CO')}</span></li>`;
    });

    html += '</ul>';
    return html;
}

function mostrarTabla(datos) {
    const tbody = document.querySelector('#tablaVentas tbody');
    tbody.innerHTML = '';

    if (!datos.length) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--text-400);padding:40px;">No hay ventas registradas.</td></tr>`;
        return;
    }

    const porDia = {};
    datos.forEach(v => {
        const dia = soloDia(v.fecha);
        if (!porDia[dia]) porDia[dia] = [];
        porDia[dia].push(v);
    });

    Object.keys(porDia).sort((a, b) => b.localeCompare(a)).forEach(dia => {
        const grupo = porDia[dia];
        const total = grupo.reduce((s, v) => s + Number(v.valor_total || 0), 0);
        let promoCount = 0;
        grupo.forEach(v => { promoCount += (detallesGlobal[v.id]?.promos || []).length; });

        const sep = document.createElement('tr');
        sep.className = 'day-sep';
        sep.innerHTML = `<td colspan="8">
            ${formatDayTitle(dia)}
            &nbsp;·&nbsp; ${grupo.length} venta${grupo.length !== 1 ? 's' : ''}
            &nbsp;·&nbsp; $${Math.round(total).toLocaleString('es-CO')}
            ${promoCount ? `&nbsp;·&nbsp; ${promoCount} promocion${promoCount !== 1 ? 'es' : ''}` : ''}
        </td>`;
        tbody.appendChild(sep);

        grupo.forEach(v => {
            const hora   = (v.fecha || '').substring(11, 16) || '—';
            const nombre = v.nombre_usuario
                ? `${v.nombre_usuario} ${v.apellido_usuario || ''}`.trim()
                : 'Sin usuario';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="color:var(--text-400);font-size:12px;font-weight:600;">#${v.id}</td>
                <td class="usuario-cell">${escHtml(nombre)}</td>
                <td>${renderItems(v.id)}</td>
                <td class="valor-cell">$${Number(v.valor_total).toLocaleString('es-CO')}</td>
                <td style="color:var(--text-400);font-size:12.5px;">${hora}</td>
                <td>${metodoBadge(v.metodo_pago)}</td>
                <td>${estadoBadge(v.estado)}</td>
                <td>
                    <button class="btn-icon btn-icon-edit" onclick="prepararEdicion(${v.id})" title="Editar">Editar</button>
                    <button class="btn-icon btn-icon-del" onclick="eliminarVenta(${v.id})" title="Eliminar" style="margin-left:6px;">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    });
}

async function guardar() {
    const idProd   = document.getElementById('producto').value;
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const precio   = parseFloat(document.getElementById('precio').value) || 0;
    const metodo   = document.getElementById('metodo_pago').value;

    if (editIdVenta) {
        if (!metodo) return alert('Selecciona un método de pago.');
        try {
            const res  = await fetch(`${API}/ventas.php?id=${editIdVenta}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ metodo_pago: metodo })
            });
            const data = await res.json();
            if (data.success) { limpiarCampos(); await listarVentas(); }
            else alert('Error: ' + (data.error || ''));
        } catch (e) { alert('Error de conexión'); }
        return;
    }

    if (!idProd || cantidad <= 0 || precio <= 0 || !metodo)
        return alert('Completa todos los campos.');

    const body = {
        metodo_pago: metodo,
        items: [{ producto_id: idProd, cantidad: cantidad, precio_unitario: precio }],
        promociones: []
    };

    try {
        const res  = await fetch(`${API}/ventas.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.venta_id) { limpiarCampos(); await listarVentas(); }
        else alert('Error al guardar: ' + (data.error || 'Error desconocido'));
    } catch (e) { alert('Error de conexión'); }
}

function prepararEdicion(id) {
    const v = ventasGlobal.find(x => x.id === id);
    if (!v) return;
    editIdVenta = id;
    document.getElementById('metodo_pago').value         = v.metodo_pago || '';
    document.getElementById('form-title').textContent    = `Editar Venta #${id}`;
    document.getElementById('btn-guardar-v').textContent = 'Actualizar venta';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function limpiarCampos() {
    editIdVenta = null;
    document.getElementById('producto').value    = '';
    document.getElementById('cantidad').value    = '1';
    document.getElementById('precio').value      = '';
    document.getElementById('metodo_pago').value = '';
    document.getElementById('form-title').textContent    = 'Nueva Venta';
    document.getElementById('btn-guardar-v').textContent = 'Guardar venta';
}

async function eliminarVenta(id) {
    if (!confirm(`¿Eliminar la venta #${id}? Esta acción no se puede deshacer.`)) return;
    try {
        const res  = await fetch(`${API}/ventas.php?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
            ventasGlobal = ventasGlobal.filter(v => v.id !== id);
            delete detallesGlobal[id];
            actualizarKPIs(ventasGlobal);
            renderCharts(ventasGlobal);
            mostrarTabla(ventasGlobal);
        } else alert('Error al eliminar: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

document.getElementById('buscar').addEventListener('input', e => {
    const txt = e.target.value.toLowerCase().trim();
    if (!txt) { mostrarTabla(ventasGlobal); return; }
    const fil = ventasGlobal.filter(v => {
        const nombre = `${v.nombre_usuario || ''} ${v.apellido_usuario || ''}`.toLowerCase();
        const det    = detallesGlobal[v.id];
        const prods  = (det?.items  || []).map(i => i.nombre || '').join(' ').toLowerCase();
        const promos = (det?.promos || []).map(p => p.nombre || '').join(' ').toLowerCase();
        return nombre.includes(txt)
            || (v.metodo_pago || '').toLowerCase().includes(txt)
            || (v.estado      || '').toLowerCase().includes(txt)
            || prods.includes(txt)
            || promos.includes(txt)
            || String(v.id).includes(txt);
    });
    mostrarTabla(fil);
});

function solonumeros(e) {
    if (!/^[0-9.]$/.test(String.fromCharCode(e.keyCode))) e.preventDefault();
}

function escHtml(str) {
    return String(str || '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>