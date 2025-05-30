<?php
// Importar clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Requerir los archivos de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Crear instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'mail.smtp2go.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tu-usuario-smtp2go';       // Cambia por tu usuario SMTP2GO
    $mail->Password   = 'tu-contraseña-smtp2go';     // Cambia por tu contraseña SMTP2GO
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 2525; // SMTP2GO usa 2525, 587, o 465 para SSL

    // Configuración del remitente y destinatario
    $mail->setFrom('tu-correo@dominio.com', 'Nombre Remitente');
    $mail->addAddress('destinatario@dominio.com', 'Nombre Destinatario'); 

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Prueba de Envío';
    $mail->Body    = '<b>Este es un correo de prueba</b> enviado desde PHP usando PHPMailer y SMTP2GO.';
    $mail->AltBody = 'Este es un correo de prueba enviado desde PHP usando PHPMailer y SMTP2GO.';

    // Enviar el correo
    $mail->send();
    echo '✅ El mensaje ha sido enviado correctamente.';
} catch (Exception $e) {
    echo "❌ No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
}
?>
