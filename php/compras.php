<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'compras';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT — Gestión de Compras</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .compras-page { padding: 36px 40px 60px; }

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

        .field input,
        .field select {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface);
        }

        .field input::placeholder { color: var(--text-400); }

        .metodo-row {
            display: grid;
            grid-template-columns: 280px;
            gap: 16px;
            margin-bottom: 24px;
        }

        .lineas-section {
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .lineas-header {
            background: var(--surface-3);
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .lineas-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }

        .linea-row {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr 1.2fr auto;
            gap: 10px;
            align-items: end;
        }

        .linea-row .field label { font-size: 10px; }

        .nuevo-insumo-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
            padding: 12px;
            background: var(--surface-3);
            border-radius: var(--r-sm);
            border: 1px dashed var(--border-strong);
        }

        .btn-quitar-linea {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: var(--r-sm);
            background: #fde8e8;
            color: #922;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.18s;
        }

        .btn-quitar-linea:hover { background: var(--danger); color: #fff; }

        .btn-agregar-linea {
            padding: 10px 18px;
            background: transparent;
            border: 1.5px dashed var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 12.5px;
            color: var(--text-600);
            cursor: pointer;
            transition: all 0.18s;
            align-self: flex-start;
        }

        .btn-agregar-linea:hover { background: var(--surface-3); border-color: var(--brand); color: var(--brand); }

        .compra-total-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            background: var(--surface-2);
        }

        .compra-total-row .label { font-size: 12.5px; color: var(--text-600); font-weight: 700; }
        .compra-total-row .valor {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--brand);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-guardar-compra {
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

        .btn-guardar-compra:hover { background: var(--brand-deep); transform: translateY(-2px); }
        .btn-guardar-compra:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

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

        .compras-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .compras-table thead { background: var(--text-900); }

        .compras-table thead th {
            padding: 14px 18px;
            color: rgba(255,255,255,0.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .compras-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,0.06);
            transition: background 0.15s;
        }

        .compras-table tbody tr:last-child { border-bottom: none; }
        .compras-table tbody tr:hover { background: rgba(232,130,26,0.04); }

        .compras-table td {
            padding: 14px 18px;
            font-size: 13.5px;
            color: var(--text-900);
            border: none;
            vertical-align: middle;
        }

        .valor-cell { font-weight: 700; color: var(--brand); }

        .metodo-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: var(--surface-3);
            color: var(--text-600);
        }

        .detalle-mini {
            font-size: 12px;
            color: var(--text-400);
        }

        .btn-icon-del {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-size: 13px;
            background: #922; 
            color: #fff; 
        }

         .btn-icon-det {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-size: 13px;
            background: rgb(19, 11, 74); 
            color: #fff; 
        }

        .btn-icon-del:hover { background: var(--danger); color: #fff;}
        .btn-icon-det:hover { background:rgb(50, 34, 153); color: #fff;}

        body.dark-mode .compras-table { background: var(--surface); }
        body.dark-mode .compras-table thead { background: #0e0500; }
        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .field input,
        body.dark-mode .field select { background: var(--surface-2); color: var(--text-900); }
        body.dark-mode .search-input { background: var(--surface); color: var(--text-900); }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(14, 5, 0, 0.65); 
            backdrop-filter: blur(4px); 
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.25s ease-out;
        }

        .contenido-modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            padding: 32px;
            position: relative;
            max-height: 85vh;
            overflow-y: auto; 
            animation: slideUp 0.3s var(--ease);
        }

        .cerrar {
            position: absolute;
            top: 20px;
            right: 24px;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-400);
            cursor: pointer;
            line-height: 1;
            transition: color 0.18s;
        }

        .cerrar:hover {
            color: var(--danger);
        }

        #modalFactura p {
            margin: 0;
            color: var(--text-600);
        }

        #modalFactura strong {
            color: var(--text-900);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="compras-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Gestión de Compras</h1>
            <div class="subtitulo">Registro de compras de materia prima a proveedores</div>
        </div>
    </div>

    <div class="stat-grid" style="margin-bottom:28px;">
        <div class="stat-card" style="--accent:#E8821A;">
            <div class="stat-label">Compras este mes</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-mes">—</div>
        </div>
        <div class="stat-card" style="--accent:#2ecc71;">
            <div class="stat-label">Invertido este mes</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-invertido">—</div>
        </div>
        <div class="stat-card" style="--accent:#E8821A;">
            <div class="stat-label">Total histórico</div>
            <div class="stat-val" style="font-family: var(--font-sans);" id="kpi-total">—</div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-title">Registrar compra</div>

        <div class="metodo-row">
            <div class="field">
                <label>Método de pago *</label>
                <select id="metodo_pago">
                    <option value="">Seleccionar...</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Nequi">Nequi</option>
                    <option value="Daviplata">Daviplata</option>
                </select>
            </div>
        </div>

        <div class="lineas-section">
            <div class="lineas-header">Insumos comprados</div>
            <div class="lineas-body" id="lineas-body"></div>
            <div class="compra-total-row">
                <span class="label">Total de la compra:</span>
                <span class="valor" id="compra-total">$0</span>
            </div>
        </div>

        <button type="button" class="btn-agregar-linea" onclick="agregarLinea()" style="margin-bottom:20px;">
            Agregar insumo
        </button>

        <div class="form-actions">
            <button class="btn-guardar-compra" id="btn-guardar-compra" onclick="guardarCompra()">Registrar compra</button>
        </div>
    </div>

    <div class="table-toolbar">
        <h3>Historial de compras</h3>
        <input type="text" id="buscar" class="search-input" placeholder="Buscar por método o insumo...">
    </div>

    <table class="compras-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Insumos</th>
                <th>Método de pago</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla"></tbody>
    </table>

