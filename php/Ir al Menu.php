<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Menú</title>
    <link rel="icon" href="../img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <link rel="stylesheet" href="../estilos/factura-estilos.css">
    <script src="../js/Hero-Carrusel.js" defer></script>
    <style>
        :root {
            --primario:   #3d2111;
            --secundario: #F18921;
            --alerta:     #C3402A;
            --fondo:      #f6f5e4;
        }

        body { background: var(--fondo); min-height: 100vh; }

        .titulo-seccion {
            text-align: center;
            font-size: 32px;
            padding: 36px 20px 10px;
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
            letter-spacing: 1px;
        }

        #productos-container {
            padding: 10px 30px 60px;
            max-width: 1300px;
            margin: 0 auto;
        }

        .cat-titulo {
            font-family: 'Lucida Sans', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: var(--primario);
            margin: 36px 0 16px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--secundario);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cat-titulo::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background: var(--secundario);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .prod-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #e8e0d4;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .prod-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(61,33,17,.15);
            border-color: var(--secundario);
        }
        .prod-card-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
            background: #e8e0d4;
        }
        .prod-card-body {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .prod-card-nombre {
            font-weight: bold;
            font-size: 15px;
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
        }
        .prod-card-desc {
            font-size: 12px;
            color: #777;
            line-height: 1.4;
            flex: 1;
        }
        .prod-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px 12px;
            border-top: 1px solid #f0ebe3;
        }
        .prod-card-precio {
            font-size: 16px;
            font-weight: bold;
            color: var(--alerta);
            font-family: 'Lucida Sans', sans-serif;
        }
        .btn-add {
            background: var(--secundario);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-add:hover { background: var(--primario); }

        .btn-cerrar-inv { background: #464646; color: #fff; }

        .cargando { text-align: center; padding: 60px; color: #888; font-size: 15px; }

        #pedido-modal {
            display: none; position: fixed; inset: 0;
            align-items: center; justify-content: center;
            background: rgba(0,0,0,.55); z-index: 99999;
        }
        #pedido-modal .modal-contenido {
            background: #fff; padding: 24px; border-radius: 12px;
            max-width: 360px; width: 92%; text-align: center;
            box-shadow: 0 6px 30px rgba(0,0,0,.3);
        }
        #pedido-modal h3 { color: var(--primario); margin: 0 0 16px; }
        .opciones { display: flex; gap: 12px; justify-content: center; margin-top: 14px; }
        .opciones button { flex: 1; padding: 10px 12px; border: 0; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; }
        .btn-whatsapp { background: #25D366; color: #fff; }
        .btn-local    { background: #444; color: #fff; }
        .btn-cerrar-modal { background: transparent; color: #555; border: 1px solid #ccc !important; margin-top: 10px; padding: 8px 12px; border-radius: 6px; cursor: pointer; width: 100%; }

        #mesa-modal {
            display: none; position: fixed; inset: 0;
            align-items: center; justify-content: center;
            background: rgba(0,0,0,.6); z-index: 100000;
        }
        .mesa-box {
            background: #fff; padding: 24px; border-radius: 12px;
            max-width: 300px; width: 88%; text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,.35);
        }
        .mesa-box h3 { margin: 0 0 8px; color: var(--primario); }
        .mesa-box p  { margin: 0 0 12px; font-size: 14px; color: #555; }
        #mesa-modal-input {
            width: 80px; padding: 8px; border: 1px solid #ccc;
            border-radius: 6px; text-align: center; font-size: 16px;
        }
        .mesa-btns { display: flex; gap: 8px; justify-content: center; margin-top: 12px; }
        .mesa-btns button { padding: 8px 16px; border: 0; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-confirmar-mesa { background: var(--secundario); color: #fff; }
        .btn-cancelar-mesa  { background: #555; color: #fff; }

        #invoice-overlay {
            display: none; position: fixed; inset: 0;
            align-items: center; justify-content: center;
            background: rgba(0,0,0,.6); z-index: 100000;
        }
        #invoice-box {
            background: #fff; padding: 20px; border-radius: 12px;
            max-width: 720px; width: 92%;
            box-shadow: 0 12px 40px rgba(0,0,0,.45); position: relative;
        }
        .invoice-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 14px; }
        .invoice-actions button { padding: 8px 16px; border: 0; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-imprimir { background: #ff6600; color: #fff; }
        .btn-cerrar-inv { background: #464646; color: #fff; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header_publico.php'; ?>

<<<<<<< HEAD
<div id="pedido-modal" role="dialog" aria-modal="true" aria-label="Seleccionar método de pedido">
    <div class="modal-contenido">
        <h3>¿Cómo deseas realizar el pedido?</h3>
        <div style="margin:14px 0 10px;">
            <label style="font-size:13px;font-weight:600;color:#3d2111;display:block;margin-bottom:6px;">Método de pago:</label>
            <select id="pago" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;font-family:inherit;">
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Nequi">Nequi</option>
                <option value="Daviplata">Daviplata</option>
            </select>
        </div>
        <div class="opciones">
            <button type="button" class="btn-whatsapp" onclick="elegirWhatsApp()">📱 WhatsApp</button>
            <button type="button" class="btn-local"    onclick="elegirLocal()">🏠 En el local</button>
        </div>
        <button type="button" class="btn-cerrar-modal" onclick="cerrarModal()">Cancelar</button>
    </div>
=======
    <section class="hero">
        <div class="carousel-container">
            <div class="carousel-slide active" style="background-image: url('https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('https://ranchera.com.co/wp-content/uploads/2022/11/perro-colombiano-1.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('https://chefstv.net/wp-content/uploads/2024/03/0045-empanadas-saltenas-fritas-wide-web.webp');"></div>
            <div class="carousel-slide" style="background-image: url('https://www.elespectador.com/resizer/v2/4YMEEW2QBVGALOUC7LSPUFNKMU.jpg?auth=1913090d3e141e8a3ccce35509259201363e9dddf853024e2f30ac71ce6383a9&width=1110&height=739&smart=true&quality=60');"></div>
        </div>
    </section>

<h2 class="titulo-seccion">NUESTROS PRODUCTOS</h2>

<div id="productos-container">

    <?php if (empty($grupos)): ?>
        <div class="menu-vacio">
            El menú está vacío por ahora. ¡Vuelve pronto!
        </div>

    <?php else: ?>

        <?php foreach ($grupos as $categoria => $items): ?>
            <h3 class="cat-titulo"><?= hv($categoria) ?></h3>
            <div class="productos-grid">
                <?php foreach ($items as $p): ?>
                <div class="prod-card">
                    <img class="prod-card-img"
                         src="<?= hv($p['img']) ?>"
                         alt="<?= hv($p['nombre']) ?>"
                         onerror="this.src='/burguersoft/estilos/img/placeholder.png'">
                    <div class="prod-card-body">
                        <div class="prod-card-nombre"><?= hv($p['nombre']) ?></div>
                        <div class="prod-card-desc"><?= hv($p['descripcion']) ?></div>
                    </div>
                    <div class="prod-card-footer">
                        <span class="prod-card-precio"><?= formatCOP($p['valor']) ?></span>
                        <?php if (isset($_SESSION['id_usuario'])): ?>
                        <button type="button" class="btn-add" title="Agregar al carrito"
                            onclick="agregarAlCarrito(
                                <?= (int)$p['id'] ?>,
                                '<?= hv($p['nombre']) ?>',
                                <?= (float)$p['valor'] ?>,
                                '<?= hv($p['img']) ?>',
                                'producto'
                            )">+</button>
                        <?php else: ?>
                        <a href="/burguersoft/php/login.php">
                            <button type="button" class="btn-add" title="Inicia sesión para agregar">
                                <img style="width:18px;height:18px;filter:invert(1);pointer-events:none;" src="../estilos/img/bloquear.png" alt="Login">
                            </button>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

>>>>>>> d9d8ddf1aa6a892932dc0460027b7227c73522ee
</div>

<div id="mesa-modal" role="dialog" aria-modal="true" aria-label="Número de mesa">
    <div class="mesa-box">
        <h3>Número de mesa</h3>
        <p>Escribe un número entre <strong>1</strong> y <strong>4</strong></p>
        <input id="mesa-modal-input" type="number" min="1" max="4" title="Número de mesa" aria-label="Número de mesa">
        <div class="mesa-btns">
            <button type="button" class="btn-confirmar-mesa" onclick="confirmarMesaModal()">Confirmar</button>
            <button type="button" class="btn-cancelar-mesa"  onclick="cerrarMesaModal()">Cancelar</button>
        </div>
    </div>
</div>

<<<<<<< HEAD
<div id="invoice-overlay" role="dialog" aria-hidden="true">
    <div id="invoice-box">
        <div id="invoice-content"></div>
        <div class="invoice-actions">
            <button type="button" class="btn-imprimir"  onclick="imprimirFactura()">🖨 Imprimir</button>
            <button type="button" class="btn-cerrar-inv" onclick="closeInvoice()">Cerrar</button>
        </div>
    </div>
</div>

<h2 class="titulo-seccion">NUESTROS PRODUCTOS</h2>
<div id="productos-container"><div class="cargando">⏳ Cargando menú…</div></div>
=======
<script src="/burguersoft/js/Menu.js"></script>
>>>>>>> d9d8ddf1aa6a892932dc0460027b7227c73522ee

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
    let carrito = [];
    let pedidoRealizado = false;

    async function cargarMenu() {
        const contenedor = document.getElementById('productos-container');
        try {
            const [resCat, resProd] = await Promise.all([
                fetch('../controllers/productos.php?accion=categorias'),
                fetch('../controllers/productos.php?accion=productos')
            ]);
            if (!resCat.ok)  throw new Error('Error categorías: ' + resCat.status);
            if (!resProd.ok) throw new Error('Error productos: ' + resProd.status);

            const categorias = await resCat.json();
            const productos  = await resProd.json();
            contenedor.innerHTML = '';

            if (!categorias.length) {
                contenedor.innerHTML = '<div class="cargando">El menú está vacío por ahora. ¡Vuelve pronto!</div>';
                return;
            }

            const grupos = {};
            categorias.forEach(c => { grupos[c.nombre] = []; });
            productos.forEach(p => {
                if (p.estado !== 'Agotado' && grupos[p.categoria] !== undefined) {
                    grupos[p.categoria].push(p);
                }
            });

            categorias.forEach(c => {
                const items = grupos[c.nombre] || [];
                if (items.length === 0) return;

                const titulo = document.createElement('h3');
                titulo.className = 'cat-titulo';
                titulo.textContent = c.nombre;
                contenedor.appendChild(titulo);

                const grid = document.createElement('div');
                grid.className = 'productos-grid';

                items.forEach(p => {
                    const imgSrc = p.img || '../estilos/img/Hamburguesa.webp';
                    const card = document.createElement('div');
                    card.className = 'prod-card';
                    card.innerHTML =
                        '<img class="prod-card-img" src="' + imgSrc + '" alt="' + p.nombre + '" onerror="this.src=\'../estilos/img/Hamburguesa.webp\'">' +
                        '<div class="prod-card-body">' +
                            '<div class="prod-card-nombre">' + p.nombre + '</div>' +
                            '<div class="prod-card-desc">' + (p.descripcion || '') + '</div>' +
                        '</div>' +
                        '<div class="prod-card-footer">' +
                            '<span class="prod-card-precio">$' + Number(p.valor).toLocaleString('es-CO') + '</span>' +
                            '<button type="button" class="btn-add" title="Agregar al carrito">+</button>' +
                        '</div>';
                    card.querySelector('.btn-add').addEventListener('click', function(e) {
                        e.stopPropagation();
                        agregarAlCarrito(p.nombre, p.valor, p.id, e.currentTarget);
                    });
                    grid.appendChild(card);
                });

                contenedor.appendChild(grid);
            });

        } catch (e) {
            contenedor.innerHTML =
                '<div class="cargando" style="color:#ff6b6b;">' +
                    'No se pudo cargar el menú.<br>' +
                    '<small style="opacity:0.7;">' + e.message + '</small><br><br>' +
                    '<button onclick="cargarMenu()" style="background:#F18921;color:#fff;border:none;padding:10px 24px;border-radius:8px;cursor:pointer;font-size:15px;">Reintentar</button>' +
                '</div>';
        }
    }

    function agregarAlCarrito(nombre, precio, id, btnEl) {
        try {
            carrito.push({ nombre: nombre, precio: precio, id: id });
            actualizarCarrito();
            var panel = document.getElementById('cartPanel');
            if (panel) panel.classList.add('active');
            if (btnEl) {
                btnEl.textContent = '✓';
                btnEl.style.background = '#27ae60';
                setTimeout(function() { btnEl.textContent = '+'; btnEl.style.background = ''; }, 900);
            }
        } catch(err) { console.error('agregarAlCarrito error:', err); }
    }

    function actualizarCarrito() {
        try {
            var cartItems   = document.getElementById('cartItems');
            var cartTotal   = document.getElementById('cartTotal');
            var badge       = document.getElementById('badge-carrito');
            var btnCheckout = document.getElementById('btnCheckout');

            var map = new Map();
            var total = 0;
            carrito.forEach(function(item) {
                total += Number(item.precio || 0);
                var k = item.nombre;
                if (!map.has(k)) map.set(k, { nombre: k, cantidad: 0, precioUnit: Number(item.precio) });
                map.get(k).cantidad += 1;
            });

            if (cartItems) {
                cartItems.innerHTML = '';
                if (carrito.length === 0) {
                    cartItems.innerHTML =
                        '<div class="empty-cart">' +
                            '<svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>' +
                            '<p>Tu carrito está vacío</p>' +
                        '</div>';
                } else {
                    map.forEach(function(v) {
                        var row = document.createElement('div');
                        row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f0ebe3;gap:8px;';
                        var safeNombre = v.nombre.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
                        var badgeHtml = v.cantidad > 1
                            ? '<span style="background:#F18921;color:#fff;border-radius:12px;padding:1px 7px;font-size:11px;margin-left:4px;">x' + v.cantidad + '</span>'
                            : '';
                        row.innerHTML =
                            '<div style="flex:1;min-width:0;">' +
                                '<div style="font-weight:600;font-size:14px;color:#3d2111;">' + v.nombre + badgeHtml + '</div>' +
                                '<div style="font-size:13px;color:#C3402A;font-weight:bold;margin-top:2px;">$' + (v.precioUnit * v.cantidad).toLocaleString('es-CO') + '</div>' +
                            '</div>' +
                            '<button onclick="quitarDelCarrito(\'' + safeNombre + '\')" ' +
                                'style="flex-shrink:0;background:none;border:1px solid #e0d5c5;border-radius:6px;cursor:pointer;padding:4px 9px;color:#888;font-size:13px;" title="Quitar">&#x2715;</button>';
                        cartItems.appendChild(row);
                    });
                }
            }

            if (cartTotal) cartTotal.textContent = '$' + total.toLocaleString('es-CO');
            var totalUnidades = carrito.length;
            if (badge) {
                badge.textContent = totalUnidades;
                badge.style.display = totalUnidades > 0 ? 'flex' : 'none';
            }
            if (btnCheckout) btnCheckout.disabled = totalUnidades === 0;
        } catch(err) { console.error('actualizarCarrito error:', err); }
    }

    function quitarDelCarrito(nombre) {
        var idx = carrito.findIndex(function(i) { return i.nombre === nombre; });
        if (idx !== -1) carrito.splice(idx, 1);
        actualizarCarrito();
    }

    function vaciarCarrito() {
        carrito = [];
        actualizarCarrito();
    }

    function abrirCheckout() {
        if (carrito.length === 0) { alert('El carrito está vacío'); return; }
        var panel = document.getElementById('cartPanel');
        if (panel) panel.classList.remove('active');
        document.getElementById('pedido-modal').style.display = 'flex';
    }

    async function guardarPedidoDB(metodo_pago, nom_cliente) {
        var map = new Map();
        carrito.forEach(function(item) {
            var key = item.nombre;
            if (!map.has(key)) map.set(key, { id_producto: item.id, nombre: item.nombre, precio_unitario: Number(item.precio), cantidad: 0 });
            map.get(key).cantidad += 1;
        });
        var items = Array.from(map.values());
        try {
            await fetch('../controllers/pedidos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom_cliente: nom_cliente || 'Cliente', metodo_pago: metodo_pago, items: items })
            });
        } catch (e) {
            console.error('Error guardando pedido en BD:', e);
        }
    }

    function enviarPedido() {
        if (carrito.length === 0) { alert('El carrito está vacío'); return; }
        document.getElementById('pedido-modal').style.display = 'flex';
    }

    function cerrarModal() { document.getElementById('pedido-modal').style.display = 'none'; }

    function elegirWhatsApp() {
        var map = new Map();
        var total = 0;
        carrito.forEach(function(item) {
            total += Number(item.precio);
            if (!map.has(item.nombre)) map.set(item.nombre, { nombre: item.nombre, cantidad: 0, precioUnit: Number(item.precio) });
            map.get(item.nombre).cantidad += 1;
        });
        var pagoEl = document.getElementById('pago');
        var metodoPago = pagoEl ? pagoEl.value : 'Efectivo';
        var msg = 'Pedido BurgerSoft%0A%0A';
        map.forEach(function(v) {
            msg += '- ' + v.nombre + (v.cantidad > 1 ? ' x' + v.cantidad : '') + ' - $' + v.precioUnit + '%0A';
        });
        msg += '%0ATOTAL: $' + total + '%0AMetodo de pago: ' + metodoPago;
        window.open('https://wa.me/573224548294?text=' + msg);
        cerrarModal();
        guardarPedidoDB(metodoPago, 'Cliente WhatsApp');
        pedidoRealizado = true;
        window.mesaSeleccionada = '-';
        actualizarFactura();
    }

    function elegirLocal() {
        cerrarModal();
        document.getElementById('mesa-modal').style.display = 'flex';
        var inp = document.getElementById('mesa-modal-input');
        inp.value = '';
        setTimeout(function() { inp.focus(); }, 100);
    }

    function cerrarMesaModal() { document.getElementById('mesa-modal').style.display = 'none'; }

    function confirmarMesaModal() {
        var v = parseInt(document.getElementById('mesa-modal-input').value, 10);
        if (!v || v < 1 || v > 4) { alert('Ingresa un número de mesa entre 1 y 4.'); return; }
        window.mesaSeleccionada = v;
        cerrarMesaModal();
        var pagoEl = document.getElementById('pago');
        var metodoPago = pagoEl ? pagoEl.value : 'Efectivo';
        guardarPedidoDB(metodoPago, 'Mesa ' + v);
        actualizarFactura();
    }

    function mostrarFactura() { actualizarFactura(); }

    function actualizarFactura() {
        var map = new Map();
        var total = 0;
        carrito.forEach(function(it) {
            total += Number(it.precio);
            if (!map.has(it.nombre)) map.set(it.nombre, { nombre: it.nombre, cantidad: 0, precioUnit: Number(it.precio) });
            map.get(it.nombre).cantidad += 1;
        });
        var pagoEl = document.getElementById('pago');
        var metodoPago = pagoEl ? pagoEl.value : 'Efectivo';
        var mesa = window.mesaSeleccionada || '-';
        var cont = document.getElementById('invoice-content');
        var html = '<div class="invoice-header"><div class="brand"><strong>BURGUERSOFT</strong><br><small>Factura</small></div><div class="mesa">Mesa: <strong>' + mesa + '</strong></div></div><hr class="invoice-hr">';
        if (map.size === 0) {
            html += '<p>No hay productos en el carrito.</p>';
        } else {
            html += '<ul class="invoice-list">';
            map.forEach(function(v) {
                html += '<li class="invoice-item">' + v.nombre + (v.cantidad > 1 ? ' x' + v.cantidad : '') + ' — $' + v.precioUnit + '</li>';
            });
            html += '</ul>';
        }
        html += '<p class="invoice-total">Total: $' + total + '</p><p>Método de pago: <strong>' + metodoPago + '</strong></p>';
        cont.innerHTML = html;
        var overlay = document.getElementById('invoice-overlay');
        overlay.style.display = 'flex';
        overlay.setAttribute('aria-hidden', 'false');
    }

    function closeInvoice() {
        var o = document.getElementById('invoice-overlay');
        o.style.display = 'none';
        o.setAttribute('aria-hidden', 'true');
    }

    function imprimirFactura() {
        actualizarFactura();
        var content = document.getElementById('invoice-content');
        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Factura</title><link rel="stylesheet" href="factura-estilos.css"></head><body><div class="print-content">' + content.innerHTML + '</div></body></html>';
        var w = window.open('', '_blank');
        if (!w) { alert('El navegador bloqueó la ventana de impresión.'); return; }
        w.document.write(html);
        w.document.close();
        w.focus();
        setTimeout(function() { w.print(); setTimeout(function() { try { w.close(); } catch(e){} }, 300); }, 250);
    }

    cargarMenu();
</script>
<link rel="stylesheet" href="../estilos/Accesibilidad.css">
<script src="../js/Accesibilidad.js"></script>

<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                    <img src="../estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
                    <hr>
                    <h3 style="margin: 6px;">El Oriente</h3>
                </div>
                <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
            </div>
        </div>
        <div class="footer-section">
            <h4>Horarios de atención</h4>
            <ul class="footer-horarios">
                <li><span>Lunes – Viernes:</span> <span>3:30 PM – 10:00 PM</span></li>
                <li><span>Sábado:</span> <span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span> <span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>