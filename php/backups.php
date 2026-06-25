<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'backups';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT —  Copias de Seguridad</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .backups-page { padding: 36px 40px 60px; }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }

        .action-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 28px 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            transition: transform 0.22s var(--ease), box-shadow 0.22s var(--ease);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--card-accent, var(--brand));
        }

        .action-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .action-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--icon-bg, rgba(232,130,26,0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .action-card h3 {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            color: var(--text-900);
            margin: 0;
        }

        .action-card p {
            font-size: 14px;
            color: var(--text-400);
            line-height: 1.5;
            margin: 0;
            flex: 1;
            font-weight: 700;
        }

        .action-btn {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s var(--ease);
            color: #fff;
            background: var(--btn-color, var(--brand));
            box-shadow: 0 4px 14px var(--btn-shadow, rgba(232,130,26,0.3));
            margin-top: auto;
        }

        .action-btn:hover { filter: brightness(0.9); transform: translateY(-1px); }

        .toast {
            position: fixed;
            top: 90px;
            right: 28px;
            padding: 14px 22px;
            border-radius: var(--r-md);
            font-size: 13px;
            font-weight: 700;
            box-shadow: var(--shadow-lg);
            z-index: 300;
            display: none;
            animation: toastIn 0.3s var(--ease);
            max-width: 360px;
        }

        @keyframes toastIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }

        .toast.ok  { background: #d5f5e3; color: #1a7a42; border-left: 4px solid #2ecc71; }
        .toast.err { background: #fde8e8; color: #922; border-left: 4px solid var(--danger); }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-header h3 {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
        }

        .historial-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: var(--r-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .historial-table thead { background: var(--text-900); }

        .historial-table thead th {
            padding: 14px 18px;
            color: rgba(255,255,255,0.80);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }

        .historial-table tbody tr {
            border-bottom: 1px solid rgba(22,8,0,0.06);
            transition: background 0.15s;
        }

        .historial-table tbody tr:last-child { border-bottom: none; }
        .historial-table tbody tr:hover { background: rgba(232,130,26,0.04); }

        .historial-table td {
            padding: 14px 18px;
            font-size: 13.5px;
            color: var(--text-900);
            border: none;
            vertical-align: middle;
        }

        .tipo-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .tipo-export  { background: #d5f5e3; color: #1a7a42; }
        .tipo-restore { background: #dbeafe; color: #1a3f7e; }
        .tipo-manual  { background: #fef3cd; color: #8a6200; }

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

        .btn-icon-del:hover { background: var(--danger); color: #fff;}

        body.dark-mode .action-card { background: var(--surface); }
        body.dark-mode .historial-table { background: var(--surface); }
        body.dark-mode .historial-table thead { background: #0e0500; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="backups-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans);">Copias de Seguridad</h1>
            <div class="subtitulo">Gestiona el respaldo completo de la base de datos</div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <div class="actions-grid">
        <div class="action-card" style="--card-accent:#E8821A;--icon-bg:rgba(232,130,26,0.1);">
            <h3>Registrar copia</h3>
            <p>Guarda un registro de copia manual en el historial del sistema.</p>
            <button class="action-btn" style="--btn-color:#E8821A;--btn-shadow:rgba(232,130,26,0.3);" onclick="crearBackup()">Crear registro</button>
        </div>

        <div class="action-card" style="--card-accent:#E8821A;--icon-bg:rgba(45,137,239,0.1);">
            <h3>Exportar base de datos</h3>
            <p>Descarga un archivo JSON con todos los datos actuales del sistema.</p>
            <button class="action-btn" style="--btn-color:#E8821A;--btn-shadow:rgba(232,130,26,0.3);" onclick="exportarBackup()">Exportar JSON</button>
        </div>

        <div class="action-card" style="--card-accent:#E8821A;--icon-bg:rgba(155,89,182,0.1);">
            <h3>Restaurar desde archivo</h3>
            <p>Carga un backup JSON previo para restaurar datos en la base de datos.</p>
            <button class="action-btn" style="--btn-color:#E8821A;--btn-shadow:rgba(232,130,26,0.3);" onclick="document.getElementById('fileInput').click()">Importar y restaurar</button>
            <input type="file" id="fileInput" accept=".json" style="display:none;" onchange="importarBackup(event)">
        </div>
    </div>

    <div class="section-header">
        <h3> Historial de copias</h3>
    </div>

    <table class="historial-table" id="tablaHistorial">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Fecha y hora</th>
                <th>Realizado por</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="5" style="text-align:center;color:var(--text-400);padding:40px;">Cargando historial...</td></tr>
        </tbody>
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
const API = '/burguersoft/controllers/backups.php';

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function mostrarToast(texto, tipo = 'ok') {
    const el = document.getElementById('toast');
    el.textContent = texto;
    el.className   = `toast ${tipo}`;
    el.style.display = 'block';
    clearTimeout(el._t);
    el._t = setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function tipoBadge(nombre) {
    if (nombre === 'EXPORTACION_COMPLETA')
        return '<span class="tipo-badge tipo-export">⬇ Exportación</span>';
    if (nombre === 'RESTAURACION')
        return '<span class="tipo-badge tipo-restore">⬆ Restauración</span>';
    return `<span class="tipo-badge tipo-manual"> ${nombre}</span>`;
}

async function cargarHistorial() {
    try {
        const res   = await fetch(`${API}?accion=historial`);
        const data  = await res.json();
        const tbody = document.querySelector('#tablaHistorial tbody');

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-400);padding:40px;">Sin registros aún.</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(r => `
            <tr>
                <td style="color:var(--text-400);font-size:12px;">#${r.id}</td>
                <td>${tipoBadge(r.nombre_tabla)}</td>
                <td style="color:var(--text-600);font-size:15px;font-weight:600;">${r.fecha ? r.fecha.replace('T',' ').substring(0,19) : '—'}</td>
                <td style="font-weight:600;">${r.usuario_nombre ? r.usuario_nombre + ' ' + r.usuario_apellido : 'Sistema'}</td>
                <td>
                    <button class="btn-icon-del" onclick="eliminarRegistro(${r.id})" title="Eliminar registro"><img src="../estilos/img/trash.png"; style="filter:invert(1);pointer-events:none;width:18px;height:18px;";></button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        mostrarToast('No se pudo cargar el historial.', 'err');
    }
}

async function crearBackup() {
    try {
        const res  = await fetch(`${API}?accion=crear`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tabla: 'TODAS' })
        });
        const data = await res.json();
        if (data.success) { mostrarToast(' Registro de copia guardado.'); cargarHistorial(); }
        else mostrarToast('Error: ' + (data.error || ''), 'err');
    } catch (e) { mostrarToast('Error de conexión.', 'err'); }
}

async function exportarBackup() {
    mostrarToast('⏳ Generando exportación...');
    try {
        const res  = await fetch(`${API}?accion=exportar`);
        if (!res.ok) { mostrarToast('Error al exportar.', 'err'); return; }
        const blob = await res.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `backup_burguersoft_${new Date().toISOString().slice(0,10)}.json`;
        a.click();
        URL.revokeObjectURL(url);
        mostrarToast(' Exportación completada. Revisa tus descargas.');
        cargarHistorial();
    } catch (e) { mostrarToast('Error al exportar: ' + e.message, 'err'); }
}

async function importarBackup(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!confirm(' ¿Restaurar la base de datos desde este archivo?\n\nSe insertarán los registros del backup.')) {
        event.target.value = ''; return;
    }

    mostrarToast(' Restaurando base de datos...');
    try {
        const texto  = await file.text();
        const backup = JSON.parse(texto);
        if (!backup.tablas) { mostrarToast('El archivo no tiene el formato esperado.', 'err'); return; }
        const res  = await fetch(`${API}?accion=restaurar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(backup)
        });
        const data = await res.json();
        if (data.success) { mostrarToast(' ' + (data.mensaje || 'Base de datos restaurada.')); cargarHistorial(); }
        else mostrarToast('Error: ' + (data.error || ''), 'err');
    } catch (e) { mostrarToast('Error al leer el archivo: ' + e.message, 'err'); }
    event.target.value = '';
}

async function eliminarRegistro(id) {
    if (!confirm('¿Eliminar este registro del historial?')) return;
    try {
        const res  = await fetch(`${API}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) { mostrarToast('Registro eliminado.'); cargarHistorial(); }
        else mostrarToast('Error: ' + (data.error || ''), 'err');
    } catch (e) { mostrarToast('Error de conexión.', 'err'); }
}

document.addEventListener('DOMContentLoaded', cargarHistorial);
</script>
</body>
</html>