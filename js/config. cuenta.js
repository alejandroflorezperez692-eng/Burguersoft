function obtenerUsuario() {
    return JSON.parse(localStorage.getItem('usuarioActual'));
}

function mostrarNombreSidebar() {
    const u  = obtenerUsuario();
    const el = document.getElementById('nombre-sidebar');
    if (el && u) {
        el.textContent = (u.nombre || '') + ' ' + (u.apellido || '');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const u = obtenerUsuario();
    if (!u) {
        alert("Debes iniciar sesión primero");
        window.location.href = "login.html";
        return;
    }
    mostrarNombreSidebar();
    document.getElementById('confNombre').value   = u.nombre   || '';
    document.getElementById('confApellido').value = u.apellido || '';
    document.getElementById('confCorreo').value   = u.correo   || '';
});

document.getElementById('formConfiguracion').addEventListener('submit', function(e) {
    e.preventDefault();
    const nombre         = document.getElementById('confNombre').value.trim();
    const apellido       = document.getElementById('confApellido').value.trim();
    const correo         = document.getElementById('confCorreo').value.trim();
    const passwordActual = document.getElementById('confPassActual').value;
    const passwordNueva  = document.getElementById('confPassNueva').value;

    if (!nombre || !correo || !passwordActual || !passwordNueva) {
        alert('Por favor completa todos los campos');
        return;
    }
    if (!correo.includes('@')) {
        alert('Por favor ingresa un correo valido');
        return;
    }
    if (passwordNueva.length < 6) {
        alert('La nueva contraseña debe tener al menos 6 caracteres');
        return;
    }

    const u = obtenerUsuario();
    fetch('http://localhost:3000/usuarios/' + u.id_Usuario, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            nombre_usuario:    nombre,
            apellido_usuario:  apellido,
            correo_personal:   correo,
            contrasena_actual: passwordActual,
            contrasena_nueva:  passwordNueva
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const actualizado = { ...u, nombre, apellido, correo, nombre_completo: nombre + ' ' + apellido };
            localStorage.setItem('usuarioActual', JSON.stringify(actualizado));
            mostrarNombreSidebar();
            alert('Cambios guardados exitosamente');
        } else {
            alert('Error: ' + (data.mensaje || 'No se pudo guardar'));
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
});

document.getElementById('cerrarSesion').addEventListener('click', function() {
    if (confirm('¿Estas seguro de que deseas cerrar sesion?')) {
        localStorage.removeItem('usuarioActual');
        window.location.href = 'login.html';
    }
});

document.getElementById('eliminarCuenta').addEventListener('click', function() {
    if (confirm('¿Estas seguro de que deseas eliminar tu cuenta? Esta accion no se puede deshacer.')) {
        const u = obtenerUsuario();
        fetch('http://localhost:3000/usuarios/' + u.id_Usuario, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                localStorage.removeItem('usuarioActual');
                alert('Cuenta eliminada');
                window.location.href = 'login.html';
            } else {
                alert('Error al eliminar: ' + (data.mensaje || ''));
            }
        })
        .catch(() => alert('Error de conexión'));
    }
});