</div>
</div>

<div id="modalFactura" class="modal" style="display: none;">
  <div class="contenido-modal" style="max-width: 600px; width: 90%;">
    <span class="cerrar" onclick="cerrarModal()">&times;</span>
    
    <div class="form-panel-title" style="margin-bottom: 15px;">Detalle de la Compra / Factura</div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 13px;">
        <p><strong>ID Compra:</strong> <span id="factura-id">—</span></p>
        <p><strong>Fecha:</strong> <span id="factura-fecha">—</span></p>
        <p><strong>Método de Pago:</strong> <span id="factura-metodo" class="metodo-badge">—</span></p>
    </div>

    <table class="compras-table" style="box-shadow: none; margin-bottom: 15px;">
        <thead style="background: var(--surface-3);">
            <tr>
                <th style="color: var(--text-900);">Insumo / Materia Prima</th>
                <th style="color: var(--text-900);">Proveedor</th>
                <th style="color: var(--text-900);">Cantidad</th>
                <th style="color: var(--text-900);">Precio Unit.</th>
                <th style="color: var(--text-900);">Subtotal</th>
            </tr>
        </thead>
        <tbody id="factura-lineas">
            </tbody>
    </table>

    <div class="compra-total-row" style="border-radius: var(--r-sm);">
        <span class="label">Total Facturado:</span>
        <span class="valor" id="factura-total">$0</span>
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
const API_COMPRAS = '/burguersoft/controllers/compras.php';
const API_MP      = '/burguersoft/controllers/materiaprima.php';
const fmt = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

let materiasGlobal = [];
let marcasGlobal   = [];
let comprasGlobal  = [];
let detallesGlobal = {};
let lineaSeq = 0;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

window.onload = async () => {
    await cargarMaterias();
    await cargarMarcas();
    agregarLinea();
    await listarCompras();
};

async function cargarMaterias() {
    try {
        const res = await fetch(API_MP);
        materiasGlobal = await res.json();
    } catch (e) { materiasGlobal = []; }
}

async function cargarMarcas() {
    try {
        const res = await fetch('/burguersoft/controllers/marcas.php');
        const data = await res.json();
        marcasGlobal = Array.isArray(data) ? data.filter(m => (m.estado || '').toLowerCase() !== 'inactivo') : [];
    } catch (e) { marcasGlobal = []; }
}

