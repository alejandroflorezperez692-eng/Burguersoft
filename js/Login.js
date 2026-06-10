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

       
        });
    }
});
