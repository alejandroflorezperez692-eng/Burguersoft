// ─── ESTADO GLOBAL ───────────────────────────────────────
var carrito = typeof carrito !== 'undefined' ? carrito : [];
var pedidoRealizado = false;

// ─── CARRITO ─────────────────────────────────────────────
// DESPUÉS:
function agregarAlCarrito(id, nombre, precio, img, tipo) {
    carrito.push({ id, nombre, precio: Number(precio), img, tipo });
    actualizarCarrito();

    // Feedback visual
    const btn = document.querySelector(`.btn-add[onclick*="${id}"]`);
    if (btn) {
        setTimeout(() => {
            btn.textContent = '+';
            btn.style.background = '';
        }, 800);
    }
}

function actualizarCarrito() {
    const badge      = document.getElementById('badge-carrito');
    const cartItems  = document.getElementById('cartItems');
    const cartTotal  = document.getElementById('cartTotal');
    const btnCheckout = document.getElementById('btnCheckout');
    const emptyCart  = document.getElementById('emptyCart');

    if (badge) badge.textContent = carrito.length;
    if (!cartItems) return;

    // Agrupar por nombre
    const map = new Map();
    let total = 0;

    carrito.forEach(item => {
        total += item.precio;
        if (!map.has(item.nombre)) {
            map.set(item.nombre, { ...item, cantidad: 0 });
        }
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
            div.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #eee';
            div.innerHTML = `
                <img src="${item.img}"
                     style="width:50px;height:50px;object-fit:cover;border-radius:8px"
                     onerror="this.src='/burguersoft/estilos/img/placeholder.png'">
                <div style="flex:1">
                    <div style="font-weight:600;font-size:13px">${item.nombre}</div>
                    <div style="font-size:12px;color:#888">
                        x${item.cantidad} — $${(item.precio * item.cantidad).toLocaleString('es-CO')}
                    </div>
                </div>
                <button onclick="quitarDelCarrito('${item.nombre}')"
                    style="background:none;border:none;color:#e63946;font-size:18px;cursor:pointer;padding:4px">✕</button>
            `;
            cartItems.appendChild(div);
        }
    }

    if (cartTotal) cartTotal.textContent = '$' + total.toLocaleString('es-CO');
}

function quitarDelCarrito(nombre) {
    const idx = carrito.findIndex(i => i.nombre === nombre);
    if (idx !== -1) carrito.splice(idx, 1);
    actualizarCarrito();
}

function vaciarCarrito() {
    if (!confirm('¿Vaciar el carrito?')) return;
    carrito = [];
    actualizarCarrito();
}

// ─── PEDIDO / WHATSAPP ───────────────────────────────────
function enviarPedido() {
    if (carrito.length === 0) { alert('El carrito está vacío'); return; }

    const map = new Map();
    let total = 0;
    carrito.forEach(item => {
        total += item.precio;
        if (!map.has(item.nombre)) map.set(item.nombre, { ...item, cantidad: 0 });
        map.get(item.nombre).cantidad += 1;
    });

    const metodoPago = document.getElementById('pago')?.value || '-';
    let mensaje = ' Pedido BurgerSoft%0A%0A';
    for (const [, v] of map.entries()) {
        mensaje += `• ${v.nombre} x${v.cantidad} — $${(v.precio * v.cantidad).toLocaleString('es-CO')}%0A`;
    }
    mensaje += `%0A*TOTAL:* $${total.toLocaleString('es-CO')}`;
    mensaje += `%0A*Método de pago:* ${metodoPago}`;

    window.open('https://wa.me/573224548294?text=' + mensaje);
    pedidoRealizado = true;

    const btn = document.getElementById('btn-ver-factura');
    if (btn) btn.style.display = 'block';
}

function mostrarFactura() {
    if (typeof actualizarFactura === 'function') actualizarFactura();
}

// ─── INGREDIENTES (menú antiguo) ─────────────────────────
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