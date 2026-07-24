<?php
function cargarEnv(string $ruta): void
{
    static $cargado = false;
    if ($cargado) return;

    if (!is_file($ruta)) {
        // No detenemos la app si falta el .env en local, pero avisamos en el log.
        error_log("Aviso: no se encontró el archivo de entorno en $ruta");
        return;
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '' || str_starts_with($linea, '#')) continue;
        if (!str_contains($linea, '=')) continue;

        [$clave, $valor] = explode('=', $linea, 2);
        $clave = trim($clave);
        $valor = trim($valor);

        if (strlen($valor) >= 2 && (
            ($valor[0] === '"' && $valor[-1] === '"') ||
            ($valor[0] === "'" && $valor[-1] === "'")
        )) {
            $valor = substr($valor, 1, -1);
        }

        if ($clave === '') continue;

        putenv("$clave=$valor");
        $_ENV[$clave]    = $valor;
        $_SERVER[$clave] = $valor;
    }

    $cargado = true;
}

function env(string $clave, ?string $default = null): ?string
{
    $valor = getenv($clave);
    return $valor !== false ? $valor : $default;
}