function agregarLinea() {
    const id = ++lineaSeq;
    const cont = document.getElementById('lineas-body');
    const wrap = document.createElement('div');
    wrap.id = `linea-${id}`;

    const opciones = materiasGlobal.map(m =>
        `<option value="${m.id}">${m.nombre} (${m.unidad_medida || ''})</option>`
    ).join('');

    const opcionesMarca = marcasGlobal.map(m =>
        `<option value="${m.id}">${m.nombre}</option>`
    ).join('');

    wrap.innerHTML = `
        <div class="linea-row">
            <div class="field">
                <label>Materia prima</label>
                <select onchange="onCambioMateria(${id})" id="linea-${id}-materia">
                    <option value="">Seleccionar...</option>
                    <option value="__nuevo__">+ Crear nuevo insumo</option>
                    ${opciones}
                </select>
            </div>
            <div class="field">
                <label>Cantidad</label>
                <input type="number" min="0" step="0.01" placeholder="0" id="linea-${id}-cantidad" oninput="recalcularTotal()">
            </div>
            <div class="field">
                <label>Precio unitario</label>
                <input type="number" min="0" step="0.01" placeholder="0" id="linea-${id}-precio" oninput="recalcularTotal()">
            </div>
            <div class="field">
                <label>Subtotal</label>
                <input type="text" disabled id="linea-${id}-subtotal" value="$0">
            </div>
            <div class="field">
                <label>Proveedor *</label>
                <select id="linea-${id}-marca">
                    <option value="">Seleccionar...</option>
                    ${opcionesMarca}
                </select>
            </div>
            <button type="button" class="btn-quitar-linea" onclick="quitarLinea(${id})" title="Quitar">×</button>
        </div>
        <div class="nuevo-insumo-row" id="linea-${id}-nuevo-wrap" style="display:none;">
            <div class="field">
                <label>Nombre del insumo nuevo</label>
                <input type="text" placeholder="Ej: Queso mozzarella" id="linea-${id}-nuevo-nombre">
            </div>
            <div class="field">
                <label>Tipo</label>
                <input type="text" placeholder="Ej: Lácteo" id="linea-${id}-nuevo-tipo">
            </div>
            <div class="field">
                <label>Unidad de medida</label>
                <input type="text" placeholder="Ej: Kg, L, Unidades" id="linea-${id}-nuevo-unidad">
            </div>
        </div>
    `;
    cont.appendChild(wrap);
}

function onCambioMateria(id) {
    const sel = document.getElementById(`linea-${id}-materia`);
    const wrapNuevo = document.getElementById(`linea-${id}-nuevo-wrap`);
    wrapNuevo.style.display = sel.value === '__nuevo__' ? 'grid' : 'none';
    recalcularTotal();
}

function quitarLinea(id) {
    const row = document.getElementById(`linea-${id}`);
    if (row) row.remove();
    if (!document.getElementById('lineas-body').children.length) agregarLinea();
    recalcularTotal();
}

function recalcularTotal() {
    let total = 0;
    document.querySelectorAll('#lineas-body > div[id^="linea-"]').forEach(row => {
        const idMatch = row.id.match(/^linea-(\d+)$/);
        if (!idMatch) return;
        const id = idMatch[1];
        const cantidad = parseFloat(document.getElementById(`linea-${id}-cantidad`)?.value) || 0;
        const precio   = parseFloat(document.getElementById(`linea-${id}-precio`)?.value) || 0;
        const subtotal = cantidad * precio;
        const campoSubtotal = document.getElementById(`linea-${id}-subtotal`);
        if (campoSubtotal) campoSubtotal.value = '$' + fmt.format(subtotal);
        total += subtotal;
    });
    document.getElementById('compra-total').textContent = '$' + fmt.format(total);
}

