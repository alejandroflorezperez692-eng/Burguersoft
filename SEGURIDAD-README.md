# Seguridad implementada en BURGUERSOFT

## ⚠️ Acción manual OBLIGATORIA antes de nada
La contraseña de aplicación de Gmail (`SMTP_PASSWORD`) estaba escrita en texto plano en
el código y probablemente ya quedó en el historial de Git. **Ve a tu cuenta de Google,
revoca esa contraseña de aplicación y genera una nueva.** Colócala solo en el archivo
`.env` (que ya está en `.gitignore` y nunca se sube al repositorio).

## 1) Antes de usar el proyecto
1. Copia `.env.example` como `.env` y coloca tus credenciales reales (BD y SMTP).
2. Ejecuta la migración `sql/migracion_seguridad.sql` sobre tu base de datos:
   ```
   mysql -u root -p burguersoft < sql/migracion_seguridad.sql
   ```
   Esto crea la tabla `intentos_seguridad` (rate limiting) y amplía la columna
   `token_recuperacion` para que quepa un hash en vez de un código de 6 dígitos.
3. Verifica que `.env` NO se suba a git: `git status` no debe mostrarlo.

## 2) Qué se implementó

### Credenciales fuera del código
- `includes/env.php`: cargador simple de `.env` (sin dependencias).
- `includes/conexion.php` y `includes/funciones.php` (`getPDO()`) ahora leen host/usuario/
  contraseña de BD desde `.env` en vez de tenerlos escritos en el código.
- `includes/enviar_correo.php` lee las credenciales SMTP desde `.env`.

### CSRF (Cross-Site Request Forgery)
- `includes/seguridad.php`: `generarCSRFToken()`, `requerirCSRF()` (para APIs JSON) y
  `requerirCSRFFormulario()` (para formularios HTML clásicos).
- Se validó en: login, registro, recuperación de contraseña (solicitar código, verificar
  código, guardar nueva contraseña), edición de perfil/contraseña del cliente, y en los
  controladores `usuarios`, `Gestion-usuarios`, `productos`, `promociones`, `backups`,
  `compras`, `ventas`, `materiaprima`, `marcas`, `pedidos`.
- `js/csrf.js`: parcha `window.fetch` para adjuntar automáticamente el header
  `X-CSRF-Token` en toda petición POST/PUT/DELETE, sin tener que tocar cada archivo JS
  existente. Se carga desde `includes/admin_layout.php`, `includes/header_publico.php` y
  `php/Registro.php`.

### Cabeceras HTTP de seguridad y sesión
- `enviarCabecerasSeguridad()`: agrega `Content-Security-Policy`, `X-Frame-Options`,
  `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` y `Strict-Transport-
  Security` (cuando hay HTTPS).
- `configurarCookieSesion()`: cookies de sesión con `HttpOnly`, `SameSite=Lax` y `Secure`
  automático si el sitio corre por HTTPS.
- Todos los `session_start()` sueltos se reemplazaron por `includes/sesion_segura.php`
  (o por `iniciarSesionSegura()`), que configura la cookie ANTES de iniciar la sesión.
- `regenerarSesionTrasLogin()`: se llama justo después de un login exitoso para evitar
  fijación de sesión (session fixation).

### CORS
- `Gestion-usuarios.php` ya no responde `Access-Control-Allow-Origin: *` (peligroso en un
  endpoint autenticado por cookie). Ahora restringe el origen a tu propio dominio.

### Subida de archivos (imágenes de productos y promociones)
- `validarImagenSubida()`: valida el tipo MIME real (con `finfo`, no la extensión que
  declara el navegador), limita el tamaño a 5MB y genera un nombre de archivo aleatorio.
- `uploads/.htaccess` (y en `uploads/productos/`, `uploads/promociones/`): bloquea que
  se pueda ejecutar PHP u otros scripts dentro de esas carpetas, aunque alguien logre
  subir un archivo malicioso.

### Rate limiting persistente (por IP + identificador, en base de datos)
- Antes, el bloqueo de intentos fallidos vivía solo en `$_SESSION`, así que bastaba con
  borrar cookies para resetearlo. Ahora se guarda en la tabla `intentos_seguridad` y se
  aplica a: login, solicitud de código de recuperación y verificación de código.

### Código de recuperación de contraseña
- Antes se guardaba en texto plano en la BD. Ahora se guarda con `password_hash()` y se
  valida con `password_verify()`, igual que las contraseñas de usuario.

## 3) Lo que NO se automatizó (recomendado para después)
- **CAPTCHA** (reCAPTCHA/hCaptcha) en login, registro y recuperación, para frenar bots.
- **2FA (TOTP)** opcional para cuentas de Administrador.
- **Backups**: `backups.php` exporta la tabla `usuario` completa (incluye hashes de
  contraseña). Vale la pena excluir esas columnas del export o cifrar el archivo.
- **`display_errors`**: revisa que `APP_ENV=produccion` en el `.env` del servidor real
  para no mostrar detalles internos si algo falla.
- **WAF / rate limiting de red** (Cloudflare, ModSecurity) como capa adicional.
- Noté un archivo `js/config. cuenta.js` que llama a `http://localhost:3000/usuarios/...`
  — parece un endpoint de prueba (json-server) no conectado al backend real; no lo toqué
  porque no forma parte de la API de BURGUERSOFT, pero conviene revisarlo o eliminarlo.

## 4) Archivos nuevos
- `includes/env.php`, `includes/seguridad.php`, `includes/sesion_segura.php`
- `.env`, `.env.example`
- `sql/migracion_seguridad.sql`
- `js/csrf.js`
- `uploads/.htaccess` (+ copias en `uploads/productos/` y `uploads/promociones/`)
