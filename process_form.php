<?php
// Conexión a la base de datos
$servername = "localhost"; // Nombre del servidor
$username = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$dbname = "ecomovi"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Terminar si la conexión falla
}

// Verificar si se recibió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la fecha y hora actual para los valores iniciales
    $fecha_inicial = date('Y-m-d'); // Fecha actual
    $hora_inicial = date('H:i'); // Hora actual
    
    // Obtener datos del formulario
    $Departamento = isset($_POST['Departamento']) ? $_POST['Departamento'] : ''; // Departamento ingresado
    $municipio = isset($_POST['Municipio']) ? $_POST['Municipio'] : ''; // Municipio ingresado
    $placa_vehiculo = isset($_POST['placa_vehiculo']) ? $_POST['placa_vehiculo'] : ''; // Placa del vehículo
    $fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : date('Y-m-d'); // Fecha final ingresada o actual
    $hora_final = isset($_POST['hora_final']) ? $_POST['hora_final'] : date('H:i'); // Hora final ingresada o actual

    // Validar que la fecha/hora final no sea anterior a la inicial
    $inicio = strtotime($fecha_inicial . ' ' . $hora_inicial); // Convertir fecha/hora inicial a timestamp
    $fin = strtotime($fecha_final . ' ' . $hora_final); // Convertir fecha/hora final a timestamp
    
    if ($fin < $inicio) {
        // Mostrar error si la fecha/hora final es anterior
        echo "<script>
                alert('Error: La fecha/hora final no puede ser anterior a la inicial');
                window.location.href = 'Seguimiento de movilidad.html';
              </script>";
        exit(); // Terminar ejecución
    }

    // Procesar archivos subidos
    $uploadDir = "uploads/"; // Directorio de subida
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Crear directorio si no existe
    }

    // Función para validar y subir archivos
    function uploadFile($fieldName) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null; // Retornar null si no hay archivo o hay error
        }

        $uploadDir = 'uploads/'; // Directorio de subida
        $fileName = basename($_FILES[$fieldName]['name']); // Obtener nombre del archivo
        $uploadFilePath = $uploadDir . $fileName; // Ruta completa del archivo

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadFilePath)) {
            return $uploadFilePath; // Retornar ruta si se subió correctamente
        }

        return null; // Retornar null si no se pudo mover el archivo
    }

    // Subir archivos inicial y final
    $foto_inicial = uploadFile('foto_inicial'); // Foto inicial
    $foto_final = uploadFile('foto_final'); // Foto final

    // Calcular puntos basados en la diferencia de tiempo
    $inicio = strtotime($fecha_inicial . ' ' . $hora_inicial); // Timestamp inicial
    $fin = strtotime($fecha_final . ' ' . $hora_final); // Timestamp final
    $diferencia = $fin - $inicio; // Diferencia en segundos
    $horas = $diferencia / 3600; // Convertir a horas
    $puntos = round($horas * 10); // Calcular puntos (10 puntos por hora)

    // Insertar datos en la base de datos
    $sql = "INSERT INTO movilidad (Departamento, municipio, placa_vehiculo, fecha_inicial, hora_inicial, 
            fecha_final, hora_final, foto_inicial, foto_final, puntos)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql); // Preparar consulta
    $stmt->bind_param("sssssssssi", 
        $Departamento, 
        $municipio, 
        $placa_vehiculo, 
        $fecha_inicial, 
        $hora_inicial, 
        $fecha_final, 
        $hora_final, 
        $foto_inicial, 
        $foto_final, 
        $puntos
    );

    if ($stmt->execute()) {
        // Mostrar mensaje de éxito
        echo "<script>
                alert('Registro guardado exitosamente');
                window.location.href = 'RegistroV.php';
              </script>";
    } else {
        // Mostrar mensaje de error
        echo "<script>
                alert('Error al guardar el registro: " . $stmt->error . "');
                window.location.href = 'Seguimiento de movilidad.html';
              </script>";
    }

    $stmt->close(); // Cerrar statement
} else {
    // Mostrar error si no se recibió el formulario
    echo "<script>
            alert('Error: Formulario no recibido');
            window.location.href = 'Seguimiento de movilidad.html';
          </script>";
}

$conn->close(); // Cerrar conexión
?>
