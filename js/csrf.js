
(function () {
    function obtenerTokenCSRF() {
        if (window.CSRF_TOKEN) return window.CSRF_TOKEN;
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    var fetchOriginal = window.fetch;
    if (!fetchOriginal || fetchOriginal.__csrfParcheado) return;

    var fetchConCSRF = function (recurso, opciones) {
        opciones = opciones || {};
        var metodo = (opciones.method || 'GET').toUpperCase();
        var modificaDatos = ['POST', 'PUT', 'DELETE', 'PATCH'].indexOf(metodo) !== -1;

        if (modificaDatos) {
            var token = obtenerTokenCSRF();
            if (token) {
                if (opciones.headers instanceof Headers) {
                    opciones.headers.set('X-CSRF-Token', token);
                } else {
                    opciones.headers = Object.assign({}, opciones.headers, { 'X-CSRF-Token': token });
                }
            }
        }
        return fetchOriginal.call(this, recurso, opciones);
    };
    fetchConCSRF.__csrfParcheado = true;
    window.fetch = fetchConCSRF;
})();
