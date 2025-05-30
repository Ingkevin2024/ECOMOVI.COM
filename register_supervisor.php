<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $tipo_id = $_POST['tipo_id'];
    $num_doc_usu = $_POST['num_doc_usu'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = 'Supervisor';

    // Verify connection to ecomovi database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if document number exists in supervisor table
    $check_doc = "SELECT * FROM supervisor WHERE num_doc_usu = ? OR correo = ?";
    $stmt = $conn->prepare($check_doc);
    $stmt->bind_param("ss", $num_doc_usu, $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Este número de documento o correo ya está registrado');
                window.location.href = 'REGISTROADMI.html';
              </script>";
    } else {
        // Insert into supervisor table
        $sql = "INSERT INTO supervisor (nombre, tipo_id, num_doc_usu, telefono, correo, password, rol) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssssss", $nombre, $tipo_id, $num_doc_usu, $telefono, $correo, $password, $rol);

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
    }
    $conn->close();
}
?>
