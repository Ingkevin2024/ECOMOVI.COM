<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Database connection
$conexion = new mysqli("localhost", "root", "", "ecomovi");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Get the latest registered user's information
$sql = "SELECT nom_usu, apell_usu, email, num_doc_usu FROM usuarios";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $nom_usu = $fila['nom_usu'];
    $apell_usu = $fila['apell_usu'];
    $email = $fila['email'];
    $num_doc_usu = $fila['num_doc_usu'];
} else {
    die("No se encontró información del usuario");
}

$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ingkevinrivera25@gmail.com';
    $mail->Password   = 'uewd phtb ufjt riyw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Sender and recipient
    $mail->setFrom('ingkevinrivera25@gmail.com', 'EcoMovi');
    $mail->addAddress($email, $nom_usu, $apell_usu);



    // Contenido HTML del correo
    $mail->isHTML(true);
    $mail->Subject = 'Bienvenido a - EcoMovi';
    $mail->Body = '
    <div style="background-color:#f7f7f7; padding: 30px; text-align: center; font-family: Arial, sans-serif;">
        <h3 style="color: #2c3e50;">¡Bienvenido, ' . $nom_usu . ' ' . $apell_usu . '!</h3>
        <p style="font-size: 16px; color: #333;">
            Gracias por registrarte en nuestra plataforma EcoMovi.<br>
            Tu cuenta ha sido creada exitosamente.<br><br>
            Tus credenciales de acceso son:<br>
            <strong>Usuario:</strong> ' . $num_doc_usu . '<br>
            <strong>Contraseña:</strong> La que registraste durante el proceso de inscripción<br><br>
            Para comenzar a usar nuestros servicios, haz clic en el botón de abajo:
        </p>
        <a href="http://localhost/PAGINAPRESENTACION/INICIOSESION.html" style="
            background-color: #4CAF50;
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;">
            Iniciar Sesión
        </a>
        <hr style="margin: 40px 0; border: none; border-top: 1px solid #ccc;">
        <img src="http://localhost/PAGINAPRESENTACION/Imgen/logofinal.png" alt="Logo EcoMovi" style="width: 220px; height: auto; margin-bottom: 10px;">
        <h2 style="color: #4CAF50; margin: 0;">Grupo EcoMovi</h2>
    </div>';

    $mail->AltBody = '¡Bienvenido ' . $nom_usu . '! Tu cuenta ha sido creada exitosamente. Por favor, visita http://localhost/PAGINAPRESENTACION/login.php para iniciar sesión. Grupo EcoMovi.';

    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
}
?>
