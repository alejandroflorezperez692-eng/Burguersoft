<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'menu';

$categorias_enum = [
    'Hamburguesa','Perros Caliente','Salchipapa','Fritos',
    'Arepas','Picada','Bebidas Frias','Bebidas Calientes','Pizza'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft – Gestión del Menú</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        :root {
            --primario:   #3d2111;
            --secundario: #E8821A;
            --alerta:     #C3402A;
            --fondo:      #fdf8f0;
        }

        body { font-family: 'Lato', Arial, sans-serif;  background: var(--fondo); margin: 0; padding: 0; }

.marcas-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-left: 50px;
    margin-right: 50px;
    padding-bottom: 14px;
    border-bottom: 3px solid #E8821A;
}

.marcas-header h1 {
    font-family:  'Lato', 'Segoe UI', sans-serif;
    font-size: 28px;
    font-weight: 700 !important;
    color: #1e100a;
    margin: 0;
    line-height: 1.2;
}

.marcas-header .subtitulo {
    font-size: 13px;
    color: #6b4c38;
    margin: 4px 0 0;
    font-family: 'Lato', 'Segoe UI', sans-serif;
}


        /* ── FORMULARIO ── */
        .contenedor-form {
            background: #fff; border-radius: 0 12px 12px 12px;
            margin: 30px 30px 20px; padding: 24px 28px;
            border: 1px solid rgba(30,16,10,0.12);
            box-shadow: 0 2px 12px rgba(30,16,10,0.07);
        }
        .contenedor-form h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primario); margin: 0 0 18px; font-size: 20px;
            padding-bottom: 12px; border-bottom: 2px solid var(--secundario);
            
        }

        #formProducto {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px 22px;
        }
        #formProducto label {
            font-weight: 800; color: #555; font-size: 12px;
            text-transform: uppercase; letter-spacing: 0.6px; align-self: center;
        }
        #formProducto input,
        #formProducto select,
        #formProducto textarea {
            padding: 10px 12px; border: 1.5px solid rgba(30,16,10,0.18);
            border-radius: 8px; font-size: 14px; width: 100%;
            box-sizing: border-box; font-family: 'Lato', Arial, sans-serif;
            outline: none; transition: border-color .2s;
        }
        #formProducto input:focus,
        #formProducto select:focus,
        #formProducto textarea:focus { border-color: var(--secundario); }
        #formProducto textarea { resize: vertical; min-height: 68px; grid-column: 2/3; }

        .btn-guardar {
            grid-column: 1/3; padding: 12px; background: var(--secundario);
            color: #fff; border: none; font-weight: 800; border-radius: 8px;
            cursor: pointer; font-size: 15px; margin-top: 4px;
            font-family: 'Lato', Arial, sans-serif;
            box-shadow: 0 3px 12px rgba(232,130,26,0.32);
            transition: background .2s, transform .15s;
        }
        .btn-guardar:hover { background: #cf6e12; transform: translateY(-1px); }

        .btn-cancelar-edicion {
            grid-column: 1/3; padding: 9px; background: #eee; color: #555;
            border: none; font-weight: 700; border-radius: 8px;
            cursor: pointer; font-size: 13px; display: none;
            font-family: 'Lato', Arial, sans-serif;
        }
        .btn-cancelar-edicion:hover { background: #ddd; }

        /* ── ZONA IMAGEN ── */
        .imagen-zona {
            grid-column: 1/3; display: flex; gap: 20px;
            align-items: flex-start; margin-top: 4px;
        }
        .imagen-preview-box {
            width: 160px; height: 130px; border: 2px dashed #bbb;
            border-radius: 10px; display: flex; flex-direction: column;
            align-items: center; justify-content: center; cursor: pointer;
            overflow: hidden; flex-shrink: 0; background: #faf8f4;
            transition: border-color .2s; position: relative;
        }
        .imagen-preview-box:hover { border-color: var(--secundario); }
        .imagen-preview-box.con-imagen { border-style: solid; border-color: var(--secundario); }
        .imagen-placeholder { text-align: center; color: #aaa; font-size: 13px; pointer-events: none; }
        .imagen-placeholder .icono-imagen { font-size: 36px; }
        .imagen-preview-box img { display: none; width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
        .imagen-preview-box img.visible { display: block; }
        .imagen-controles { flex: 1; display: flex; flex-direction: column; gap: 10px; justify-content: center; }
        .btn-seleccionar-img {
            padding: 9px 16px; background: var(--primario); color: #fff;
            border: none; border-radius: 8px; cursor: pointer;
            font-weight: 800; font-size: 13px; width: fit-content;
            font-family: 'Lato', Arial, sans-serif; transition: background .2s;
        }
        .btn-seleccionar-img:hover { background: #5a3320; }
        .imagen-nombre { font-size: 13px; color: #555; font-style: italic; }
        .imagen-nota   { font-size: 11px; color: #aaa; line-height: 1.5; }
        .imagen-nota span { color: var(--secundario); }
        .btn-quitar-img {
            display: none; padding: 5px 12px; background: #eee; color: #c00;
            border: none; border-radius: 6px; cursor: pointer; font-size: 12px;
        }

        /* ── INGREDIENTES ── */
        .ingredientes-zona {
            grid-column: 1/3; border: 1px solid #e0d8ce; border-radius: 10px;
            padding: 14px 16px; margin-top: 6px; background: #faf8f4;
        }
        .ingredientes-titulo { font-weight: 800; color: var(--primario); font-size: 13px; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .ing-form-row { display: flex; gap: 8px; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }
        .ing-form-row select,
        .ing-form-row input { flex: 1; padding: 8px 10px; border: 1.5px solid rgba(30,16,10,0.18); border-radius: 7px; font-size: 13px; min-width: 100px; box-sizing: border-box; outline: none; transition: border-color .2s; }
        .ing-form-row select:focus,
        .ing-form-row input:focus { border-color: var(--secundario); }
        .btn-add-ing { padding: 8px 14px; background: var(--secundario); color: #fff; border: none; border-radius: 7px; cursor: pointer; font-weight: 800; font-size: 13px; white-space: nowrap; transition: background .2s; }
        .btn-add-ing:hover { background: #cf6e12; }
        #lista-receta { list-style: none; padding: 0; margin: 0; }
        #lista-receta li { display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; background: #fff; border: 1px solid #e0d8ce; border-radius: 7px; margin-bottom: 5px; font-size: 13px; color: #333; }
        #lista-receta li span { flex: 1; }
        .btn-del-ing { background: #cc2200; color: #fff; border: none; border-radius: 5px; padding: 3px 8px; cursor: pointer; font-size: 12px; font-weight: bold; }
        .receta-empty { color: #aaa; font-size: 13px; text-align: center; padding: 6px 0; }

        /* ── BUSCADOR ── */
        #buscar {
            width: calc(100% - 60px); margin: 0 30px 20px;
            padding: 12px 16px; border: 1.5px solid rgba(30,16,10,0.18);
            border-radius: 8px; font-size: 15px; outline: none;
            font-family: 'Lato', Arial, sans-serif;
            transition: border-color .2s;
        }
        #buscar:focus { border-color: var(--secundario); }

        /* ── SECCIÓN MENÚ ── */
        .menu-seccion { padding: 10px 30px 30px; }
        .menu-seccion h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 22px; color: var(--primario);
            margin-bottom: 16px; padding-bottom: 10px;
            border-bottom: 3px solid var(--secundario);
        }
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 20px; margin-bottom: 10px;
        }

        /* ── TARJETA ── */
        .product-card {
            background: #fff; border: 1px solid #e0d8ce;
            border-radius: 12px; overflow: hidden;
            display: flex; flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(61,33,17,.15); }
        .product-card-img { width: 100%; height: 180px; object-fit: cover; display: block; background: #e8e0d4; }
        .product-card-body { padding: 12px 14px; flex: 1; display: flex; flex-direction: column; gap: 4px; }
        .product-name { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; color: var(--primario); font-size: 15px; }
        .product-desc { font-size: 12px; color: #666; margin: 4px 0 6px; line-height: 1.4; flex: 1; }
        .product-price { font-family: 'Playfair Display', Georgia, serif; font-size: 17px; color: var(--alerta); font-weight: 700; }
        .product-estado {
            display: inline-block; font-size: 11px; font-weight: 800;
            padding: 2px 8px; border-radius: 20px; margin-top: 4px;
        }
        .estado-Disponible    { background: rgba(56,161,105,0.15); color: #276749; }
        .estado-Agotado       { background: rgba(200,56,42,0.12);  color: #9b2c2c; }
        .estado-Por_agotarse  { background: rgba(232,130,26,0.15); color: #8a4a10; }
        .product-actions { display: flex; gap: 8px; padding: 10px 14px 12px; border-top: 1px solid #f0ebe3; }
        .btn-edit, .btn-delete { flex: 1; padding: 7px; border: none; border-radius: 7px; cursor: pointer; font-weight: 800; font-size: 13px; transition: opacity .2s; }
        .btn-edit   { background: #2d89ef; color: #fff; }
        .btn-delete { background: #cc2200; color: #fff; }
        .btn-edit:hover, .btn-delete:hover { opacity: .85; }
        .empty-state { text-align: center; padding: 40px; color: #aaa; font-size: 15px; grid-column: 1/-1; }

        /* ── MODAL ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); align-items: center; justify-content: center; z-index: 99999; }
        .modal-overlay.activo { display: flex; }
        .modal-box { background: #fff; border-radius: 14px; padding: 28px; max-width: 400px; width: 92%; box-shadow: 0 12px 40px rgba(0,0,0,.35); text-align: center; }
        .modal-box p { font-size: 16px; margin-bottom: 20px; color: #333; font-family: 'Lato', Arial, sans-serif; }
        .modal-btns { display: flex; gap: 10px; justify-content: center; }
        .modal-btns button { padding: 9px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 800; font-size: 14px; font-family: 'Lato', Arial, sans-serif; }
        .btn-confirmar { background: var(--alerta); color: #fff; }
        .btn-cancelar  { background: #ddd; color: #333; }

        /* ── TOAST ── */
        #toast {
            position: fixed; bottom: 30px; right: 30px;
            background: var(--primario); color: #fff;
            padding: 12px 22px; border-radius: 10px; font-size: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,.3);
            opacity: 0; transform: translateY(10px);
            transition: opacity .3s, transform .3s;
            z-index: 999999; pointer-events: none;
            font-family: 'Lato', Arial, sans-serif;
        }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok   { background: #2a7a3b; }
        #toast.err  { background: #cc2200; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content" style="padding-top: 100px;">

    <!-- FORMULARIO --> 
    <div class="marcas-header">
        <div>
            <h1>Gestión del Menu</h1>
            <p class="subtitulo">Productos disponibles en el menú</p>
        </div>
    </div>
    <div class="contenedor-form">
        <form id="formProducto" novalidate>
            <input type="hidden" id="editId">
            <input type="hidden" id="prodImagenActual">

            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" placeholder="Ej: Hamburguesa Especial" required>

            <label for="precio">Precio *</label>
            <input type="number" id="precio" placeholder="Ej: 12000" min="0" required>

            <label for="descripcion">Descripción / Ingredientes</label>
            <textarea id="descripcion" placeholder="Ej: Carne 150g, queso, tocineta…"></textarea>

            <label for="categoria">Categoría *</label>
            <select id="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php foreach ($categorias_enum as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="estado">Estado</label>
            <select id="estado">
                <option value="Disponible">Disponible</option>
                <option value="Agotado">Agotado</option>
                <option value="Por agotarse">Por agotarse</option>
            </select>

            <label for="catidad">Cantidad</label>
            <input type="text" id="catidad" placeholder="Ej: 10">

            <!-- Imagen -->
            <div class="imagen-zona">
                <div class="imagen-preview-box" id="prod-preview-box"
                     onclick="document.getElementById('prodImagenFile').click()">
                    <div class="imagen-placeholder" id="prod-preview-placeholder">
                        <div class="icono-imagen">🍔</div>
                        <div>Imagen del<br>producto</div>
                    </div>
                    <img id="prodPreview" src="" alt="Vista previa">
                </div>
                <div class="imagen-controles">
                    <input type="file" id="prodImagenFile" accept="image/*"
                           style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;">
                    <button type="button" class="btn-seleccionar-img"
                            onclick="document.getElementById('prodImagenFile').click()">
                        Seleccionar imagen
                    </button>
                    <span id="prodImagenNombre" class="imagen-nombre">Ningún archivo seleccionado</span>
                    <span class="imagen-nota"><span>Al editar, si no cambias la imagen se conserva la actual.</span></span>
                    <button type="button" id="prod-btn-quitar-img" class="btn-quitar-img"
                            onclick="quitarImagenProd()">Quitar imagen</button>
                </div>
            </div>

            <!-- Ingredientes -->
            <div class="ingredientes-zona">
                <div class="ingredientes-titulo">Ingredientes (Receta)</div>
                <div class="ing-form-row">
                    <select id="ing-select-mp">
                        <option value="">Seleccionar materia prima…</option>
                    </select>
                    <input type="number" id="ing-cantidad" placeholder="Cantidad" min="0.01" step="0.01">
                    <button type="button" class="btn-add-ing" onclick="agregarIngrediente()">+ Agregar</button>
                </div>
                <ul id="lista-receta"><li class="receta-empty">Sin ingredientes asignados.</li></ul>
            </div>

            <button type="submit" class="btn-guardar" id="btn-submit">Guardar Producto</button>
            <button type="button" class="btn-cancelar-edicion" id="btn-cancelar" onclick="cancelarEdicion()">Cancelar edición</button>
        </form>
    </div>

    <input type="text" id="buscar" placeholder="Buscar productos por nombre o categoría…">

    <div id="menu-container"></div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal-overlay" id="modal-eliminar">
    <div class="modal-box">
        <p id="modal-texto">¿Estás seguro de que deseas eliminar este elemento?</p>
        <div class="modal-btns">
            <button class="btn-confirmar" id="btn-confirmar-ok">Sí, eliminar</button>
            <button class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
        </div>
    </div>
</div>

<div id="toast"></div>

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
<button class="acc-fab" id="accFab" onclick="togglePanel()">
    <img style="width:24px;height:24px;filter:invert(1);pointer-events:none;" src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>

<script>
const CTRL = '../controllers/productos.php';

let productos          = [];
let materiasDisponibles = [];
let ingredientesNuevos  = [];
let idEliminar          = null;

function toast(msg, tipo = 'ok') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show ' + tipo;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.className = '', 3000);
}

// ── Cargar productos ──────────────────────────────────────────────────────
async function cargarProductos(filtro = '') {
    try {
        const res = await fetch(`${CTRL}?accion=productos`);
        productos = await res.json();
        renderMenu(filtro);
    } catch (e) {
        toast('Error al cargar productos', 'err');
    }
}

// ── Render agrupado por categoría ─────────────────────────────────────────
function renderMenu(filtro = '') {
    const contenedor = document.getElementById('menu-container');
    contenedor.innerHTML = '';
    const fil = filtro.toLowerCase().trim();

    // Agrupar productos por su campo categoria (enum)
    const grupos = {};
    productos.forEach(p => {
        if (fil && !p.nombre.toLowerCase().includes(fil) &&
                   !p.categoria.toLowerCase().includes(fil)) return;
        if (!grupos[p.categoria]) grupos[p.categoria] = [];
        grupos[p.categoria].push(p);
    });

    if (!Object.keys(grupos).length) {
        contenedor.innerHTML = `<div class="empty-state" style="padding:60px;">No se encontraron productos${filtro ? ` para "<strong>${filtro}</strong>"` : ''}.</div>`;
        return;
    }

    // Mostrar en el orden del enum
    const orden = ['Hamburguesa','Perros Caliente','Salchipapa','Fritos','Arepas','Picada','Bebidas Frias','Bebidas Calientes','Pizza'];
    orden.forEach(cat => {
        if (!grupos[cat]) return;
        const seccion = document.createElement('div');
        seccion.className = 'menu-seccion';
        const titulo = document.createElement('h2');
        titulo.textContent = cat;
        seccion.appendChild(titulo);
        const grid = document.createElement('div');
        grid.className = 'productos-grid';
        grupos[cat].forEach(p => grid.appendChild(crearTarjeta(p)));
        seccion.appendChild(grid);
        contenedor.appendChild(seccion);
    });
}

function crearTarjeta(p) {
    const card = document.createElement('div');
    card.className = 'product-card';
    const imgSrc = p.img || '../estilos/img/default.jpg';
    const estadoClass = 'estado-' + p.estado.replace(/ /g, '_');
    card.innerHTML = `
        <img class="product-card-img" src="${imgSrc}" alt="${p.nombre}"
             onerror="this.src='../estilos/img/default.jpg'">
        <div class="product-card-body">
            <div class="product-name">${p.nombre}</div>
            <div class="product-desc">${p.descripcion || ''}</div>
            <div class="product-price">$${Number(p.valor).toLocaleString('es-CO')}</div>
            <span class="product-estado ${estadoClass}">${p.estado}</span>
        </div>
        <div class="product-actions">
            <button class="btn-edit"   onclick="prepararEdicion(${p.id})">Editar</button>
            <button class="btn-delete" onclick="confirmarEliminar(${p.id}, '${p.nombre.replace(/'/g,"\\'")}')">Eliminar</button>
        </div>`;
    return card;
}

// ── Preview imagen ────────────────────────────────────────────────────────
document.getElementById('prodImagenFile').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) { quitarImagenProd(); return; }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('prodPreview').src = e.target.result;
        document.getElementById('prodPreview').classList.add('visible');
        document.getElementById('prod-preview-placeholder').style.display = 'none';
        document.getElementById('prod-preview-box').classList.add('con-imagen');
    };
    reader.readAsDataURL(file);
    document.getElementById('prodImagenNombre').textContent = file.name;
    document.getElementById('prod-btn-quitar-img').style.display = 'inline-block';
});

function quitarImagenProd() {
    document.getElementById('prodImagenFile').value = '';
    document.getElementById('prodPreview').classList.remove('visible');
    document.getElementById('prod-preview-placeholder').style.display = 'flex';
    document.getElementById('prodImagenNombre').textContent = 'Ningún archivo seleccionado';
    document.getElementById('prod-btn-quitar-img').style.display = 'none';
    document.getElementById('prod-preview-box').classList.remove('con-imagen');
}

// ── Guardar producto ──────────────────────────────────────────────────────
document.getElementById('formProducto').addEventListener('submit', async e => {
    e.preventDefault();
    const id          = document.getElementById('editId').value;
    const nombre      = document.getElementById('nombre').value.trim();
    const precio      = document.getElementById('precio').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const categoria   = document.getElementById('categoria').value;
    const estado      = document.getElementById('estado').value;
    const catidad     = document.getElementById('catidad').value.trim();
    const file        = document.getElementById('prodImagenFile').files[0];
    if (!id && !file) { toast('La imagen es obligatoria para un producto nuevo', 'err'); return; }

    const fd = new FormData();
    fd.append('nombre',      nombre);
    fd.append('valor',       precio);
    fd.append('descripcion', descripcion);
    fd.append('categoria',   categoria);
    fd.append('estado',      estado);
    fd.append('catidad',     catidad || '0');
    if (file) fd.append('imagen', file);

    const url    = id ? `${CTRL}?accion=productos&id=${id}` : `${CTRL}?accion=productos`;
    const method = id ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, { method, body: fd });
        const data = await res.json();
        if (data.success || data.id) {
            // Guardar ingredientes en buffer si es producto nuevo
            const nuevoId = data.id;
            if (nuevoId && ingredientesNuevos.length > 0) {
                for (const ing of ingredientesNuevos) {
                    await fetch(`${CTRL}?accion=receta`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ producto_id: nuevoId, materia_id: ing.materia_id, cantidad_usada: ing.cantidad })
                    });
                }
                ingredientesNuevos = [];
            }
            toast(id ? 'Producto actualizado' : 'Producto agregado');
            cancelarEdicion();
            await cargarProductos(document.getElementById('buscar').value);
        } else {
            toast(data.error || 'Error al guardar', 'err');
        }
    } catch (err) {
        toast('Error de conexión', 'err');
    }
});

// ── Editar producto ───────────────────────────────────────────────────────
function prepararEdicion(id) {
    const p = productos.find(x => x.id === id);
    if (!p) return;
    document.getElementById('editId').value      = p.id;
    document.getElementById('nombre').value      = p.nombre;
    document.getElementById('precio').value      = p.valor;
    document.getElementById('descripcion').value = p.descripcion || '';
    document.getElementById('categoria').value   = p.categoria;
    document.getElementById('estado').value      = p.estado;
    document.getElementById('catidad').value     = p.catidad || '0';
    document.getElementById('prodImagenActual').value = p.img || '';
    if (p.img) {
        document.getElementById('prodPreview').src = p.img;
        document.getElementById('prodPreview').classList.add('visible');
        document.getElementById('prod-preview-placeholder').style.display = 'none';
        document.getElementById('prod-preview-box').classList.add('con-imagen');
        document.getElementById('prodImagenNombre').textContent = 'Imagen actual cargada';
    }
    document.getElementById('form-titulo').textContent    = 'Editando: ' + p.nombre;
    document.getElementById('btn-submit').textContent     = 'Guardar cambios';
    document.getElementById('btn-cancelar').style.display = 'block';
    cargarReceta(p.id);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicion() {
    document.getElementById('formProducto').reset();
    document.getElementById('editId').value               = '';
    document.getElementById('prodImagenActual').value     = '';
    document.getElementById('form-titulo').textContent    = 'Agregar Producto';
    document.getElementById('btn-submit').textContent     = 'Guardar Producto';
    document.getElementById('btn-cancelar').style.display = 'none';
    ingredientesNuevos = [];
    document.getElementById('lista-receta').innerHTML = '<li class="receta-empty">Sin ingredientes asignados.</li>';
    quitarImagenProd();
}

// ── Ingredientes (receta) ─────────────────────────────────────────────────
async function cargarMateriasPrimas() {
    if (materiasDisponibles.length > 0) return;
    try {
        materiasDisponibles = await (await fetch(`${CTRL}?accion=materias`)).json();
    } catch (e) { toast('Error al cargar materias primas', 'err'); return; }
    const sel = document.getElementById('ing-select-mp');
    sel.innerHTML = '<option value="">Seleccionar materia prima…</option>';
    materiasDisponibles.forEach(mp => {
        const opt = document.createElement('option');
        opt.value = mp.id;
        opt.textContent = `${mp.nombre} (${mp.cantidad} ${mp.unidad_medida || ''})`;
        sel.appendChild(opt);
    });
}

async function cargarReceta(producto_id) {
    try {
        const data = await (await fetch(`${CTRL}?accion=receta&id=${producto_id}`)).json();
        renderListaReceta(data.map(d => ({
            id: d.id, nombre: d.nombre_materia,
            cantidad: d.cantidad_usada, materia_id: d.materia_id
        })));
    } catch (e) { toast('Error al cargar ingredientes', 'err'); }
}

function renderListaReceta(lista) {
    const ul = document.getElementById('lista-receta');
    if (!lista.length) { ul.innerHTML = '<li class="receta-empty">Sin ingredientes asignados.</li>'; return; }
    ul.innerHTML = '';
    lista.forEach((ing, idx) => {
        const li = document.createElement('li');
        li.innerHTML = `<span>${ing.nombre} — <strong>${ing.cantidad}</strong> und.</span>
                        <button class="btn-del-ing" onclick="eliminarIngrediente(${ing.id || -1}, ${idx})">✕</button>`;
        ul.appendChild(li);
    });
}

async function agregarIngrediente() {
    const producto_id = document.getElementById('editId').value;
    const materia_id  = document.getElementById('ing-select-mp').value;
    const cantidad    = parseFloat(document.getElementById('ing-cantidad').value);
    if (!materia_id)                 { toast('Selecciona una materia prima', 'err'); return; }
    if (!cantidad || cantidad <= 0)  { toast('Ingresa una cantidad válida', 'err'); return; }

    if (producto_id) {
        try {
            const data = await (await fetch(`${CTRL}?accion=receta`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ producto_id, materia_id, cantidad_usada: cantidad })
            })).json();
            if (data.success) { toast('Ingrediente agregado'); await cargarReceta(producto_id); }
            else { toast(data.error || 'Error', 'err'); return; }
        } catch (e) { toast('Error de conexión', 'err'); return; }
    } else {
        ingredientesNuevos.push({ materia_id, cantidad });
        renderListaReceta(ingredientesNuevos.map(i => ({
            id: -1, nombre: (materiasDisponibles.find(m => m.id == i.materia_id) || {}).nombre || '',
            cantidad: i.cantidad
        })));
        toast('Ingrediente agregado');
    }
    document.getElementById('ing-cantidad').value  = '';
    document.getElementById('ing-select-mp').value = '';
}

async function eliminarIngrediente(id_receta, idx) {
    const producto_id = document.getElementById('editId').value;
    if (id_receta !== -1) {
        try {
            const data = await (await fetch(`${CTRL}?accion=receta&id=${id_receta}`, { method: 'DELETE' })).json();
            if (data.success) { toast('Ingrediente eliminado'); await cargarReceta(producto_id); }
            else toast('No se pudo eliminar', 'err');
        } catch (e) { toast('Error de conexión', 'err'); }
    } else {
        ingredientesNuevos.splice(idx, 1);
        renderListaReceta(ingredientesNuevos.map(i => ({
            id: -1, nombre: (materiasDisponibles.find(m => m.id == i.materia_id) || {}).nombre || '',
            cantidad: i.cantidad
        })));
        toast('Ingrediente eliminado');
    }
}

// ── Eliminar producto ─────────────────────────────────────────────────────
function confirmarEliminar(id, nombre) {
    idEliminar = id;
    document.getElementById('modal-texto').textContent = `¿Eliminar "${nombre}"? Esta acción no se puede deshacer.`;
    document.getElementById('modal-eliminar').classList.add('activo');
}
function cerrarModal() {
    idEliminar = null;
    document.getElementById('modal-eliminar').classList.remove('activo');
}

document.getElementById('btn-confirmar-ok').addEventListener('click', async () => {
    if (!idEliminar) return;
    cerrarModal();
    try {
        const res  = await fetch(`${CTRL}?accion=productos&id=${idEliminar}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) { toast('Producto eliminado'); await cargarProductos(document.getElementById('buscar').value); }
        else toast(data.error || 'No se pudo eliminar', 'err');
    } catch (err) { toast('Error de conexión', 'err'); }
});

document.getElementById('buscar').addEventListener('input', e => renderMenu(e.target.value));

(async () => {
    await cargarProductos();
    await cargarMateriasPrimas();
})();
</script>
</body>
</html>
