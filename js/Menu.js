const ingredientesDB = {
    hamburguesa: {
        Sencilla: "Pan artesanal, carne 120g, queso, lechuga, tomate y salsas.",
        Doble:    "Pan artesanal, doble carne 240g, doble queso, lechuga, tomate y salsas.",
        Especial: "Carne 150g, queso, tocineta, jamón, huevo, lechuga, tomate y salsas.",
        Pollo:    "Pechuga de pollo apanada, queso, lechuga, tomate y salsas."
    },
    perros: {
        Sencillo:   "Salchicha, pan perro, ripio de papa, salsas.",
        Especial:   "Salchicha, pollo desmechado, queso, ripio, salsas.",
        Americano:  "Salchicha americana, queso, cebolla grill, tocineta y salsas."
    },
    bebidas: {
        CocaCola: "Bebida gaseosa sabor cola.",
        Agua:     "Agua potable.",
        JugoFresa:"Jugo natural de fresa."
        // ... etc
    }
};


function hoverIngredientes(categoria, elemento) {
    const item = elemento.getAttribute("data-item"); 
    const caja = elemento.parentElement.parentElement.querySelector(".ingredientes-box");

    if (ingredientesDB[categoria] && ingredientesDB[categoria][item]) {
        caja.style.display = "block"; 
        caja.innerHTML = `<strong>Ingredientes:</strong><br>${ingredientesDB[categoria][item]}`;
    }
}


function clickIngredientes(categoria, elemento) {
    const item = elemento.getAttribute("data-item");
    const caja = elemento.parentElement.parentElement.querySelector(".ingredientes-box");
    caja.style.display = "block";
    if (ingredientesDB[categoria] && ingredientesDB[categoria][item]) {
        caja.innerHTML = `<strong>Ingredientes:</strong><br>${ingredientesDB[categoria][item]}`;
    }
}


function mostrarSubmenu(id) {
  
    document.querySelectorAll(".submenu").forEach(sm => sm.style.display = "none");

    const submenu = document.getElementById("submenu-" + id); 
    if (submenu) submenu.style.display = "block";           
}


function volverSubmenu(id) {
    const submenu = document.getElementById("submenu-" + id);
    if (submenu) submenu.style.display = "none";
}

let pedidoRealizado = false; 
let carrito = [];            

function agregarAlCarrito(nombre, precio) {
    carrito.push({ nombre, precio }); 
    actualizarCarrito();               
}


function actualizarCarrito() {
    const lista = document.getElementById("lista-carrito");
    lista.innerHTML = ""; 

    
    const map = new Map();
    let total = 0;

    carrito.forEach(item => {
        total += Number(item.precio || 0); 
        const key = item.nombre || 'Item';
        if (!map.has(key)) map.set(key, { nombre: key, cantidad: 0, precioUnit: Number(item.precio || 0) });
        map.get(key).cantidad += 1;
    });

    for (const [k, v] of map.entries()) {
        const qtyLabel = v.cantidad > 1 ? ` x${v.cantidad}` : ''; 
        const li = document.createElement("li");
        li.textContent = `${v.nombre}${qtyLabel} — $${v.precioUnit}`;
        lista.appendChild(li);
    }

    document.getElementById("total").textContent = "Total: $" + total;
}
function vaciarCarrito() {
    carrito = [];           
    actualizarCarrito();     
}


function enviarPedido() {
    if (carrito.length === 0) {
        alert("El carrito está vacío");
        return;
    }

    
    const map = new Map();
    let total = 0;
    carrito.forEach(item => {
        total += Number(item.precio || 0);
        const key = item.nombre || 'Item';
        if (!map.has(key)) map.set(key, { nombre: key, cantidad: 0, precioUnit: Number(item.precio || 0) });
        map.get(key).cantidad += 1;
    });

    const metodoPago = document.getElementById("pago") ? document.getElementById("pago").value : '-';

    let mensaje = "🛒 Pedido BurgerSoft%0A%0A";
    for (const [k, v] of map.entries()) {
        const qtyLabel = v.cantidad > 1 ? ` x${v.cantidad}` : '';
        mensaje += `• ${v.nombre}${qtyLabel} — $${v.precioUnit}%0A`;
    }
    mensaje += `%0A*TOTAL:* $${total}`;
    mensaje += `%0A*Método de pago:* ${metodoPago}`;

    const telefono = "573224548294"; 
    window.open("https://wa.me/" + telefono + "?text=" + mensaje);

    pedidoRealizado = true; 

    const btn = document.getElementById("btn-ver-factura");
    if (btn) btn.style.display = "block";
}


function mostrarFactura() {
    actualizarFactura();
}
