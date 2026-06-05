<?php
// ============================================================
//  includes/enviar_correo.php
// ============================================================

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USUARIO',  'burguersoft@gmail.com');
define('SMTP_PASSWORD', 'zxvh jfaq dylw ndzq');
define('SMTP_REMITENTE','BURGUERSOFT - El Oriente');

function enviarCodigoRecuperacion(string $correoDestino, string $codigo): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet   = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USUARIO;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_USUARIO, SMTP_REMITENTE);
        $mail->addAddress($correoDestino);
        $mail->isHTML(true);
        $mail->Subject = 'Código de recuperación - BURGUERSOFT';

        $digitos = str_split($codigo);

        $mail->addEmbeddedImage(
            __DIR__ . '/imagenes/logotrapa.png', 
            'logo_cid'                    
        );

        $mail->Body = '
<div style="background:#f0ece8;padding:40px;font-family:Arial,sans-serif;">
  <div style="max-width:580px;margin:auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e0d8d0;">
    <div style="background:#2c1810;padding:28px 32px;text-align:center;">
      <p style="margin:0;font-size:11px;letter-spacing:2px;color:#E8821A;text-transform:uppercase;">BURGUERSOFT · EL ORIENTE</p>
      <p style="margin:8px 0 0;font-size:22px;font-weight:bold;color:#ffffff;">Recuperación de contraseña</p>
    </div>
    <div style="padding:36px 40px;text-align:center;">
      <img src="cid:logo_cid" alt="BURGUERSOFT" style="width:180px;margin-bottom:15px;">
      <p style="font-size:16px;color:#2c1810;margin:0 0 8px;font-weight:bold;">Hola, recibimos tu solicitud.</p>
      <p style="font-size:14px;color:#666666;margin:0 0 28px;line-height:1.6;">
        Ingresa este código en la página para restablecer tu contraseña.<br>
        <strong>Expira en 30 minutos.</strong>
      </p>
      <div style="margin-bottom:28px;">
        <p style="margin:0 0 12px;font-size:12px;letter-spacing:1.5px;color:#E8821A;text-transform:uppercase;font-weight:900;">Tu código de recuperación</p>
        <div style="display:inline-block;background:#2c1810;color:#ffffff;font-size:28px;font-weight:bold;letter-spacing:8px;padding:16px 32px;border-radius:8px;font-family:monospace;">
          ' . $codigo . '
        </div>
      </div>
      <div style="background:#faeeda;border-radius:8px;padding:12px 16px;margin-bottom:24px;">
        <p style="margin:0;font-size:13px;color:#633806;line-height:1.5;text-align:center;">
          &#9888; No compartas este código con nadie.
        </p>
      </div>
      <p style="font-size:13px;color:#999999;margin:0;">
        Si no solicitaste este cambio, ignora este correo.
      </p>
    </div>
    <div style="border-top:1px solid #e0d8d0;padding:16px 32px;text-align:center;">
      <p style="margin:0;font-size:12px;color:#999999;">&copy; 2026 BURGUERSOFT &mdash; El Oriente</p>
    </div>
  </div>
</div>';
        $mail->AltBody = "Tu código de recuperación es: $codigo\n\nExpira en 30 minutos.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
