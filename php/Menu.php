<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'menu';

$categorias_enum = [
    'Hamburguesa','Perros Caliente','Salchipapa','Fritos',
    'Arepas','Picada','Bebidas Frias','Bebidas Calientes','Pizza'
];

$cat_icons = [
    'Hamburguesa'      => '🍔',
    'Perros Caliente'  => '🌭',
    'Salchipapa'       => '🍟',
    'Fritos'           => '🥐',
    'Arepas'           => '🫓',
    'Picada'           => '🥩',
    'Bebidas Frias'    => '🥤',
    'Bebidas Calientes'=> '☕',
    'Pizza'            => '🍕',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgersoft — Gestión del Menú</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .menu-page { padding: 36px 40px 60px; }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 220px;
            padding: 11px 16px 11px 42px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 14px;
            background: var(--surface);
            color: var(--text-900);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .search-wrap {
            flex: 1;
            position: relative;
            min-width: 220px;
        }

        .search-wrap::before {
            content: '🔍';
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            pointer-events: none;
            z-index: 1;
        }

        .search-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        .search-input::placeholder { color: var(--text-400); }

        .layout-cols {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 28px;
            align-items: flex-start;
        }

        .form-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: calc(var(--header-h) + 20px);
            overflow: hidden;
        }

        .form-panel-header {
            background: var(--text-900);
            padding: 20px 24px;
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

        .form-panel-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 14px; }

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

        .field textarea { resize: vertical; min-height: 70px; }
        .field input::placeholder, .field textarea::placeholder { color: var(--text-400); }

        .fields-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .img-upload-zone {
            border: 2px dashed var(--border-strong);
            border-radius: var(--r-md);
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: var(--surface-2);
        }

        .img-upload-zone:hover { border-color: var(--brand); background: rgba(232,130,26,0.04); }
        .img-upload-zone.has-img { border-style: solid; border-color: var(--brand); }

        .img-thumb {
            width: 72px;
            height: 72px;
            border-radius: var(--r-sm);
            object-fit: cover;
            display: none;
            flex-shrink: 0;
            background: var(--surface-3);
        }

        .img-thumb.visible { display: block; }

        .img-placeholder {
            width: 72px;
            height: 72px;
            border-radius: var(--r-sm);
            background: var(--surface-3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .img-info { flex: 1; min-width: 0; }
        .img-info-name { font-size: 12px; font-weight: 600; color: var(--text-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .img-info-hint { font-size: 11px; color: var(--text-400); margin-top: 3px; }

        .btn-pick-img {
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            background: var(--text-900);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 6px;
            display: inline-block;
            box-shadow: none;
        }

        .btn-pick-img:hover { opacity: 0.8; transform: none; box-shadow: none; }

        .ingredientes-section {
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            overflow: hidden;
        }

        .ing-header {
            background: var(--surface-3);
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-600);
        }

        .ing-body { padding: 12px 14px; display: flex; flex-direction: column; gap: 8px; }

        .ing-row {
            display: flex;
            gap: 8px;
        }

        .ing-row select,
        .ing-row input {
            flex: 1;
            padding: 8px 10px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-size: 13px;
            background: var(--surface-2);
            color: var(--text-900);
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .ing-row input { max-width: 90px; }
        .ing-row select:focus, .ing-row input:focus { border-color: var(--brand); }

        .btn-add-ing {
            padding: 8px 12px;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
            transition: background 0.18s;
            box-shadow: none;
        }

        .btn-add-ing:hover { background: var(--brand-deep); transform: none; box-shadow: none; }

        #lista-receta {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        #lista-receta li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            font-size: 12.5px;
            color: var(--text-900);
        }

        .receta-empty { color: var(--text-400); font-size: 12.5px; text-align: center; padding: 6px 0; }

        .btn-del-ing {
            background: rgba(200,56,42,0.12);
            color: var(--danger);
            border: none;
            border-radius: 5px;
            padding: 3px 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            box-shadow: none;
        }

        .btn-del-ing:hover { background: var(--danger); color: #fff; transform: none; box-shadow: none; }

        .form-panel-footer {
            padding: 16px 24px 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-top: 1px solid var(--border);
        }

        .btn-submit-form {
            width: 100%;
            padding: 12px;
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
            width: 100%;
            padding: 10px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-600);
            cursor: pointer;
            display: none;
            transition: background 0.18s;
            box-shadow: none;
        }

        .btn-cancel-form:hover { background: var(--surface-3); transform: none; box-shadow: none; }

        .menu-container { display: flex; flex-direction: column; gap: 32px; }

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
            font-family: var(--font-display);
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
            height: 160px;
            object-fit: cover;
            display: block;
            background: var(--surface-3);
            transition: transform 0.35s var(--ease);
        }

        .product-card:hover .product-img { transform: scale(1.04); }

        .product-body {
            padding: 13px 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .product-name {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 14.5px;
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
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 900;
            color: var(--brand);
        }

        .product-estado {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .estado-Disponible   { background: #d5f5e3; color: #1a7a42; }
        .estado-Agotado      { background: #fde8e8; color: #922; }
        .estado-Por_agotarse { background: rgba(232,130,26,0.15); color: #8a4a10; }

        .product-actions {
            display: flex;
            gap: 8px;
            padding: 10px 14px 13px;
            border-top: 1px solid var(--border);
        }

        .btn-edit-p, .btn-del-p {
            flex: 1;
            padding: 7px;
            border: none;
            border-radius: var(--r-sm);
            cursor: pointer;
            font-weight: 700;
            font-size: 12.5px;
            transition: filter 0.18s, transform 0.15s;
            color: #fff;
            box-shadow: none;
        }

        .btn-edit-p { background: var(--info); }
        .btn-del-p  { background: var(--danger); }
        .btn-edit-p:hover, .btn-del-p:hover { filter: brightness(0.87); transform: translateY(-1px); box-shadow: none; }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px;
            color: var(--text-400);
            font-size: 15px;
        }

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
            padding: 32px;
            max-width: 400px;
            width: 92%;
            box-shadow: var(--shadow-lg);
            text-align: center;
            animation: slideM 0.22s;
        }

        @keyframes slideM { from { transform:translateY(16px);opacity:0; } to { transform:translateY(0);opacity:1; } }

        .modal-box .modal-icon { font-size: 42px; margin-bottom: 12px; }

        .modal-box p {
            font-size: 15px;
            color: var(--text-900);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .modal-actions { display: flex; gap: 10px; justify-content: center; }

        .btn-confirm-del {
            padding: 10px 28px;
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.18s;
            box-shadow: 0 4px 14px rgba(200,56,42,0.3);
        }

        .btn-confirm-del:hover { background: #a82d22; transform: none; }

        .btn-cancel-del {
            padding: 10px 24px;
            background: transparent;
            border: 1.5px solid var(--border-strong);
            border-radius: var(--r-sm);
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 14px;
            color: var(--text-600);
            cursor: pointer;
            transition: background 0.18s;
            box-shadow: none;
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

        @media (max-width: 900px) {
            .layout-cols { grid-template-columns: 1fr; }
            .form-panel { position: static; }
        }

        body.dark-mode .form-panel { background: var(--surface); }
        body.dark-mode .form-panel-header { background: #0e0500; }
        body.dark-mode .ing-header { background: var(--surface-2); }
        body.dark-mode .ingredientes-section { border-color: var(--border); }
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
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
<div class="menu-page">

    <div class="page-header">
        <div>
            <h1>Gestión del Menú</h1>
            <div class="subtitulo">Productos disponibles en el menú</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="search-wrap">
            <input type="text" id="buscar" class="search-input" placeholder="Buscar por nombre o categoría...">
        </div>
    </div>

    <div class="layout-cols">

        <div class="form-panel">
            <div class="form-panel-header">
                <h3 id="form-titulo">Agregar Producto</h3>
            </div>

            <form id="formProducto" novalidate>
                <input type="hidden" id="editId">
                <input type="hidden" id="prodImagenActual">

                <div class="form-panel-body">
                    <div class="field">
                        <label>Nombre *</label>
                        <input type="text" id="nombre" placeholder="Ej: Hamburguesa Especial" required>
                    </div>

                    <div class="fields-row">
                        <div class="field">
                            <label>Precio *</label>
                            <input type="number" id="precio" placeholder="12000" min="0" required>
                        </div>
                        <div class="field">
                            <label>Cantidad</label>
                            <input type="text" id="catidad" placeholder="Ej: 10">
                        </div>
                    </div>

                    <div class="field">
                        <label>Descripción</label>
                        <textarea id="descripcion" placeholder="Ej: Carne 150g, queso, tocineta…"></textarea>
                    </div>

                    <div class="fields-row">
                        <div class="field">
                            <label>Categoría *</label>
                            <select id="categoria" required>
                                <option value="">Selecciona...</option>
                                <?php foreach ($categorias_enum as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Estado</label>
                            <select id="estado">
                                <option value="Disponible">Disponible</option>
                                <option value="Agotado">Agotado</option>
                                <option value="Por agotarse">Por agotarse</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Imagen del producto</label>
                        <div class="img-upload-zone" id="prod-preview-box"
                             onclick="document.getElementById('prodImagenFile').click()">
                            <div class="img-placeholder" id="prod-preview-placeholder">🍔</div>
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

                    <div class="ingredientes-section">
                        <div class="ing-header">Ingredientes (Receta)</div>
                        <div class="ing-body">
                            <div class="ing-row">
                                <select id="ing-select-mp">
                                    <option value="">Seleccionar materia prima…</option>
                                </select>
                                <input type="number" id="ing-cantidad" placeholder="Cant." min="0.01" step="0.01">
                                <button type="button" class="btn-add-ing" onclick="agregarIngrediente()">+</button>
                            </div>
                            <ul id="lista-receta">
                                <li class="receta-empty">Sin ingredientes asignados.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-panel-footer">
                    <button type="submit" class="btn-submit-form" id="btn-submit">Guardar Producto</button>
                    <button type="button" class="btn-cancel-form" id="btn-cancelar" onclick="cancelarEdicion()">Cancelar edición</button>
                </div>
            </form>
        </div>

        <div class="menu-container" id="menu-container">
            <div style="text-align:center;padding:60px;color:var(--text-400);">Cargando menú...</div>
        </div>
    </div>

</div>
</div>

<div class="modal-overlay" id="modal-eliminar">
    <div class="modal-box">
        <div class="modal-icon">🗑️</div>
        <p id="modal-texto">¿Estás seguro de que deseas eliminar este producto?</p>
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

const CAT_ICONS = <?= json_encode($cat_icons) ?>;
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
            <div class="cat-icon">${CAT_ICONS[cat] || '🍽️'}</div>
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
    const imgSrc     = p.img || '../estilos/img/default.jpg';
    const estadoCls  = 'estado-' + p.estado.replace(/ /g, '_');
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
    document.getElementById('catidad').value     = p.catidad || '0';
    document.getElementById('prodImagenActual').value = p.img || '';
    if (p.img) {
        const prev = document.getElementById('prodPreview');
        prev.src = p.img;
        prev.classList.add('visible');
        document.getElementById('prod-preview-placeholder').style.display = 'none';
        document.getElementById('prod-preview-box').classList.add('has-img');
        document.getElementById('prodImagenNombre').textContent = 'Imagen actual cargada';
    }
    document.getElementById('form-titulo').textContent     = '✏️ Editando: ' + p.nombre;
    document.getElementById('btn-submit').textContent      = 'Guardar cambios';
    document.getElementById('btn-cancelar').style.display  = 'block';
    cargarReceta(p.id);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicion() {
    document.getElementById('formProducto').reset();
    document.getElementById('editId').value              = '';
    document.getElementById('prodImagenActual').value    = '';
    document.getElementById('form-titulo').textContent   = 'Agregar Producto';
    document.getElementById('btn-submit').textContent    = 'Guardar Producto';
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
        li.innerHTML = `<span>${ing.nombre} — <strong>${ing.cantidad}</strong> und.</span>
                        <button class="btn-del-ing" onclick="eliminarIngrediente(${ing.id || -1}, ${idx})">✕</button>`;
        ul.appendChild(li);
    });
}

async function agregarIngrediente() {
    const producto_id = document.getElementById('editId').value;
    const materia_id  = document.getElementById('ing-select-mp').value;
    const cantidad    = parseFloat(document.getElementById('ing-cantidad').value);
    if (!materia_id)               { toast('Selecciona una materia prima', 'err'); return; }
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
</script>
</body>
</html>