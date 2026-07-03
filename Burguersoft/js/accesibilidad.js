let fontSizeFactor = parseFloat(localStorage.getItem('acc_factor')) || 1.0;
let panel, body;

document.addEventListener('DOMContentLoaded', function () {
    panel = document.getElementById('accPanel');
    body  = document.body;

    if (localStorage.getItem('acc_tema') === 'oscuro')  body.classList.add('dark-mode');
    if (localStorage.getItem('acc_cursor') === 'grande') body.classList.add('big-cursor');

    const fontGuardada = localStorage.getItem('acc_font');
    if (fontGuardada) body.style.fontFamily = fontGuardada;

    if (fontSizeFactor !== 1.0) aplicarEscala(fontSizeFactor);

    
    document.addEventListener('click', function (e) {
        if (!panel) return;
        if (panel.classList.contains('open') &&
            !panel.contains(e.target) &&
            !e.target.closest('#accFab')) {
            panel.classList.remove('open');
        }
    });
});


function togglePanel() {
    if (!panel) panel = document.getElementById('accPanel');
    panel.classList.toggle('open');
}


function cambiarFuente(direccion) {
    let nuevoFactor = Math.round((fontSizeFactor + direccion * 0.1) * 10) / 10;
    if (nuevoFactor >= 0.8 && nuevoFactor <= 2.0) {
        fontSizeFactor = nuevoFactor;
        aplicarEscala(fontSizeFactor);
        localStorage.setItem('acc_factor', fontSizeFactor);
    }
}

function aplicarEscala(factor) {
    const elementos = document.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span, a, li, button, label, b, i, strong, td, th');
    elementos.forEach(el => {
        if (!el.dataset.origSize) {
            el.dataset.origSize = window.getComputedStyle(el).fontSize;
        }
        el.style.setProperty('font-size', (parseFloat(el.dataset.origSize) * factor) + 'px', 'important');
    });
}

function setTema(modo) {
    if (!body) body = document.body;
    if (modo === 'oscuro') {
        body.classList.add('dark-mode');
        localStorage.setItem('acc_tema', 'oscuro');
    } else {
        body.classList.remove('dark-mode');
        localStorage.setItem('acc_tema', 'claro');
    }
}

function setCursor(tipo) {
    if (!body) body = document.body;
    if (tipo === 'grande') {
        body.classList.add('big-cursor');
        localStorage.setItem('acc_cursor', 'grande');
    } else {
        body.classList.remove('big-cursor');
        localStorage.setItem('acc_cursor', 'normal');
    }
}


function aplicarFuente(fuente) {
    if (!body) body = document.body;
    body.style.fontFamily = fuente;
    localStorage.setItem('acc_font', fuente);
}


function restablecer() {
    localStorage.clear();
    location.reload();
}


function soloLetras(input) {
    input.value = input.value.replace(/[^a-záéíóúüñA-ZÁÉÍÓÚÜÑ\s]/g, "");
}
function soloNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, "");
}