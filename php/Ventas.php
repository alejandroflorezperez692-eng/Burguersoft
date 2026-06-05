<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'ventas';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Burgersoft - Gestion de Ventas</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css" />
    <link rel="stylesheet" href="../estilos/Estilo-Venta.css" />
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon" />
</head>
<body>

    <?php include __DIR__ . '/../includes/admin_layout.php'; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        
        <!-- HEADER NAV DERECHA -->
        <div class="marcas-header">
            <div>
                <h1>Gestión de Ventas</h1>
                <p class="subtitulo">Ventas realizadas</p>
            </div>
         </div>

        <!-- CAMPOS EN UNA FILA -->
        <div class="form-row" role="form" aria-label="Formulario de gestión de ventas">
            <div class="fila">
                <input type="text" id="cliente" placeholder="Nombre del Cliente" />
            </div>
            <div class="fila">
                <input type="text" id="producto" placeholder="Producto" aria-label="Producto" />
            </div>

            <div >
                <select class = "fila" type="select" id="metodo_pago" placeholder = "Método de pago" aria-label="Método de pago">
                        <option value="">Seleccione el método de pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                </select>
            </div>

            <div class="fila">
                <input type="number" id="cantidad" placeholder="Cantidad" min="1" value="1" />
            </div>
            <div class="fila">
                <input type="number" id="precio" placeholder="Precio" aria-label="Precio" step="500" min="1" />
            </div>
            <div class="fila">
                <input type="datetime-local" id="fecha" placeholder="Fecha y Hora" aria-label="Fecha y Hora" />
            </div>
            
            <button class="save-btn" onclick="guardar()" aria-label="Guardar venta">Guardar</button>
            <button class="save-btn" style="background:#666;" onclick="limpiarCampos()" aria-label="Cancelar edición">Cancelar</button>
        </div>

        <div class="busqueda">
            <input type="text" id="buscar" placeholder="Buscar venta..." aria-label="Buscar venta" />
        </div>

        <!-- TABLA -->
        <table id="tablaVentas" aria-describedby="descTablaVentas">
            <thead>
                <tr>
                    <th scope="col">Cliente</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Valor total</th>
                    <th scope="col">Fecha y Hora</th>
                    <th scope="col">Método de pago</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

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

