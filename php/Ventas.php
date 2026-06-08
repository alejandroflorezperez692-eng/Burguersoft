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
    <title>Burgersoft — Gestión de Ventas</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        .ventas-page { padding: 36px 40px 60px; }

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
            letter-spacing: 0.8px;
            color: var(--text-400);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-title span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--brand);
        }

        .chart-wrap {
            position: relative;
            height: 220px;
        }

        .form-panel {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 28px;
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            letter-spacing: 0.7px;
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
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-group input:focus,
        .field-group select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface);
        }

        .field-group input::placeholder { color: var(--text-400); }

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
            box-shadow: 0 4px 14px rgba(232,130,26,0.35);
            transition: all 0.2s var(--ease);
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
            transition: all 0.18s;
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
            color: rgba(255,255,255,0.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .ventas-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,0.06);
            transition: background 0.15s;
        }

        .ventas-table tbody tr:last-child { border-bottom: none; }
        .ventas-table tbody tr:hover { background: rgba(232,130,26,0.04); }

        .ventas-table td {
            padding: 14px 18px;
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

        .metodo-efectivo { background: #d5f5e3; color: #1a7a42; }
        .metodo-tarjeta  { background: #dbeafe; color: #1a3f7e; }
        .metodo-transfer { background: #f3e8ff; color: #6b21a8; }

        .valor-cell { font-weight: 700; color: var(--brand); }

        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .chart-card { background: var(--surface); }
        body.dark-mode .ventas-table { background: var(--surface); }
        body.dark-mode .ventas-table thead { background: #0e0500; }
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
            <h1>Gestión de Ventas</h1>
            <div class="subtitulo">Historial y registro de transacciones</div>
        </div>
    </div>

    <div class="stat-grid" style="margin-bottom:28px;">
        <div class="stat-card" style="--accent:#E8821A;--icon-bg:rgba(232,130,26,0.1);">
            <div class="stat-icon">💳</div>
            <div class="stat-label">Total ventas</div>
            <div class="stat-val" id="kpi-total">—</div>
            <div class="stat-sub">Registros en sistema</div>
        </div>
        <div class="stat-card" style="--accent:#2ecc71;--icon-bg:rgba(46,204,113,0.1);">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Ingresos totales</div>
            <div class="stat-val" id="kpi-ingresos">—</div>
            <div class="stat-sub">Suma acumulada</div>
        </div>
        <div class="stat-card" style="--accent:#2d89ef;--icon-bg:rgba(45,137,239,0.1);">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Ticket promedio</div>
            <div class="stat-val" id="kpi-promedio">—</div>
            <div class="stat-sub">Por transacción</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-card-title"><span></span>Ventas por método de pago</div>
            <div class="chart-wrap"><canvas id="chartMetodo"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><span></span>Distribución</div>
            <div class="chart-wrap"><canvas id="chartDonut"></canvas></div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-title">
            ✏️ <span id="form-title">Nueva Venta</span>
        </div>
        <div class="form-row-grid">
            <div class="field-group">
                <label>Cliente</label>
                <input type="text" id="cliente" placeholder="Nombre del cliente">
            </div>
            <div class="field-group">
                <label>Producto</label>
                <select id="producto">
                    <option value="">Seleccionar producto</option>
                </select>
            </div>
            <div class="field-group">
                <label>Método de pago</label>
                <select id="metodo_pago">
                    <option value="">Seleccionar método</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                    <option value="Transferencia bancaria">Transferencia bancaria</option>
                </select>
            </div>
            <div class="field-group">
                <label>Cantidad</label>
                <input type="number" id="cantidad" placeholder="1" min="1" value="1">
            </div>
            <div class="field-group">
                <label>Precio unitario</label>
                <input type="number" id="precio" placeholder="0" step="500" min="1">
            </div>
            <div class="field-group">
                <label>Fecha y hora</label>
                <input type="datetime-local" id="fecha">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn-save-venta" onclick="guardar()" id="btn-guardar-v">Guardar venta</button>
            <button class="btn-cancel-venta" onclick="limpiarCampos()">Cancelar</button>
        </div>
    </div>

    <div class="table-section">
        <div class="table-toolbar">
            <h3>Historial de ventas</h3>
            <input type="text" class="search-input" id="buscar" placeholder="Buscar por cliente, producto...">
        </div>
        <table class="ventas-table" id="tablaVentas">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Valor total</th>
                    <th>Fecha</th>
                    <th>Método</th>
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
let editIdVenta = null;
let ventasGlobal = [];
let productosGlobal = [];
let chartMetodo = null;
let chartDonut = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function fechaActual() {
    const ahora = new Date();
    ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
    return ahora.toISOString().slice(0, 16);
}

window.onload = async () => {
    document.getElementById('fecha').value = fechaActual();
    await cargarProductos();
    await listarVentas();
};

async function cargarProductos() {
    try {
        const res = await fetch(`${API}/productos.php?accion=productos`);
        productosGlobal = await res.json();
        const sel = document.getElementById('producto');
        sel.innerHTML = '<option value="">Seleccionar producto</option>';
        productosGlobal.forEach(p => {
            sel.innerHTML += `<option value="${p.id_producto}">${p.nombre_producto}</option>`;
        });
    } catch (e) {}
}

async function listarVentas() {
    try {
        const res = await fetch(`${API}/ventas.php`);
        ventasGlobal = await res.json();
        mostrarTabla(ventasGlobal);
        actualizarKPIs(ventasGlobal);
        renderCharts(ventasGlobal);
    } catch (e) {}
}

function actualizarKPIs(datos) {
    document.getElementById('kpi-total').textContent = datos.length;
    const ingresos = datos.reduce((s, v) => s + Number(v.valor_total || 0), 0);
    const prom = datos.length ? ingresos / datos.length : 0;
    document.getElementById('kpi-ingresos').textContent = '$' + ingresos.toLocaleString('es-CO');
    document.getElementById('kpi-promedio').textContent = '$' + Math.round(prom).toLocaleString('es-CO');
}

function renderCharts(datos) {
    const metodos = {};
    datos.forEach(v => {
        const m = v.metodo_pago || 'Otro';
        metodos[m] = (metodos[m] || 0) + Number(v.valor_total || 0);
    });

    const labels = Object.keys(metodos);
    const values = Object.values(metodos);
    const colors = ['#E8821A', '#2d89ef', '#9b59b6', '#2ecc71'];

    if (chartMetodo) chartMetodo.destroy();
    const ctxB = document.getElementById('chartMetodo').getContext('2d');
    chartMetodo = new Chart(ctxB, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Ingresos ($)',
                data: values,
                backgroundColor: colors.map(c => c + 'CC'),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Plus Jakarta Sans' }, color: '#6b4c38' } },
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, callback: v => '$' + v.toLocaleString('es-CO') } }
            }
        }
    });

    if (chartDonut) chartDonut.destroy();
    const ctxD = document.getElementById('chartDonut').getContext('2d');
    chartDonut = new Chart(ctxD, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors.map(c => c + 'DD'),
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11, family: 'Plus Jakarta Sans' }, color: '#6b4c38', padding: 16 }
                }
            }
        }
    });
}

