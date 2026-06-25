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
        dv.cantidad        AS cantidad,
        dv.precio_unitario AS precio_unitario,
        dv.subtotal        AS subtotal
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
    $pedidos[$vid]['items'][] = $f;
    $pedidos[$vid]['total']  += (float)$f['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos – Burguersoft</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/estilos-paginas-clientes.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <style>
        :root {
            --brand:   #E8821A;
            --brand-d: #c96d12;
            --dark:    #f5f2f0;
            --mid:     #2e1f0a;
            --text:    #ecebe9;
            --muted:   #6b5e52;
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

        /* ── Layout principal ── */
        .pedidos-page {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px 80px;
            flex: 1;
        }

        .pedidos-header { margin-bottom: 36px; animation: fadeDown .5s ease both; }
        .pedidos-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            color: var(--brand);
        }
        .pedidos-header p { color: var(--muted); margin-top: 6px; font-size: 1.04rem; }

        /* ── Pedido card ── */
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
        .pedido-num  { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--brand); font-weight: 700; }
        .pedido-fecha { font-size: .8rem; color: var(--muted); }

        /* ── Barra de progreso ── */
        .progreso-container {
            padding: 20px 22px 24px;
            border-bottom: 1px solid var(--border);
        }

        .progreso-estado-label {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--brand);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .progreso-estado-label.cancelado {
            color: #e06060;
        }

        .progreso-barra-wrapper {
            display: flex;
            align-items: center;
            margin: 20px 0 8px;
            position: relative;
        }

        .progreso-linea-bg {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255,255,255,.1);
            transform: translateY(-50%);
            z-index: 0;
        }

        .progreso-linea-fill {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background: var(--brand);
            transform: translateY(-50%);
            z-index: 1;
            transition: width .4s ease;
        }

        .progreso-pasos {
            display: flex;
            justify-content: space-between;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .progreso-paso {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .paso-circulo {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 2px solid rgba(255,255,255,.15);
            transition: all .3s;
            flex-shrink: 0;
        }

        .paso-circulo.activo {
            background: var(--brand);
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(232,130,26,.25);
        }

        .paso-circulo.completado {
            background: var(--brand);
            border-color: var(--brand);
        }

        .paso-label {
            font-size: .72rem;
            color: rgba(255,255,255,.3);
            text-align: center;
            font-weight: 600;
            letter-spacing: .3px;
            line-height: 1.3;
        }

        .paso-label.activo,
        .paso-label.completado {
            color: rgba(255,255,255,.85);
        }

        .progreso-cancelado {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(220,50,50,.1);
            border: 1px solid rgba(220,50,50,.25);
            border-radius: 8px;
            margin-top: 12px;
        }
        .progreso-cancelado span { color: #e06060; font-size: .88rem; font-weight: 700; }

        /* ── Tabla ── */
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
            color: #d4c8bc;
        }
        .pedido-table tr:hover td { background: rgba(232,130,26,.04); }
        .producto-nombre { font-weight: 700; color: #f0e8df; }
        .precio-unit { color: var(--muted); font-size: .82rem; }

        /* ── Pie de pedido ── */
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

        .btn-cancelar-pedido {
            margin-right: auto;
            padding: 8px 18px;
            background: transparent;
            border: 1.5px solid rgba(220,50,50,.4);
            color: #e06060;
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .3px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .btn-cancelar-pedido:hover { background: rgba(220,50,50,.12); color: #ff8080; }

        .acc-fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
         }
        /* ── Estado vacío ── */
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

        @keyframes fadeDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeUp   { from { opacity:0; transform:translateY(18px);  } to { opacity:1; transform:translateY(0); } }

        @media (max-width: 540px) {
            .pedido-table th:nth-child(2),
            .pedido-table td:nth-child(2) { display: none; }
            .paso-label { font-size: .65rem; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header_publico.php'; ?>

<div class="pedidos-page">

    <div class="pedidos-header">
        <h1>Mis Pedidos</h1>
        <p>Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>. Aquí puedes ver el estado de tus pedidos.</p>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="pedidos-empty">
            <div class="icon">🍔</div>
            <p>Aún no tienes pedidos registrados.</p>
            <a href="/burguersoft/php/Ir al Menu.php">Ver el menú</a>
        </div>

    <?php else: ?>

        <?php
        $pasos = ['Pendiente de pago', 'En cocina', 'En barra', 'Entregado'];

        foreach ($pedidos as $p):
            $estado    = $p['estado'] ?? 'Pendiente de pago';
            $cancelado = strtolower($estado) === 'cancelado';

            $paso_actual = array_search($estado, $pasos);
            if ($paso_actual === false) $paso_actual = 0;

            $porcentaje = $cancelado ? 0 : round(($paso_actual / (count($pasos) - 1)) * 100);

            $fechaFormato = !empty($p['fecha'])
                ? date('d/m/Y H:i', strtotime($p['fecha']))
                : '—';
        ?>

        <div class="pedido-card">

            <!-- Encabezado -->
            <div class="pedido-head">
                <div class="pedido-head-left">
                    <span class="pedido-num">Pedido #<?= $p['id'] ?></span>
                    <span class="pedido-fecha"><?= $fechaFormato ?></span>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="progreso-container">
                <div class="progreso-estado-label <?= $cancelado ? 'cancelado' : '' ?>">
                    <?= $cancelado ? '✗ Pedido cancelado' : htmlspecialchars($estado) ?>
                </div>

                <?php if (!$cancelado): ?>
                <div class="progreso-barra-wrapper">
                    <div class="progreso-linea-bg"></div>
                    <div class="progreso-linea-fill" style="width: <?= $porcentaje ?>%"></div>

                    <div class="progreso-pasos">
                        <?php foreach ($pasos as $i => $paso):
                            $clase = '';
                            if ($i < $paso_actual)      $clase = 'completado';
                            elseif ($i === $paso_actual) $clase = 'activo';
                        ?>
                        <div class="progreso-paso">
                            <div class="paso-circulo <?= $clase ?>"></div>
                            <span class="paso-label <?= $clase ?>"><?= $paso ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pedido-foot">
                    <?php if (strtolower($p['estado'] ?? '') === 'pagado'): ?>
                        <button type="button" class="btn-cancelar-pedido" onclick="cancelarPedido(<?php echo (int)$p['id']; ?>)">
                            Cancelar pedido
                        </button>
                    <?php endif; ?>
                    <span class="label">Total del pedido:</span>
                    <span class="total">$<?php echo number_format($p['total'], 0, ',', '.'); ?></span>
                <?php else: ?>
                <div class="progreso-cancelado">
                    <span>Este pedido fue cancelado.</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tabla de productos -->
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
                        <td><span class="producto-nombre"><?= htmlspecialchars($item['producto']) ?></span></td>
                        <td><span class="precio-unit">$<?= number_format((float)$item['precio_unitario'], 0, ',', '.') ?></span></td>
                        <td><?= (int)$item['cantidad'] ?></td>
                        <td>$<?= number_format((float)$item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Total -->
            <div class="pedido-foot">
                <span class="label">Total del pedido:</span>
                <span class="total">$<?= number_format($p['total'], 0, ',', '.') ?></span>
            </div>

        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-text">
                <div style="display:flex;align-items:center;gap:8px;justify-content:center;margin-bottom:10px;margin-top:-30px;">
                    <img src="../estilos/img/icono.png" alt="Logo" class="footer-logo">
                    <hr>
                    <h3 style="margin:6px;">El Oriente</h3>
                </div>
                <p>El sabor auténtico de El Oriente. Calidad y servicio en cada mordida.</p>
            </div>
        </div>
        <div class="footer-section">
            <h4>Horarios de atención</h4>
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
<script>
async function cancelarPedido(id) {
    if (!confirm(`¿Cancelar el pedido #${id}? Esta acción no se puede deshacer.`)) return;
    try {
        const res = await fetch(`/burguersoft/controllers/ventas.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ estado: 'Cancelado' })
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert('No se pudo cancelar el pedido: ' + (data.error || 'Error desconocido'));
        }
    } catch (e) {
        alert('Error de conexión al cancelar el pedido.');
    }
}
</script>
</body>
</html>