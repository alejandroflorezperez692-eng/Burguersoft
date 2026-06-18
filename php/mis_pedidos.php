<?php
session_start();
// require_once '../includes/funciones.php'; // Si usas conexiones, también lleva ../

// SIMULACIÓN PARA PRUEBAS:
// null = Muestra la vista limpia de "No tienes pedidos"
// 'En cocina', 'En barra' o 'Entregado' = Muestra el stepper de seguimiento gráfico
$estado_actual = null; 
$numero_orden = "#0842";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Mis Pedidos</title>
    
    <link rel="stylesheet" href="../estilos/estilos-login.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    
    <style>
        body, input, button {
            font-family: 'Lato', sans-serif;
            color: #2c1810;
        }

        .header-bar, h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        .btn-regresar {
            font-family: 'Playfair Display', serif !important;
            font-weight: 700 !important;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            color: #ffffff !important;
        }

        /* --- TIMELINE / STEPPER GRAPHIC --- */
        .tracking-container {
            margin: 25px 0 15px 0;
            padding: 10px 0;
            width: 100%;
        }

        .timeline {
            position: relative;
            display: flex;
            justify-content: space-between;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 17px;
            left: 10px;
            right: 10px;
            height: 4px;
            background: #e0e0e0;
            z-index: 1;
        }

        .timeline-bar {
            position: absolute;
            top: 17px;
            left: 10px;
            height: 4px;
            background: #E8821A;
            z-index: 2;
            transition: width 0.4s ease;
            width: <?php 
                if ($estado_actual === 'En cocina') echo '0%';
                elseif ($estado_actual === 'En barra') echo '50%';
                elseif ($estado_actual === 'Entregado') echo '100%';
                else echo '0%';
            ?>;
        }

        .step {
            position: relative;
            z-index: 3;
            text-align: center;
            flex: 1;
        }

        .step-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e0e0e0;
            margin: 0 auto 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            box-shadow: 0 0 0 4px #faf6f0;
        }

        .step-label {
            font-size: 11px;
            font-weight: 700;
            color: #8c7e7a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step.active .step-icon {
            background: #E8821A;
            box-shadow: 0 0 0 4px #faf6f0, 0 0 10px rgba(232, 130, 26, 0.25);
        }
        
        .step.active .step-label {
            color: #E8821A;
            font-weight: 900;
        }

        .step.completed-success .step-icon {
            background: #27ae60;
            box-shadow: 0 0 0 4px #faf6f0;
        }
        
        .step.completed-success .step-label {
            color: #27ae60;
            font-weight: 900;
        }

        .pedido-info {
            background: #fdfcfb;
            border: 1px solid #eadecc;
            border-radius: 10px;
            padding: 14px;
            margin-top: 20px;
            font-size: 13px;
            text-align: left;
        }
        
        .pedido-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            border-bottom: 1px dashed #f3ebd9;
            padding-bottom: 4px;
        }
        
        .pedido-info div:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        /* --- VISTA ESTADO VACÍO --- */
        .sin-pedidos-contenedor {
            padding: 20px 10px;
            text-align: center;
        }
        .sin-pedidos-icon {
            width: 70px;
            height: auto;
            opacity: 0.6;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <img src="../estilos/img/icono.png" class="logo" alt="Logo Burguersoft">
        <a href="index.php" class="btn-regresar">[ Menú ]</a>
    </div>

    <div class="header-bar">ESTADO DE TU PEDIDO</div>

    <div class="card" style="padding: 24px; max-width: 450px; margin: 40px auto;">
        
        <?php if ($estado_actual !== null): ?>
            <div class="icono" style="margin-bottom: 10px; text-align: center;">
                <img src="../estilos/img/bloquear.png" alt="Pedido" style="width: 48px; height: auto; filter: hue-rotate(15deg);">
            </div>

            <h3 style="margin: 5px 0; font-size: 19px; color: #2c1810; text-align: center;">¡Gracias por tu compra!</h3>
            <p class="descripcion" style="margin-bottom: 10px; font-size: 13px; color: #666; text-align: center;">
                Tu orden está siendo procesada en tiempo real.
            </p>

            <div class="tracking-container">
                <div class="timeline">
                    <div class="timeline-bar"></div>

                    <div class="step <?php echo ($estado_actual === 'En cocina' || $estado_actual === 'En barra' || $estado_actual === 'Entregado') ? 'active' : ''; ?>">
                        <div class="step-icon">1</div>
                        <div class="step-label">En Cocina</div>
                    </div>

                    <div class="step <?php echo ($estado_actual === 'En barra' || $estado_actual === 'Entregado') ? 'active' : ''; ?>">
                        <div class="step-icon">2</div>
                        <div class="step-label">En Barra</div>
                    </div>

                    <div class="step <?php echo ($estado_actual === 'Entregado') ? 'completed-success' : ''; ?>">
                        <div class="step-icon">3</div>
                        <div class="step-label">Entregado</div>
                    </div>
                </div>
            </div>

            <div class="pedido-info">
                <div><strong>Orden:</strong> <span><?php echo $numero_orden; ?></span></div>
                <div><strong>Método:</strong> <span>Para retirar en local</span></div>
                <div>
                    <strong>Estado actual:</strong> 
                    <span style="font-weight: bold; color: <?php echo $estado_actual === 'Entregado' ? '#27ae60' : '#E8821A'; ?>;">
                        <?php 
                            if ($estado_actual === 'En cocina') echo 'Preparando tus hamburguesas';
                            elseif ($estado_actual === 'En barra') echo '¡Listo en barra para retirar!';
                            elseif ($estado_actual === 'Entregado') echo 'Pedido entregado exitosamente';
                        ?>
                    </span>
                </div>
            </div>

            <button type="button" class="btn-primario" style="margin-top: 20px;" onclick="window.location.reload();">
                Actualizar Estado
            </button>

        <?php else: ?>
            <div class="sin-pedidos-contenedor">
                <img src="../estilos/img/bloquear.png" alt="Sin pedidos" class="sin-pedidos-icon" style="filter: grayscale(1) sepia(0.4) contrast(0.8);">
                <h3 style="margin: 5px 0; font-size: 19px; color: #2c1810;">No tienes pedidos activos</h3>
                <p class="descripcion" style="margin-bottom: 25px; font-size: 14px; color: #776e6a; line-height: 1.4;">
                    Parece que aún no has armado tu Combo de hoy o no tienes órdenes pendientes por retirar.
                </p>
                
                <a href="index.php" class="btn-primario" style="text-decoration: none; display: block; line-height: 40px; height: 40px; text-align: center;">
                    Ver el Menú Completo
                </a>
            </div>
        <?php endif; ?>

    </div>

    <div class="acc-panel" id="accPanel">
        <div class="acc-panel-title">Accesibilidad</div>
        <div class="acc-row">
            <div class="acc-row-label">Tema</div>
            <div class="acc-row-btns">
                <button class="acc_tema" onclick="setTema('claro')">Claro</button>
                <button class="acc_tema" onclick="setTema('oscuro')">Oscuro</button>
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
                    <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                        <img src="../estilos/img/icono.png" alt="Logo" class="footer-logo">
                        <hr>
                        <h3 style="margin: 6px; color:#fff;">El Oriente</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BURGUERSOFT - EL ORIENTE.</p>
        </div>
    </footer>
</body>
</html>