async function guardarCompra() {
    const metodo = document.getElementById('metodo_pago').value;
    if (!metodo) return alert('Selecciona un método de pago.');

    const items = [];
    let lineaInvalida = false;

    document.querySelectorAll('#lineas-body > div[id^="linea-"]').forEach(row => {
        const idMatch = row.id.match(/^linea-(\d+)$/);
        if (!idMatch) return;
        const id = idMatch[1];

        const materiaSel       = document.getElementById(`linea-${id}-materia`)?.value || '';
        const cantidad         = parseFloat(document.getElementById(`linea-${id}-cantidad`)?.value) || 0;
        const precio_unitario  = parseFloat(document.getElementById(`linea-${id}-precio`)?.value) || 0;
        const marca_id         = document.getElementById(`linea-${id}-marca`)?.value || '';

        if (!materiaSel && cantidad === 0 && precio_unitario === 0) return;

        if (!materiaSel || cantidad <= 0 || precio_unitario <= 0 || !marca_id) {
            lineaInvalida = true;
            return;
        }

        if (materiaSel === '__nuevo__') {
            const nombre = document.getElementById(`linea-${id}-nuevo-nombre`)?.value.trim() || '';
            const tipo   = document.getElementById(`linea-${id}-nuevo-tipo`)?.value.trim() || '';
            const unidad = document.getElementById(`linea-${id}-nuevo-unidad`)?.value.trim() || '';
            if (!nombre || !tipo) { lineaInvalida = true; return; }
            items.push({ nuevo_insumo: { nombre, tipo, unidad_medida: unidad }, cantidad, precio_unitario, marca_id });
        } else {
            items.push({ materia_prima_id: materiaSel, cantidad, precio_unitario, marca_id });
        }
    });

    if (lineaInvalida) return alert('Revisa las líneas: cada insumo necesita materia prima, cantidad, precio y proveedor. Si es un insumo nuevo, también nombre y tipo.');
    if (!items.length) return alert('Agrega al menos un insumo válido con cantidad y precio.');

    const btn = document.getElementById('btn-guardar-compra');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res  = await fetch(API_COMPRAS, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ metodo_pago: metodo, items })
        });
        const data = await res.json();
        if (data.success) {
            limpiarFormulario();
            await cargarMaterias();
            await listarCompras();
        } else {
            alert('Error al guardar la compra: ' + (data.error || 'Error desconocido'));
        }
    } catch (e) {
        alert('Error de conexión al guardar la compra.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Registrar compra';
    }
}

function limpiarFormulario() {
    document.getElementById('metodo_pago').value = '';
    document.getElementById('lineas-body').innerHTML = '';
    agregarLinea();
    recalcularTotal();
}

async function listarCompras() {
    try {
        const res  = await fetch(API_COMPRAS);
        comprasGlobal = await res.json();
        await Promise.all(comprasGlobal.slice(0, 60).map(async c => {
            try {
                const r = await fetch(`${API_COMPRAS}?id=${c.id}`);
                const d = await r.json();
                detallesGlobal[c.id] = d.items || [];
            } catch (e) { detallesGlobal[c.id] = []; }
        }));
        actualizarKPIs(comprasGlobal);
        mostrarTabla(comprasGlobal);
    } catch (e) {}
}

function actualizarKPIs(datos) {
    const hoy = new Date();
    const mesActual = hoy.getMonth();
    const anioActual = hoy.getFullYear();

    const delMes = datos.filter(c => {
        const f = new Date(c.fecha);
        return f.getMonth() === mesActual && f.getFullYear() === anioActual;
    });

    const invertidoMes = delMes.reduce((s, c) => s + Number(c.valor_total || 0), 0);
    const totalHistorico = datos.reduce((s, c) => s + Number(c.valor_total || 0), 0);

    document.getElementById('kpi-mes').textContent = delMes.length;
    document.getElementById('kpi-invertido').textContent = '$' + fmt.format(invertidoMes);
    document.getElementById('kpi-total').textContent = '$' + fmt.format(totalHistorico);
}

function resumenInsumos(id) {
    const items = detallesGlobal[id] || [];
    if (!items.length) return '<span class="detalle-mini">Sin detalle</span>';
    const nombres = items.map(i => {
        const proveedor = i.nombre_marca ? ` · ${i.nombre_marca}` : '';
        return `${i.nombre_materia} (${i.cantidad})${proveedor}`;
    }).join(', ');
    return `<span class="detalle-mini">${nombres}</span>`;
}

