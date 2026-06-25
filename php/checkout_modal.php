<?php 
$logueado = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : false;
if ($logueado): ?>
<div class="co-overlay" id="coOverlay" onclick="cerrarCheckout()"></div>

<div class="co-panel" id="coPanel" role="dialog" aria-modal="true" aria-label="Finalizar compra">

    <div class="co-head">
        <span class="co-head-title">Finalizar compra</span>
        <button class="co-close" onclick="cerrarCheckout()" aria-label="Cerrar">&#x2715;</button>
    </div>

    <div class="co-tabs-row">
        <button class="co-tab co-tab-active" id="coTabDom" onclick="coSwitchTab('domicilio')">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="2"/><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7z"/>
            </svg>
            Domicilio
        </button>
        <button class="co-tab" id="coTabRec" onclick="coSwitchTab('recoger')">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            Recoger / Consumir
        </button>
    </div>

    <div class="co-section" id="co-sec-domicilio">

        <div class="co-info-banner">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Entrega a domicilio &mdash; tiempo estimado: <strong>30 - 45 min</strong>
        </div>

        <div class="co-fieldset">
            <label class="co-label">Nombre completo <span class="co-req">*</span></label>
            <input type="text" id="co-dom-nombre" class="co-input"
                   value="<?= hv(($uModal['nombre'] ?? '').' '.($uModal['apellido'] ?? '')) ?>"
                   placeholder="Tu nombre completo" maxlength="60"/>
        </div>

        <div class="co-fieldset">
            <label class="co-label">Telefono de contacto <span class="co-req">*</span></label>
            <div class="co-tel-wrap">
                <span class="co-tel-prefix">+57</span>
                <input type="tel" id="co-dom-tel" class="co-input co-input-tel"
                       value="<?= hv($uModal['telefono'] ?? '') ?>"
                       placeholder="3001234567" maxlength="10"
                       oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value.length>0&&this.value[0]!=='3') this.value='3';"/>
            </div>
            <span class="co-hint" id="co-dom-tel-hint"></span>
        </div>

        <div class="co-fieldset">
            <label class="co-label">Direccion de entrega <span class="co-req">*</span></label>
            <button class="co-btn-location" onclick="usarUbicacion()" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                </svg>
                Usar mi ubicacion actual
            </button>
            <div class="co-divider-or"><span>o ingresa tu direccion</span></div>
            <input type="text" id="co-dom-dir" class="co-input"
                   placeholder="Ej: Calle 50 # 32-18, Apto 201" maxlength="120"/>
            <span class="co-hint" id="co-dom-dir-hint"></span>
        </div>

        <div class="co-fieldset">
            <label class="co-label">Indicaciones adicionales</label>
            <textarea id="co-dom-notas" class="co-input co-textarea"
                      placeholder="Torre, piso, apartamento, referencias para el repartidor..."
                      maxlength="200" rows="2"></textarea>
        </div>

        <div class="co-fieldset">
            <label class="co-label">Metodo de pago <span class="co-req">*</span></label>
            <div class="co-pay-grid" id="co-dom-pay-grid">
                <button class="co-pay-opt" data-val="efectivo" onclick="selPago(this,'dom')" type="button">
                    <img src="../estilos/img/dinero.png" alt="Efectivo">
                    Efectivo
                </button>
                <button class="co-pay-opt" data-val="tarjeta" onclick="selPago(this,'dom')" type="button">
                    <img src="../estilos/img/tarjeta.png" alt="Tarjeta">
                    Tarjeta
                </button>
                <button class="co-pay-opt" data-val="nequi" onclick="selPago(this,'dom')" type="button">
                    <img src="../estilos/img/nequi.png" alt="Nequi">
                    Nequi
                </button>
                <button class="co-pay-opt" data-val="daviplata" onclick="selPago(this,'dom')" type="button">
                    <img src="../estilos/img/daviplata.png" alt="Daviplata">
                    Daviplata
                </button>
            </div>
            <input type="hidden" id="co-dom-pago" value=""/>
            <span class="co-hint" id="co-dom-pay-hint"></span>
        </div>

        <div class="co-summary">
            <div class="co-summary-row">
                <span>Subtotal</span><span id="co-dom-subtotal">$0</span>
            </div>
            <div class="co-summary-row">
                <span>Costo de envio</span><span class="co-envio">$3.000</span>
            </div>
            <div class="co-summary-row co-summary-total">
                <span>Total</span><span id="co-dom-total">$3.000</span>
            </div>
        </div>

        <button class="co-btn-confirm" onclick="confirmarPedido('domicilio')" type="button">
            Confirmar pedido
        </button>
    </div>

    <div class="co-section co-hidden" id="co-sec-recoger">

        <div class="co-subtabs-row">
            <button class="co-subtab co-subtab-active" id="coSubLlevar" onclick="coSwitchSubtab('llevar')" type="button">
                Para llevar
            </button>
            <button class="co-subtab" id="coSubRest" onclick="coSwitchSubtab('restaurante')" type="button">
                Consumir en restaurante
            </button>
        </div>

        <div id="co-sub-llevar">
            <div class="co-info-banner">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Recoge tu pedido en el local &mdash; Te avisamos cuando este listo
            </div>

            <div class="co-fieldset">
                <label class="co-label">Nombre para el pedido <span class="co-req">*</span></label>
                <input type="text" id="co-rec-nombre" class="co-input"
                       value="<?= hv($uModal['nombre'] ?? '') ?>"
                       placeholder="Nombre en el que queda el pedido" maxlength="40"/>
            </div>

            <div class="co-fieldset">
                <label class="co-label">Telefono de contacto <span class="co-req">*</span></label>
                <div class="co-tel-wrap">
                    <span class="co-tel-prefix">+57</span>
                    <input type="tel" id="co-rec-tel" class="co-input co-input-tel"
                           value="<?= hv($uModal['telefono'] ?? '') ?>"
                           placeholder="3001234567" maxlength="10"
                           oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value.length>0&&this.value[0]!=='3') this.value='3';"/>
                </div>
                <span class="co-hint" id="co-rec-tel-hint"></span>
            </div>

            <div class="co-fieldset">
                <label class="co-label">Metodo de pago <span class="co-req">*</span></label>
                <div class="co-pay-grid" id="co-rec-pay-grid">
                    <button class="co-pay-opt" data-val="efectivo" onclick="selPago(this,'rec')" type="button">
                        <img src="/burguersoft/estilos/img/dinero.png" alt="Efectivo">
                        Efectivo
                    </button>
                    <button class="co-pay-opt" data-val="tarjeta" onclick="selPago(this,'rec')" type="button">
                        <img src="/burguersoft/estilos/img/tarjeta.png" alt="Tarjeta">
                        Tarjeta
                    </button>
                    <button class="co-pay-opt" data-val="nequi" onclick="selPago(this,'rec')" type="button">
                        <img src="/burguersoft/estilos/img/nequi.png">
                        Nequi
                    </button>
                    <button class="co-pay-opt" data-val="daviplata" onclick="selPago(this,'rec')" type="button">
                        <img src="/burguersoft/estilos/img/daviplata.png">
                        Daviplata
                    </button>
                </div>
                <input type="hidden" id="co-rec-pago" value=""/>
                <span class="co-hint" id="co-rec-pay-hint"></span>
            </div>
        </div>

        <div class="co-hidden" id="co-sub-restaurante">
            <div class="co-info-banner co-banner-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2s-5 2-5 7v6"/>
                </svg>
                Consumiras en el restaurante &mdash; Bienvenido
            </div>

            <div class="co-fieldset">
                <label class="co-label">Numero de mesa <span class="co-req">*</span></label>
                <input type="text" id="co-res-mesa" class="co-input"
                    placeholder="Mesa (1 al 7)" maxlength="1"
                    oninput="this.value = this.value.replace(/[^1-7]/g, '');"/>
                <span class="co-hint" id="co-res-mesa-hint"></span>
            </div>

            <div class="co-fieldset">
                <label class="co-label">Nombre para el pedido <span class="co-req">*</span></label>
                <input type="text" id="co-res-nombre" class="co-input"
                       value="<?= hv($uModal['nombre'] ?? '') ?>"
                       placeholder="Tu nombre" maxlength="40"/>
            </div>

            <div class="co-fieldset">
                <label class="co-label">Metodo de pago <span class="co-req">*</span></label>
                <div class="co-pay-grid" id="co-res-pay-grid">
                    <button class="co-pay-opt" data-val="efectivo" onclick="selPago(this,'res')" type="button">
                        <img src="/burguersoft/estilos/img/dinero.png" alt="Efectivo">
                        Efectivo
                    </button>
                    <button class="co-pay-opt" data-val="tarjeta" onclick="selPago(this,'res')" type="button">
                        <img src="../estilos/img/tarjeta.png" alt="Tarjeta">
                        Tarjeta
                    </button>
                    <button class="co-pay-opt" data-val="nequi" onclick="selPago(this,'res')" type="button">
                        <img src="../estilos/img/nequi.png" alt="Nequi">
                        Nequi
                    </button>
                    <button class="co-pay-opt" data-val="daviplata" onclick="selPago(this,'res')" type="button">
                        <img src="../estilos/img/daviplata.png" alt="Daviplata">
                        Daviplata
                    </button>
                </div>
                <input type="hidden" id="co-res-pago" value=""/>
                <span class="co-hint" id="co-res-pay-hint"></span>
            </div>
        </div>

        <div class="co-summary">
            <div class="co-summary-row co-summary-total">
                <span>Total</span><span id="co-rec-total">$0</span>
            </div>
        </div>

        <button class="co-btn-confirm" onclick="confirmarPedido('recoger')" type="button">
            Confirmar pedido
        </button>
    </div>

