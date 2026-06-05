<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Inicio</title>
    <link rel="icon" href="../estilos/img\icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/Estilos-paginas-clientes.css">
    <script src="../js/Hero-Carrusel.js" defer></script>
</head>

<body>
<?php include __DIR__ . '/../includes/header_publico.php'; ?>

    <section class="hero">
            <div class="carousel-container">
            <div class="carousel-slide active" style="background-image: url('https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('https://ranchera.com.co/wp-content/uploads/2022/11/perro-colombiano-1.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('https://chefstv.net/wp-content/uploads/2024/03/0045-empanadas-saltenas-fritas-wide-web.webp');"></div>
            <div class="carousel-slide" style="background-image: url('https://www.elespectador.com/resizer/v2/4YMEEW2QBVGALOUC7LSPUFNKMU.jpg?auth=1913090d3e141e8a3ccce35509259201363e9dddf853024e2f30ac71ce6383a9&width=1110&height=739&smart=true&quality=60');"></div>
    </section>  
    <section class="promociones">
        <h2>Combos Diarios</h2>
        <p>Disfruta de nuestros combos exclusivos por tiempo limitado. ¡No te los pierdas!</p>
        <div class="productos-grid" id="grid-promociones">
            <p style="padding:20px;color:#888;">Cargando promociones…</p>
        </div>
        <br><br>
    </section>

    
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
</body>
</html>