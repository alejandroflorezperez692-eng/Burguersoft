// Variables Globales
let fontSizeFactor = parseFloat(localStorage.getItem('acc_factor')) || 1.0;
const panel = document.getElementById('accPanel');
const body  = document.body;

// 1. Inicialización (Cargar preferencias guardadas)
(function init() {
    if (localStorage.getItem('acc_tema') === 'oscuro') {
        body.classList.add('dark-mode');
    }
    if (localStorage.getItem('acc_cursor') === 'grande') {
        body.classList.add('big-cursor');
    }
    // Aplicar escala de fuente si existe
    if (fontSizeFactor !== 1.0) {
        window.addEventListener('DOMContentLoaded', () => aplicarEscala(fontSizeFactor));
    }
})();

// 2. Funciones del Panel
function togglePanel() {
    panel.classList.toggle('activo');
}

document.addEventListener('click', function(e) {
    if (panel.classList.contains('activo') && !panel.contains(e.target) && !e.target.closest('#accFab')) {
        panel.classList.remove('activo');
    }
});

// 3. Lógica de Tamaño de Fuente (Aumentar/Disminuir)
function cambiarFuente(direccion) {
    // direccion es 1 para subir, -1 para bajar
    let nuevoFactor = fontSizeFactor + (direccion * 0.1);

    // Límites de seguridad (80% a 200%)
    if (nuevoFactor >= 0.8 && nuevoFactor <= 2.0) {
        fontSizeFactor = nuevoFactor;
        aplicarEscala(fontSizeFactor);
        localStorage.setItem('acc_factor', fontSizeFactor);
    }
}

function aplicarEscala(factor) {
    // Seleccionamos todas las etiquetas de texto
    const elementos = document.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span, a, li, button, label, b, i, strong, td, th');

    elementos.forEach(el => {
        // Guardamos el tamaño original solo la primera vez
        if (!el.dataset.origSize) {
            el.dataset.origSize = window.getComputedStyle(el).fontSize;
        }
        const tamanoBase = parseFloat(el.dataset.origSize);
        const nuevoTamano = (tamanoBase * factor) + 'px';
        el.style.setProperty('font-size', nuevoTamano, 'important');
    });
}

// 4. Tema y Cursor
function setTema(modo) {
    if (modo === 'oscuro') {
        body.classList.add('dark-mode');
        localStorage.setItem('acc_tema', 'oscuro');
    } else {
        body.classList.remove('dark-mode');
        localStorage.setItem('acc_tema', 'claro');
    }
}

function setCursor(tipo) {
    if (tipo === 'grande') {
        body.classList.add('big-cursor');
        localStorage.setItem('acc_cursor', 'grande');
    } else {
        body.classList.remove('big-cursor');
        localStorage.setItem('acc_cursor', 'normal');
    }
}

// 5. Restablecer Todo
function restablecer() {
    localStorage.clear();
    location.reload(); // Recarga la página para limpiar todos los estilos inline y clases
}

// Validaciones de Input (Tus funciones originales)
function soloLetras(input) {
    input.value = input.value.replace(/[^a-záéíóúüñA-ZÁÉÍÓÚÜÑ\s]/g, "");
}
function soloNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, "");
}