</div>

<style>
.co-overlay{position:fixed;inset:0;background:rgba(26,9,13,.6);backdrop-filter:blur(4px);z-index:10200;opacity:0;pointer-events:none;transition:opacity .28s ease}
.co-overlay.co-show{opacity:1;pointer-events:all}
.co-panel{position:fixed;top:50%;left:50%;transform:translate(-50%,-48%) scale(.96);width:min(520px,95vw);max-height:90vh;overflow-y:auto;background:#fff;border-radius:20px;box-shadow:0 24px 64px rgba(26,9,13,.35);z-index:10201;opacity:0;pointer-events:none;transition:opacity .28s ease,transform .28s ease;scrollbar-width:thin;scrollbar-color:#E0D5C5 transparent}
.co-panel.co-show{opacity:1;pointer-events:all;transform:translate(-50%,-50%) scale(1)}
.co-head{display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #F0EAE0;position:sticky;top:0;background:#fff;z-index:5}
.co-head-title{font-size:17px;font-weight:800;color:#1C1410;letter-spacing:.01em}
.co-close{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#F0EAE0;border:none;cursor:pointer;font-size:15px;color:#7A6855;transition:background .15s}
.co-close:hover{background:#E0D5C5;color:#1C1410}
.co-tabs-row{display:flex;padding:18px 24px 0;border-bottom:2px solid #F0EAE0}
.co-tab{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 10px;font-family:inherit;font-size:13px;font-weight:700;border:none;background:none;cursor:pointer;color:#7A6855;border-bottom:3px solid transparent;margin-bottom:-2px;transition:color .2s,border-color .2s}
.co-tab-active{color:#BA7517;border-bottom-color:#EF9F27}
.co-tab:hover:not(.co-tab-active){color:#412402}
.co-section{padding:20px 24px 24px}
.co-hidden{display:none!important}
.co-subtabs-row{display:flex;gap:10px;margin-bottom:20px}
.co-subtab{flex:1;padding:11px;font-family:inherit;font-size:13px;font-weight:600;border:1.5px solid #E0D5C5;border-radius:40px;background:#F7F2EA;color:#7A6855;cursor:pointer;transition:all .2s}
.co-subtab-active{background:#1C1410;color:#EF9F27;border-color:#1C1410}
.co-subtab:hover:not(.co-subtab-active){background:#E0D5C5}
.co-info-banner{display:flex;align-items:center;gap:9px;background:#FEF6E7;border:1px solid #F5D98B;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#7A4A00;margin-bottom:18px}
.co-banner-green{background:#EAF3DE;border-color:#C0DD97;color:#27500A}
.co-fieldset{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.co-label{font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#7A6855}
.co-req{color:#EF9F27}
.co-hint{font-size:11.5px;color:#e63946;min-height:16px}
.co-input{width:100%;padding:11px 14px;font-family:inherit;font-size:13.5px;color:#1C1410;background:#F7F2EA;border:1.5px solid #E0D5C5;border-radius:10px;outline:none;transition:border-color .2s,box-shadow .2s;box-sizing:border-box}
.co-input:focus{border-color:#EF9F27;background:#fff;box-shadow:0 0 0 3px rgba(239,159,39,.13)}
.co-input.co-err{border-color:#e63946;box-shadow:0 0 0 3px rgba(230,57,70,.1)}
.co-input::placeholder{color:#B0A090}
.co-textarea{resize:vertical;min-height:60px}
.co-tel-wrap{display:flex;align-items:center;background:#F7F2EA;border:1.5px solid #E0D5C5;border-radius:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s}
.co-tel-wrap:focus-within{border-color:#EF9F27;background:#fff;box-shadow:0 0 0 3px rgba(239,159,39,.13)}
.co-tel-prefix{padding:0 12px;font-size:13px;font-weight:600;color:#7A6855;white-space:nowrap;border-right:1.5px solid #E0D5C5;display:flex;align-items:center;background:transparent;height:44px}
.co-input-tel{background:transparent;border:none;box-shadow:none;border-radius:0;flex:1}
.co-input-tel:focus{border:none;box-shadow:none;background:transparent}
.co-btn-location{width:100%;padding:13px;border-radius:10px;border:none;background:#E8821A;color:#fff;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s,transform .15s}
.co-btn-location:hover{background:#C96B0E;transform:translateY(-1px)}
.co-btn-location:active{transform:scale(.98)}
.co-divider-or{display:flex;align-items:center;gap:10px;margin:12px 0;color:#B0A090;font-size:12px}
.co-divider-or::before,.co-divider-or::after{content:'';flex:1;height:1px;background:#E0D5C5}
.co-pay-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.co-pay-opt{display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 10px;font-family:inherit;font-size:13px;font-weight:600;border:1.5px solid #E0D5C5;border-radius:10px;background:#F7F2EA;color:#1C1410;cursor:pointer;transition:all .18s}
.co-pay-opt img{width:20px;height:20px;object-fit:contain}
.co-pay-opt:hover{border-color:#EF9F27;background:#FEF6E7}
.co-pay-opt.co-pay-sel{border-color:#EF9F27;background:#FAEEDA;color:#7A4A00;box-shadow:0 0 0 3px rgba(239,159,39,.15)}
.co-summary{background:#F7F2EA;border-radius:12px;padding:14px 16px;margin:20px 0 16px}
.co-summary-row{display:flex;justify-content:space-between;font-size:13.5px;color:#7A6855;padding:4px 0}
.co-summary-total{font-size:16px;font-weight:800;color:#1C1410;border-top:1px solid #E0D5C5;margin-top:6px;padding-top:10px}
.co-btn-confirm{width:100%;padding:15px;border:none;border-radius:12px;background:linear-gradient(135deg,#EF9F27,#E8821A);color:#fff;font-family:inherit;font-size:15px;font-weight:800;letter-spacing:.04em;cursor:pointer;box-shadow:0 4px 18px rgba(232,130,26,.4);transition:transform .15s,box-shadow .15s}
.co-btn-confirm:hover{transform:translateY(-2px);box-shadow:0 7px 24px rgba(232,130,26,.5)}
.co-btn-confirm:active{transform:scale(.98)}
@media(max-width:480px){
    .co-section{padding:16px}
    .co-head{padding:16px}
}
</style>

<script>
function abrirCheckout() {
    const tot = document.getElementById('cartTotal');
    if (tot) {
        const val = tot.textContent.trim();
        ['co-dom-subtotal','co-rec-total'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        });
        const raw = tot.textContent.replace(/[^0-9]/g,'');
        const num = parseInt(raw||'0', 10);
        const dom = document.getElementById('co-dom-total');
        if (dom) dom.textContent = '$' + (num + 3000).toLocaleString('es-CO');
    }
    document.getElementById('coOverlay').classList.add('co-show');
    document.getElementById('coPanel').classList.add('co-show');
    document.body.style.overflow = 'hidden';
}

function cerrarCheckout() {
    document.getElementById('coOverlay').classList.remove('co-show');
    document.getElementById('coPanel').classList.remove('co-show');
    document.body.style.overflow = '';
}

function coSwitchTab(tab) {
    var tabs   = {domicilio:'coTabDom', recoger:'coTabRec'};
    var panels = {domicilio:'co-sec-domicilio', recoger:'co-sec-recoger'};
    Object.keys(tabs).forEach(function(k) {
        document.getElementById(tabs[k]).classList.toggle('co-tab-active', k === tab);
        document.getElementById(panels[k]).classList.toggle('co-hidden', k !== tab);
    });
}

function coSwitchSubtab(sub) {
    var tabs   = {llevar:'coSubLlevar', restaurante:'coSubRest'};
    var panels = {llevar:'co-sub-llevar', restaurante:'co-sub-restaurante'};
    Object.keys(tabs).forEach(function(k) {
        document.getElementById(tabs[k]).classList.toggle('co-subtab-active', k === sub);
        document.getElementById(panels[k]).classList.toggle('co-hidden', k !== sub);
    });
}

function selPago(btn, prefix) {
    var grid = btn.closest('.co-pay-grid');
    grid.querySelectorAll('.co-pay-opt').forEach(function(b) { b.classList.remove('co-pay-sel'); });
    btn.classList.add('co-pay-sel');
    document.getElementById('co-' + prefix + '-pago').value = btn.dataset.val;
    var hint = document.getElementById('co-' + prefix + '-pay-hint');
    if (hint) hint.textContent = '';
}

function usarUbicacion() {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalizacion.');
        return;
    }
    var btn  = document.querySelector('.co-btn-location');
    var orig = btn.innerHTML;
    btn.innerHTML = 'Obteniendo ubicacion...';
    btn.disabled  = true;

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng)
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    document.getElementById('co-dom-dir').value = d.display_name || (lat.toFixed(5) + ', ' + lng.toFixed(5));
                })
                .catch(function() {
                    document.getElementById('co-dom-dir').value = lat.toFixed(5) + ', ' + lng.toFixed(5);
                })
                .finally(function() { btn.innerHTML = orig; btn.disabled = false; });
        },
        function() {
            alert('No se pudo obtener la ubicacion. Por favor ingresala manualmente.');
            btn.innerHTML = orig;
            btn.disabled  = false;
        }
    );
}

function markInput(id, hasError) {
    var el = document.getElementById(id);
    if (!el) return;
    if (hasError) el.classList.add('co-err');
    else el.classList.remove('co-err');
}

function setHint(id, msg) {
    var el = document.getElementById(id);
    if (el) el.textContent = msg;
}

function confirmarPedido(modo) {
    var ok     = true;
    var telReg = /^3[0-9]{9}$/;

    if (modo === 'domicilio') {
        var nombre = document.getElementById('co-dom-nombre').value.trim();
        var tel    = document.getElementById('co-dom-tel').value.trim();
        var dir    = document.getElementById('co-dom-dir').value.trim();
        var pago   = document.getElementById('co-dom-pago').value;

        markInput('co-dom-nombre', !nombre);
        markInput('co-dom-tel',    !telReg.test(tel));
        markInput('co-dom-dir',    !dir);

        if (!nombre) ok = false;

        if (!telReg.test(tel)) {
            setHint('co-dom-tel-hint', 'Ingresa un numero valido (ej: 3001234567)');
            ok = false;
        } else {
            setHint('co-dom-tel-hint', '');
        }

        if (!dir) {
            setHint('co-dom-dir-hint', 'La direccion es obligatoria');
            ok = false;
        } else {
            setHint('co-dom-dir-hint', '');
        }

        if (!pago) {
            setHint('co-dom-pay-hint', 'Selecciona un metodo de pago');
            ok = false;
        } else {
            setHint('co-dom-pay-hint', '');
        }

        if (ok) {
            _enviarPedido({
                modo:   'domicilio',
                nombre: nombre,
                tel:    tel,
                dir:    dir,
                pago:   pago,
                notas:  document.getElementById('co-dom-notas').value.trim()
            });
        }

    } else {
        var esRest = !document.getElementById('co-sub-restaurante').classList.contains('co-hidden');

        if (esRest) {
            var mesa   = document.getElementById('co-res-mesa').value.trim();
            var nombre = document.getElementById('co-res-nombre').value.trim();
            var pago   = document.getElementById('co-res-pago').value;

            markInput('co-res-mesa',   !mesa || isNaN(mesa));
            markInput('co-res-nombre', !nombre);

            if (!mesa || isNaN(mesa)) {
                setHint('co-res-mesa-hint', 'Ingresa el numero de tu mesa');
                ok = false;
            } else {
                setHint('co-res-mesa-hint', '');
            }

            if (!nombre) ok = false;

            if (!pago) {
                setHint('co-res-pay-hint', 'Selecciona un metodo de pago');
                ok = false;
            } else {
                setHint('co-res-pay-hint', '');
            }

            if (ok) _enviarPedido({modo:'restaurante', mesa:mesa, nombre:nombre, pago:pago});

        } else {
            var nombre = document.getElementById('co-rec-nombre').value.trim();
            var tel    = document.getElementById('co-rec-tel').value.trim();
            var pago   = document.getElementById('co-rec-pago').value;

            markInput('co-rec-nombre', !nombre);
            markInput('co-rec-tel',    !telReg.test(tel));

            if (!nombre) ok = false;

            if (!telReg.test(tel)) {
                setHint('co-rec-tel-hint', 'Ingresa un numero valido');
                ok = false;
            } else {
                setHint('co-rec-tel-hint', '');
            }

            if (!pago) {
                setHint('co-rec-pay-hint', 'Selecciona un metodo de pago');
                ok = false;
            } else {
                setHint('co-rec-pay-hint', '');
            }

            if (ok) _enviarPedido({modo:'recoger', nombre:nombre, tel:tel, pago:pago});
        }
    }
}

function _enviarPedido(datos) {
    if (typeof enviarPedido === 'function') {
        enviarPedido(datos);
    }
    cerrarCheckout();
}
</script>
<?php endif; ?>