<script>

    const API = '/burguersoft/controllers'; // URL base del servidor backend
    let editIdVenta     = null; // null = modo crear; con ID = modo editar venta existente
    let ventasGlobal    = [];   // Copia local de todas las ventas para filtrar sin ir al servidor
    let productosGlobal = [];   // Copia local de productos para el select del formulario

    //  Utilidad: fecha actual en formato compatible con datetime-local 
    function fechaActual() {
        const ahora = new Date();
        // getTimezoneOffset devuelve la diferencia en minutos; se ajusta para mostrar hora local
        ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
        return ahora.toISOString().slice(0, 16); // Recorta a "YYYY-MM-DDTHH:MM"
    }

    //  Inicialización al cargar la página 
    window.onload = async () => {
        document.getElementById("fecha").value = fechaActual(); // Fecha por defecto = ahora
        await cargarProductos(); // Primero carga los productos (para el select)
        listarVentas();          // Luego carga el historial de ventas
    };

    // ── Cargar productos en el selector del formulario ────────
    async function cargarProductos() {
        try {
            const res       = await fetch(`${API}/productos.php?accion=productos`);
            productosGlobal = await res.json();
            const sel = document.getElementById("producto");
            sel.innerHTML = '<option value="">Seleccione producto</option>';
            // Genera un <option> por cada producto con su ID como valor
            productosGlobal.forEach(p => {
                sel.innerHTML += `<option value="${p.id_producto}">${p.nombre_producto}</option>`;
            });
        } catch (e) {
            console.error("Error cargando productos:", e);
        }
    }

    // ── Obtener historial de ventas del servidor ──────────────
    async function listarVentas() {
        try {
            const res    = await fetch(`${API}/ventas.php`);
            ventasGlobal = await res.json(); // Guarda la copia local para el buscador
            mostrarTabla(ventasGlobal);      // Dibuja todas las ventas en la tabla
        } catch (e) {
            console.error("Error listando ventas:", e);
        }
    }

    // ── Dibujar filas de la tabla con los datos ───────────────
    function mostrarTabla(datos) {
        const tbody = document.querySelector("#tablaVentas tbody");
        tbody.innerHTML = "";

        if (datos.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:#888;padding:20px;">No hay ventas registradas.</td></tr>`;
            return;
        }

        datos.forEach(v => {
            // substring(0,16) recorta la fecha a "YYYY-MM-DDTHH:MM", replace quita la "T"
            const fecha = v.fecha_venta ? v.fecha_venta.substring(0, 16).replace("T", " ") : "-";
            const fila  = document.createElement("tr");
            fila.innerHTML = `
                <td>${v.nom_cliente || "-"}</td>
                <td>${v.nombre_producto || "-"}</td>
                <td>${v.cantidad_comprada}</td>
                <!-- toLocaleString('es-CO') formatea como $12.000 (formato colombiano) -->
                <td>$${Number(v.valor_total).toLocaleString('es-CO')}</td>
                <td>${fecha}</td>
                <td>${v.metodo_pago || "-"}</td>
                <td>
                    <button class="btn-accion editar"  onclick="prepararEdicion(${v.idventa})">✏️</button>
                    <button class="btn-accion eliminar" onclick="eliminarVenta(${v.idventa})">🗑️</button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    }

    // ── Crear o actualizar una venta ──────────────────────────
    async function guardar() {
        const cliente  = document.getElementById("cliente").value.trim();
        const idProd   = document.getElementById("producto").value;
        const cantidad = parseFloat(document.getElementById("cantidad").value) || 0;
        const precio   = parseFloat(document.getElementById("precio").value)   || 0;
        const fecha    = document.getElementById("fecha").value;
        const metodo   = document.getElementById("metodo_pago").value;

        if (!cliente || !idProd || cantidad <= 0 || precio <= 0 || !metodo) {
            return alert("Por favor completa todos los campos.");
        }

        const total = cantidad * precio; // Calcula el total automáticamente
        const body  = {
            nom_cliente:       cliente,
            id_producto:       idProd,
            cantidad_comprada: cantidad,
            valor_total:       total,   // Se calcula aquí, no en el servidor
            fecha_venta:       fecha,
            metodo_pago:       metodo
        };

        try {
            // Si hay editIdVenta usa PUT con el ID; si no, usa POST para crear nuevo
            const url    = editIdVenta ? `${API}/ventas.php?id=${editIdVenta}` : `${API}/ventas.php`;
            const method = editIdVenta ? 'PUT' : 'POST';
            const res    = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });
            const data = await res.json();
            if (data.success || data.idventa) {
                limpiarCampos(); // Resetea el formulario
                listarVentas();  // Recarga la tabla con el nuevo/actualizado registro
            } else {
                alert("Error: " + (data.error || "No se pudo guardar"));
            }
        } catch (e) {
            alert("Error de conexión: " + e.message);
        }
    }

    // ── Eliminar una venta ────────────────────────────────────
    async function eliminarVenta(id) {
        if (!confirm("¿Eliminar esta venta?")) return;
        try {
            const res  = await fetch(`${API}/ventas.php?id=${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) listarVentas(); // Recarga sin la venta eliminada
            else alert("Error al eliminar: " + (data.error || ""));
        } catch (e) {
            alert("Error de conexión");
        }
    }

    // ── Cargar venta en el formulario para editarla ───────────
    function prepararEdicion(id) {
        const v = ventasGlobal.find(x => x.idventa === id); // Busca la venta en el array local
        if (!v) return;
        editIdVenta = id; // Activa el modo edición

        // Rellena todos los campos del formulario con los datos de la venta
        document.getElementById("cliente").value     = v.nom_cliente || "";
        document.getElementById("producto").value    = v.id_producto || "";
        document.getElementById("cantidad").value    = v.cantidad_comprada;
        // Calcula el precio unitario dividiendo total / cantidad
        document.getElementById("precio").value      = v.valor_total / v.cantidad_comprada;
        document.getElementById("fecha").value       = v.fecha_venta ? v.fecha_venta.substring(0, 16) : "";
        document.getElementById("metodo_pago").value = v.metodo_pago || "";

        document.querySelector(".save-btn").textContent = "Actualizar"; // Cambia texto del botón
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Sube al formulario suavemente
    }

    // ── Limpiar formulario y volver a modo creación ───────────
    function limpiarCampos() {
        editIdVenta = null; // Vuelve a modo crear
        document.getElementById("cliente").value     = "";
        document.getElementById("producto").value    = "";
        document.getElementById("cantidad").value    = "1";
        document.getElementById("precio").value      = "";
        document.getElementById("metodo_pago").value = "";
        document.getElementById("fecha").value       = fechaActual();
        document.querySelector(".save-btn").textContent = "Guardar";
    }

    // ── Buscador reactivo ─────────────────────────────────────
    // Se ejecuta cada vez que el usuario escribe; filtra sobre el array local
    document.getElementById("buscar").addEventListener("input", (e) => {
        const txt = e.target.value.toLowerCase().trim();
        const filtrados = ventasGlobal.filter(v =>
            (v.nom_cliente     || "").toLowerCase().includes(txt) || // Busca por cliente
            (v.nombre_producto || "").toLowerCase().includes(txt) || // O por producto
            (v.metodo_pago     || "").toLowerCase().includes(txt)    // O por método de pago
        );
        mostrarTabla(filtrados); // Actualiza la tabla con los resultados del filtro
    });
</script>

<script>
    // Sidebar: Personalización del nombre de usuario desde localStorage 
    (function() {
        const u = JSON.parse(localStorage.getItem('usuarioActual'));
        if (!u) return;
        const el = document.getElementById('nombre-sidebar');
        if (el) el.textContent = (u.nombre || '') + ' ' + (u.apellido || '');
    })();
</script>
</body>
</html>
