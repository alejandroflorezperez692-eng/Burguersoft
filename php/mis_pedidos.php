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

$total_pedidos = count($pedidos);
$contador = 0;
foreach ($pedidos as $vid => $datos) {
    $pedidos[$vid]['numero_secuencial'] = $total_pedidos - $contador;
    $contador++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT – Mis Pedidos</title>
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../estilos/estilos-paginas-clientes.css">
    <link rel="stylesheet" href="../estilos/accesibilidad.css">
    <style>
        :root {
            --primario:   #3d2111;
            --secundario: #F18921;
            --alerta:     #C3402A;
            --fondo:      #f6f5e4;
            --card-bg:    #ffffff;
            --border:     #e8e0d4;
            --radius:     14px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--fondo);
            color: var(--primario);
            font-family: 'Lucida Sans', sans-serif;
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

        .pedidos-header { 
            text-align: center;
            margin-bottom: 36px; 
            animation: fadeDown .5s ease both; 
        }
        .pedidos-header h1 {
            font-family: 'Lucida Sans', sans-serif;
            font-size: 32px;
            color: var(--primario);
            letter-spacing: 1px;
        }
        .pedidos-header p { 
            color: #777; 
            margin-top: 8px; 
            font-size: 14px; 
        }

        /* ── Pedido card ── */
        .pedido-card {
            background: var(--card-bg);
            border: 2px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 24px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(61,33,17,.05);
            animation: fadeUp .45s ease both;
            transition: border-color .2s, box-shadow .2s;
        }
        .pedido-card:hover {
            border-color: var(--secundario);
            box-shadow: 0 12px 30px rgba(61,33,17,.1);
        }

        .pedido-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            background: #fff8ee;
        }
        .pedido-head-left { display: flex; flex-direction: column; gap: 3px; }
        .pedido-num  { font-family: 'Lucida Sans', sans-serif; font-size: 16px; color: var(--secundario); font-weight: 700; }
        .pedido-fecha { font-size: 12px; color: #777; }

        /* ── Barra de progreso ── */
        .progreso-container {
            padding: 20px 22px 24px;
            border-bottom: 1px solid var(--border);
            background: #fff;
        }

        .progreso-estado-label {
            font-family: 'Lucida Sans', sans-serif;
            font-size: 15px;
            color: var(--secundario);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .progreso-estado-label.cancelado {
            color: var(--alerta);
        }

        .progreso-barra-wrapper {
            display: flex;
            align-items: center;
            margin: 20px 0 8px;
            position: relative;
        }

        .progreso-linea-bg {
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 4px;
            background: #e8e0d4;
            transform: translateY(-50%);
            z-index: 0;
        }

        .progreso-linea-fill {
            position: absolute;
            top: 50%; left: 0;
            height: 4px;
            background: var(--secundario);
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
            width: 18px; height: 18px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e8e0d4;
            transition: all .3s;
            flex-shrink: 0;
        }

        .paso-circulo.activo {
            background: var(--secundario);
            border-color: var(--secundario);
            box-shadow: 0 0 0 4px rgba(241,137,33,.25);
        }

        .paso-circulo.completado {
            background: var(--secundario);
            border-color: var(--secundario);
        }

        .paso-label {
            font-size: 11px;
            color: #888;
            text-align: center;
            font-weight: 600;
            line-height: 1.3;
        }

        .paso-label.activo,
        .paso-label.completado {
            color: var(--primario);
        }

        .progreso-cancelado {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(195,64,42,.05);
            border: 1px solid rgba(195,64,42,.2);
            border-radius: 8px;
            margin-top: 12px;
        }
        .progreso-cancelado span { color: var(--alerta); font-size: 13px; font-weight: 700; }

        /* ── Tabla ── */
        .pedido-table { width: 100%; border-collapse: collapse; font-size: 13px; background: #fff; }
        .pedido-table thead tr { background: #fbfbfa; }
        .pedido-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #777;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
        }
        .pedido-table th:last-child,
        .pedido-table td:last-child { text-align: right; }
        .pedido-table td {
            padding: 12px 16px;
            border-top: 1px solid #f0ebe3;
            color: #555;
        }
        .pedido-table tr:hover td { background: rgba(241,137,33,.03); }
        .producto-nombre { font-weight: 700; color: var(--primario); }
        .precio-unit { color: #888; font-size: 12px; }

        /* ── Pie de pedido ── */
        .pedido-foot {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            gap: 8px;
            background: #fff8ee;
        }
        .pedido-foot .label { color: #666; font-size: 13px; }
        .pedido-foot .total {
            font-family: 'Lucida Sans', sans-serif;
            font-size: 18px;
            color: var(--alerta);
            font-weight: bold;
        }

        .btn-cancelar-pedido {
            margin-right: auto;
            padding: 8px 18px;
            background: transparent;
            border: 2px solid rgba(195,64,42,.4);
            color: var(--alerta);
            border-radius: 8px;
            font-family: 'Lucida Sans', sans-serif;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }
        .btn-cancelar-pedido:hover { background: var(--alerta); color: #fff; border-color: var(--alerta); }

        .acc-fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            z-index: 999;
        }

        /* ── Estado vacío ── */
        .pedidos-empty {
            text-align: center;
            padding: 80px 20px;
            color: #888;
            animation: fadeUp .5s ease both;
        }
        .pedidos-empty .icon { font-size: 50px; margin-bottom: 16px; }
        .pedidos-empty p { font-size: 16px; }
        .pedidos-empty a {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 28px;
            background: var(--secundario);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: background .2s;
        }
        .pedidos-empty a:hover { background: var(--primario); }

        @keyframes fadeDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeUp   { from { opacity:0; transform:translateY(18px);  } to { opacity:1; transform:translateY(0); } }

        @media (max-width: 540px) {
            .pedido-table th:nth-child(2),
            .pedido-table td:nth-child(2) { display: none; }
            .paso-label { font-size: 9px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header_publico.php'; ?>

<div class="pedidos-page">

    <div class="pedidos-header">
        <h1>Mis Pedidos</h1>
        <p>Hola, <?= htmlspecialchars(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '')) ?>, aquí puedes ver el estado de tus pedidos.</p>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="pedidos-empty">
            <div class="icon">🍔</div>
            <p>Aún no tienes pedidos registrados.</p>
            <a href="/burguersoft/php/Ir al Menu.php">Ver el menú</a>
        </div>

    <?php else: ?>

        <?php
        $pasos = ['En cocina', 'En barra','Entregado', 'Pendiente de pago', 'Pagado' ];

        foreach ($pedidos as $p):
            $estado    = $p['estado'] ?? 'En cocina';
            $cancelado = strtolower($estado) === 'cancelado';

            $paso_actual = array_search($estado, $pasos);
            if ($paso_actual === false) $paso_actual = 0;

            $porcentaje = $cancelado ? 0 : round(($paso_actual / (count($pasos) - 1)) * 100);

            $fechaFormato = !empty($p['fecha'])
                ? date('d/m/Y H:i', strtotime($p['fecha']))
                : '—';
        ?>

        <div class="pedido-card">

            <div class="pedido-head">
                <div class="pedido-head-left">
                    <span class="pedido-num">Pedido #<?= $p['numero_secuencial'] ?></span>
                    <span class="pedido-fecha"><?= $fechaFormato ?></span>
                </div>
            </div>

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
                <?php else: ?>
                <div class="progreso-cancelado">
                    <span>Este pedido fue cancelado.</span>
                </div>
                <?php endif; ?>
            </div>

            <table class="pedido-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio unitario</th>
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

            <div class="pedido-foot">
                <?php $cancelables = ['Pagado','En cocina','En barra','Pendiente de pago'];
                    if (in_array($p['estado'] ?? '', $cancelables)): ?>
                    <button type="button" class="btn-cancelar-pedido" onclick="cancelarPedido(<?php echo (int)$p['id']; ?>)">
                        Cancelar pedido
                    </button>
                <?php endif; ?>
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
    if (!confirm(`¿Cancelar el pedido? Esta acción no se puede deshacer.`)) return;
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