function cargarCarritoGuardado() {
    try {
        const guardado = localStorage.getItem('burguersoft_carrito');
        return guardado ? JSON.parse(guardado) : [];
    } catch (e) {
        return [];
    }
}

function guardarCarrito() {
    try {
        localStorage.setItem('burguersoft_carrito', JSON.stringify(carrito));
    } catch (e) {
        console.error('No se pudo guardar el carrito', e);
    }
}

var carrito = cargarCarritoGuardado();
var pedidoRealizado = false;

document.addEventListener('DOMContentLoaded', actualizarCarrito);

function agregarAlCarrito(id, nombre, precio, img, tipo, btnElement) {
    carrito.push({ id, nombre, precio: Number(precio), img, tipo });
    guardarCarrito();
    actualizarCarrito();

    if (btnElement) {
        btnElement.textContent = '✓';
        btnElement.style.background = '#27ae60';
        setTimeout(() => {
            btnElement.textContent = '+';
            btnElement.style.background = '';
        }, 800);
    }
}
function actualizarCarrito() {
    const badge       = document.getElementById('badge-carrito');
    const cartItems   = document.getElementById('cartItems');
    const cartTotal   = document.getElementById('cartTotal');
    const btnCheckout = document.getElementById('btnCheckout');
    const emptyCart   = document.getElementById('emptyCart');

    if (badge) badge.textContent = carrito.length;
    if (!cartItems) return;

    const map = new Map();
    let total = 0;

    carrito.forEach(item => {
        total += item.precio;
        if (!map.has(item.nombre)) map.set(item.nombre, { ...item, cantidad: 0 });
        map.get(item.nombre).cantidad += 1;
    });

    cartItems.innerHTML = '';

    if (map.size === 0) {
        if (emptyCart) {
            emptyCart.style.display = '';
            cartItems.appendChild(emptyCart);
        }
        if (btnCheckout) btnCheckout.disabled = true;

    } else {

        if (emptyCart) emptyCart.style.display = 'none';
        if (btnCheckout) btnCheckout.disabled = false;

        for (const [, item] of map.entries()) {

            const div = document.createElement('div');
            div.className = "cart-item";

            const descripcion = item.nota && item.nota.trim()
                ? item.nota
                : (item.tipo === 'promocion' ? 'Combo promocional' : 'Producto individual');

            div.innerHTML = `
                <img class="cart-img"
                    src="${item.img}"
                    onerror="this.src='/burguersoft/estilos/img/placeholder.png'">

                <div class="cart-info">

                    <div class="cart-top">
                        <span class="cart-name">${item.nombre}</span>
                        <span class="cart-price">
                            $${(item.precio * item.cantidad).toLocaleString('es-CO')}
                        </span>
                    </div>

                    <div class="cart-desc">
                        ${descripcion}
                    </div>

                    <div class="cart-bottom">
                        <div class="cart-actions-text">
                            <button class="cart-delete"
                                onclick="quitarDelCarrito('${item.nombre}')">
                                Eliminar
                            </button>
                            <span class="cart-sep">|</span>
                            <button class="cart-edit"
                                onclick="editarProducto('${item.nombre}')">
                                Editar
                            </button>
                        </div>

                        <div class="cart-qty">
                            <button class="cart-qty-btn" onclick="disminuirCantidad('${item.nombre}')">−</button>
                            <span class="cart-qty-num">${item.cantidad}</span>
                            <button class="cart-qty-btn" onclick="aumentarCantidad('${item.nombre}')">+</button>
                        </div>
                    </div>

                </div>
            `;

            cartItems.appendChild(div);
        }
    }

    if (cartTotal) {
        cartTotal.textContent = '$' + total.toLocaleString('es-CO');
    }
}

function aumentarCantidad(nombre) {
    const base = carrito.find(i => i.nombre === nombre);
    if (base) {
        carrito.push({ ...base });
        guardarCarrito();
        actualizarCarrito();
    }
}

function disminuirCantidad(nombre) {
    const idx = carrito.findIndex(i => i.nombre === nombre);
    if (idx !== -1) {
        carrito.splice(idx, 1);
        guardarCarrito();
        actualizarCarrito();
    }
}

function quitarDelCarrito(nombre) {
    carrito = carrito.filter(i => i.nombre !== nombre);
    guardarCarrito();
    actualizarCarrito();
}

function editarProducto(nombre) {
    const actual = carrito.find(i => i.nombre === nombre);
    const notaActual = actual?.nota || '';

    const nuevaNota = prompt(
        `Observaciones para "${nombre}" (ej: sin cebolla, extra salsa):`,
        notaActual
    );

    if (nuevaNota === null) return; // canceló

    carrito.forEach(item => {
        if (item.nombre === nombre) item.nota = nuevaNota.trim();
    });

    guardarCarrito();
    actualizarCarrito();
}
function quitarDelCarrito(nombre) {
    const idx = carrito.findIndex(i => i.nombre === nombre);
    if (idx !== -1) carrito.splice(idx, 1);
    guardarCarrito();
    actualizarCarrito();
}

function vaciarCarrito() {
    if (!confirm('¿Vaciar el carrito?')) return;
    carrito = [];
    guardarCarrito();
    actualizarCarrito();
}


let ultimaVentaId = null;

