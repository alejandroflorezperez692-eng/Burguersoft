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
    <style>
     
        #grid-promociones {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 22px;
            padding: 10px 0;
        }

        .promo-card-pub {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,0.10);
            transition: transform 0.25s, box-shadow 0.25s;
            display: flex;
            flex-direction: column;
        }

        .promo-card-pub:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .promo-img-pub {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #f5edd8;
        }

        .promo-img-pub img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .promo-card-pub:hover .promo-img-pub img { transform: scale(1.06); }

        .promo-badge-pub {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #e8821a;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .promo-info-pub {
            padding: 18px 20px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .promo-info-pub h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e0a00;
            margin: 0;
            line-height: 1.3;
        }

        .promo-info-pub p {
            font-size: 13px;
            color: #888;
            margin: 0;
            line-height: 1.5;
        }

        .promo-tags-pub {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 2px;
        }

        .promo-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            background: rgba(232,130,26,0.12);
            border: 1px solid rgba(232,130,26,0.3);
            border-radius: 20px;
            color: #8a4a10;
        }

        .promo-precio-pub {
            font-size: 1.4rem;
            font-weight: 900;
            color: #e8821a;
            margin-top: auto;
            padding-top: 10px;
        }
    </style>
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
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('grid-promociones');
    try {
        const res   = await fetch('../controllers/promociones.php');
        const promos = await res.json();
        if (!promos.length) { grid.innerHTML = '<p style="padding:20px;color:#888;">No hay promociones activas.</p>'; return; }
        grid.innerHTML = promos.map(p => `
            <div class="product-card">
                <div style="position:relative;">
                    <img class="product-image" src="${p.imagen || '../estilos/img/promocion.png'}"
                         alt="${p.nombre_promocion}"
                         onerror="this.src='../estilos/img/promocion.png'">
                    <span style="position:absolute;top:10px;right:10px;background:#F18921;color:#fff;font-size:10px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;padding:3px 10px;border-radius:20px;">PROMO</span>
                </div>
                <div class="product-info">
                    <div class="product-name">${p.nombre_promocion}</div>
                    <p style="font-size:13px;color:#888;margin:0;">${p.descripcion || ''}</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;">
                        <span class="product-price">$${Number(p.precio).toLocaleString('es-CO')}</span>
                        <button style="background:none;border:none;cursor:pointer;padding:4px;" title="Agregar al carrito">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="#F18921" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>`).join('');
    } catch(e) {
        grid.innerHTML = '<p style="padding:20px;color:#888;">No se pudieron cargar las promociones.</p>';
    }
});
</script>
    
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

<script>
(async function cargarPromociones() {
    const grid = document.getElementById('grid-promociones');
    try {
        const res   = await fetch('../controllers/promociones.php');
        const promos = await res.json();

        if (!promos.length) {
            grid.innerHTML = '<p style="padding:20px;color:#888;">No hay promociones disponibles por el momento.</p>';
            return;
        }

        grid.innerHTML = promos.map(p => {
            const imgSrc  = p.imagen || '../estilos/img/promocion.png';
            const prods   = p.productos || [];
            const tagsHtml = prods.length
                ? prods.map(pr => `<span class="promo-tag">${pr.nombre}</span>`).join('')
                : '';

            return `
            <div class="promo-card-pub">
                <div class="promo-img-pub">
                    <img src="${imgSrc}" alt="${p.nombre}"
                         onerror="this.src='../estilos/img/promocion.png'">
                    <span class="promo-badge-pub">PROMO</span>
                </div>
                <div class="promo-info-pub">
                    <h3>${p.nombre}</h3>
                    ${p.descripcion ? `<p>${p.descripcion}</p>` : ''}
                    ${tagsHtml ? `<div class="promo-tags-pub">${tagsHtml}</div>` : ''}
                    <div class="promo-precio-pub">$${Number(p.precio).toLocaleString('es-CO')}</div>
                </div>
            </div>`;
        }).join('');

    } catch (e) {
        grid.innerHTML = '<p style="padding:20px;color:#888;">No se pudo cargar las promociones.</p>';
    }
})();
</script>

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