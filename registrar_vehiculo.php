<?php


// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecomovi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_doc_usu = $_POST['num_doc_usu'] ?? null;

    if (!$num_doc_usu) {
        echo "<script>
                alert('Número de documento no proporcionado.');
                window.location.href = 'registrarv.html';
              </script>";
        exit;
    }
}


// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener campos del formulario
    $plac_veh = strtoupper($_POST['plac_veh']);
    $tip_veh = $_POST['tip_veh'];
    $tarj_prop_veh = $_POST['tarj_prop_veh'];
    $tecno_m = $_POST['tecno_m'];
    $soat = $_POST['soat'];
    $mar_veh = $_POST['mar_veh'];
    $lin_veh = $_POST['lin_veh'];
    $color_veh = $_POST['color_veh'];
    $num_motor_veh = $_POST['num_motor_veh'];
    $clase_veh = $_POST['clase_veh'];
    $combus_veh = $_POST['combus_veh'];
    $capaci_veh = $_POST['capaci_veh'];
    $num_chasis_veh = $_POST['num_chasis_veh'];
    $model_veh = $_POST['model_veh'];

    // Obtener el número de documento del usuario (desde POST o sesión)
    $num_doc_usu = isset($_POST['num_doc_usu']) ? $_POST['num_doc_usu'] : (isset($_SESSION['num_doc_usu']) ? $_SESSION['num_doc_usu'] : '');

    if (empty($num_doc_usu)) {
        echo "<script>
                alert('Número de documento del usuario no encontrado. Por favor, inicie sesión.');
                window.location.href = 'iniusu.html';
              </script>";
        exit();
    }

   // Verificar que el usuario exista en la tabla usuarios y esté asociado a un vehículo
// Verificar que el usuario exista en la tabla usuarios
$verificar_usuario = $conn->prepare("
    SELECT nom_usu FROM usuarios WHERE num_doc_usu = ?
");

$verificar_usuario->bind_param("s", $num_doc_usu);
$verificar_usuario->execute();
$res_usuario = $verificar_usuario->get_result();

if ($res_usuario->num_rows == 0) {
    echo "<script>
            alert('El usuario no existe o no tiene vehículos registrados. Debe registrarse primero.');
            window.location.href = 'Registro de usuario.html';
          </script>";
    exit();
}


    // Verificar si la placa ya está registrada
    $check_placa = $conn->prepare("SELECT plac_veh FROM vehiculos WHERE plac_veh = ?");
    $check_placa->bind_param("s", $plac_veh);
    $check_placa->execute();
    $result = $check_placa->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('La placa ya está registrada.');
            window.history.back();
          </script>";
    exit();
}

    // Verificar duplicidad en tarjeta de propiedad
$check_tarj = $conn->prepare("SELECT tarj_prop_veh FROM vehiculos WHERE tarj_prop_veh = ?");
$check_tarj->bind_param("s", $tarj_prop_veh);
$check_tarj->execute();
$res_tarj = $check_tarj->get_result();
if ($res_tarj->num_rows > 0) {
    echo "<script>
            alert('La tarjeta de propiedad ya está registrada.');
            window.history.back();
          </script>";
    exit();
}

// Verificar duplicidad en tecno
$check_tecno = $conn->prepare("SELECT tecno_m FROM vehiculos WHERE tecno_m = ?");
$check_tecno->bind_param("s", $tecno_m);
$check_tecno->execute();
$res_tecno = $check_tecno->get_result();
if ($res_tecno->num_rows > 0) {
    echo "<script>
            alert('El número de revisión técnico mecánica ya está registrado.');
            window.history.back();
          </script>";
    exit();
}

// Verificar duplicidad en SOAT
$check_soat = $conn->prepare("SELECT soat FROM vehiculos WHERE soat = ?");
$check_soat->bind_param("s", $soat);
$check_soat->execute();
$res_soat = $check_soat->get_result();
if ($res_soat->num_rows > 0) {
    echo "<script>
            alert('El número del SOAT ya está registrado.');
            window.history.back();
          </script>";
    exit();
}

// Verificar duplicidad en número de motor
$check_motor = $conn->prepare("SELECT num_motor_veh FROM vehiculos WHERE num_motor_veh = ?");
$check_motor->bind_param("s", $num_motor_veh);
$check_motor->execute();
$res_motor = $check_motor->get_result();
if ($res_motor->num_rows > 0) {
    echo "<script>
            alert('El número de motor ya está registrado.');
            window.history.back();
          </script>";
    exit();
}

