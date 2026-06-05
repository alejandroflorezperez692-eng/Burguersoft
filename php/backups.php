<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft - Copias de Seguridad</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="stylesheet" href="../estilos/Estilos-materiaprima.css">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .backup-container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .backup-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 32px; }
        .backup-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 22px; text-align: center; }
        .backup-card h3 { font-size: 1rem; color: #555; margin-bottom: 8px; }
        .backup-card .icon { font-size: 2.2rem; margin-bottom: 10px; }
        .btn-backup { display: inline-block; padding: 10px 22px; border: none; border-radius: 8px; cursor: pointer; font-size: .95rem; font-weight: 600; transition: .2s; margin-top: 8px; width: 100%; }
        .btn-crear   { background: #2ecc71; color: #fff; }
        .btn-exportar{ background: #3498db; color: #fff; }
        .btn-importar{ background: #9b59b6; color: #fff; }
        .btn-danger  { background: #e74c3c; color: #fff; }
        .btn-backup:hover { opacity: .85; }
        .tabla-historial { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .tabla-historial th { background: #2c3e50; color: #fff; padding: 12px 16px; text-align: left; font-size: .9rem; }
        .tabla-historial td { padding: 11px 16px; border-bottom: 1px solid #f0f0f0; font-size: .88rem; }
        .tabla-historial tr:last-child td { border-bottom: none; }
        .tabla-historial tr:hover td { background: #f8f9fa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
        .badge-export  { background: #d5f5e3; color: #1a8a45; }
        .badge-restore { background: #d6eaf8; color: #1a5276; }
        .badge-manual  { background: #fdebd0; color: #935116; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #2c3e50; margin: 24px 0 12px; }
        #msg-backup { padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; display: none; font-weight: 600; }
        .msg-ok  { background: #d5f5e3; color: #1a8a45; }
        .msg-err { background: #fde8e8; color: #c0392b; }
        .loading { opacity: .6; pointer-events: none; }
        input[type="file"] { display: none; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/admin_layout.php'; ?>

    <div class="main-content">
        <div class="backup-container">
            <h1>💾 Copias de Seguridad</h1>
            <p style="color:#666;margin-bottom:20px;">Gestiona el respaldo completo de la base de datos de Burguersoft.</p>

            <div id="msg-backup"></div>

            <!-- Acciones principales -->
            <div class="backup-cards">
                <div class="backup-card">
                    <div class="icon">📸</div>
                    <h3>Registrar copia</h3>
                    <p style="font-size:.82rem;color:#888;">Guarda un registro de copia en el historial.</p>
                    <button class="btn-backup btn-crear" onclick="crearBackup()">Crear registro</button>
                </div>
                <div class="backup-card">
                    <div class="icon">⬇️</div>
                    <h3>Exportar base de datos</h3>
                    <p style="font-size:.82rem;color:#888;">Descarga un archivo JSON con todos los datos actuales.</p>
                    <button class="btn-backup btn-exportar" onclick="exportarBackup()">Exportar JSON</button>
                </div>
                <div class="backup-card">
                    <div class="icon">⬆️</div>
                    <h3>Restaurar desde archivo</h3>
                    <p style="font-size:.82rem;color:#888;">Carga un backup JSON previo a la base de datos.</p>
                    <button class="btn-backup btn-importar" onclick="document.getElementById('fileInput').click()">Importar y restaurar</button>
                    <input type="file" id="fileInput" accept=".json" onchange="importarBackup(event)">
                </div>
            </div>

            <!-- Historial -->
            <div class="section-title">📋 Historial de copias</div>
            <table class="tabla-historial" id="tablaHistorial">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo / Tabla</th>
                        <th>Fecha y hora</th>
                        <th>Realizado por</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">Cargando historial...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="widget-accesibilidad">
        <button id="boton-accesibilidad" title="Opciones de Accesibilidad"></button>
        <div id="menu-accesibilidad">
            <h4>Panel de Accesibilidad</h4>
            <div class="opcion-acc">
                <label>Tamaño de letra: <span id="val-size">100%</span></label>
                <input type="range" id="slider-size" min="80" max="150" value="100">
            </div>
            <div class="opcion-acc">
                <label>Tipo de fuente:</label>
                <select id="select-font">
                    <option value="Arial, sans-serif">Predeterminada</option>
                    <option value="'Courier New', monospace">Monoespaciado</option>
                    <option value="'Georgia', serif">Elegante (Serif)</option>
                    <option value="'OpenDyslexic', sans-serif">Lectura Fácil</option>
                </select>
            </div>
            <div class="opcion-acc">
                <button id="btn-contraste" onclick="toggleContrast()">Activar Modo Oscuro</button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <script src="../js/accesibilidad.js"></script>

<script>
    const API = '/burguersoft/controllers/backups.php';

    // ── Mensaje de estado ─────────────────────────────────────
    function mostrarMsg(texto, tipo = 'ok') {
        const el = document.getElementById('msg-backup');
        el.textContent = texto;
        el.className   = tipo === 'ok' ? 'msg-ok' : 'msg-err';
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

    // ── Tipo de backup → badge HTML ───────────────────────────
    function badgeTipo(nombre) {
        if (nombre === 'EXPORTACION_COMPLETA')
            return '<span class="badge badge-export">Exportación</span>';
        if (nombre === 'RESTAURACION')
            return '<span class="badge badge-restore">Restauración</span>';
        return `<span class="badge badge-manual">${nombre}</span>`;
    }

    // ── Cargar historial desde el servidor ────────────────────
    async function cargarHistorial() {
        try {
            const res  = await fetch(`${API}?accion=historial`);
            const data = await res.json();
            const tbody = document.querySelector('#tablaHistorial tbody');

            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">Sin registros aún.</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(r => `
                <tr>
                    <td>${r.id}</td>
                    <td>${badgeTipo(r.nombre_tabla)}</td>
                    <td>${r.fecha ? r.fecha.replace('T',' ').substring(0,19) : '-'}</td>
                    <td>${r.usuario_nombre ? r.usuario_nombre + ' ' + r.usuario_apellido : 'Sistema'}</td>
                    <td>
                        <button onclick="eliminarRegistro(${r.id})" style="background:#e74c3c;color:#fff;border:none;border-radius:6px;padding:5px 12px;cursor:pointer;font-size:.82rem;">🗑️ Eliminar</button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            mostrarMsg('No se pudo cargar el historial.', 'err');
        }
    }

    // ── Crear registro de copia manual ────────────────────────
    async function crearBackup() {
        try {
            const res  = await fetch(`${API}?accion=crear`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tabla: 'TODAS' })
            });
            const data = await res.json();
            if (data.success) {
                mostrarMsg('✅ Registro de copia guardado correctamente.');
                cargarHistorial();
            } else {
                mostrarMsg('Error: ' + (data.error || ''), 'err');
            }
        } catch (e) {
            mostrarMsg('Error de conexión.', 'err');
        }
    }

    // ── Exportar → descarga JSON ──────────────────────────────
    async function exportarBackup() {
        mostrarMsg('⏳ Generando exportación, por favor espera...');
        try {
            const res  = await fetch(`${API}?accion=exportar`);
            if (!res.ok) { mostrarMsg('Error al exportar.', 'err'); return; }
            const blob = await res.blob();
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = `backup_burguersoft_${new Date().toISOString().slice(0,10)}.json`;
            a.click();
            URL.revokeObjectURL(url);
            mostrarMsg('✅ Exportación completada. Revisa tus descargas.');
            cargarHistorial();
        } catch (e) {
            mostrarMsg('Error al exportar: ' + e.message, 'err');
        }
    }

    // ── Importar y restaurar desde archivo JSON ───────────────
    async function importarBackup(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!confirm('⚠️ ¿Estás seguro de restaurar la base de datos desde este archivo?\n\nSe INSERTARÁN los registros del backup (no borra los actuales).')) {
            event.target.value = '';
            return;
        }

        mostrarMsg('⏳ Restaurando base de datos...');
        try {
            const texto   = await file.text();
            const backup  = JSON.parse(texto);

            if (!backup.tablas) {
                mostrarMsg('El archivo no tiene el formato esperado.', 'err');
                return;
            }

            const res  = await fetch(`${API}?accion=restaurar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(backup)
            });
            const data = await res.json();
            if (data.success) {
                mostrarMsg('✅ ' + (data.mensaje || 'Base de datos restaurada correctamente.'));
                cargarHistorial();
            } else {
                mostrarMsg('Error en restauración: ' + (data.error || ''), 'err');
            }
        } catch (e) {
            mostrarMsg('Error al leer el archivo: ' + e.message, 'err');
        }
        event.target.value = '';
    }

    // ── Eliminar registro del historial ───────────────────────
    async function eliminarRegistro(id) {
        if (!confirm('¿Eliminar este registro del historial?')) return;
        try {
            const res  = await fetch(`${API}?id=${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) {
                mostrarMsg('Registro eliminado.');
                cargarHistorial();
            } else {
                mostrarMsg('Error: ' + (data.error || ''), 'err');
            }
        } catch (e) {
            mostrarMsg('Error de conexión.', 'err');
        }
    }

    // ── Inicializar ───────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', cargarHistorial);

    (function() {
        const u  = JSON.parse(localStorage.getItem('usuarioActual') || 'null');
        if (!u) return;
        const el = document.getElementById('nombre-sidebar');
        if (el) el.textContent = (u.nombre || '') + ' ' + (u.apellido || '');
    })();
</script>
</body>
</html>
