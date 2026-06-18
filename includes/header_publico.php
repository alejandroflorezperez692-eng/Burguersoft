<?php
$paginaActiva = $paginaActiva ?? '';
$logueado = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : false;
$modalError   = '';
$modalSuccess = '';
$modalTab     = 'datos';

if ($logueado) {
    if (!function_exists('getPDO')) {
        require_once __DIR__ . '/../includes/conexion.php';
    }
    global $pdo;
    $uid = (int)$_SESSION['id_usuario'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_perfil'])) {

        if ($_POST['accion_perfil'] === 'datos') {
            $nombre         = trim($_POST['nombre']         ?? '');
            $apellido       = trim($_POST['apellido']       ?? '');
            $Tdocumento     = trim($_POST['Tdocumento']     ?? '');
            $Ndocumento     = trim($_POST['Ndocumento']     ?? '');
            $telefono       = trim($_POST['telefono']       ?? '');
            $modalTab       = 'datos';

            if (!$nombre || !$apellido) {
              $modalError = 'Nombre y apellido son obligatorios.';
            } elseif (!$Tdocumento) {
              $modalError = 'Debes seleccionar un tipo de documento.';
            } elseif (!$Ndocumento) {
              $modalError = 'El número de documento es obligatorio.';
            } elseif ($Tdocumento === 'Cédula de Ciudadanía' && (strlen($Ndocumento) < 6 || strlen($Ndocumento) > 10)) {
              $modalError = 'La cédula debe tener entre 6 y 10 dígitos.';
            } elseif ($Tdocumento === 'Tarjeta de Identidad' && (strlen($Ndocumento) < 10 || strlen($Ndocumento) > 11)) {
              $modalError = 'La tarjeta de identidad debe tener entre 10 y 11 dígitos.';
            } elseif ($Tdocumento === 'Cédula de Extranjería' && (strlen($Ndocumento) < 6 || strlen($Ndocumento) > 10)) {
              $modalError = 'La cédula de extranjería debe tener entre 6 y 10 dígitos.';
            } elseif ($Tdocumento === 'Pasaporte' && (strlen($Ndocumento) < 6 || strlen($Ndocumento) > 9)) {
              $modalError = 'El pasaporte debe tener entre 6 y 9 caracteres.';
            } elseif ($telefono && !preg_match('/^(\+57|57)?3[0-9]{9}$/', preg_replace('/\s/', '', $telefono))) {
              $modalError = 'El teléfono debe ser un número colombiano válido (ej: 3001234567).';
            } else {
                $pdo->prepare(
                    "UPDATE usuario SET nombre=?, apellido=?, Tdocumento=?, Ndocumento=?, telefono=? WHERE id=?"
                )->execute([$nombre, $apellido, $Tdocumento, $Ndocumento ?: null, $telefono, $uid]);
                $_SESSION['nombre']   = $nombre;
                $_SESSION['apellido'] = $apellido;
                $modalSuccess = 'Datos actualizados correctamente.';
            }

        } elseif ($_POST['accion_perfil'] === 'password') {
            $modalTab = 'pwd';
            $row = $pdo->prepare("SELECT contrasena FROM usuario WHERE id=?");
            $row->execute([$uid]);
            $hashActual = $row->fetchColumn();

            $actual    = $_POST['actual']    ?? '';
            $nueva     = $_POST['nueva']     ?? '';
            $confirmar = $_POST['confirmar'] ?? '';

            if (!password_verify($actual, $hashActual)) {
                $modalError = 'La contraseña actual es incorrecta.';
            } elseif (strlen($nueva) < 8) {
                $modalError = 'La nueva contraseña debe tener al menos 8 caracteres.';
            } elseif ($nueva !== $confirmar) {
                $modalError = 'Las contraseñas no coinciden.';
            } else {
                $pdo->prepare("UPDATE usuario SET contrasena=? WHERE id=?")
                    ->execute([password_hash($nueva, PASSWORD_BCRYPT), $uid]);
                $modalSuccess = 'Contraseña actualizada correctamente.';
            }
        }
    }

    $stmt = $pdo->prepare(
        "SELECT id, nombre, apellido, correo, Ndocumento, Tdocumento, telefono FROM usuario WHERE id=?"
    );
    $stmt->execute([$uid]);
    $uModal = $stmt->fetch(PDO::FETCH_ASSOC);
}

