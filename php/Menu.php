<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'menu';

$categorias_enum = [
    'Hamburguesa','Perros Caliente','Salchipapa','Fritos',
    'Arepas','Picada','Bebidas Frias','Bebidas Calientes','Pizza'
];

$cat_icons = [
    'Hamburguesa'      => '',
    'Perros Caliente'  => '',
    'Salchipapa'       => '',
    'Fritos'           => '',
    'Arepas'           => '',
    'Picada'           => '',
    'Bebidas Frias'    => '',
    'Bebidas Calientes'=> '',
    'Pizza'            => '',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT — Gestión del Menú</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .menu-page { padding: 36px 40px 60px; }

      
        .form-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 36px;
        }

        .form-panel-header {
            background: var(--text-900);
            padding: 18px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .form-panel-header h3 {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .form-panel-body {
            padding: 24px 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

     
        .form-main-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 14px;
            align-items: end;
        }

      
        .form-secondary-row {
            display: grid;
            grid-template-columns: 1fr 260px 1fr;
            gap: 14px;
            align-items: start;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .field input,
        .field select,
        .field textarea {
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 13.5px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            width: 100%;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface);
        }

        .field textarea { resize: vertical; min-height: 96px; }
        .field input::placeholder, .field textarea::placeholder { color: var(--text-400); }

        
        .img-upload-zone {
            border: 2px dashed var(--border-strong);
            border-radius: var(--r-md);
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: var(--surface-2);
            height: 100%;
            box-sizing: border-box;
        }

        .img-upload-zone:hover { border-color: var(--brand); background: rgba(232,130,26,0.04); }
        .img-upload-zone.has-img { border-style: solid; border-color: var(--brand); }

        .img-thumb {
            width: 64px;
            height: 64px;
            border-radius: var(--r-sm);
            object-fit: cover;
            display: none;
            flex-shrink: 0;
        }

        .img-thumb.visible { display: block; }

        .img-placeholder {
            width: 64px;
            height: 64px;
            border-radius: var(--r-sm);
            background: var(--surface-3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .img-info { flex: 1; min-width: 0; }
        .img-info-name { font-size: 11.5px; font-weight: 600; color: var(--text-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .img-info-hint { font-size: 10.5px; color: var(--text-400); margin-top: 2px; line-height: 1.3; }

        .btn-pick-img {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            background: var(--text-900);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            margin-top: 6px;
            display: inline-block;
            box-shadow: none;
            transition: opacity 0.18s;
        }

        .btn-pick-img:hover { opacity: 0.75; transform: none; box-shadow: none; }

       
        .ingredientes-section {
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            overflow: hidden;
        }

        .ing-header {
            background: var(--surface-3);
            padding: 9px 14px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .ing-body { padding: 10px 12px; display: flex; flex-direction: column; gap: 8px; }

        .ing-row { display: flex; gap: 7px; }

        .ing-row select,
        .ing-row input {
            flex: 1;
            padding: 8px 10px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 12.5px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .ing-row input { max-width: 80px; }
        .ing-row select:focus, .ing-row input:focus { border-color: var(--brand); }

        .btn-add-ing {
            padding: 8px 12px;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            flex-shrink: 0;
            box-shadow: none;
            transition: background 0.18s;
        }

        .btn-add-ing:hover { background: var(--brand-deep); transform: none; box-shadow: none; }

        #lista-receta {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 120px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        #lista-receta li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            font-size: 12px;
            color: var(--text-900);
        }

        .receta-empty { color: var(--text-400); font-size: 12px; text-align: center; padding: 4px 0; }

        .btn-del-ing {
            background: rgba(200,56,42,0.1);
            color: var(--danger);
            border: none;
            border-radius: 4px;
            padding: 2px 7px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            box-shadow: none;
        }

        .btn-del-ing:hover { background: var(--danger); color: #fff; transform: none; box-shadow: none; }

        .form-panel-footer {
            padding: 14px 28px 22px;
            display: flex;
            gap: 12px;
            border-top: 1px solid var(--border);
        }

        .btn-submit-form {
            padding: 11px 32px;
            background: var(--brand);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232,130,26,0.35);
            transition: all 0.2s var(--ease);
        }

        .btn-submit-form:hover { background: var(--brand-deep); transform: translateY(-1px); }

        .btn-cancel-form {
            padding: 11px 24px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-600);
            cursor: pointer;
            display: none;
            box-shadow: none;
            transition: background 0.18s;
        }

        .btn-cancel-form:hover { background: var(--surface-3); transform: none; box-shadow: none; }

        .catalog-toolbar {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-wrap::before {
            content: '';
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 11px 16px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface);
            color: var(--text-900);
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-glow); }
        .search-input::placeholder { color: var(--text-400); }

        .menu-container { display: flex; flex-direction: column; gap: 36px; }

        .cat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        .cat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(232,130,26,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .cat-name {
            font-family: var(--font-sans);
            font-size: 20px;
            font-weight: 700;
            color: var(--text-900);
        }

        .cat-count {
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-400);
            background: var(--surface-3);
            padding: 3px 10px;
            border-radius: 20px;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 18px;
        }

        .product-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.22s var(--ease), box-shadow 0.22s, border-color 0.22s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }

        .product-img {
            width: 100%;
            height: 155px;
            object-fit: cover;
            display: block;
            background: var(--surface-3);
            transition: transform 0.35s var(--ease);
        }

        .product-card:hover .product-img { transform: scale(1.04); }

        .product-body {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .product-name {
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 14px;
            color: var(--text-900);
            line-height: 1.3;
        }

        .product-desc {
            font-size: 11.5px;
            color: var(--text-400);
            line-height: 1.4;
            flex: 1;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .product-price {
            font-family: var(--font-sans);
            font-size: 15px;
            font-weight: 900;
            color: var(--brand);
        }

        .product-estado {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        .estado-Disponible   { background: #d5f5e3; color: #1a7a42; }
        .estado-Agotado      { background: #fde8e8; color: #922; }
        .estado-Por_agotarse { background: rgba(232,130,26,0.15); color: #8a4a10; }

        .product-actions {
            display: flex;
            gap: 8px;
            padding: 10px 13px 13px;
            border-top: 1px solid var(--border);
        }

        .btn-edit-p, .btn-del-p {
            flex: 1;
            padding: 7px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
            box-shadow: none;
            transition: filter 0.18s, transform 0.15s;
        }

        .btn-edit-p { background: var(--info); }
        .btn-del-p  { background: var(--danger); }
        .btn-edit-p:hover, .btn-del-p:hover { filter: brightness(0.87); transform: translateY(-1px); box-shadow: none; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(22,8,0,0.55);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            z-index: 300;
        }

        .modal-overlay.activo { display: flex; animation: fadeM 0.2s; }
        @keyframes fadeM { from { opacity:0; } to { opacity:1; } }

        .modal-box {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 36px;
            max-width: 380px;
            width: 92%;
            box-shadow: var(--shadow-lg);
            text-align: center;
            animation: slideM 0.22s;
        }

        @keyframes slideM { from { transform:translateY(16px);opacity:0; } to { transform:translateY(0);opacity:1; } }

        .modal-box .modal-icon { font-size: 40px; margin-bottom: 12px; }
        .modal-box p { font-size: 15px; color: var(--text-900); margin-bottom: 24px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }

        .btn-confirm-del {
            padding: 10px 28px;
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(200,56,42,0.3);
            transition: background 0.18s;
        }

        .btn-confirm-del:hover { background: #a82d22; transform: none; }

        .btn-cancel-del {
            padding: 10px 22px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-600);
            cursor: pointer;
            box-shadow: none;
            transition: background 0.18s;
        }

        .btn-cancel-del:hover { background: var(--surface-3); transform: none; box-shadow: none; }

        .toast {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: var(--text-900);
            color: #fff;
            padding: 12px 24px;
            border-radius: var(--r-md);
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            z-index: 400;
            pointer-events: none;
            white-space: nowrap;
        }

        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.ok  { background: #1a7a42; }
        .toast.err { background: var(--danger); }

        @media (max-width: 1100px) {
            .form-main-row { grid-template-columns: 1fr 1fr 1fr; }
            .form-secondary-row { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 700px) {
            .form-main-row { grid-template-columns: 1fr 1fr; }
            .form-secondary-row { grid-template-columns: 1fr; }
        }

        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .form-panel-header { background: #0e0500; }
        body.dark-mode .ing-header { background: var(--surface-2); }
        body.dark-mode .img-upload-zone { background: var(--surface-2); }
        body.dark-mode .img-placeholder { background: var(--surface-3); }
        body.dark-mode #lista-receta li { background: var(--surface-2); }
        body.dark-mode .product-card { background: var(--surface); }
        body.dark-mode .modal-box { background: var(--surface); }
        body.dark-mode .field input,
        body.dark-mode .field select,
        body.dark-mode .field textarea,
        body.dark-mode .ing-row input,
        body.dark-mode .ing-row select { background: var(--surface-2); color: var(--text-900); }
        body.dark-mode .search-input { background: var(--surface); color: var(--text-900); }
        body.dark-mode .btn-pick-img{ background: #8b8683 }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="menu-page">

    <div class="page-header">
        <div>
            <h1 style="font-family: var(--font-sans) !important;">Gestión del Menú</h1>
            <div class="subtitulo">Productos disponibles en el menú</div>
        </div>
    </div>

    <div class="form-panel" id="form-panel">

        <form id="formProducto" novalidate>
            <input type="hidden" id="editId">
            <input type="hidden" id="prodImagenActual">

            <div class="form-panel-body">

                <div class="form-main-row">
                    <div class="field">
                        <label>Nombre *</label>
                        <input type="text" id="nombre" onkeypress="sololetras(event)" placeholder="Ej: Hamburguesa Especial" required>
                    </div>
                    <div class="field">
                        <label>Precio *</label>
                        <input type="number" id="precio"  onkeypress="solonumeros(event)" placeholder="12000" min="0" required>
                    </div>
                    <div class="field">
                        <label>Cantidad</label>
                        <input type="text" id="cantidad" onkeypress="solonumeros(event)" placeholder="Ej: 10" oninput="actualizarEstado(this.value)">
                    </div>
                    <div class="field">
                        <label>Categoría *</label>
                        <select id="categoria" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($categorias_enum as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" id="estado" value="Disponible">
                </div>

                <div class="form-secondary-row">
                    <div class="field">
                        <label>Descripción</label>
                        <textarea id="descripcion" placeholder="Ej: Carne 150g, queso, tocineta…"></textarea>
                    </div>

                    <div class="field">
                        <label>Imagen del producto</label>
                        <div class="img-upload-zone" id="prod-preview-box"
                             onclick="document.getElementById('prodImagenFile').click()">
                            <div class="img-placeholder" id="prod-preview-placeholder"></div>
                            <img id="prodPreview" src="" alt="" class="img-thumb">
                            <div class="img-info">
                                <div class="img-info-name" id="prodImagenNombre">Ningún archivo seleccionado</div>
                                <div class="img-info-hint">Al editar, se conserva la imagen actual si no cambias.</div>
                                <button type="button" class="btn-pick-img"
                                        onclick="event.stopPropagation();document.getElementById('prodImagenFile').click()">
                                    Seleccionar
                                </button>
                            </div>
                        </div>
                        <input type="file" id="prodImagenFile" accept="image/*"
                               style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;">
                    </div>

                    <div class="field">
                        <label>Ingredientes</label>
                        <div class="ingredientes-section">
                            <div class="ing-body">
                                <div class="ing-row">
                                    <select id="ing-select-mp">
                                        <option value="">Seleccionar materia prima…</option>
                                    </select>
                                    <input type="number" id="ing-cantidad"  onkeypress="solonumeros(event)" placeholder="Cant." min="0.01" step="0.01">
                                    <button type="button" class="btn-add-ing" onclick="agregarIngrediente()">+</button>
                                </div>
                                <ul id="lista-receta">
                                    <li class="receta-empty">Sin ingredientes asignados.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-panel-footer">
                <button type="submit" class="btn-submit-form" id="btn-submit">Guardar Producto</button>
                <button type="button" class="btn-cancel-form" id="btn-cancelar" onclick="cancelarEdicion()">Cancelar edición</button>
            </div>
        </form>
    </div>

    <div class="catalog-toolbar">
        <div class="search-wrap">
            <input type="text" id="buscar" class="search-input" placeholder="Buscar por nombre o categoría...">
        </div>
    </div>

    <div class="menu-container" id="menu-container">
        <div style="text-align:center;padding:60px;color:var(--text-400);">Cargando menú...</div>
    </div>

</div>
</div>

<div class="modal-overlay" id="modal-eliminar">
    <div class="modal-box">
        <p id="modal-texto">¿Eliminar este producto?</p>
        <div class="modal-actions">
            <button class="btn-cancel-del" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-confirm-del" id="btn-confirmar-ok">Sí, eliminar</button>
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
<div id="toast" class="toast"></div>

<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>

<script>
const CTRL = '../controllers/productos.php';

const CAT_ORDER = <?= json_encode($categorias_enum) ?>;

let productos           = [];
let materiasDisponibles = [];
let ingredientesNuevos  = [];
let idEliminar          = null;

function togglePanel() { document.getElementById('accPanel').classList.toggle('open'); }

function toast(msg, tipo = 'ok') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'toast show ' + tipo;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.className = 'toast'; }, 3000);
}

async function cargarProductos(filtro = '') {
    try {
        const res = await fetch(`${CTRL}?accion=productos`);
        productos = await res.json();
        renderMenu(filtro);
    } catch (e) { toast('Error al cargar productos', 'err'); }
}

function renderMenu(filtro = '') {
    const contenedor = document.getElementById('menu-container');
    contenedor.innerHTML = '';
    const fil = filtro.toLowerCase().trim();

    const grupos = {};
    productos.forEach(p => {
        if (fil && !p.nombre.toLowerCase().includes(fil) && !p.categoria.toLowerCase().includes(fil)) return;
        if (!grupos[p.categoria]) grupos[p.categoria] = [];
        grupos[p.categoria].push(p);
    });

    if (!Object.keys(grupos).length) {
        contenedor.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-400);">
            No se encontraron productos${filtro ? ` para "<strong>${filtro}</strong>"` : ''}.</div>`;
        return;
    }

    CAT_ORDER.forEach(cat => {
        if (!grupos[cat]) return;
        const seccion = document.createElement('div');
        seccion.className = 'cat-section';

        const header = document.createElement('div');
        header.className = 'cat-header';
        header.innerHTML = `
            <div class="cat-name">${cat}</div>
            <div class="cat-count">${grupos[cat].length} ${grupos[cat].length === 1 ? 'producto' : 'productos'}</div>
        `;

        const grid = document.createElement('div');
        grid.className = 'productos-grid';
        grupos[cat].forEach(p => grid.appendChild(crearTarjeta(p)));

        seccion.appendChild(header);
        seccion.appendChild(grid);
        contenedor.appendChild(seccion);
    });
}

function crearTarjeta(p) {
    const card = document.createElement('div');
    card.className = 'product-card';
    const imgSrc    = p.img || '../estilos/img/default.jpg';
    const estadoCls = 'estado-' + p.estado.replace(/ /g, '_');
    card.innerHTML = `
        <img class="product-img" src="${imgSrc}" alt="${p.nombre}"
             onerror="this.src='../estilos/img/default.jpg'">
        <div class="product-body">
            <div class="product-name">${p.nombre}</div>
            <div class="product-desc">${p.descripcion || ''}</div>
            <div class="product-meta">
                <div class="product-price">$${Number(p.valor).toLocaleString('es-CO')}</div>
                <span class="product-estado ${estadoCls}">${p.estado}</span>
            </div>
        </div>
        <div class="product-actions">
            <button class="btn-edit-p" onclick="prepararEdicion(${p.id})">Editar</button>
            <button class="btn-del-p"  onclick="confirmarEliminar(${p.id}, '${p.nombre.replace(/'/g,"\\'")}')">Eliminar</button>
        </div>`;
    return card;
}

document.getElementById('prodImagenFile').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) { quitarImagenProd(); return; }
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('prodPreview');
        prev.src = e.target.result;
        prev.classList.add('visible');
        document.getElementById('prod-preview-placeholder').style.display = 'none';
        document.getElementById('prod-preview-box').classList.add('has-img');
    };
    reader.readAsDataURL(file);
    document.getElementById('prodImagenNombre').textContent = file.name;
});

function quitarImagenProd() {
    document.getElementById('prodImagenFile').value = '';
    document.getElementById('prodPreview').classList.remove('visible');
    document.getElementById('prod-preview-placeholder').style.display = 'flex';
    document.getElementById('prodImagenNombre').textContent = 'Ningún archivo seleccionado';
    document.getElementById('prod-preview-box').classList.remove('has-img');
}

function calcularEstado(cantidad) {
    const n = parseInt(cantidad) || 0;
    if (n === 0)    return 'Agotado';
    if (n <= 5)     return 'Por agotarse';
    return 'Disponible';
}

function actualizarEstado(valor) {
    const estado = calcularEstado(valor);
    document.getElementById('estado').value = estado;
    const display = document.getElementById('estado-display');
    const colores = {
        'Disponible':   { bg: '#d5f5e3', color: '#1a7a42' },
        'Agotado':      { bg: '#fde8e8', color: '#922222' },
        'Por agotarse': { bg: 'rgba(232,130,26,0.15)', color: '#8a4a10' }
    };
    const c = colores[estado];
    display.style.background = c.bg;
    display.style.color      = c.color;
    display.textContent      = estado;
}

document.getElementById('formProducto').addEventListener('submit', async e => {
    e.preventDefault();
    const id          = document.getElementById('editId').value;
    const nombre      = document.getElementById('nombre').value.trim();
    const precio      = document.getElementById('precio').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const categoria   = document.getElementById('categoria').value;
    const estado      = document.getElementById('estado').value;
    const cantidad    = document.getElementById('cantidad').value.trim();
    const file        = document.getElementById('prodImagenFile').files[0];

    if (!id && !file) { toast('La imagen es obligatoria para un producto nuevo', 'err'); return; }

    const fd = new FormData();
    fd.append('nombre',      nombre);
    fd.append('valor',       precio);
    fd.append('descripcion', descripcion);
    fd.append('categoria',   categoria);
    fd.append('estado',      estado);
    fd.append('cantidad',    cantidad || '0');

    if (file) fd.append('imagen', file);

    const url          = id ? `${CTRL}?accion=productos&id=${id}` : `${CTRL}?accion=productos`;
    const fetchOptions = { method: 'POST', body: fd };
    if (id) fetchOptions.headers = { 'X-HTTP-Method-Override': 'PUT' };

    try {
        const res = await fetch(url, fetchOptions);

        if (!res.ok) {
            const textoError = await res.text();
            console.error("Respuesta del servidor:", textoError);
            toast(`Error del servidor (${res.status})`, 'err');
            return;
        }

        const data = await res.json();
        if (data.success || data.id) {
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
    } catch (err) { toast('Error de conexión', 'err'); }
});

function prepararEdicion(id) {
    const p = productos.find(x => x.id === id);
    if (!p) return;

    document.getElementById('editId').value      = p.id;
    document.getElementById('nombre').value      = p.nombre;
    document.getElementById('precio').value      = p.valor;
    document.getElementById('descripcion').value = p.descripcion || '';
    document.getElementById('categoria').value   = p.categoria;
    document.getElementById('estado').value      = p.estado;
    document.getElementById('cantidad').value     = p.cantidad || '0';
    actualizarEstado(p.cantidad || '0');
    document.getElementById('prodImagenActual').value = p.img || '';

    if (p.img) {
        const prev = document.getElementById('prodPreview');
        prev.src = p.img;
        prev.classList.add('visible');
        document.getElementById('prod-preview-placeholder').style.display = 'none';
        document.getElementById('prod-preview-box').classList.add('has-img');
        document.getElementById('prodImagenNombre').textContent = 'Imagen actual cargada';
    }

    document.getElementById('btn-submit').textContent     = 'Guardar cambios';
    document.getElementById('btn-cancelar').style.display = 'inline-flex';
    cargarReceta(p.id);

    const panel = document.getElementById('form-panel');
    const y = panel.getBoundingClientRect().top + window.scrollY - 20;
    window.scrollTo({ top: y, behavior: 'smooth' });
}

function cancelarEdicion() {
    document.getElementById('formProducto').reset();
    document.getElementById('editId').value               = '';
    document.getElementById('prodImagenActual').value     = '';
    document.getElementById('btn-submit').textContent     = 'Guardar Producto';
    document.getElementById('btn-cancelar').style.display = 'none';
    ingredientesNuevos = [];
    document.getElementById('lista-receta').innerHTML = '<li class="receta-empty">Sin ingredientes asignados.</li>';
    quitarImagenProd();
}

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
        li.innerHTML = `<span>${ing.nombre} — <strong>${ing.cantidad}</strong></span>
                        <button class="btn-del-ing" onclick="eliminarIngrediente(${ing.id || -1}, ${idx})"></button>`;
        ul.appendChild(li);
    });
}

async function agregarIngrediente() {
    const producto_id = document.getElementById('editId').value;
    const materia_id  = document.getElementById('ing-select-mp').value;
    const cantidad    = parseFloat(document.getElementById('ing-cantidad').value);
    if (!materia_id)                { toast('Selecciona una materia prima', 'err'); return; }
    if (!cantidad || cantidad <= 0) { toast('Ingresa una cantidad válida', 'err'); return; }

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
            id: -1,
            nombre: (materiasDisponibles.find(m => m.id == i.materia_id) || {}).nombre || '',
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
            id: -1,
            nombre: (materiasDisponibles.find(m => m.id == i.materia_id) || {}).nombre || '',
            cantidad: i.cantidad
        })));
        toast('Ingrediente eliminado');
    }
}

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

document.getElementById('modal-eliminar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('buscar').addEventListener('input', e => renderMenu(e.target.value));

(async () => {
    await cargarProductos();
    await cargarMateriasPrimas();
})();

function sololetras(e) {
    const char = String.fromCharCode(e.keyCode);
    if (!/^[a-zA-Z\s]+$/.test(char)) e.preventDefault();
}

function solonumeros(e) {
    const char = String.fromCharCode(e.keyCode);
    if (!/^[0-9.]$/.test(char)) e.preventDefault();
}
</script>
</body>
</html>