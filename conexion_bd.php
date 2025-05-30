<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecomovi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>
            alert('Error de conexión: " . $conn->connect_error . "');
            window.location.href = 'Registro de usuario.html';
         </script>");
}

// Get form data
$nom_usu = $_POST['nom_usu'];
$apell_usu = $_POST['apell_usu'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$tipo_documento = $_POST['tipo_documento'];
$num_doc_usu = $_POST['num_doc_usu'];
$direccion = $_POST['direccion'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
$rol = $_POST['rol'];


// Prepare SQL statement
// Corrected SQL statement
$sql = "INSERT INTO usuarios (nom_usu, apell_usu, fecha_nacimiento, tipo_documento, num_doc_usu, direccion, email, telefono, contrasena, rol) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssississ", $nom_usu, $apell_usu, $fecha_nacimiento, $tipo_documento, $num_doc_usu, $direccion, $email, $telefono, $contrasena, $rol);

// Execute and check if successful
if ($stmt->execute()) {
    echo "<script>
            alert('Usuario registrado exitosamente');
            window.location.href = 'INICIOSESION.html';
          </script>";
} else {
    echo "<script>
            alert('Error al registrar usuario: " . $stmt->error . "');
            window.location.href = 'Registro de usuario.html';
          </script>";
}

// Close connection
$stmt->close();
$conn->close();
?>