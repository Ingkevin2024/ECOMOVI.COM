<?php

$dsn = 'mysql:host=localhost;dbname=Admi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $tipo_id = $_POST['tipo_id'];
    $numero_id = $_POST['numero_id'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    if (empty($nombre)) {
        echo "<p class='error'>El campo Nombre está vacío.</p>";
    } else {
        echo "<p>Nombre recibido: " . htmlspecialchars($nombre) . "</p>"; // Mostramos el nombre recibido para verificación

        $sql = "INSERT INTO administradores (nombre, tipo_id, numero_id, telefono, correo)
                VALUES (:nombre, :tipo_id, :numero_id, :telefono, :correo)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':numero_id', $numero_id);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':correo', $correo);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Administrador registrado exitosamente.');
                    window.location.href = 'paginap.php'; 
                  </script>";
            exit();
        } else {
            echo "<p class='error'>Error al registrar el administrador.</p>";
        }
    }
}