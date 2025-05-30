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

// Consulta para obtener la primera placa
$sql = "SELECT placa FROM vehiculo LIMIT 1";
$resultado = $conexion->query($sql);
$placa = "NO DISPONIBLE";

if ($resultado && $resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $placa = $fila['placa'];
}

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sebastianleong15@gmail.com';
    $mail->Password   = 'qcsd lfqg cpbg oxud';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('sebastianleon123@outlook.com', 'EcoMovi');
    $mail->addAddress('sebastianleong15@gmail.com', 'Usuario');



    // Contenido HTML del correo
    $mail->isHTML(true);
    $mail->Subject = 'Puntos regresados';
    $mail->Body = '
    <div style="background-color:#f7f7f7; padding: 30px; text-align: center; font-family: Arial, sans-serif;">
        <h3 style="color: #2c3e50;">Hola, kevin giovanny</h3>
        <p style="font-size: 16px; color: #333;">
            Tus puntos del vehículo con la placa <strong>' . $placa . '</strong> han sido devueltos  correctamente a su plataforma.<br><br>
            ¡Gracias por usar nuestra plataforma!
        </p>
        <hr style="margin: 40px 0; border: none; border-top: 1px solid #ccc;">
        <img src="https://i.ibb.co/SXzV5TBX/logo-blanco.png" " alt="Logo EcoMovi" style="width: 220px; height: auto; margin-bottom: 10px;">
        <h2 style="color: #4CAF50; margin: 0;">Grupo EcoMovi</h2>
    </div>';

    $mail->AltBody = 'Hola, tus puntos del vehículo con la placa ' . $placa . ' han sido regresados correctamente. Grupo EcoMovi.';

    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
}
?>