$tipos_doc = [
    'Cédula de Ciudadanía'  => 'Cédula de Ciudadanía',
    'Tarjeta de Identidad'  => 'Tarjeta de Identidad',
    'Pasaporte'             => 'Pasaporte',
    'Cédula de Extranjería' => 'Cédula de Extranjería',
];

if (!function_exists('hv')) {
    function hv($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
}

$iniciales = strtoupper(mb_substr($uModal['nombre'] ?? '', 0, 1));
?>

<header>
    <div class="header-left">
        <a href="/burguersoft/php/Burguersoft.php" class="logo"></a>
        <hr>
        <a href="/burguersoft/php/El Oriente.php" class="nom-local">El Oriente</a>
    </div>

    <nav class="header-center">
        <a href="/burguersoft/php/Burguersoft.php"  <?= $paginaActiva==='inicio'  ?'class="activo"':'' ?>>Inicio</a>
        <a href="/burguersoft/php/el_oriente.php"   <?= $paginaActiva==='oriente' ?'class="activo"':'' ?>>El Oriente</a>
        <a href="/burguersoft/php/Ir al Menu.php"   <?= $paginaActiva==='menu'    ?'class="activo"':'' ?>>Menú</a>
        <a href="/burguersoft/php/contactanos.php"  <?= $paginaActiva==='contacto'?'class="activo"':'' ?>>Contactanos</a>
    </nav>

    <div class="header-right">

    <?php if ($logueado): ?>
        
        <button class="btn-icono" id="toggleCart" title="Carrito">
            <div class="icono-circulo">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="badge-carrito" id="badge-carrito">0</span>
            </div>
        </button>

        
        <div class="cart-panel" id="cartPanel">
            <div class="cart-header-title">
                <span> MI CARRITO</span>
                <button class="close-cart" id="closeCart">&times;</button>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="empty-cart" id="emptyCart">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    <p>Tu carrito está vacío</p>
                </div>
            </div>
            <div class="cart-footer">
                <div class="subtotal">
                    <span>Total a pagar:</span>
                    <strong id="cartTotal">$0</strong>
                </div>
                <button class="btn-checkout" id="btnCheckout" disabled onclick="abrirCheckout()">
                    Finalizar Compra
                </button>
            </div>
        </div>

        <div class="perfil-dropdown">
            <button class="btn-perfil" title="Mi perfil" id="btnPerfilDropdown">
                <div class="icono-circulo-perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <span class="perfil-nombre"><?= hv($_SESSION['nombre'] ?? 'Usuario') ?></span>
            </button>

            <div class="dropdown-menu" id="perfilDropdownMenu">
                <a href="#" onclick="abrirModalPerfil(); return false;">
                    <img class="dropdown-icon" src="/burguersoft/estilos/img/avatar-de-usuario.png" alt="">
                    Mi perfil
                </a>
                <a href="/burguersoft/php/mis_pedidos.php">
                    <img class="dropdown-icon" src="/burguersoft/estilos/img/bolsa-de-la-compra.png" alt="">
                    Mis pedidos
                </a>
                <a href="logout.php" class="cerrar-sesion">
                    <img class="dropdown-icon" src="/burguersoft/estilos/img/cerrar-sesion.png" alt="">
                    Cerrar sesión
                </a>
            </div>
        </div>

        <script>
            // Lógica para abrir/cerrar el dropdown del perfil de usuario
            document.addEventListener('DOMContentLoaded', function() {
                const btnDropdown = document.getElementById('btnPerfilDropdown');
                const menuDropdown = document.getElementById('perfilDropdownMenu');

                if (btnDropdown && menuDropdown) {
                    btnDropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                        menuDropdown.classList.toggle('abierto');
                    });

                    document.addEventListener('click', function() {
                        menuDropdown.classList.remove('abierto');
                    });
                }
            });
        </script>

    <?php else: ?>
        <a href="/burguersoft/php/login.php" class="link-sesion">
            <button class="btn-sesion">
                <img src="/burguersoft/estilos/img/Icono persona.png" alt="Perfil">
            </button>
        </a>
    <?php endif; ?>

    </div>