// Verificar duplicidad en número de chasis
$check_chasis = $conn->prepare("SELECT num_chasis_veh FROM vehiculos WHERE num_chasis_veh = ?");
$check_chasis->bind_param("s", $num_chasis_veh);
$check_chasis->execute();
$res_chasis = $check_chasis->get_result();
if ($res_chasis->num_rows > 0) {
    echo "<script>
            alert('El número de chasis ya está registrado.');
            window.history.back();
          </script>";
    exit();
}

    // Manejo de archivos
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    function subirArchivo($campo) {
        global $upload_dir;
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] == 0) {
            $ruta = $upload_dir . uniqid() . "_" . basename($_FILES[$campo]['name']);
            move_uploaded_file($_FILES[$campo]['tmp_name'], $ruta);
            return $ruta;
        }
        return "";
    }

    $foto_tecno = subirArchivo('foto_tecno');
    $foto_soat = subirArchivo('foto_soat');
    

    // Insertar el vehículo
    $sql = "INSERT INTO vehiculos (
                plac_veh, tip_veh, tarj_prop_veh, tecno_m, foto_tecno,
                soat, foto_soat, mar_veh, lin_veh, color_veh, num_motor_veh,
                clase_veh, combus_veh, capaci_veh, num_chasis_veh, model_veh,
              num_doc_usu
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssssi",
        $plac_veh, $tip_veh, $tarj_prop_veh, $tecno_m, $foto_tecno,
        $soat, $foto_soat, $mar_veh, $lin_veh, $color_veh,
        $num_motor_veh, $clase_veh, $combus_veh, $capaci_veh,
        $num_chasis_veh, $model_veh, $num_doc_usu);

    if ($stmt->execute()) {
        echo "<script>
                alert('Vehículo registrado exitosamente');
                window.location.href = 'iniusu.html';
              </script>";
    } else {
        echo "<script>
                alert('Error al registrar el vehículo: " . $stmt->error . "');
                window.location.href = 'registrarv.html';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de vehiculo </title>
</head>
<body>
      <style>
      body {
        height: 100vh;
        margin: 0;
        justify-content: center;
        align-items: center;
        background: url(images/fonfi.jpeg) no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
      }

      body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0);
        z-index: -1;
      }

      .container {
        background-color:#e6f3d97e;
        padding: 20px;
        border-radius: 0.6rem;
        max-width: 590px;
        margin: 60px auto;
        position: relative;
        z-index: 2;
      }

      h2 {
        text-align: center;
        margin-bottom: 10px;
        color: black;
        font-family: 'Times New Roman', Times, serif;
      }

      .form-label {
        display: block;
        margin-bottom: 4px;
        font-weight: bold;
        color: black;
      }

      .col-12 button {
        border: none;
        outline: none;
        background-color: #28a745;
        padding: 12px 30px;
        border-radius: 25px;
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        width: 100%;
        cursor: pointer;
      }

      .col-12 button:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
      }

      .col-12 button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.2);
      }

      .btn-secondary {
        position: fixed;
        top: 20px;
        left: 20px;
        border: none;
        outline: none;
        background-color: #4CAF50;
        padding: 12px 25px;
        border-radius: 25px;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        text-decoration: none;
        z-index: 1000;
        cursor: pointer;
      }

      .btn-secondary:hover {
        background-color: #45a049;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        color: white;
        text-decoration: none;
      }

      .btn-secondary:active {
        transform: translateY(1px);
        box-shadow: 0 2px 10px rgba(76, 175, 80, 0.2);
      }

      .btn-secondary img {
        width: 20px;
        height: 20px;
      }

      @keyframes brillo {
        0% {
          box-shadow: 0 0 5px 1px #00ff00;
        }

        50% {
          box-shadow: 0 0 15px 6px #00ff00;
        }

        100% {
          box-shadow: 0 0 5px 1px #00ff00;
        }
      }

      .borde-verde {
        padding: 20px;
        border: 3px solid #00ff00;
        border-radius: 15px;
        box-shadow: 0 0 8px 2px #00ff00;
        animation: brillo 2s infinite ease-in-out;
      }
    </style>


</body>
</html>