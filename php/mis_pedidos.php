<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requerirLogin();

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT 
        ve.id              AS id_venta,
        ve.fecha           AS fecha,
        ve.estado          AS estado_venta,
        ve.metodo_pago     AS metodo_pago,
        ve.valor_total     AS valor_total,
        p.nombre           AS producto,
        p.img              AS producto_img,
        dv.cantidad        AS cantidad,
        dv.precio_unitario AS precio_unitario,
        dv.subtotal        AS subtotal,
        dv.estado          AS estado_item
    FROM venta ve
    JOIN detalle_venta dv ON dv.venta_id = ve.id
    JOIN producto p       ON p.id = dv.producto_id
    WHERE ve.usuario_id = ?
    ORDER BY ve.fecha DESC, ve.id DESC
");
$stmt->execute([$_SESSION['id_usuario']]);
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pedidos = [];
foreach ($filas as $f) {
    $vid = $f['id_venta'];
    if (!isset($pedidos[$vid])) {
        $pedidos[$vid] = [
            'id'     => $vid,
            'fecha'  => $f['fecha'],
            'estado' => $f['estado_venta'],
            'items'  => [],
            'total'  => 0,
        ];
    }
    $pedidos[$vid]['items'][]  = $f;
    $pedidos[$vid]['total']   += (float)$f['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos – Burguersoft</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <style>
        :root {
            --brand:   #E8821A;
            --brand-d: #c96d12;
            --dark:    #f5f2f0;
            --mid:     #2e1f0a;
            --text:    #f5ede0;
            --muted:   #a89070;
            --card-bg: #241609;
            --border:  rgba(232,130,26,.18);
            --radius:  12px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--dark);
            color: var(--text);
            font-family: 'Lato', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 32px;
            background: #110b04;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .navbar-brand img {
            width: 83px;
            height: 83px;
            object-fit: contain;
        }
        .navbar-brand span {
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            color: var(--brand);
            font-weight: 700;
            letter-spacing: 1px;
        }
        .btn-regresar {
            padding: 8px 22px;
            border: 2px solid var(--brand);
            color: var(--brand);
            background: transparent;
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: .88rem;
            letter-spacing: .5px;
            text-decoration: none;
            text-transform: uppercase;
            transition: background .2s, color .2s;
            cursor: pointer;
        }
        .btn-regresar:hover {
            background: var(--brand);
            color: #fff;
        }

        .pedidos-page {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px 80px;
            flex: 1;
        }

        .pedidos-header {
            margin-bottom: 36px;
            animation: fadeDown .5s ease both;
        }
        .pedidos-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            color: var(--brand);
        }
        .pedidos-header p {
            color: var(--muted);
            margin-top: 6px;
            font-size: .95rem;
        }

        .pedidos-empty {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
            animation: fadeUp .5s ease both;
        }
        .pedidos-empty .icon { font-size: 3.5rem; margin-bottom: 16px; }
        .pedidos-empty p { font-size: 1.05rem; }
        .pedidos-empty a {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 28px;
            background: var(--brand);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: background .2s;
        }
        .pedidos-empty a:hover { background: var(--brand-d); }

        .pedido-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 24px;
            overflow: hidden;
            animation: fadeUp .45s ease both;
        }
        .pedido-card:nth-child(2) { animation-delay: .08s; }
        .pedido-card:nth-child(3) { animation-delay: .16s; }
        .pedido-card:nth-child(4) { animation-delay: .24s; }

        .pedido-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            background: rgba(232,130,26,.06);
        }
        .pedido-head-left { display: flex; flex-direction: column; gap: 3px; }
        .pedido-num {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--brand);
            font-weight: 700;
        }
        .pedido-fecha { font-size: .8rem; color: var(--muted); }

        .badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .badge-pagado    { background: rgba(50,180,80,.12);  color: #5ecb7a; border: 1px solid rgba(50,180,80,.3); }
        .badge-cancelado { background: rgba(220,50,50,.12);  color: #e06060; border: 1px solid rgba(220,50,50,.3); }
        .badge-reembolsada { background: rgba(100,160,220,.12); color: #7ab8f0; border: 1px solid rgba(100,160,220,.3); }
        .badge-rechazada { background: rgba(180,60,60,.15);  color: #e08080; border: 1px solid rgba(180,60,60,.3); }

        .pedido-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
        .pedido-table thead tr { background: rgba(255,255,255,.03); }
        .pedido-table th {
            padding: 10px 16px;
            text-align: left;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--muted);
            font-weight: 700;
        }
        .pedido-table th:last-child,
        .pedido-table td:last-child { text-align: right; }
        .pedido-table td {
            padding: 11px 16px;
            border-top: 1px solid var(--border);
        }
        .pedido-table tr:hover td { background: rgba(232,130,26,.04); }
        .producto-nombre { font-weight: 700; }
        .precio-unit { color: var(--muted); font-size: .82rem; }

        .pedido-foot {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            gap: 8px;
        }
        .pedido-foot .label { color: var(--muted); font-size: .85rem; }
        .pedido-foot .total {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: var(--brand);
            font-weight: 700;
        }

        footer {
            background: #0d0804;
            border-top: 1px solid var(--border);
            padding: 40px 32px 0;
            margin-top: auto;
        }
        .footer-container {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            justify-content: space-between;
            padding-bottom: 30px;
        }
        .footer-brand { display: flex; align-items: flex-start; gap: 14px; }
        .footer-logo { width: 54px; height: 54px; object-fit: contain; }
        .footer-brand-text h3 {
            font-family: 'Playfair Display', serif;
            color: var(--brand);
            font-size: 1.1rem;
            margin-bottom: 6px;
        }
        .footer-brand-text p { color: var(--muted); font-size: .85rem; max-width: 220px; line-height: 1.5; }

        .footer-section h4 {
            font-family: 'Playfair Display', serif;
            color: var(--brand);
            font-size: 1rem;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .footer-horarios { list-style: none; display: flex; flex-direction: column; gap: 7px; }
        .footer-horarios li {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            font-size: .85rem;
            color: var(--muted);
        }
        .footer-horarios li span:first-child { color: var(--text); font-weight: 700; }

        .footer-bottom {
            border-top: 1px solid var(--border);
            text-align: center;
            padding: 14px 0;
            font-size: .78rem;
            color: var(--muted);
        }

        .acc-fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--brand);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(232,130,26,.4);
            z-index: 999;
            transition: background .2s;
        }
        .acc-fab:hover { background: var(--brand-d); }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 540px) {
            .pedido-table th:nth-child(2),
            .pedido-table td:nth-child(2) { display: none; }
            .navbar { padding: 12px 16px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a class="navbar-brand" href="../index.php">
        <img src="../estilos/img/icono.png" alt="Logo Burguersoft">
        <span>El Oriente</span>
    </a>
    <a class="btn-regresar" href="javascript:history.back()">Regresar</a>
</nav>

<div class="pedidos-page">

    <div class="pedidos-header">
        <h1>Mis Pedidos</h1>
        <p>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>. Aquí están todos tus pedidos realizados.</p>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="pedidos-empty">
            <div class="icon">🍔</div>
            <p>Aún no tienes pedidos registrados.</p>
            <a href="/burguersoft/php/Ir al Menu.php">Ver el menú</a>
        </div>

    <?php else: ?>
        <?php foreach ($pedidos as $p): ?>
            <?php
                $estado = strtolower($p['estado'] ?? 'pendiente');
                $badgeClass = match($estado) {
                    'pagado'      => 'badge-pagado',
                    'cancelado'   => 'badge-cancelado',
                    'reembolsada' => 'badge-reembolsada',
                    'rechazada'   => 'badge-rechazada',
                    default       => 'badge-cancelado',
                };
                $fechaFormato = !empty($p['fecha'])
                    ? date('d/m/Y H:i', strtotime($p['fecha']))
                    : '—';
            ?>
            <div class="pedido-card">
                <div class="pedido-head">
                    <div class="pedido-head-left">
                        <span class="pedido-num">Pedido #<?php echo $p['id']; ?></span>
                        <span class="pedido-fecha"><?php echo $fechaFormato; ?></span>
                    </div>
                    <span class="badge <?php echo $badgeClass; ?>">
                        <?php echo htmlspecialchars(ucfirst($p['estado'] ?? 'Pendiente')); ?>
                    </span>
                </div>

                <table class="pedido-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($p['items'] as $item): ?>
                        <tr>
                            <td><span class="producto-nombre"><?php echo htmlspecialchars($item['producto']); ?></span></td>
                            <td><span class="precio-unit">$<?php echo number_format((float)$item['precio_unitario'], 0, ',', '.'); ?></span></td>
                            <td><?php echo (int)$item['cantidad']; ?></td>
                            <td>$<?php echo number_format((float)$item['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pedido-foot">
                    <span class="label">Total del pedido:</span>
                    <span class="total">$<?php echo number_format($p['total'], 0, ',', '.'); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <img src="../estilos/img/icono.png" alt="Logo El Oriente" class="footer-logo">
            <div class="footer-brand-text">
                <h3>El Oriente</h3>
                <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
            </div>
        </div>
        <div class="footer-section">
            <h4>Horarios de Atención</h4>
            <ul class="footer-horarios">
                <li><span>Lunes – Viernes:</span><span>3:30 PM – 10:00 PM</span></li>
                <li><span>Sábado:</span><span>3:00 PM – 11:00 PM</span></li>
                <li><span>Domingo:</span><span>3:00 PM – 10:00 PM</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 BURGUERSOFT - EL ORIENTE. Todos los derechos reservados.</p>
    </div>
</footer>

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

<button class="acc-fab" id="accFab" onclick="togglePanel()">
    <img style="width:24px;height:24px;filter:invert(1);pointer-events:none;"
         src="../estilos/img/accesibilidad.png" alt="Accesibilidad">
</button>

<script src="../js/accesibilidad.js"></script>
</body>
</html>