function metodoBadge(m) {
    if (!m) return '<span class="metodo-chip metodo-efectivo">—</span>';
    const cls = m.includes('fect') ? 'metodo-efectivo' : m.includes('arjet') ? 'metodo-tarjeta' : 'metodo-transfer';
    return `<span class="metodo-chip ${cls}">${m}</span>`;
}

function mostrarTabla(datos) {
    const tbody = document.querySelector('#tablaVentas tbody');
    tbody.innerHTML = '';

    if (!datos.length) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--text-400);padding:40px;">No hay ventas registradas.</td></tr>`;
        return;
    }

    datos.forEach(v => {
        const fecha = v.fecha_venta ? v.fecha_venta.substring(0, 16).replace('T', ' ') : '—';
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td style="font-weight:600;">${v.nom_cliente || '—'}</td>
            <td>${v.nombre_producto || '—'}</td>
            <td style="text-align:center;">${v.cantidad_comprada}</td>
            <td class="valor-cell">$${Number(v.valor_total).toLocaleString('es-CO')}</td>
            <td style="color:var(--text-400);font-size:12.5px;">${fecha}</td>
            <td>${metodoBadge(v.metodo_pago)}</td>
            <td>
                <button class="btn-icon btn-icon-edit" onclick="prepararEdicion(${v.idventa})" title="Editar">✏️</button>
                <button class="btn-icon btn-icon-del" onclick="eliminarVenta(${v.idventa})" title="Eliminar" style="margin-left:6px;">🗑️</button>
            </td>
        `;
        tbody.appendChild(fila);
    });
}

async function guardar() {
    const cliente  = document.getElementById('cliente').value.trim();
    const idProd   = document.getElementById('producto').value;
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precio   = parseFloat(document.getElementById('precio').value) || 0;
    const fecha    = document.getElementById('fecha').value;
    const metodo   = document.getElementById('metodo_pago').value;

    if (!cliente || !idProd || cantidad <= 0 || precio <= 0 || !metodo)
        return alert('Completa todos los campos.');

    const body = { nom_cliente: cliente, id_producto: idProd, cantidad_comprada: cantidad,
                   valor_total: cantidad * precio, fecha_venta: fecha, metodo_pago: metodo };

    try {
        const url = editIdVenta ? `${API}/ventas.php?id=${editIdVenta}` : `${API}/ventas.php`;
        const res = await fetch(url, { method: editIdVenta ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (data.success || data.idventa) { limpiarCampos(); listarVentas(); }
        else alert('Error: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

async function eliminarVenta(id) {
    if (!confirm('¿Eliminar esta venta?')) return;
    try {
        const res  = await fetch(`${API}/ventas.php?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) listarVentas();
        else alert('Error al eliminar: ' + (data.error || ''));
    } catch (e) { alert('Error de conexión'); }
}

