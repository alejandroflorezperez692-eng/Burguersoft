document.addEventListener("DOMContentLoaded", () => {

   
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        inputNombre.addEventListener('input', function() {
            let pos = this.selectionStart;
            this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '');
            if (this.value.length > 0)
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            this.setSelectionRange(pos, pos);
        });
    }

    const inputApellido = document.getElementById('apellido');
    if (inputApellido) {
        inputApellido.addEventListener('input', function() {
            let pos = this.selectionStart;
            this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ ]/g, '');
            if (this.value.length > 0)
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            this.setSelectionRange(pos, pos);
        });
    }

    const passwordInput = document.getElementById("password");
    if (passwordInput) {
        passwordInput.addEventListener("input", (e) => evaluarPassword(e.target.value));
    }

    const form = document.getElementById("registroForm");
    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const nombre           = document.getElementById("nombre").value.trim();
            const apellido         = document.getElementById("apellido").value.trim();
            const correo           = document.getElementById("correo").value.trim();
            const tipoDocumento    = document.getElementById("tipo-documento").value.trim();
            const numeroDocumento  = document.getElementById("numero-documento").value.trim();
            const password         = document.getElementById("password").value.trim();
            const confirmar        = document.getElementById("confirmar-password").value.trim();

            if (!nombre || !apellido || !correo || !tipoDocumento || !numeroDocumento || !password || !confirmar) {
                alert("Por favor completa todos los campos.");
                return;
            }

            const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexCorreo.test(correo)) {
                alert("El correo no es válido.");
                return;
            }

            const dominiosPermitidos = ['@gmail.com', '@hotmail.com', '@outlook.com', '@yahoo.com', '@icloud.com'];
            if (!dominiosPermitidos.some(d => correo.endsWith(d))) {
                alert("Solo se permiten correos de Gmail, Hotmail, Outlook, Yahoo o iCloud.");
                return;
            }

            if (!/^\d{6,15}$/.test(numeroDocumento)) {
                alert("El número de documento debe tener entre 6 y 15 dígitos.");
                return;
            }

            if (password !== confirmar) {
                alert("Las contraseñas no coinciden.");
                return;
            }

            if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[^A-Za-z0-9]/.test(password)) {
                alert("La contraseña no cumple los requisitos mínimos de seguridad.");
                return;
            }

            const formData = new FormData();
            formData.append('nombre',           nombre);
            formData.append('apellido',         apellido);
            formData.append('correo',           correo);
            formData.append('Tdocumento',        tipoDocumento);
            formData.append('numero_documento', numeroDocumento);
            formData.append('password',         password);

            fetch('/burguersoft/php/Registro.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) { alert("Error: " + data.error); return; }
                alert("¡Registro exitoso! Ya puedes iniciar sesión.");
                window.location.href = "/burguersoft/php/login.php";
            })
            .catch(err => {
                console.error('Error:', err);
                alert("Error al conectar con el servidor.");
            });
        });
    }
});

function evaluarPassword(val) {
    const checks = {
        longitud:  val.length >= 8,
        mayuscula: /[A-Z]/.test(val),
        numero:    /[0-9]/.test(val),
        especial:  /[^A-Za-z0-9]/.test(val)
    };

    let puntos = Object.values(checks).filter(Boolean).length;

    Object.entries(checks).forEach(([id, ok]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = (ok ? '✅' : '❌') + el.textContent.slice(1);
    });

    const barra = document.getElementById('progreso');
    if (!barra) return;

    if (val.length === 0) {
        barra.style.width = '0%';
        barra.style.backgroundColor = '#e0e0e0';
        return;
    }

    const colores = ['#e53e3e', '#dd6b20', '#d69e2e', '#38a169'];
    barra.style.width = (puntos * 25) + '%';
    barra.style.backgroundColor = colores[puntos - 1] || '#e0e0e0';

    verificarCoincidencia();
}

function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    const oculto = input.type === 'password';
    input.type = oculto ? 'text' : 'password';
    btn.textContent = oculto ? 'Ocultar' : 'Mostrar';
}

function verificarCoincidencia() {
    const pass = document.getElementById('password');
    const conf = document.getElementById('confirmar-password');
    const msg  = document.getElementById('msg-confirmar');
    if (!pass || !conf || !msg) return;
    if (!conf.value) { msg.textContent = ''; return; }
    if (pass.value === conf.value) {
        msg.textContent = '✅ Las contraseñas coinciden';
        msg.style.color = '#38a169';
    } else {
        msg.textContent = '❌ Las contraseñas no coinciden';
        msg.style.color = '#e53e3e';
    }
}
