<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'marca';
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burguersoft – Gestión de Marcas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="stylesheet" href="../estilos/Estilo-gestionmarca.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="marcas-page">

    <!-- CABECERA -->
    <div class="marcas-header">
        <div>
            <h1>Gestión de Marcas</h1>
            <p class="subtitulo">Proveedores y socios del negocio</p>
        </div>
    </div>

    <!-- BARRA -->
    <div class="marcas-topbar">
        <input type="text" id="buscarMarca" class="marcas-search" placeholder="Buscar marca..." oninput="filtrarMarcas()">
        <button class="btn-nueva-marca" onclick="abrirModal()"> Nueva marca</button>
    </div>

    <div class="marcas-meta">
        <span id="contador-marcas">0 marcas</span>
    </div>

    <!-- GRID -->
    <div class="container" id="listaGaseosas"></div>

</div>
</div>

<div class="modal-overlay" id="modalMarca">
    <div class="modal-box">
        <h2 id="modal-titulo">Nueva Marca</h2>
        
        <div class="form-group">
            <label for="m-nombre">Nombre *</label>
            <input type="text" id="m-nombre">
        </div>
        
        <div class="form-group">
            <label for="m-nit">NIT *</label>
            <input type="text" id="m-nit">
        </div>

        <div class="form-group">
            <label for="m-imagen">URL Imagen *</label>
            <input type="text" id="m-imagen" oninput="previewLogo()">
        </div>
        
        <div class="logo-preview-container">
            <img id="logo-preview" src="" alt="Vista previa" style="justify-content: center; max-width: 100px; display: none;">
        </div>

        <div class="form-group">
            <label for="m-estado">Estado</label>
            <select id="m-estado">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="Suspendido">Suspendido</option>
            </select>
        </div>

        <div class="form-group">
            <label for="m-telefono">Teléfono</label>
            <input type="text" id="m-telefono">
        </div>

        <div class="form-group">
            <label for="m-correo">Correo</label>
            <input type="email" id="m-correo">
        </div>

        <div class="modal-buttons">
            <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
            <button type="button" id="btn-guardar-marca" onclick="guardarMarca()">Guardar</button>
        </div>
    </div>
</div>

<!-- MODAL -->
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

<script>
const API_M   = '/burguersoft/controllers/marcas.php';
let allMarcas = [];
let editIdMarca = null;

function renderMarcas(lista) {
    const cont = document.getElementById('listaGaseosas');
    const ctr  = document.getElementById('contador-marcas');
    ctr.textContent = lista.length === 1 ? '1 marca' : `${lista.length} marcas`;

    if (!lista.length) {
        cont.innerHTML = '<div class="empty-brands">No hay marcas registradas. ¡Agrega la primera!</div>';
        return;
    }

    cont.innerHTML = lista.map(m => `
        <div class="card">
            <img src="${m.img}" alt="${m.nombre}" onerror="this.src='../estilos/img/default.jpg'">
            <div class="info-marca"><h3>${m.nombre}</h3></div>
            <div class="info-estado" style="color: ${m.estado === 'Activo' ? 'green' : m.estado === 'Inactivo' ? 'red' : 'orange'}; border: 2px solid ${m.estado === 'Activo' ? 'green' : m.estado === 'Inactivo' ? 'red' : 'orange'};">${m.estado}</div>
            <div class="info-hover">
                <p><strong style="font-family:'Arial', 'Helvetica', sans-serif;">${m.nombre}</strong></p>
                <p>Tel: ${m.telefono || '—'}</p>
                <p>Email: ${m.correo || '—'}</p>
                <p>NIT: ${m.nit || '—'}</p>
                <div class="botones-contenedor">
                    <button class="btn-editar" onclick="prepararEdicion(${m.id},'${encodeURIComponent(m.nombre)}','${encodeURIComponent(m.img)}','${encodeURIComponent(m.telefono||'')}','${encodeURIComponent(m.correo||'')}','${encodeURIComponent(m.nit||'')}','${encodeURIComponent(m.estado||'')}')">Editar</button>
                    <button class="btn-eliminar" onclick="eliminarMarca(${m.id})">Eliminar</button>
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
        document.getElementById('listaGaseosas').innerHTML =
            '<div class="empty-brands">Error al cargar marcas.</div>';
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
    if (url) { 
        prev.src = url; 
        prev.style.display = 'block'; 
    } else { 
        prev.style.display = 'none'; 
    }
}

function prepararEdicion(id, nombre, imagen, tel, mail, nit, estado) {
    editIdMarca = id;
    document.getElementById('modal-titulo').textContent = 'Editar Marca';
    document.getElementById('m-nombre').value   = decodeURIComponent(nombre);
    document.getElementById('m-imagen').value   = decodeURIComponent(imagen);
    document.getElementById('m-correo').value   = decodeURIComponent(mail);
    document.getElementById('m-telefono').value = decodeURIComponent(tel);
    document.getElementById('m-nit').value      = decodeURIComponent(nit);
    document.getElementById('m-estado').value = decodeURIComponent(estado);
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
    else alert(editIdMarca ? 'Error al actualizar' : 'Error al agregar');
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