function prepararEdicion(id) {
    const v = ventasGlobal.find(x => x.idventa === id);
    if (!v) return;
    editIdVenta = id;
    document.getElementById('cliente').value     = v.nom_cliente || '';
    document.getElementById('producto').value    = v.id_producto || '';
    document.getElementById('cantidad').value    = v.cantidad_comprada;
    document.getElementById('precio').value      = v.valor_total / v.cantidad_comprada;
    document.getElementById('fecha').value       = v.fecha_venta ? v.fecha_venta.substring(0, 16) : '';
    document.getElementById('metodo_pago').value = v.metodo_pago || '';
    document.getElementById('form-title').textContent = 'Editar Venta';
    document.getElementById('btn-guardar-v').textContent = 'Actualizar venta';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function limpiarCampos() {
    editIdVenta = null;
    ['cliente', 'cantidad', 'precio', 'metodo_pago', 'producto'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = el.tagName === 'SELECT' ? '' : '';
    });
    document.getElementById('cantidad').value = '1';
    document.getElementById('fecha').value = fechaActual();
    document.getElementById('form-title').textContent = 'Nueva Venta';
    document.getElementById('btn-guardar-v').textContent = 'Guardar venta';
}

document.getElementById('buscar').addEventListener('input', e => {
    const txt = e.target.value.toLowerCase().trim();
    mostrarTabla(ventasGlobal.filter(v =>
        (v.nom_cliente || '').toLowerCase().includes(txt) ||
        (v.nombre_producto || '').toLowerCase().includes(txt) ||
        (v.metodo_pago || '').toLowerCase().includes(txt)
    ));
});
</script>
</body>
</html>