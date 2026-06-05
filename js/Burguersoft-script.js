        
        let index = 0;
        const slides = document.querySelectorAll(".carousel-slide");

        function cambiarSlide() {
            slides[index].classList.remove("active");
            index = (index + 1) % slides.length;
            slides[index].classList.add("active");
        }

document.addEventListener("DOMContentLoaded", function() {
    let index = 0;
    const slides = document.querySelectorAll(".carousel-slide");

    function cambiarSlide(nuevoIndex) {
        slides[index].classList.remove("active");
        index = (nuevoIndex !== undefined) ? nuevoIndex : (index + 1) % slides.length;
        slides[index].classList.add("active");
    }

    setInterval(() => cambiarSlide(), 2000);

    document.addEventListener("keydown", function(e) {
        if (e.key === "ArrowRight") {
            cambiarSlide((index + 1) % slides.length);
        } else if (e.key === "ArrowLeft") {
            cambiarSlide((index - 1 + slides.length) % slides.length);
        }
    });
});

/* Materia Prima Script */
        

