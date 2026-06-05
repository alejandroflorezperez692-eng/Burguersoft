<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirLogin();
$navActivo = 'materia';
?><!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft - Materia Prima</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="stylesheet" href="../estilos/Estilos-materiaprima.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
</head><body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>
<div class="main-content">
    <div class="contenedor">
        <div class="marcas-header">
        <div>
            <h1>Gestión de Materia Prima</h1>
            <p class="subtitulo">Materia prima disponible en el inventario</p>
        </div>
        </div>
        <div class="formulario">
            <input type="text"   id="nombre"   placeholder="Nombre de la materia">
            <input type="text"   id="tipo"     placeholder="Tipo">
            <input type="text"   id="unidad_medida" placeholder="Ej: Kg, Unidades, g, L, etc.">
            <input type="number" id="valor"    placeholder="Valor">
            <input type="number" id="cantidad" placeholder="Cantidad disponible">
            <input type="text"   id="marca"    placeholder="ID de Marca (número)">
            <button id="btn-guardar">Guardar</button>
        </div>
        <div class="busqueda"><input type="text" id="buscar" placeholder="Buscar materia..."></div>
        <table>
            <thead><tr><th>Nombre</th><th>Tipo</th><th>Unidad de Medida</th><th>Valor</th><th>Cantidad</th><th>Estado</th><th>Marca</th><th>Acciones</th></tr></thead>
            <tbody id="cuerpoTabla"></tbody>
        </table>
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
<link rel="stylesheet" href="../estilos/Accesibilidad.css">
<script>
const API_MP = '/burguersoft/controllers/materiaprima.php';
let editId = null, materiasGlobal = [];
document.getElementById('btn-guardar').addEventListener('click', guardar);
document.getElementById('buscar').addEventListener('input', filtrar);
window.onload = listar;

async function listar() {
    try {
        const res = await fetch(API_MP);
        materiasGlobal = await res.json();
        mostrarTabla(materiasGlobal);
    } catch (error) {
        console.error("Error al listar:", error);
    }
}

function mostrarTabla(datos) {
    const c = document.getElementById('cuerpoTabla'); 
    c.innerHTML = '';
    
    const formatearMiles = new Intl.NumberFormat('es-CO', {
        maximumFractionDigits: 0
    });
    if(!Array.isArray(datos)) return;

    datos.forEach(m => {
        const numeroLimpio = parseFloat(m.valor) || 0;
        const valorFormateado = formatearMiles.format(numeroLimpio);
        c.innerHTML += `<tr>
            <td>${m.nombre}</td>
            <td>${m.tipo}</td>
            <td>${m.unidad_medida}</td>
            <td>${valorFormateado}</td>
            <td>${m.cantidad}</td>
            <td>${m.estado || 'N/A'}</td>
            <td>${m.nombre_marca || 'N/A'}</td>
            <td>
                <button class="btn-editar-tabla" onclick="editar(${m.id},'${encodeURIComponent(m.nombre)}','${encodeURIComponent(m.tipo)}','${encodeURIComponent(m.cantidad)}','${encodeURIComponent(m.valor)}','${encodeURIComponent(m.unidad_medida)}',${m.marca_id || 0})">Editar</button>
                <button class="btn-eliminar-tabla" onclick="eliminar(${m.id})">Eliminar</button>
            </td></tr>`;
    });
}

function filtrar() {
    const t = document.getElementById('buscar').value.toLowerCase();
    mostrarTabla(materiasGlobal.filter(m => m.nombre.toLowerCase().includes(t) || m.tipo.toLowerCase().includes(t)));
}

async function guardar() {
    const nombre   = document.getElementById('nombre').value.trim(), 
          tipo     = document.getElementById('tipo').value.trim(),
          unidad   = document.getElementById('unidad_medida').value.trim(),
          valor    = document.getElementById('valor').value.trim(), 
          cantidad = document.getElementById('cantidad').value.trim(),
          marca    = document.getElementById('marca').value.trim();

    if (!nombre || !tipo || !cantidad || !unidad) return alert('Faltan campos obligatorios (Nombre, Tipo, Cantidad y Unidad de Medida)');

    // Forzamos 0 si el valor viene vacío para evitar el error de la BD NOT NULL
    const data = {
        nombre: nombre,
        tipo: tipo,
        unidad_medida: unidad,
        valor: valor === '' ? 0 : parseFloat(valor),
        cantidad: cantidad,
        marca_id: marca || null
    };

    const url = editId ? `${API_MP}?id=${editId}` : API_MP;
    const method = editId ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const respuestaServidor = await res.json();

        if (res.ok) {
            alert(editId ? 'Actualizado correctamente' : 'Guardado correctamente');
            limpiar();
            listar();
        } else {
            alert('Error: ' + (respuestaServidor.error || 'No se pudo procesar la solicitud'));
        }
    } catch (err) {
        alert('Error de conexión con el servidor');
    }
}

function editar(id, nombre, tipo, stock, valor, unidad, marca) {
    document.getElementById('nombre').value = decodeURIComponent(nombre);
    document.getElementById('tipo').value = decodeURIComponent(tipo);
    document.getElementById('unidad_medida').value = decodeURIComponent(unidad);
    document.getElementById('cantidad').value = decodeURIComponent(stock); 
    document.getElementById('valor').value = decodeURIComponent(valor);
    document.getElementById('marca').value = decodeURIComponent(marca === 0 ? '' : marca);
    editId = id;
}

async function eliminar(id) {
    if (!confirm('¿Eliminar este insumo?')) return;
    const res = await fetch(`${API_MP}?id=${id}`, { method: 'DELETE' });
    if (res.ok) listar();
    else alert('No se pudo eliminar');
}

function limpiar() {
    editId = null;
    ['nombre', 'tipo', 'unidad_medida', 'valor', 'cantidad', 'marca'].forEach(id => document.getElementById(id).value = '');
}
</script>
</body>
</html>