</header>

<?php if ($logueado && !empty($uModal)): ?>
<?php 
$claseShow = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_perfil'])) ? 'mp-show' : ''; 
?>
<div class="mp-overlay <?= $claseShow ?>" id="mpOverlay" onclick="cerrarModalPerfil()"></div>

<div class="mp-panel <?= $claseShow ?>" id="mpPanel" role="dialog" aria-modal="true" aria-label="Mi perfil">
    <div class="mp-head">
        <div class="mp-head-left">
            <div class="mp-ava"><?= hv($iniciales ?: 'U') ?></div>
            <div>
                <div class="mp-title"><?= hv($uModal['nombre'].' '.$uModal['apellido']) ?></div>
                <div class="mp-sub"><?= hv($uModal['correo']) ?></div>
            </div>
        </div>
        <button class="mp-close" onclick="cerrarModalPerfil()" aria-label="Cerrar">✕</button>
    </div>

    <div class="mp-body">

        <?php if ($modalError && $modalTab === 'datos'): ?>
            <div class="mp-alert mp-alert-err">⚠ <?= hv($modalError) ?></div>
        <?php endif; ?>
        <?php if ($modalSuccess && $modalTab === 'datos'): ?>
            <div class="mp-alert mp-alert-ok">✓ <?= hv($modalSuccess) ?></div>
        <?php endif; ?>
        <?php if ($modalError && $modalTab === 'pwd'): ?>
    <div class="mp-alert mp-alert-err">⚠ <?= hv($modalError) ?></div>
    <?php if (str_contains($modalError, 'actual')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('mpPw1');
            if (el) {
                el.style.borderColor = '#e63946';
                el.style.boxShadow = '0 0 0 3px rgba(230,57,70,.13)';
                el.addEventListener('input', function() {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                });
            }
        });
    </script>
    <?php endif; ?>
