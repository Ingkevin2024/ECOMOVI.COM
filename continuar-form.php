<?php
// Conexión a la BD 
$conn = new mysqli("localhost", "root", "", "ecomovi");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID de la placa
// Change in the initial GET/POST check
$plac_veh = $_POST['plac_veh'] ?? $_GET['id'] ?? null;
if (!$plac_veh) {
    die("Placa de vehículo no especificada.");
}

// Consultar los datos del vehículo en la BD
// Update the query to use correct field name
$query_plac = "SELECT * FROM movilidad WHERE plac_veh = ?";
$stmt_plac = mysqli_prepare($conn, $query_plac);
mysqli_stmt_bind_param($stmt_plac, 's', $id_plac);

mysqli_stmt_execute($stmt_plac);
$result_plac = mysqli_stmt_get_result($stmt_plac);
$plac = mysqli_fetch_assoc($result_plac);

// Update the default array with new field names
if (!$plac) {
    $plac = [
        'plac_veh' => $plac_veh,
        'fecha_inicial' => '',
        'hora_inicial' => '',
        'fecha_final' => '',
        'hora_final' => '',
        'foto_inicial' => '',
        'foto_final' => '',
        'puntos' => 0
    ];
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        mysqli_begin_transaction($conn);

        // Manejo de subida de fotos
        function subirFoto($nombreCampo, $directorio) {
            if (!empty($_FILES[$nombreCampo]['name'])) {
                $nombreArchivo = basename($_FILES[$nombreCampo]['name']);
                $rutaDestino = $directorio . $nombreArchivo;

                // Mover archivo al servidor
                if (move_uploaded_file($_FILES[$nombreCampo]['tmp_name'], $rutaDestino)) {
                    return $nombreArchivo;
                }
            }
            return null;
        }

        $foto_inicial = subirFoto('foto_inicial', 'uploads/');
        $foto_final = subirFoto('foto_final', 'uploads/');

        // Si ya hay una foto inicial, mantener la existente
        if (empty($foto_inicial)) {
            $foto_inicial = $plac['foto_inicial'];
        } else {
            // Si se sube la foto inicial, registrar fecha y hora actuales
            $_POST['fecha_inicial'] = date("Y-m-d");
            $_POST['hora_inicial'] = date("H:i");
        }

        // Si ya hay una foto final, mantener la existente
        if (empty($foto_final)) {
            $foto_final = $plac['foto_final'];
        } else {
            // Si se sube la foto final, registrar fecha y hora actuales
            $_POST['fecha_final'] = date("Y-m-d");
            $_POST['hora_final'] = date("H:i");
        }

        // Actualizar los datos del vehículo
        // Update the UPDATE query with new field names
        $query_update_plac = "UPDATE movilidad SET
                    fecha_inicial = ?, hora_inicial = ?, 
                    fecha_final = ?, hora_final = ?,
                    foto_inicial = ?, foto_final = ?, 
                    puntos = ?, created_at = NOW()
                    WHERE plac_veh = ?";

        $stmt_update_plac = mysqli_prepare($conn, $query_update_plac);
        // Update in the SQL binding
        mysqli_stmt_bind_param($stmt_update_plac, 'ssssssss',
            $_POST['fecha_inicial'], $_POST['hora_inicial'],
            $_POST['fecha_final'], $_POST['hora_final'],
            $foto_inicial, $foto_final,
            $puntos, $plac_veh
        );
        mysqli_stmt_execute($stmt_update_plac);
        mysqli_commit($conn);
        
        echo "Datos actualizados correctamente.";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Error en la actualización: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Fotos</title>
    <link rel="stylesheet" href="seguimiento.css">
    
    <script>
        function verificarTiempo() {
            let horaInicial = document.getElementById("hora_inicial").value;
            let fechaInicial = document.getElementById("fecha_inicial").value;
            let foto2 = document.getElementById("foto_final");

            if (horaInicial && fechaInicial) {
                let fechaHoraInicial = new Date(`${fechaInicial}T${horaInicial}`);
                let ahora = new Date();
                let diferenciaHoras = (ahora - fechaHoraInicial) / (1000 ); // Diferencia en horas

                foto2.disabled = diferenciaHoras < 1;
            }
        }

        function guardarHora(campo) {
            let ahora = new Date();
            if (campo === 'inicial') {
                document.getElementById("fecha_inicial").value = ahora.toISOString().split('T')[0];
                document.getElementById("hora_inicial").value = ahora.toTimeString().slice(0, 5);
            } else if (campo === 'final') {
                document.getElementById("fecha_final").value = ahora.toISOString().split('T')[0];
                document.getElementById("hora_final").value = ahora.toTimeString().slice(0, 5);
            }
        }
    </script>
</head>
<body onload="verificarTiempo()">
  <div class="container mt-5 mx-auto">
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="Departamento">Departamento:</label>
        <input type="text" name="Departamento" value="<?= htmlspecialchars($plac['Departamento']) ?>" required readonly>

        <label for="municipio">Municipio:</label>
        <input type="text" name="municipio" value="<?= htmlspecialchars($plac['municipio']) ?>" required readonly>

        <label for="id_plac">Placa del Vehículo:</label>
        <input type="text" name="id_plac" value="<?= htmlspecialchars($plac['id_plac']) ?>" required readonly>

        <label for="foto_inicial">Foto Inicial:</label>
        <input type="file" name="foto_inicial" id="foto_inicial" <?= !empty($plac['foto_inicial']) ? 'disabled' : 'required' ?> onchange="guardarHora('inicial')">
        <?php if (!empty($plac['foto_inicial'])): ?>
            <p>Imagen guardada: <a href="uploads/<?= htmlspecialchars($plac['foto_inicial']) ?>" target="_blank">Ver</a></p>
        <?php endif; ?>

        <label for="fecha_inicial">Fecha Inicial:</label>
        <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?= htmlspecialchars($plac['fecha_inicial']) ?>" readonly>

        <label for="hora_inicial">Hora Inicial:</label>
        <input type="time" name="hora_inicial" id="hora_inicial" value="<?= htmlspecialchars($plac['hora_inicial']) ?>" readonly>

        <label for="foto_final">Foto Final:</label>
        <input type="file" name="foto_final" id="foto_final" disabled onchange="guardarHora('final')">
        <?php if (!empty($plac['foto_final'])): ?>
            <p>Imagen guardada: <a href="uploads/<?= htmlspecialchars($plac['foto_final']) ?>" target="_blank">Ver</a></p>
        <?php endif; ?>

        <label for="fecha_final">Fecha Final:</label>
        <input type="date" name="fecha_final" id="fecha_final" value="<?= htmlspecialchars($plac['fecha_final']) ?>" readonly>

        <label for="hora_final">Hora Final:</label>
        <input type="time" name="hora_final" id="hora_final" value="<?= htmlspecialchars($plac['hora_final']) ?>" readonly>

        <label for="puntos">Puntos:</label>
        <input type="number" name="puntos" value="<?= htmlspecialchars($plac['puntos']) ?>" readonly>

        <button type="submit">Subir Fotos</button>
    </form>
  </div>
</body>
</html>
