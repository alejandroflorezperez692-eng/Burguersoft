document.addEventListener("DOMContentLoaded", () => {

    const containers = document.querySelectorAll('.carousel-container');

    containers.forEach(container => {

        const slides = Array.from(container.querySelectorAll('.carousel-slide'));
        if (!slides.length) return; 

        let index = slides.findIndex(s => s.classList.contains('active'));
        if (index < 0) index = 0; 

        
        const prev = document.createElement('button');
        prev.className = 'carousel-prev';
        prev.setAttribute('aria-label', 'Anterior'); 
        prev.innerHTML = '&#10094;'; 

        const next = document.createElement('button');
        next.className = 'carousel-next';
        next.setAttribute('aria-label', 'Siguiente');
        next.innerHTML = '&#10095;'; 

        
        container.appendChild(prev);
        container.appendChild(next);

        
        const indicators = document.createElement('div');
        indicators.className = 'carousel-indicators';

        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-indicator';
            dot.setAttribute('data-slide', i); 
            if (i === index) dot.classList.add('active'); 
            indicators.appendChild(dot);
        });
        container.appendChild(indicators);

        const dots = Array.from(indicators.querySelectorAll('.carousel-indicator'));

        function showSlide(i) {
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            index = (i + slides.length) % slides.length;
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
        }

        function nextSlide() { showSlide(index + 1); }
        function prevSlide() { showSlide(index - 1); }

        prev.addEventListener('click', () => { prevSlide(); resetTimer(); });
        next.addEventListener('click', () => { nextSlide(); resetTimer(); });

        dots.forEach(d => d.addEventListener('click', (e) => {
            const i = parseInt(e.currentTarget.getAttribute('data-slide'), 10);
            showSlide(i);
            resetTimer(); 
        }));

        let interval = 3000; 
        let timer = setInterval(nextSlide, interval); 

        
        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, interval);
        }

        
        container.addEventListener('mouseenter', () => clearInterval(timer)); 
        container.addEventListener('mouseleave', () => { resetTimer(); });     
        container.addEventListener('focusin',    () => clearInterval(timer)); 
        container.addEventListener('focusout',   () => { resetTimer(); });    

        
        let touchStartX = 0; 
        let touchEndX   = 0; 

        container.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX; 
            clearInterval(timer); 
        });

        container.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX; 
            const diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 40) {
                if (diff < 0) nextSlide(); 
                else prevSlide();          
            }
            resetTimer(); 
        });

        
        container.tabIndex = 0;
        container.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') { nextSlide(); resetTimer(); } 
            if (e.key === 'ArrowLeft')  { prevSlide(); resetTimer(); } 
        });
    });
});
