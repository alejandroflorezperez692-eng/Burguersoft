<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
$paginaActiva = 'oriente';
?><!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - El Oriente</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <script src="../js/Hero-Carrusel.js" defer></script>
</head><body>
<?php include __DIR__ . '/../includes/header_publico.php'; ?>
<section class="hero"><div class="carousel-container">
    <div class="carousel-slide active" style="background-image:url('https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg');"></div>
    <div class="carousel-slide" style="background-image:url('https://ranchera.com.co/wp-content/uploads/2022/11/perro-colombiano-1.jpg');"></div>
    <div class="carousel-slide" style="background-image:url('https://chefstv.net/wp-content/uploads/2024/03/0045-empanadas-saltenas-fritas-wide-web.webp');"></div>
    <div class="carousel-slide" style="background-image:url('https://www.elespectador.com/resizer/v2/4YMEEW2QBVGALOUC7LSPUFNKMU.jpg?auth=1913090d3e141e8a3ccce35509259201363e9dddf853024e2f30ac71ce6383a9&width=1110&height=739&smart=true&quality=60');"></div>
</div></section>
<section class="nosotros-section">
    <div class="nosotros-content">
        <h2>Sobre Nosotros</h2><br>
        <p>En Comidas Rápidas El Oriente llevamos 14 años compartiendo sabor y tradición con Sogamoso. Desde nuestro inicio, hemos trabajado con pasión para ofrecer platos deliciosos, preparados con ingredientes frescos y un toque único que nos ha convertido en un referente de las comidas rápidas en la ciudad.</p>
        <p>Somos un lugar donde la buena atención, las porciones generosas y el ambiente familiar hacen que cada visita sea especial.</p>
    </div>
    <div class="nosotros-img"><img src="../estilos/img/Local.png" alt="Nuestro Local" class="nosotros-img"></div>
</section>
<section class="nosotros-ubicacion">
    <h2 class="section-title">¿DÓNDE NOS ENCONTRAMOS?</h2><br>
    <p class="section-text">Encuéntranos en el corazón de Sogamoso. Ven y disfruta de nuestros deliciosos platos en un ambiente acogedor y familiar.</p><br>
    <p class="section-text">Calle 9a #1a-47</p>
    <p class="section-text">Barrio el Oriente</p>
</section>
<section class="map-content">
    <iframe class="nosotros-map" src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d997.0558698401526!2d-72.92006585457908!3d5.708015928459993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1ses-419!2sco!4v1764791648761!5m2!1ses-419!2sco" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>
<div class="acc-panel" id="accPanel">
    <div class="acc-panel-title">Accesibilidad</div>
    <div class="acc-row">
        <div class="acc-row-label">Tema</div>
        <div class="acc-row-btns">
            <button class="acc_tema" onclick="setTema('claro')">Claro</button>
            <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
        </div>
    </div>
    <div class="acc-row">
        <div class="acc-row-label">Tamaño de letra</div>
        <div class="acc-row-btns">
            <button class="acc-btn-option" onclick="cambiarFuente(-1)">A−</button>
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
<button class="acc-fab" id="accFab" onclick="togglePanel()">
    <img style="width:22px;height:22px;filter:invert(1);pointer-events:none;" src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>
<link rel="stylesheet" href="../estilos/accesibilidad.css">
<script src="../js/accesibilidad.js"></script>
  <footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style =" display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                    <img src="../estilos/img/icono.png" alt="Logo de El Oriente" class="footer-logo">
                    <hr>
                    <h3 style="margin: 6px;">El Oriente</h3>
                </div>
                <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
            </div>
        </div>

        <div class="footer-section">
            <h4>Horarios de atención</h4>
            <ul class="footer-horarios">
                <li><span>Lunes – Viernes:</span> <span>3:30 PM – 10:00 PM</span></li>
                <li><span>Sábado:</span> <span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span> <span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>
</body></html>
