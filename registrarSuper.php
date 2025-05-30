<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecomovi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $tipo_documento = $_POST['tipo_id'];
    $numero_documento = $_POST['numero_id'];
    $telefono = $_POST['telefono'];
    $email = $_POST['correo'];
    $contrasena = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    $rol = "Supervisor";

    // Check if user already exists
    $check_sql = "SELECT * FROM usuarios WHERE numero_documento = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $numero_documento);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Este número de documento ya está registrado');
                window.location.href = 'REGISTROADMI.html';
              </script>";
    } else {
        // Insert new supervisor
        $sql = "INSERT INTO usuarios (nombre, tipo_documento, numero_documento, telefono, email, contrasena, rol) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $nombre, $tipo_documento, $numero_documento, $telefono, $email, $contrasena, $rol);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Supervisor registrado exitosamente');
                    window.location.href = 'INICIOSESION.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al registrar supervisor: " . $stmt->error . "');
                    window.location.href = 'REGISTROADMI.html';
                  </script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
$conn->close();
?>