<?php endif; ?>
        <?php if ($modalSuccess && $modalTab === 'pwd'): ?>
            <div class="mp-alert mp-alert-ok">✓ <?= hv($modalSuccess) ?></div>
        <?php endif; ?>

        
        <div class="mp-tabs-container">
            <button class="mp-tab mp-tab-active" id="mpTabDatos" onclick="mpSwitchTab('datos')">
                <img class="mp-tab-icon" src="/burguersoft/estilos/img/avatar-de-usuario.png" alt="">
                <span>Mis datos</span>
            </button>
            <button class="mp-tab" id="mpTabPwd" onclick="mpSwitchTab('pwd')">
                <img class="mp-tab-icon" src="/burguersoft/estilos/img/cerrar-con-llave.png" alt="">
                <span>Contraseña</span>
            </button>
            <button class="mp-tab" id="mpTabInfo" onclick="mpSwitchTab('info')">
                <img class="mp-tab-icon" src="/burguersoft/estilos/img/usuario.png" alt="">
                <span>Cuenta</span>
            </button>
        </div>

        
        <div class="mp-tab-panel" id="mp-panel-datos">
            <form method="POST" action="">
                <input type="hidden" name="accion_perfil" value="datos"/>
                <div class="mp-grid">

                    <div class="mp-field">
                        <label class="mp-label">Nombre <span class="mp-req">*</span></label>
                        <input type="text" name="nombre"
                            value="<?= hv($uModal['nombre']) ?>"
                            placeholder="Tu nombre" required maxlength="20"
                            onkeypress="return /[a-zA-Zá-úÁ-Ú\s]/.test(event.key)"
                            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"/>
                    </div>

                    <div class="mp-field">
                        <label class="mp-label">Apellido <span class="mp-req">*</span></label>
                        <input type="text" name="apellido"
                            value="<?= hv($uModal['apellido']) ?>"
                            placeholder="Tu apellido" required maxlength="20"
                            onkeypress="return /[a-zA-Zá-úÁ-Ú\s]/.test(event.key)"
                            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"/>
                    </div>

                    <div class="mp-field">
                        <label class="mp-label">Tipo de documento</label>
                        <div class="mp-select-wrap">
                            <select name="Tdocumento" id="Tdocumento" required onchange="configurarDocumento(true)">
                                <option value="">Seleccione...</option>
                                <?php foreach ($tipos_doc as $val => $lbl): ?>
                                    <option value="<?= hv($val) ?>" <?= ($uModal['Tdocumento'] === $val) ? 'selected' : '' ?>><?= hv($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mp-field">
                        <label class="mp-label">Número de documento</label>
                        <input type="text" name="Ndocumento" id="Ndocumento"
                            value="<?= hv($uModal['Ndocumento']) ?>"
                            placeholder="Número de documento" maxlength="12" required/>
                    </div>

                    <div class="mp-field mp-full">
                        <label class="mp-label">Teléfono</label>
                        <input type="tel" name="telefono"
                            value="<?= hv($uModal['telefono']) ?>"
                            placeholder="3001234567" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length > 0 && this.value[0] !== '3') this.value = '3';"/>
                    </div>

                    <div class="mp-field mp-full">
                        <label class="mp-label">Correo electrónico</label>
                        <input type="email" value="<?= hv($uModal['correo']) ?>" disabled/>
                        <span class="mp-hint">El correo no se puede cambiar.</span>
                    </div>

                </div>
                <div class="mp-actions">
                    <button type="button" class="mp-btn-cancel" onclick="cerrarModalPerfil()">Cancelar</button>
                    <button type="submit" class="mp-btn-save">Guardar cambios</button>
                </div>
            </form>
        </div>

        <div class="mp-tab-panel mp-hidden" id="mp-panel-pwd">
            <form method="POST" action="" onsubmit="return validarFormPassword()">
                <input type="hidden" name="accion_perfil" value="password"/>
                <div class="mp-grid">

                    <div class="mp-field mp-full">
                        <label class="mp-label">Contraseña actual <span class="mp-req">*</span></label>
                        <div class="mp-pw">
                            <input type="password" name="actual" id="mpPw1"
                                placeholder="••••••••" required
                                oninput="limpiarErrorActual()"/>
                        <button type="button" onclick="mpTogglePW('mpPw1', this)"
                            onmouseover="this.style.color='#000000'"
                            onmouseout="this.style.color='#E8821A'"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                background:none; border:none; cursor:pointer; font-size:13px;
                                font-weight:700; color:#E8821A;">
                            Mostrar
                        </button>
                        </div>
                        <p id="msg-actual" style="font-size:12px;margin-top:5px;min-height:16px;font-family:inherit;"></p>
                    </div>

                    <div class="mp-field mp-full">
                        <label class="mp-label">Nueva contraseña <span class="mp-req">*</span></label>
                        <div class="mp-pw">
                            <input type="password" name="nueva" id="mpPw2"
                                placeholder="Mínimo 8 caracteres" required
                                oninput="mpCheckRules(this.value); verificarCoincidencia();"/>
                           <button type="button" onclick="mpTogglePW('mpPw2', this)"
                                onmouseover="this.style.color='#000000'"
                                onmouseout="this.style.color='#E8821A'"
                                style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                    background:none; border:none; cursor:pointer; font-size:13px;
                                    font-weight:700; color:#E8821A;">
                                Mostrar
                            </button>
                        </div>
                        <div id="contenedor-barra" style="height:6px;width:100%;background:#e0e0e0;margin-top:10px;border-radius:4px;overflow:hidden;">
                            <div id="progreso" style="height:100%;width:0%;transition:0.3s;"></div>
                        </div>
                        <ul style="list-style:none;padding:0;font-size:13px;margin-top:10px;color:#999;font-family:inherit;display:flex;flex-direction:column;gap:6px;">
                            <li id="longitud">❌ Mínimo 8 caracteres</li>
                            <li id="mayuscula">❌ Al menos una mayúscula</li>
                            <li id="numero">❌ Al menos un número</li>
                            <li id="especial">❌ Al menos un símbolo (@, #, $, etc.)</li>
                        </ul>
                    </div>

                    <div class="mp-field mp-full">
                        <label class="mp-label">Confirmar nueva <span class="mp-req">*</span></label>
                        <div class="mp-pw">
                            <input type="password" name="confirmar" id="mpPw3"
                                placeholder="Repite la contraseña" required
                                oninput="verificarCoincidencia()"/>
                            <button type="button" onclick="mpTogglePW('mpPw3', this)"
                                onmouseover="this.style.color='#000000'"
                                onmouseout="this.style.color='#E8821A'"
                                style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                    background:none; border:none; cursor:pointer; font-size:13px;
                                    font-weight:700; color:#E8821A;">
                                Mostrar
                            </button>
                        </div>
                        <p id="msg-confirmar" style="font-size:12px;margin-top:5px;min-height:16px;font-family:inherit;"></p>
                    </div>

                </div>
                <div class="mp-actions">
                    <button type="button" class="mp-btn-cancel" onclick="cerrarModalPerfil()">Cancelar</button>
                    <button type="submit" class="mp-btn-save">Actualizar contraseña</button>
                </div>
            </form>
        </div>

        <div class="mp-tab-panel mp-hidden" id="mp-panel-info">
            <div class="mp-info-list">
                <div class="mp-info-row">
                    <span class="mp-info-lbl">Correo</span>
                    <span><?= hv($uModal['correo']) ?></span>
                </div>
                <div class="mp-info-row">
                    <span class="mp-info-lbl">Tipo documento</span>
                    <span><?= hv($uModal['Tdocumento'] ?: '—') ?></span>
                </div>
                <div class="mp-info-row">
                    <span class="mp-info-lbl">N.º documento</span>
                    <span><?= hv($uModal['Ndocumento'] ?: '—') ?></span>
                </div>
                <div class="mp-info-row">
                    <span class="mp-info-lbl">Teléfono</span>
                    <span><?= hv($uModal['telefono'] ?: '—') ?></span>
                </div>
                <div class="mp-info-row">
                    <span class="mp-info-lbl">Estado</span>
                    <span class="mp-badge-ok">✓ Activo</span>
                </div>
            </div>
            <div class="mp-actions" style="margin-top:16px">
                <button type="button" class="mp-btn-cancel" style="flex:1" onclick="cerrarModalPerfil()">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_perfil'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        mpSwitchTab('<?= $modalTab === 'pwd' ? 'pwd' : 'datos' ?>');
    });
