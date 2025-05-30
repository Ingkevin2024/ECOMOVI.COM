<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_doc_usu = $_POST['num_doc_usu'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_Inicio'];

    // Mapping user types to roles in database
    $rol_map = [
        'Usuario' => 'usuario',
        'Administrador' => 'admin',
        'Supervisor' => 'supervisor'
    ];

    $rol = $rol_map[$tipo_usuario];

    // Select from the correct table based on role
    $tabla = ($rol === 'supervisor') ? 'supervisor' : 'usuarios';
    
    $sql = "SELECT * FROM $tabla WHERE num_doc_usu = ? AND rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $num_doc_usu, $rol);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $rol;
            
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
                    alert('Contraseña incorrecta');
                    window.location.href = 'INICIOSESION.html';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Por favor, verifique su usuario. Los datos ingresados no coinciden con nuestros registros.');
                window.location.href = 'INICIOSESION.html';
              </script>";
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: INICIOSESION.html");
    exit();
}
?>