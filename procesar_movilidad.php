<?php
session_start(); 
date_default_timezone_set('America/Bogota');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plac_veh = $_POST["plac_veh"] ?? '';
    $departamento = $_POST["Departamento"] ?? '';
    $municipio = $_POST["Municipio"] ?? '';
    $foto_inicial = $_FILES['foto_inicial']['name'] ?? '';

    // Obtener fecha y hora actual
    $fecha_inicial = date('Y-m-d');
    $hora_inicial = date('H:i:s');

    // Validar imagen
    $upload_dir = __DIR__ . '/uploads';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $extension = pathinfo($foto_inicial, PATHINFO_EXTENSION);
    $nombre_foto = uniqid('img_') . '.' . $extension;
    $ruta_foto_inicial = $upload_dir . '/' . $nombre_foto;

    if (!move_uploaded_file($_FILES['foto_inicial']['tmp_name'], $ruta_foto_inicial)) {
        echo "<script>alert('Error al subir la imagen.'); window.location.href = 'RegistroV.php';</script>";
        exit;
    }

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "ecomovi");
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Validar si ya existe un registro para esa placa hoy
    $verificar_sql = "SELECT COUNT(*) AS total FROM movilidad WHERE plac_veh = ? AND DATE(fecha_inicial) = ?";
    $stmt = $conn->prepare($verificar_sql);
    $stmt->bind_param("ss", $plac_veh, $fecha_inicial);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila['total'] > 0) {
        echo "<script>alert('Ya se ha registrado un movimiento para esta placa hoy.'); window.location.href = 'RegistroV.php';</script>";
        $conn->close();
        exit;
    }

    // Buscar último registro anterior (de otro día) para actualizar
    $query_antiguo = "SELECT id_mov FROM movilidad 
                      WHERE plac_veh = ? 
                      ORDER BY fecha_inicial DESC LIMIT 1";
    $stmt = $conn->prepare($query_antiguo);
    $stmt->bind_param("s", $plac_veh);
    $stmt->execute();
    $res_antiguo = $stmt->get_result();

    if ($res_antiguo->num_rows > 0) {
        $registro = $res_antiguo->fetch_assoc();
        $id_mov = $registro['id_mov'];
        $_SESSION['id_mov'] = $id_mov;

        // Actualizar el registro anterior con nuevos datos (sin sumar puntos)
        $sql_update = "UPDATE movilidad SET 
            departamento = ?, municipio = ?, 
            fecha_inicial = ?, hora_inicial = ?, 
            foto_inicial = ?, 
            fecha_final = NULL, hora_final = NULL, foto_final = NULL
            WHERE id_mov = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssssi", $departamento, $municipio, $fecha_inicial, $hora_inicial, $ruta_foto_inicial, $id_mov);
        $stmt->execute();
        $stmt->close();

        echo "<script>
            alert('Se actualizó un registro anterior. La segunda parte se habilitará en 1 minuto.');
            window.location.href = 'RegistroV" . urlencode($plac_veh) . "';
        </script>";
    } else {
        // Insertar nuevo registro
        $puntos = 0;

        $sql_insert = "INSERT INTO movilidad 
            (departamento, municipio, plac_veh, fecha_inicial, hora_inicial, foto_inicial, puntos)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssssssi", $departamento, $municipio, $plac_veh, $fecha_inicial, $hora_inicial, $ruta_foto_inicial, $puntos);

        if ($stmt->execute()) {
            $_SESSION['id_mov'] = $stmt->insert_id;
            echo "<script>
                window.location.href = 'RegistroV.php?mensaje=" . urlencode('Registro de movilidad exitoso. La segunda parte se habilitará en 1 minuto.') . "';
            </script>";
        } else {
            echo "<script>alert('Error al registrar: " . $stmt->error . "'); window.location.href = 'RegistroV.php';</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>
