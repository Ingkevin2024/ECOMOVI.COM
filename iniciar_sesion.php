<?php
session_start(); // Inicia la sesión para guardar información del usuario entre páginas

// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecomovi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_doc_usu = $_POST['num_doc_usu'];
    $contrasena = $_POST['contrasena'];
    $tipo_usuario = $_POST['tipo_Inicio'];

    $rol_map = [
        'Usuario' => 'usuario',
        'Administrador' => 'admin',
        'Supervisor' => 'supervisor'
    ];
    $rol = $rol_map[$tipo_usuario];

    // Consulta usuarios
    $sql = "SELECT * FROM usuarios WHERE num_doc_usu = ? AND rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $num_doc_usu, $rol);
    $stmt->execute();
    $resultUsuario = $stmt->get_result();

    // Consulta supervisores
    $sql2 = "SELECT * FROM supervisor WHERE num_doc_usu = ? AND rol = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ss", $num_doc_usu, $rol);
    $stmt2->execute();
    $resultSupervisor = $stmt2->get_result();

    if ($resultUsuario->num_rows > 0) {
        $user = $resultUsuario->fetch_assoc();
    } elseif ($resultSupervisor->num_rows > 0) {
        $user = $resultSupervisor->fetch_assoc();
    }

    if (isset($user)) {
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $rol;
        $_SESSION['num_doc_usu'] = $user['num_doc_usu']; // ✅ Guarda el número de documento

        switch($tipo_usuario) {
            case 'Usuario':
                header("Location: iniusu.html");
                break;
            case 'Supervisor':
                header("Location: supervisor(PRINCIPAL).html");
                break;
            case 'Administrador':
                header("Location: paginaadministrador.html");
                break;
            default:
                header("Location: INICIOSESION.html");
                break;
        }
        exit();
    } else {
        echo "<script>
                alert('Por favor, verifique su usuario. Los datos ingresados no coinciden con nuestros registros.');
                window.location.href = 'INICIOSESION.html';
              </script>";
    }
} else {
    echo "<script>
            alert('Por favor, verifique su usuario. Los datos ingresados no coinciden con nuestros registros.');
            window.location.href = 'INICIOSESION.html';
          </script>";
}

// Cerrar conexiones
$stmt->close();
$stmt2->close();
$conn->close();
?>