async function enviarPedido(datos) {
    if (carrito.length === 0) return;

    const metodoPago = (datos?.pago || '').trim();
    if (!metodoPago) return;

    const mapProductos = new Map();
    const mapPromos    = new Map();

    carrito.forEach(item => {
        const destino = item.tipo === 'promocion' ? mapPromos : mapProductos;
        if (!destino.has(item.id)) destino.set(item.id, { ...item, cantidad: 0 });
        destino.get(item.id).cantidad += 1;
    });

    const items = Array.from(mapProductos.values()).map(item => ({
        producto_id:     item.id,
        cantidad:        item.cantidad,
        precio_unitario: item.precio
    }));

    const promociones = [];
    mapPromos.forEach(item => {
        for (let i = 0; i < item.cantidad; i++) {
            promociones.push({ promocion_id: item.id, precio: item.precio });
        }
    });

    const subtotal = items.reduce((s, it) => s + (it.precio_unitario * it.cantidad), 0)
                   + promociones.reduce((s, p) => s + p.precio, 0);

    const btnCheckout = document.getElementById('btnCheckout');
    if (btnCheckout) { btnCheckout.disabled = true; btnCheckout.textContent = 'Procesando...'; }

    try {
        const res = await fetch('/burguersoft/controllers/ventas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                metodo_pago: metodoPago,
                items,
                promociones
            })
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.success) {
            alert('No se pudo registrar la venta: ' + (data.error || 'Error desconocido'));
            return;
        }

        ultimaVentaId   = data.venta_id;
        pedidoRealizado = true;

        const todosLosItems = [...mapProductos.values(), ...mapPromos.values()];
        const mensajeWa     = construirMensajeWhatsapp(todosLosItems, subtotal, datos);
        window.open('https://wa.me/573224548294?text=' + mensajeWa);

        carrito = [];
        guardarCarrito();
        actualizarCarrito();

        alert('¡Compra confirmada! Tu pedido #' + ultimaVentaId + ' fue registrado.');
    } catch (e) {
        console.error('Error al enviar el pedido', e);
        alert('Error de conexión al confirmar la compra. Intenta de nuevo.');
    } finally {
        if (btnCheckout) {
            btnCheckout.disabled  = carrito.length === 0;
            btnCheckout.textContent = 'Finalizar Compra';
        }
    }
}

function construirMensajeWhatsapp(items, subtotal, datos) {
    let msg = 'Pedido BurgerSoft%0A%0A';

    items.forEach(v => {
        msg += `• ${v.nombre} x${v.cantidad} — $${(v.precio * v.cantidad).toLocaleString('es-CO')}%0A`;
    });

    msg += `%0A*TOTAL:* $${subtotal.toLocaleString('es-CO')}`;
    msg += `%0A*Pago:* ${datos.pago}`;

    if (datos.modo === 'domicilio') {
        msg += `%0A*Entrega:* Domicilio`;
        msg += `%0A*Dirección:* ${datos.dir || ''}`;
        if (datos.notas) msg += `%0A*Indicaciones:* ${datos.notas}`;
        msg += `%0A*Tel:* ${datos.tel || ''}`;
        msg += `%0A*Nombre:* ${datos.nombre || ''}`;
    } else if (datos.modo === 'restaurante') {
        msg += `%0A*Entrega:* Restaurante`;
        msg += `%0A*Mesa:* ${datos.mesa || ''}`;
        msg += `%0A*Nombre:* ${datos.nombre || ''}`;
    } else {
        msg += `%0A*Entrega:* Para llevar`;
        msg += `%0A*Nombre:* ${datos.nombre || ''}`;
        if (datos.tel) msg += `%0A*Tel:* ${datos.tel}`;
    }

    return msg;
}

function mostrarFactura() {
    if (typeof actualizarFactura === 'function') actualizarFactura();
}


const ingredientesDB = {
    hamburguesa: {
        Sencilla: "Pan artesanal, carne 120g, queso, lechuga, tomate y salsas.",
        Doble:    "Pan artesanal, doble carne 240g, doble queso, lechuga, tomate y salsas.",
        Especial: "Carne 150g, queso, tocineta, jamón, huevo, lechuga, tomate y salsas.",
        Pollo:    "Pechuga de pollo apanada, queso, lechuga, tomate y salsas."
    },
    perros: {
        Sencillo:  "Salchicha, pan perro, ripio de papa, salsas.",
        Especial:  "Salchicha, pollo desmechado, queso, ripio, salsas.",
        Americano: "Salchicha americana, queso, cebolla grill, tocineta y salsas."
    },
    bebidas: {
        CocaCola:  "Bebida gaseosa sabor cola.",
        Agua:      "Agua potable.",
        JugoFresa: "Jugo natural de fresa."
    }
};

function hoverIngredientes(categoria, elemento) {
    const item = elemento.getAttribute('data-item');
    const caja = elemento.parentElement.parentElement.querySelector('.ingredientes-box');
    if (ingredientesDB[categoria]?.[item] && caja) {
        caja.style.display = 'block';
        caja.innerHTML = `<strong>Ingredientes:</strong><br>${ingredientesDB[categoria][item]}`;
    }
}

function clickIngredientes(categoria, elemento) {
    hoverIngredientes(categoria, elemento);
}

function mostrarSubmenu(id) {
    document.querySelectorAll('.submenu').forEach(sm => sm.style.display = 'none');
    const submenu = document.getElementById('submenu-' + id);
    if (submenu) submenu.style.display = 'block';
}

function volverSubmenu(id) {
    const submenu = document.getElementById('submenu-' + id);
    if (submenu) submenu.style.display = 'none';
}

