<?php
session_start();
// require_once 'includes/funciones.php'; // Tu archivo de conexión y funciones

// SIMULACIÓN: En un entorno real, tomarías el ID del pedido de la URL o de la sesión,
// y harías una consulta SQL a tu base de datos:
// $id_pedido = $_GET['id'] ?? $_SESSION['ultimo_pedido'];
// $pedido = obtenerEstadoPedido($id_pedido);
// $estado_actual = $pedido['estado']; 

// Estados posibles en tu base de datos: 'cocina', 'barra', 'entregado'
$estado_actual = 'barra'; // Cambia esto a 'cocina' o 'entregado' para probar los cambios visuales
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT - Estado de tu Pedido</title>
    <link rel="stylesheet" href="estilos/estilos-login.css">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            color: #2c1810;
            background-color: #faf6f0;
        }
        .header-bar {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        /* Contenedor Principal Track */
        .tracking-container {
            margin: 20px 0;
            padding: 10px 0;
            text-align: left;
        }

        /* La Línea de Progreso (Timeline) */
        .timeline {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        /* Línea gris de fondo */
        .timeline::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 5px;
            right: 5px;
            height: 4px;
            background: #e0e0e0;
            z-index: 1;
        }

        /* Barra de progreso de color naranja que avanza según el estado */
        .timeline-bar {
            position: absolute;
            top: 15px;
            left: 5px;
            height: 4px;
            background: #E8821A;
            z-index: 2;
            transition: width 0.4s ease;
            width: <?php 
                if ($estado_actual === 'cocina') echo '0%';
                elseif ($estado_actual === 'barra') echo '50%';
                elseif ($estado_actual === 'entregado') echo '100%';
                else echo '0%';
            ?>;
        }

        /* Pasos o Nodos de la línea */
        .step {
            position: relative;
            z-index: 3;
            text-align: center;
            flex: 1;
        }

        /* Círculos indicadores */
        .step-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e0e0e0;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 0 0 4px #faf6f0;
        }

        /* Textos explicativos debajo de cada círculo */
        .step-label {
            font-size: 12px;
            font-weight: 700;
            color: #8c7e7a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- CLASES DE ESTADO ACTIVO/COMPLETADO --- */
        
        /* Estado: Ya pasó por aquí o está aquí (Naranja) */
        .step.active .step-icon {
            background: #E8821A;
            box-shadow: 0 0 0 4px #faf6f0, 0 0 10px rgba(232, 130, 26, 0.3);
        }
        .step.active .step-label {
            color: #E8821A;
            font-weight: 900;
        }

        /* Estado Final: Entregado con éxito (Verde) */
        .step.completed-success .step-icon {
            background: #27ae60;
        }
        .step.completed-success .step-label {
            color: #27ae60;
            font-weight: 900;
        }

        /* Detalle informativo del pedido */
        .pedido-info {
            background: #fdfcfb;
            border: 1px solid #eadecc;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
            font-size: 13px;
        }
        .pedido-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .pedido-info div:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <img src="estilos/img/icono.png" class="logo" alt="Logo">
        <a href="index.php" class="btn-regresar">[ Menú ]</a>
    </div>

    <div class="header-bar">ESTADO DE TU PEDIDO</div>

    <div class="card" style="padding: 22px; max-width: 450px;">
        <div class="icono" style="margin-bottom: 5px;">
            <img src="estilos/img/bloquear.png" alt="Pedido" style="width: 45px; height: auto; filter: hue-rotate(15deg);">
        </div>

        <h3 style="margin: 5px 0; font-size: 18px;">¡Gracias por tu compra!</h3>
        <p class="descripcion" style="margin-bottom: 15px; font-size: 13px;">Tu orden está siendo procesada en tiempo real.</p>

        <div class="tracking-container">
            <div class="timeline">
                <div class="timeline-bar"></div>

                <div class="step <?php echo ($estado_actual === 'cocina' || $estado_actual === 'barra' || $estado_actual === 'entregado') ? 'active' : ''; ?>">
                    <div class="step-icon">1</div>
                    <div class="step-label">En Cocina</div>
                </div>

                <div class="step <?php echo ($estado_actual === 'barra' || $estado_actual === 'entregado') ? 'active' : ''; ?>">
                    <div class="step-icon">2</div>
                    <div class="step-label">En Barra</div>
                </div>

                <div class="step <?php echo ($estado_actual === 'entregado') ? 'completed-success' : ''; ?>">
                    <div class="step-icon">3</div>
                    <div class="step-label">Entregado</div>
                </div>
            </div>
        </div>

        <div class="pedido-info">
            <div><strong>Orden:</strong> <span>#0842</span></div>
            <div><strong>Método:</strong> <span>Para retirar en local</span></div>
            <div>
                <strong>Estado actual:</strong> 
                <span style="font-weight: bold; color: <?php echo $estado_actual === 'entregado' ? '#27ae60' : '#E8821A'; ?>;">
                    <?php 
                        if ($estado_actual === 'cocina') echo 'Preparando tus hamburguesas';
                        elseif ($estado_actual === 'barra') echo '¡Listo en barra para retirar!';
                        elseif ($estado_actual === 'entregado') echo 'Pedido entregado';
                    ?>
                </span>
            </div>
        </div>

        <button type="button" class="btn-primario" style="margin-top: 15px;" onclick="window.location.reload();">
            Actualizar Estado
        </button>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-text">
                    <div style="display: flex; align-items: center; gap: 8px; justify-content: center; margin-bottom: 10px; margin-top: -30px;">
                        <img src="estilos/img/icono.png" alt="Logo" class="footer-logo">
                        <hr>
                        <h3 style="margin: 6px;">El Oriente</h3>
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