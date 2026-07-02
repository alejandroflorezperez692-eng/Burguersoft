const API_BACKUP = '/burguersoft/controllers/backups.php';

function toast(msg, tipo) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.className = 'toast show ' + (tipo || 'ok');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.className = 'toast', 3200);
}

async function exportarBackup() {
    try {
        const res = await fetch(`${API_BACKUP}?accion=exportar`);
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            toast(err.error || 'Error al exportar', 'err');
            return;
        }
        const blob     = await res.blob();
        const url      = URL.createObjectURL(blob);
        const a        = document.createElement('a');
        a.href         = url;
        a.download     = 'backup_burguersoft_' + new Date().toISOString().slice(0, 10) + '.json';
        a.click();
        URL.revokeObjectURL(url);
        toast('Backup exportado correctamente');
        await cargarHistorial();
    } catch (e) {
        toast('Error de conexión al exportar', 'err');
    }
}

async function restaurarDesdeArchivo(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async function(e) {
        try {
            const contenido = JSON.parse(e.target.result);
            if (!contenido.tablas) { toast('Archivo de backup inválido', 'err'); return; }

            if (!confirm('Se restaurarán los datos del backup. Esta acción puede sobreescribir datos actuales. ¿Continuar?')) return;

            const res  = await fetch(`${API_BACKUP}?accion=restaurar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tablas: contenido.tablas })
            });
            const data = await res.json();
            if (data.success) {
                toast(data.mensaje || 'Restauración completada');
                await cargarHistorial();
            } else {
                toast(data.error || 'Error al restaurar', 'err');
            }
        } catch (err) {
            toast('Error al leer el archivo. Verifique que sea un backup válido.', 'err');
        }
    };
    reader.readAsText(file);
    event.target.value = '';
}

async function eliminarRegistro(id) {
    if (!confirm('¿Eliminar este registro del historial?')) return;
    try {
        const res  = await fetch(`${API_BACKUP}?id=${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) { toast('Registro eliminado'); await cargarHistorial(); }
        else toast(data.error || 'Error al eliminar', 'err');
    } catch (e) {
        toast('Error de conexión', 'err');
    }
}

async function cargarHistorial() {
    const tbody = document.getElementById('historial-tbody');
    if (!tbody) return;
    try {
        const res  = await fetch(`${API_BACKUP}?accion=historial`);
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-400);padding:30px;">Sin registros de backup.</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(r => `
            <tr>
                <td>${r.nombre_tabla || 'TODAS'}</td>
                <td>${r.usuario_nombre ? r.usuario_nombre + ' ' + (r.usuario_apellido || '') : '—'}</td>
                <td style="color:var(--text-400);font-size:12.5px;">${(r.fecha || '').substring(0, 16).replace('T', ' ')}</td>
                <td>
                    <button onclick="eliminarRegistro(${r.id})"
                        style="padding:4px 12px;background:var(--danger,#c8382a);color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                        Eliminar
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-400);padding:30px;">Error al cargar historial.</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarHistorial();

    const fileInput = document.getElementById('fileInput');
    if (fileInput) fileInput.addEventListener('change', restaurarDesdeArchivo);

    const btnExportar = document.getElementById('btn-exportar');
    if (btnExportar) btnExportar.addEventListener('click', exportarBackup);

    const btnImportar = document.getElementById('btn-importar');
    if (btnImportar) btnImportar.addEventListener('click', () => {
        const fi = document.getElementById('fileInput');
        if (fi) fi.click();
    });
});
