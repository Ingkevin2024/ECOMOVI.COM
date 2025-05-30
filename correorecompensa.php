<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "administrador");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener placa
$sql = "SELECT placa FROM vehiculo LIMIT 1";
$resultado = $conexion->query($sql);
$placa = "NO DISPONIBLE";

if ($resultado && $resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $placa = $fila['placa'];
}

// Calcular hora actual y vencimiento
date_default_timezone_set('America/Bogota');
$ahora = date('Y-m-d H:i:s');
$vencimiento = date('Y-m-d H:i:s', strtotime($ahora . ' +12 hours'));

// Generar código aleatorio
$codigoRedencion = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

// Ubicación de redención (puedes cambiarla)
$ubicacionMaps = 'https://www.google.com/maps?q=4.7110,-74.0721'; // Bogotá como ejemplo

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sebastianleong15@gmail.com';
    $mail->Password   = 'qcsd lfqg cpbg oxud';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8'; 

    $mail->setFrom('sebastianleon123@outlook.com', 'EcoMovi');
    $mail->addAddress('sebastianleong15@gmail.com', 'Usuario');

    $mail->isHTML(true);
    $mail->Subject = '🎉 Redención de puntos confirmada - EcoMovi';

    $mail->Body = '
    <div style="background-color:#ffffff; padding: 30px; font-family: Arial, sans-serif; color: #2c3e50; max-width:600px; margin:auto; border-radius:10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
            <img src="https://i.ibb.co/SXzV5TBX/logo-blanco.png" alt="EcoMovi Logo" style="width: 180px; margin-bottom: 20px;">
            <h2 style="color:#4CAF50;">¡Redención Exitosa!</h2>
            <p style="font-size: 16px; line-height: 1.5;">
                Tu solicitud de redención de puntos ha sido completada con éxito.<br>
                Los puntos del vehículo con la placa <strong style="color:#e67e22;">' . $placa . '</strong> han sido utilizados correctamente para obtener tu recompensa.
            </p>

            <div style="background-color:#f2f2f2; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p style="font-size: 15px; margin: 0;">
                    Preséntate con el siguiente <strong>código de redención</strong> para reclamar tu recompensa:
                </p>
                <p style="font-size: 22px; color: #4CAF50; margin: 10px 0;"><strong>' . $codigoRedencion . '</strong></p>
                <p style="font-size: 14px; color: #555; margin: 0;">
                    Ubicación de redención: <a href="' . $ubicacionMaps . '" target="_blank" style="color: #2c7;">Ver en Google Maps</a>
                </p>
            </div>

            <p style="font-size: 15px; color: #555; margin-top: 20px;">
                Tienes <strong>12 horas</strong> a partir de este momento para hacerla efectiva.<br>
                <strong>Fecha y hora de solicitud:</strong> ' . $ahora . '<br>
                <strong>Fecha y hora de vencimiento:</strong> ' . $vencimiento . '
            </p>
           <p style="font-size: 13px; color: #e67e22; background-color: #fff6e0; padding: 10px; border-radius: 6px; margin-top: 10px;">
    <strong>Importante:</strong> Recuerda presentar el <strong>SOAT</strong>, la <strong>revisión técnico-mecánica</strong>, tu <strong>cédula de ciudadanía</strong> y la <strong>licencia de conducción</strong> vigentes al momento de reclamar tu recompensa.
</p>

            <p style="font-size: 13px; color: #e74c3c; background-color: #fef2f2; padding: 10px; border-radius: 6px; margin-top: 10px;">
    <strong>Nota:</strong> Si pasan las 12 horas y no has reclamado la recompensa, los puntos serán devueltos automáticamente.
</p>
            <p style="font-size: 15px; color: #555;">Gracias por confiar en nosotros y ser parte de una movilidad más ecológica.</p>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <div style="text-align: center; margin-top: 20px;">
            <p style="font-size: 14px; color: #777;">Síguenos en nuestras redes sociales</p>
            <a href="https://facebook.com" style="margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/24/145/145802.png" alt="Facebook"></a>
            <a href="https://instagram.com" style="margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/24/2111/2111463.png" alt="Instagram"></a>
            <a href="https://wa.me/573001234567" style="margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/24/733/733585.png" alt="WhatsApp"></a>
        </div>

        <p style="text-align:center; font-size: 12px; color: #aaa; margin-top: 30px;">
            © 2025 EcoMovi - Todos los derechos reservados
        </p>
    </div>';

    $mail->AltBody = 'Tu redención ha sido completada. Código de redención: ' . $codigoRedencion . '. Tienes 12 horas desde ahora (' . $ahora . ') hasta ' . $vencimiento . ' para reclamarla. Ubicación: ' . $ubicacionMaps . '. Placa: ' . $placa . '.';

    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
}
?>