</script>
<?php endif; ?>

<?php endif; ?>

<style>
body {
    overflow-x: hidden;
    width: 100%;
}

.mp-tabs-container{display:flex;justify-content:space-around;margin-bottom:25px;border-bottom:1px solid #eee;padding-bottom:10px;}
.mp-tab{display:flex;flex-direction:column;align-items:center;gap:8px;background:none;border:none;cursor:pointer;padding:10px 15px;border-radius:12px;transition:all 0.3s ease;color:#7A6855;font-weight:600;font-family:inherit;font-size:12px;}
.mp-tab-active{background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.08);color:#BA7517;}
.mp-tab-icon{width:24px;height:24px;object-fit:contain;}
.mp-tab:not(.mp-tab-active) .mp-tab-icon{filter:grayscale(1);opacity:0.7;}

.btn-icono{background:transparent;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;justify-content:center;transition:transform .2s}
.btn-icono:hover{background:transparent;transform:scale(1.1)}
.icono-circulo{position:relative;color:white;display:flex;align-items:center;justify-content:center}
.badge-carrito{position:absolute;top:-12px;right:-12px;background:#e63946;color:white;font-size:11px;font-weight:bold;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;border:2px solid #1a090d}

.cart-panel{position:fixed;top:0;right:-450px;width:400px;max-width:100%;height:100vh;background:white;box-shadow:-5px 0 25px rgba(0,0,0,.3);transition:right .3s ease-in-out;z-index:9999;display:flex;flex-direction:column;visibility:hidden;}
.cart-panel.active{right:0 !important;visibility:visible;}
.cart-header-title{background:#1a090d;color:white;padding:20px;display:flex;justify-content:space-between;align-items:center;font-family:sans-serif;font-weight:bold;letter-spacing:1px}
.close-cart{background:none;border:none;color:white;font-size:26px;cursor:pointer;line-height:1}
.cart-items{flex:1;overflow-y:auto;padding:20px}
.empty-cart{height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;color:#888}
.empty-cart svg{width:80px;height:80px;stroke:#ccc;fill:none;margin-bottom:15px}
.cart-footer{border-top:1px solid #eee;padding:20px}
.subtotal{display:flex;justify-content:space-between;margin-bottom:15px;align-items:center}
.subtotal strong{color:#000;font-size:24px}
.btn-checkout{width:100%;padding:14px;border:none;border-radius:4px;background:#ccc;color:#fff;font-weight:bold;text-transform:uppercase;letter-spacing:1px;cursor:not-allowed}
.btn-checkout:not(:disabled){background:#ff5722;cursor:pointer}

/* ESTILOS NUEVOS PARA LOS BOTONES DEL CARRITO */
.cart-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
}
.btn-pago {
    grid-column: 1 / -1;
}
.btn-cart-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 12px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #E0D5C5;
    border-radius: 8px;
    cursor: pointer;
    background-color: #F7F2EA;
    color: #7A6855;
    transition: all 0.2s ease;
}
.btn-cart-action:hover {
    background-color: #E0D5C5;
    color: #1C1410;
    transform: translateY(-1px);
}
.btn-vaciar:hover {
    background-color: #FFF0EE;
    border-color: #F0A090;
    color: #C0392B;
}
.btn-pago {
    background-color: #fff;
    border-color: #E0D5C5;
    color: #BA7517;
}
.btn-pago:hover {
    background-color: #FAEEDA;
    border-color: #EF9F27;
    color: #412402;
}
.btn-checkout {
    margin-top: 5px;
}

.mp-panel { opacity: 0; pointer-events: none; }
.mp-panel.mp-show { opacity: 1; pointer-events: all; }

.header-right { display: flex; align-items: center; gap: 15px; }
.perfil-dropdown { position: relative; display: inline-block; }
.btn-perfil{display:flex;align-items:center;gap:10px;cursor:pointer;background:rgba(255,255,255,.12);border:2px solid rgba(255,255,255,.25);border-radius:50px;padding:5px 16px 5px 5px;transition:background .25s,transform .2s;margin-left:5px}
.icono-circulo-perfil{width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:white}
.perfil-nombre{font-size:14px;font-weight:600;color:white;white-space:nowrap}

.dropdown-menu{display:none;position:absolute;right:0;top:calc(100% + 10px);background:white;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.18);min-width:180px;z-index:999;overflow:hidden;animation:fadeDown .2s ease}
@keyframes fadeDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.dropdown-menu a{display:flex;align-items:center;gap:10px;padding:11px 18px;color:#333;text-decoration:none;font-size:11px;font-weight:600;text-transform:uppercase;transition:background .2s}
.dropdown-menu a:hover{background:#cbbfbf}
.dropdown-icon{width:18px;height:18px;object-fit:contain;flex-shrink:0}
.cerrar-sesion{color:#e63946!important;font-weight:600}
.btn-sesion{background:transparent;border:none;cursor:pointer}

.mp-overlay{position:fixed;inset:0;background:rgba(26,9,13,.55);backdrop-filter:blur(4px);z-index:10000;opacity:0;pointer-events:none;transition:opacity .28s ease}
.mp-overlay.mp-show{opacity:1;pointer-events:all}
.mp-panel{position:fixed;top:50%;left:50%;transform:translate(-50%,-48%) scale(.96);width:min(540px,94vw);max-height:88vh;overflow-y:auto;background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(26,9,13,.3);z-index:10001;opacity:0;pointer-events:none;transition:opacity .28s ease,transform .28s ease;scrollbar-width:thin;scrollbar-color:#E0D5C5 transparent}
.mp-panel.mp-show{opacity:1;pointer-events:all;transform:translate(-50%,-50%) scale(1)}
.mp-head{display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #F0EAE0;position:sticky;top:0;background:#fff;z-index:5}
.mp-head-left{display:flex;align-items:center;gap:12px}
.mp-ava{width:46px;height:46px;border-radius:50%;background:#FAEEDA;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;color:#BA7517;border:2.5px solid #EF9F27;flex-shrink:0}
.mp-title{font-size:16px;font-weight:700;color:#1C1410}
.mp-sub{font-size:11px;color:#7A6855;margin-top:2px}
.mp-close{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#F0EAE0;border:none;cursor:pointer;font-size:15px;color:#7A6855;transition:background .15s,color .15s}
.mp-close:hover{background:#E0D5C5;color:#1C1410}
.mp-body{padding:20px 24px 24px}
.mp-alert{display:flex;align-items:center;gap:9px;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:16px;font-weight:500}
.mp-alert-ok{background:#EAF3DE;border:1px solid #C0DD97;color:#27500A}
.mp-alert-err{background:#FFF0EE;border:1px solid #F0A090;color:#C0392B}
.mp-tab-panel{display:block}
.mp-hidden{display:none!important}
.mp-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px 16px}
.mp-full{grid-column:1/-1}
.mp-field{display:flex;flex-direction:column;gap:5px}
.mp-label{font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#7A6855}
.mp-req{color:#EF9F27}
.mp-hint{font-size:11px;color:#B0A090;margin-top:2px}
.mp-panel input[type=text],.mp-panel input[type=email],.mp-panel input[type=password],.mp-panel input[type=tel],.mp-panel select{width:100%;padding:10px 13px;font-family:inherit;font-size:13px;color:#1C1410;background:#F7F2EA;border:1px solid #E0D5C5;border-radius:10px;outline:none;appearance:none;-webkit-appearance:none;transition:border-color .2s,background .2s,box-shadow .2s}
.mp-panel input::placeholder{color:#B0A090}
.mp-panel input:focus,.mp-panel select:focus{border-color:#EF9F27;background:#fff;box-shadow:0 0 0 3px rgba(239,159,39,.13)}
.mp-panel input:disabled{opacity:.5;cursor:not-allowed;background:#F0EAE0}
.mp-select-wrap{position:relative}
.mp-select-wrap::after{content:'';pointer-events:none;position:absolute;right:13px;top:50%;transform:translateY(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:5px solid #BA7517}
.mp-pw{position:relative}
.mp-pw input{padding-right:74px}
.mp-pw-toggle{position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;color:#EF9F27;padding:0}
.mp-pw-toggle:hover{color:#FAC775;background:none!important;border:none!important;outline:none!important;box-shadow:none!important}
.mp-info-list{display:flex;flex-direction:column;gap:8px}
.mp-info-row{display:flex;align-items:center;justify-content:space-between;padding:9px 14px;background:#F7F2EA;border-radius:10px;font-size:13px}
.mp-info-lbl{font-size:11px;color:#7A6855;font-weight:700;text-transform:uppercase;letter-spacing:.05em}
.mp-badge-ok{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:#EAF3DE;color:#27500A;border:1px solid #C0DD97}
.mp-actions{display:flex;gap:10px;margin-top:20px}
.mp-btn-cancel{padding:10px 20px;border-radius:10px;font-family:inherit;font-size:13px;font-weight:600;background:#F0EAE0;color:#7A6855;border:1px solid #E0D5C5;cursor:pointer;transition:background .15s}
.mp-btn-cancel:hover{background:#E0D5C5}
.mp-btn-save{flex:1;padding:10px 20px;border-radius:10px;font-family:inherit;font-size:13px;font-weight:700;background:#EF9F27;color:#412402;border:none;cursor:pointer;box-shadow:0 2px 12px rgba(239,159,39,.3);transition:background .15s,box-shadow .15s}
.mp-btn-save:hover{background:#FAC775;box-shadow:0 4px 18px rgba(239,159,39,.4)}
.mp-btn-save:active{transform:scale(.98)}

#mp-panel-pwd .mp-field{margin-bottom:16px}

@media(max-width:480px){
    .mp-grid{grid-template-columns:1fr}
    .mp-full{grid-column:1}
    .mp-body,.mp-head{padding:16px}
}
</style>
<style>
.dropdown-menu { display: none; }
.dropdown-menu.abierto { display: block !important; }
</style>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const toggleCartBtn = document.getElementById('toggleCart');
    const closeCartBtn = document.getElementById('closeCart');
    const cartPanel = document.getElementById('cartPanel');

    
    if (toggleCartBtn && cartPanel) {
        toggleCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cartPanel.classList.add('active');
        });
    }

    
    if (closeCartBtn && cartPanel) {
        closeCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cartPanel.classList.remove('active');
        });
    }

    
    document.addEventListener('click', function(e) {
        if (cartPanel && cartPanel.classList.contains('active')) {
            if (!cartPanel.contains(e.target) && !toggleCartBtn.contains(e.target)) {
                cartPanel.classList.remove('active');
            }
        }
    });
});


function verFactura() {
    console.log("Abriendo visualización de factura...");
    alert("Aquí podrás visualizar el desglose e impresión de tu factura de compra.");
}

function vaciarCarrito() {
    if (confirm("¿Estás seguro de que deseas vaciar por completo tu carrito de compras?")) {
        console.log("Vaciando carrito...");
        
        const cartItems = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const cartTotal = document.getElementById('cartTotal');
        const badgeCarrito = document.getElementById('badge-carrito');
        const btnCheckout = document.getElementById('btnCheckout');

        if (cartItems && emptyCart) {
            cartItems.innerHTML = '';
            cartItems.appendChild(emptyCart);
        }
        if (cartTotal) cartTotal.textContent = '$0';
        if (badgeCarrito) badgeCarrito.textContent = '0';
        if (btnCheckout) btnCheckout.disabled = true;
    }
}

function seleccionarMetodoPago() {
    console.log("Abriendo opciones de método de pago...");
    alert("Selecciona tu método de pago preferido (Efectivo, Transferencia o Datáfono).");
}


function abrirModalPerfil() {
    const overlay = document.getElementById('mpOverlay');
    const panel = document.getElementById('mpPanel');
    if(overlay && panel) {
        overlay.classList.add('mp-show');
        panel.classList.add('mp-show');
    }
}

function cerrarModalPerfil() {
    const overlay = document.getElementById('mpOverlay');
    const panel = document.getElementById('mpPanel');
    if(overlay && panel) {
        overlay.classList.remove('mp-show');
        panel.classList.remove('mp-show');
    }
}

function mpSwitchTab(tab) {
    const panelDatos = document.getElementById('mp-panel-datos');
    const panelPwd = document.getElementById('mp-panel-pwd');
    const panelInfo = document.getElementById('mp-panel-info');
    
    const tabDatos = document.getElementById('mpTabDatos');
    const tabPwd = document.getElementById('mpTabPwd');
    const tabInfo = document.getElementById('mpTabInfo');

    if(panelDatos) panelDatos.classList.add('mp-hidden');
    if(panelPwd) panelPwd.classList.add('mp-hidden');
    if(panelInfo) panelInfo.classList.add('mp-hidden');

    if(tabDatos) tabDatos.classList.remove('mp-tab-active');
    if(tabPwd) tabPwd.classList.remove('mp-tab-active');
    if(tabInfo) tabInfo.classList.remove('mp-tab-active');

    if (tab === 'datos') {
        if(panelDatos) panelDatos.classList.remove('mp-hidden');
        if(tabDatos) tabDatos.classList.add('mp-tab-active');
    } else if (tab === 'pwd') {
        if(panelPwd) panelPwd.classList.remove('mp-hidden');
        if(tabPwd) tabPwd.classList.add('mp-tab-active');
    } else if (tab === 'info') {
        if(panelInfo) panelInfo.classList.remove('mp-hidden');
        if(tabInfo) tabInfo.classList.add('mp-tab-active');
    }
}
</script>