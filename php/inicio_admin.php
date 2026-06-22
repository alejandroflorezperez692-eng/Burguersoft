<?php
require_once __DIR__ . '/../includes/funciones.php';
requerirAdmin();
$navActivo = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BURGUERSOFT — Inicio</title>
    <link rel="stylesheet" href="../estilos/estilos-header-lader-admin.css">
    <link rel="icon" href="../estilos/img/icono.png" type="image/x-icon">
    <style>
        .dashboard-wrap {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .welcome-banner {
            background: var(--surface-3);
           box-shadow: 
    0 4px 6px -1px rgba(22, 8, 0, 0.5), 
    0 2px 4px -1px rgba(22, 8, 0, 0.3),
    0 10px 15px -5px rgba(22, 8, 0, 0.2);
  
  transition: box-shadow 0.3s ease;
            border-radius: var(--r-lg);
            padding: 40px 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

                .toast-bienvenida {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #2f2a1f;
            color: #F2A93B;
            border: 1px solid #E8821A;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            opacity: 0;
            z-index: 9999;
            transition: opacity 0.4s ease, transform 0.4s ease;
            pointer-events: none;
            max-width: 90%;
            text-align: center;
        }

        .toast-bienvenida.mostrar {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -5%;
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(98, 68, 12, 0.3) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: 20%;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(232, 129, 26, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-text h2 {
            font-family: var(--font-sans);
            font-size: 36px;
            font-weight: 900;
            color: var(--text-900);
            letter-spacing: -0.8px;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .welcome-text p {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }

        .welcome-text .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            padding: 6px 14px;
            background: rgba(232,130,26,0.2);
            border: 1px solid rgba(232,130,26,0.35);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--brand);
        }

        .welcome-img {
            width: 160px;
            height: 160px;
            object-fit: contain;
            opacity: 0.85;
            flex-shrink: 0;
        }

        .quick-stats {
            align-items: center !important;
            justify-content: center !important;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px,1fr));
            gap: 18px;
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: flex-start;
            gap: 18px;
            transition: transform 0.22s var(--ease), box-shadow 0.22s var(--ease);
            position: relative;
            overflow: hidden;
            cursor: default;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent, var(--brand));
            border-radius: 3px 3px 0 0;
        }

        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .stat-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--icon-bg, rgba(232,130,26,0.12));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-body { flex: 1; min-width: 0; }

        .stat-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-400);
            margin-bottom: 6px;
        }

        .stat-val {
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 900;
            color: var(--text-900);
            line-height: 1;
        }

        .stat-sub {
            font-size: 11.5px;
            color: var(--text-400);
            font-weight: 500;
            margin-top: 4px;
        }

        .quick-links-section h3 {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 18px;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px,1fr));
            gap: 14px;
        }

        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 22px 16px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--r-md);
            box-shadow: var(--shadow-xs);
            cursor: pointer;
            transition: all 0.22s var(--ease);
            text-decoration: none;
            color: var(--text-900);
        }

        .quick-link:hover {
            border-color: var(--brand);
            box-shadow: 0 4px 20px var(--brand-glow);
            transform: translateY(-3px);
        }

        .quick-link .ql-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(232,130,26,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .quick-link .ql-label {
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            color: var(--text-600);
        }

        body.dark-mode .welcome-banner { background: linear-gradient(135deg, #0e0500, #2a0f02); }
        body.dark-mode .stat-card { background: var(--surface); }
        body.dark-mode .quick-link { background: var(--surface); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/admin_layout.php'; ?>

<div class="main-content">
    <div class="dashboard-wrap">

        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Bienvenido, <br>Administrador</h2>
            </div>
            <img src="../estilos/img/usuario.png" alt="Admin" class="welcome-img">
        </div>

    </div>
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
<div id="toastBienvenida" class="toast-bienvenida"></div>
<script>
function togglePanel() {
    document.getElementById('accPanel').classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', () => {
    const now = new Date();
    document.getElementById('fecha-actual').textContent =
        '' + now.toLocaleDateString('es-CO', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

    fetch('/burguersoft/controllers/ventas.php')
        .then(r => r.json()).then(d => {
            document.getElementById('stat-ventas').textContent = Array.isArray(d) ? d.length : '—';
        }).catch(() => {});

    fetch('/burguersoft/controllers/Gestion-usuarios.php')
        .then(r => r.json()).then(d => {
            document.getElementById('stat-usuarios').textContent = Array.isArray(d) ? d.length : '—';
        }).catch(() => {});

    fetch('/burguersoft/controllers/marcas.php')
        .then(r => r.json()).then(d => {
            document.getElementById('stat-marcas').textContent = Array.isArray(d) ? d.length : '—';
        }).catch(() => {});

    fetch('/burguersoft/controllers/materiaprima.php')
        .then(r => r.json()).then(d => {
            document.getElementById('stat-insumos').textContent = Array.isArray(d) ? d.length : '—';
        }).catch(() => {});
});
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const toast  = params.get('toast');
    const toastEl = document.getElementById('toastBienvenida');

    if (toastEl && toast === 'login_ok') {
        toastEl.textContent = '¡Bienvenido a BurguerSoft, Administrador!'; 
        setTimeout(() => toastEl.classList.add('mostrar'), 100);
        // Ocultar después de 3.5s
        setTimeout(() => toastEl.classList.remove('mostrar'), 3500);

        // Limpiar el parámetro de la URL
        const url = new URL(window.location.href);
        url.searchParams.delete('toast');
        window.history.replaceState({}, '', url);
    }
});
</script>
</body>
</html>