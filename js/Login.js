// ============================================================
//  js/Login.js  — VERSIÓN PHP
//
//  El formulario de login ahora es un <form method="POST">,
//  así que este archivo ya NO necesita hacer fetch().
//
//  Solo mantenemos validación visual antes de enviar.
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const correo = document.getElementById('email').value.trim();
            const pass   = document.getElementById('password').value;

            if (!correo || !pass) {
                e.preventDefault();
                alert('Por favor completa todos los campos.');
                return;
            }

            // El formulario se envía normalmente a login.php (POST)
            // PHP valida las credenciales con password_verify() y redirecciona.
        });
    }
});
