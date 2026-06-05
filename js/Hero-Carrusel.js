
// Crea carruseles de imágenes automáticos en las páginas del cliente.
// Soporta múltiples carruseles independientes en la misma página.
// Incluye: autoplay, flechas, indicadores (dots), swipe táctil y teclado

// Espera a que el HTML cargue completamente antes de ejecutar
document.addEventListener("DOMContentLoaded", () => {

    // Busca todos los contenedores de carrusel en la página
    // Permite tener varios carruseles independientes si existen
    const containers = document.querySelectorAll('.carousel-container');

    // Recorre cada contenedor y aplica la lógica del carrusel individualmente
    containers.forEach(container => {

        // Obtiene todos los slides (diapositivas) dentro del contenedor
        const slides = Array.from(container.querySelectorAll('.carousel-slide'));
        if (!slides.length) return; // Si no hay slides, no hace nada y pasa al siguiente

        // Busca el slide que ya tiene la clase 'active' para empezar desde él
        let index = slides.findIndex(s => s.classList.contains('active'));
        if (index < 0) index = 0; // Si ninguno tiene 'active', empieza desde el primero

        // Crear botones de navegación (prev / next) 
        // Se crean dinámicamente en vez de escribirlos en el HTML
        const prev = document.createElement('button');
        prev.className = 'carousel-prev';
        prev.setAttribute('aria-label', 'Anterior'); // Para lectores de pantalla (accesibilidad)
        prev.innerHTML = '&#10094;'; // Flecha izquierda: ‹

        const next = document.createElement('button');
        next.className = 'carousel-next';
        next.setAttribute('aria-label', 'Siguiente');
        next.innerHTML = '&#10095;'; // Flecha derecha: ›

        // Agrega los botones al contenedor para que sean visibles
        container.appendChild(prev);
        container.appendChild(next);

        // Crear indicadores de posición (dots)
        // Son los puntitos que muestran en qué slide estás
        const indicators = document.createElement('div');
        indicators.className = 'carousel-indicators';

        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-indicator';
            dot.setAttribute('data-slide', i); // Guarda el índice del slide que representa
            if (i === index) dot.classList.add('active'); // Marca el dot del slide inicial
            indicators.appendChild(dot);
        });
        container.appendChild(indicators);

        // Array con referencias a todos los dots para actualizarlos fácilmente
        const dots = Array.from(indicators.querySelectorAll('.carousel-indicator'));

        // Función central: cambia el slide visible 
        function showSlide(i) {
            // Quita la clase 'active' de todos los slides y dots
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            // Módulo (%) permite que el índice sea circular: tras el último vuelve al primero
            index = (i + slides.length) % slides.length;
            // Marca el nuevo slide y dot como activos
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
        }

        // Funciones de conveniencia para avanzar o retroceder un slide
        function nextSlide() { showSlide(index + 1); }
        function prevSlide() { showSlide(index - 1); }

        // Al hacer clic en los botones, navega y reinicia el temporizador del autoplay
        prev.addEventListener('click', () => { prevSlide(); resetTimer(); });
        next.addEventListener('click', () => { nextSlide(); resetTimer(); });

        // Al hacer clic en un dot, salta directamente a ese slide
        dots.forEach(d => d.addEventListener('click', (e) => {
            const i = parseInt(e.currentTarget.getAttribute('data-slide'), 10);
            showSlide(i);
            resetTimer(); // Reinicia el autoplay para no saltar demasiado rápido
        }));

        //  Autoplay 
        let interval = 3000; // Cambia de slide cada 3 segundos
        let timer = setInterval(nextSlide, interval); // Guarda el ID para poder cancelarlo

        // Cancela el timer actual y crea uno nuevo para reiniciar la cuenta
        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, interval);
        }

        //  Pausa al interactuar con el ratón o teclado 
        container.addEventListener('mouseenter', () => clearInterval(timer));  // Pausa al pasar el ratón
        container.addEventListener('mouseleave', () => { resetTimer(); });     // Reanuda al salir
        container.addEventListener('focusin',    () => clearInterval(timer)); // Pausa si un hijo recibe foco
        container.addEventListener('focusout',   () => { resetTimer(); });    // Reanuda al perder foco

        //  Soporte táctil (swipe en móvil) 
        let touchStartX = 0; // Posición inicial del dedo al tocar la pantalla
        let touchEndX   = 0; // Posición final del dedo al levantar

        container.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX; // Guarda dónde empezó el toque
            clearInterval(timer); // Pausa el autoplay mientras el usuario interactúa
        });

        container.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX; // Guarda dónde terminó el toque
            const diff = touchEndX - touchStartX;
            // Solo se considera swipe si el movimiento fue mayor a 40px
            if (Math.abs(diff) > 40) {
                if (diff < 0) nextSlide(); // Swipe hacia la izquierda → siguiente
                else prevSlide();          // Swipe hacia la derecha → anterior
            }
            resetTimer(); // Reinicia autoplay tras el swipe
        });

        //  Soporte de teclado (flechas) 
        container.tabIndex = 0; // Hace el contenedor enfocable con la tecla Tab
        container.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') { nextSlide(); resetTimer(); } // Flecha derecha → siguiente
            if (e.key === 'ArrowLeft')  { prevSlide(); resetTimer(); } // Flecha izquierda → anterior
        });
    });
});
