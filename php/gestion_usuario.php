<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="stylesheet" href="../estilos/Estilos-materiaprima.css">
    <link rel="stylesheet" href="../estilos/Accesibilidad.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">

    <style>
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid var(--cafe);
            display: inline-block;
        }
        .badge-administrador { background: #ffd700; color: #000; }
        .badge-cajero        { background: #add8e6; color: #000; }
        .badge-mesero        { background: #98fb98; color: #000; }
        .badge-cliente       { background: #e0cfff; color: #000; }

        .badge-estado-activo     { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .badge-estado-inactivo   { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .badge-estado-suspendido { background: #fff3cd; color: #856404; border-color: #ffeeba; }

        .btn-accion-u {
            padding: 6px 10px;
            border: 1px solid var(--cafe);
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
            margin: 0 2px;
        }
        .btn-editar-u  { background: #4e73df; color: white; }
        .btn-estado-u  { background: #f6c23e; color: #000; }
        .btn-eliminar-u { background: #e74a3b; color: white; }
        .btn-accion-u:hover { transform: scale(1.1); opacity: 0.9; }

        .busqueda-usuarios {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.5);
            padding: 15px;
            border-radius: 5px;
            border: 1px solid var(--cafe);
            gap: 12px;
            flex-wrap: wrap;
        }

        .busqueda-usuarios input {
            flex: 1;
            min-width: 200px;
            padding: 10px;
        }

        /* Modal editar */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.activo { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            min-width: 340px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .modal-box h3 {
            margin-bottom: 20px;
            color: #2c1810;
            font-size: 18px;
        }
        .modal-campo {
            margin-bottom: 16px;
        }
        .modal-campo label {
            display: block;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #2c1810;
        }
        .modal-campo select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 14px;
        }
        .modal-acciones {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            justify-content: flex-end;
        }
        .btn-guardar {
            background: #E8821A;
            color: #fff;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-cancelar {
            background: #ccc;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_layout.php'; ?>

    <div class="main-content">
        <div class="contenedor">
            <h2>Panel de Gestión de Usuarios</h2>

            <div class="busqueda-usuarios">
                <input type="text" id="inputBuscar" placeholder="Buscar por nombre, apellido o correo...">
                <div>
                    <strong>Total usuarios: </strong><span id="total-usuarios">0</span>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>N° Documento</th>
                        <th>Estado</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body">
                    <tr>
                        <td colspan="8" style="text-align:center;">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal editar usuario -->
    <div class="modal-overlay" id="modalEditar">
        <div class="modal-box">
            <h3>✏️ Editar Usuario</h3>
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
                <button class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                <button class="btn-guardar" onclick="guardarCambios()">Guardar</button>
            </div>
        </div>
    </div>

<div class="acc-panel" id="accPanel">
    <div class="acc-panel-title"> Accesibilidad</div>
    <div class="acc-row">
        <div class="acc-row-label">Tema</div>
        <div class="acc-row-btns">
            <button class="acc_tema" onclick="setTema('claro')">Claro</button>
            <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tamano de letra</div>
        <div class="acc-row-btns">
            <button class="acc-btn-option" onclick="cambiarFuente(-1)">A-</button>
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

<button class="acc-fab" id="accFab" onclick="togglePanel()"> <img style="width: 24px; height: 24px; filter: invert(1); pointer-events: none;"  onclick="togglePanel()" src="../estilos/img/accesibilidad.png" alt="Accesibilidad"></button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>
    <script src="../js/Accesibilidad.js"></script>
    <script>
       
        const API = '../controllers/Gestion-usuarios.php';

        let listaUsuarios = [];

        window.onload = () => fetchUsuarios();
        document.getElementById('inputBuscar').addEventListener('input', filtrarUsuarios);

        // ── Cargar usuarios ──────────────────────────────────────────
        async function fetchUsuarios() {
            try {
                const res = await fetch(API);
                if (!res.ok) throw new Error(`Error ${res.status}`);
                listaUsuarios = await res.json();
                renderizarTabla(listaUsuarios);
            } catch (err) {
                document.getElementById('tabla-usuarios-body').innerHTML =
                    `<tr><td colspan="8" style="color:red;text-align:center;">
                        Error al cargar usuarios: ${err.message}
                    </td></tr>`;
            }
        }

        // ── Renderizar tabla ─────────────────────────────────────────
        function renderizarTabla(datos) {
            const tbody = document.getElementById('tabla-usuarios-body');
            document.getElementById('total-usuarios').innerText = datos.length;

            if (datos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No se encontraron usuarios.</td></tr>';
                return;
            }

            tbody.innerHTML = datos.map(u => {
                const rol     = (u.rol     || 'Sin rol').toLowerCase();
                const estado  = (u.estado  || 'Inactivo').toLowerCase();
                const tel     = u.telefono || '—';

                return `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.nombre} ${u.apellido}</td>
                    <td>${u.correo}</td>
                    <td>${tel}</td>
                    <td>${u.Ndocumento}</td>
                    <td><span class="badge badge-estado-${estado}">${u.estado}</span></td>
                    <td><span class="badge badge-${rol}">${u.rol || 'Sin rol'}</span></td>
                    <td>
                        <button class="btn-accion-u btn-editar-u"
                            onclick="abrirModal(${u.id}, '${u.rol}', '${u.estado}')"
                            title="Editar">✏️</button>
                        <button class="btn-accion-u btn-eliminar-u"
                            onclick="eliminarUsuario(${u.id})"
                            title="Eliminar">🗑️</button>
                    </td>
                </tr>`;
            }).join('');
        }

        // ── Filtro de búsqueda ───────────────────────────────────────
        function filtrarUsuarios() {
            const term = document.getElementById('inputBuscar').value.toLowerCase();
            const filtrados = listaUsuarios.filter(u => {
                const nombre  = `${u.nombre} ${u.apellido}`.toLowerCase();
                const correo  = (u.correo || '').toLowerCase();
                return nombre.includes(term) || correo.includes(term);
            });
            renderizarTabla(filtrados);
        }

        // ── Modal editar ─────────────────────────────────────────────
        function abrirModal(id, rol, estado) {
            document.getElementById('modal-id').value    = id;
            document.getElementById('modal-rol').value   = rol;
            document.getElementById('modal-estado').value = estado;
            document.getElementById('modalEditar').classList.add('activo');
        }

        function cerrarModal() {
            document.getElementById('modalEditar').classList.remove('activo');
        }

        async function guardarCambios() {
            const id     = document.getElementById('modal-id').value;
            const rol    = document.getElementById('modal-rol').value;
            const estado = document.getElementById('modal-estado').value;

            try {
                const res = await fetch(`${API}?id=${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ rol, estado })
                });
                const data = await res.json();
                if (data.success) {
                    cerrarModal();
                    fetchUsuarios();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo actualizar'));
                }
            } catch (e) {
                alert('Error de red al guardar');
            }
        }

        async function eliminarUsuario(id) {
            if (!confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) return;
            try {
                const res = await fetch(`${API}?id=${id}`, { method: 'DELETE' });
                const data = await res.json();
                if (data.success) {
                    fetchUsuarios();
                } else {
                    alert('Error al eliminar: ' + (data.error || ''));
                }
            } catch (e) {
                alert('Error de red al eliminar');
            }
        }

        document.getElementById('modalEditar').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
</body>
</html>