function mostrarTabla(datos) {
    const c = document.getElementById('cuerpoTabla');
    c.innerHTML = '';
    if (!Array.isArray(datos) || !datos.length) {
        c.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text-400);padding:40px;">Sin compras registradas.</td></tr>`;
        return;
    }
    datos.forEach(comp => {
        const fecha = comp.fecha ? new Date(comp.fecha).toLocaleDateString('es-CO') : '—';
        c.innerHTML += `<tr>
            <td style="color:var(--text-400);font-size:12px;font-weight:600;">#${comp.id}</td>
            <td>${fecha}</td>
            <td>${resumenInsumos(comp.id)}</td>
            <td><span class="metodo-badge">${comp.metodo_pago}</span></td>
            <td class="valor-cell">$${fmt.format(Number(comp.valor_total) || 0)}</td>
            <td>
                <button class="btn-icon-del" onclick="eliminarCompra(${comp.id})" title="Eliminar"><img src="../estilos/img/trash.png"; style="filter:invert(1);pointer-events:none;width:18px;height:18px;";></button>
                <button class="btn-icon-det" onclick="verDetalle(${comp.id})" title="Ver Detalles"><img src="../estilos/img/bill.png" style="filter:invert(1);pointer-events:none;width:18px;height:18px;"></button>
            </td>
        </tr>`;
    });
}

async function eliminarCompra(id) {
    if (!confirm(`¿Eliminar la compra #${id}? Esto revertirá la cantidad sumada al inventario.`)) return;
    try {
        const res  = await fetch(`${API_COMPRAS}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
            await cargarMaterias();
            await listarCompras();
        } else {
            alert('Error al eliminar: ' + (data.error || ''));
        }
    } catch (e) { alert('Error de conexión'); }
}

document.getElementById('buscar').addEventListener('input', e => {
    const txt = e.target.value.toLowerCase().trim();
    if (!txt) { mostrarTabla(comprasGlobal); return; }
    const fil = comprasGlobal.filter(c => {
        const items = detallesGlobal[c.id] || [];
        const insumos    = items.map(i => i.nombre_materia || '').join(' ').toLowerCase();
        const proveedores = items.map(i => i.nombre_marca || '').join(' ').toLowerCase();
        return (c.metodo_pago || '').toLowerCase().includes(txt)
            || insumos.includes(txt)
            || proveedores.includes(txt)
            || String(c.id).includes(txt);
    });
    mostrarTabla(fil);
});

function verDetalle(id) {
    const compra = comprasGlobal.find(c => c.id == id);
    const items = detallesGlobal[id] || [];

    if (!compra) return alert("No se encontraron los datos de esta compra.");

    document.getElementById('factura-id').textContent = `#${compra.id}`;
    document.getElementById('factura-fecha').textContent = compra.fecha ? new Date(compra.fecha).toLocaleDateString('es-CO') : '—';
    document.getElementById('factura-metodo').textContent = compra.metodo_pago;
    document.getElementById('factura-total').textContent = '$' + fmt.format(Number(compra.valor_total) || 0);

    const tablaLineas = document.getElementById('factura-lineas');
    tablaLineas.innerHTML = '';

    if (items.length === 0) {
        tablaLineas.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:15px; color:var(--text-400);">No hay detalles registrados para esta factura.</td></tr>`;
    } else {
        items.forEach(i => {
            const cantidad = Number(i.cantidad) || 0;
            const precioUnit = Number(i.precio_unitario) || 0;
            const subtotal = cantidad * precioUnit;

            tablaLineas.innerHTML += `
                <tr>
                    <td>${i.nombre_materia || '—'}</td>
                    <td>${i.nombre_marca || '—'}</td>
                    <td>${cantidad}</td>
                    <td>$${fmt.format(precioUnit)}</td>
                    <td class="valor-cell">$${fmt.format(subtotal)}</td>
                </tr>
            `;
        });
    }

    document.getElementById('modalFactura').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalFactura').style.display = 'none';
}

window.addEventListener('click', (e) => {
    const modal = document.getElementById('modalFactura');
    if (e.target === modal) {
        cerrarModal();
    }
});
</script>
</body>
</html>

