
// Maneja el menú estático de la página Menu.html (versión antigua).
// Contiene: base de datos de ingredientes, submenús, carrito de compras
// y envío de pedidos por WhatsApp.
// NOTA: La versión activa del menú para clientes está en "Ir al Menu.html"


// Base de datos de ingredientes 
// Objeto que mapea cada categoría → variante → descripción de ingredientes
// Se consulta cuando el usuario hace hover o clic en un producto
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
    // ... (demás categorías omitidas por brevedad, siguen el mismo patrón)
    bebidas: {
        CocaCola: "Bebida gaseosa sabor cola.",
        Agua:     "Agua potable.",
        JugoFresa:"Jugo natural de fresa."
        // ... etc
    }
};

//  Mostrar ingredientes al hacer hover 
// Busca los ingredientes del producto en el objeto y los muestra en un recuadro
function hoverIngredientes(categoria, elemento) {
    const item = elemento.getAttribute("data-item"); // Lee el nombre del producto desde el HTML
    // Sube dos niveles en el DOM para encontrar el contenedor de ingredientes
    const caja = elemento.parentElement.parentElement.querySelector(".ingredientes-box");

    if (ingredientesDB[categoria] && ingredientesDB[categoria][item]) {
        caja.style.display = "block"; // Muestra el recuadro
        caja.innerHTML = `<strong>Ingredientes:</strong><br>${ingredientesDB[categoria][item]}`;
    }
}

//  Mostrar ingredientes al hacer clic
// Igual que hover pero se activa con clic (útil en móviles sin hover)
function clickIngredientes(categoria, elemento) {
    const item = elemento.getAttribute("data-item");
    const caja = elemento.parentElement.parentElement.querySelector(".ingredientes-box");
    caja.style.display = "block";
    if (ingredientesDB[categoria] && ingredientesDB[categoria][item]) {
        caja.innerHTML = `<strong>Ingredientes:</strong><br>${ingredientesDB[categoria][item]}`;
    }
}

//  Mostrar submenú de una categoría
// Oculta todos los submenús y luego muestra solo el de la categoría seleccionada
function mostrarSubmenu(id) {
    // Primero oculta TODOS los submenús (para que no queden varios abiertos)
    document.querySelectorAll(".submenu").forEach(sm => sm.style.display = "none");

    const submenu = document.getElementById("submenu-" + id); // Busca el submenú por ID dinámico
    if (submenu) submenu.style.display = "block";             // Lo muestra
}

// Ocultar submenú 
// Cierra el submenú de una categoría específica
function volverSubmenu(id) {
    const submenu = document.getElementById("submenu-" + id);
    if (submenu) submenu.style.display = "none";
}

//  Variables del carrito 
let pedidoRealizado = false; // Bandera que indica si ya se realizó un pedido
let carrito = [];            // Array donde se almacenan los productos seleccionados

//  Agregar producto al carrito 
function agregarAlCarrito(nombre, precio) {
    carrito.push({ nombre, precio }); // Agrega el producto como objeto al array
    actualizarCarrito();               // Refresca la vista del carrito
}

//  Actualizar vista del carrito 
// Redibuja la lista del carrito y calcula el total
function actualizarCarrito() {
    const lista = document.getElementById("lista-carrito");
    lista.innerHTML = ""; // Limpia el contenido anterior

    // Usa un Map para agrupar productos iguales y mostrar "Burger x2" en vez de dos filas
    const map = new Map();
    let total = 0;

    carrito.forEach(item => {
        total += Number(item.precio || 0); // Suma el precio al total
        const key = item.nombre || 'Item';
        // Si el producto no está en el map, lo agrega; si ya está, incrementa cantidad
        if (!map.has(key)) map.set(key, { nombre: key, cantidad: 0, precioUnit: Number(item.precio || 0) });
        map.get(key).cantidad += 1;
    });

    // Renderiza cada grupo como un elemento de lista
    for (const [k, v] of map.entries()) {
        const qtyLabel = v.cantidad > 1 ? ` x${v.cantidad}` : ''; // Muestra "x2" solo si hay más de uno
        const li = document.createElement("li");
        li.textContent = `${v.nombre}${qtyLabel} — $${v.precioUnit}`;
        lista.appendChild(li);
    }

    // Actualiza el texto del total en la pantalla
    document.getElementById("total").textContent = "Total: $" + total;
}

//  Vaciar el carrito 
function vaciarCarrito() {
    carrito = [];            // Resetea el array a vacío
    actualizarCarrito();     // Refresca la vista para mostrar carrito vacío
}

// Enviar pedido por WhatsApp 
// Genera un mensaje de texto con los productos y abre WhatsApp
function enviarPedido() {
    if (carrito.length === 0) {
        alert("El carrito está vacío");
        return;
    }

    // Agrupa el carrito por nombre para el mensaje
    const map = new Map();
    let total = 0;
    carrito.forEach(item => {
        total += Number(item.precio || 0);
        const key = item.nombre || 'Item';
        if (!map.has(key)) map.set(key, { nombre: key, cantidad: 0, precioUnit: Number(item.precio || 0) });
        map.get(key).cantidad += 1;
    });

    const metodoPago = document.getElementById("pago") ? document.getElementById("pago").value : '-';

    // Construye el mensaje con formato especial para URL (%0A = salto de línea en URL)
    let mensaje = "🛒 Pedido BurgerSoft%0A%0A";
    for (const [k, v] of map.entries()) {
        const qtyLabel = v.cantidad > 1 ? ` x${v.cantidad}` : '';
        mensaje += `• ${v.nombre}${qtyLabel} — $${v.precioUnit}%0A`;
    }
    mensaje += `%0A*TOTAL:* $${total}`;
    mensaje += `%0A*Método de pago:* ${metodoPago}`;

    const telefono = "573224548294"; // Número del restaurante con código de país
    // Abre WhatsApp Web con el mensaje pre-escrito listo para enviar
    window.open("https://wa.me/" + telefono + "?text=" + mensaje);

    pedidoRealizado = true; // Marca que el pedido fue realizado

    // Muestra el botón de "Ver factura" si existe en el HTML
    const btn = document.getElementById("btn-ver-factura");
    if (btn) btn.style.display = "block";
}

// Mostrar factura 
// Recalcula la factura con el estado actual del carrito antes de mostrarla
function mostrarFactura() {
    actualizarFactura();
}
