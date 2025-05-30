<?php  
date_default_timezone_set('America/Bogota');
session_start();

$plac_veh = '';
if (isset($_GET['plac_veh'])) {
    $plac_veh = $_GET['plac_veh'];
} elseif (isset($_SESSION['plac_veh'])) {
    $plac_veh = $_SESSION['plac_veh'];
} elseif (isset($_GET['id'])) {
    $plac_veh = $_GET['id'];
}

if (!empty($plac_veh)) {
    $_SESSION['plac_veh'] = $plac_veh;
} else {
    echo "<script>
        alert('Error: No se recibi\u00f3 una placa de veh\u00edculo. Por favor, seleccione un veh\u00edculo.');
        window.location.href = 'RegistroV.php';
    </script>";
    exit;
}

$conn = new mysqli("localhost", "root", "", "ecomovi");
if ($conn->connect_error) {
    die("Error de conexi\u00f3n: " . $conn->connect_error);
}

$upload_dir = __DIR__ . '/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$sql_info = "SELECT id_mov, fecha_inicial, hora_inicial FROM movilidad WHERE plac_veh = ? AND fecha_final IS NULL ORDER BY fecha_inicial DESC LIMIT 1";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("s", $plac_veh);
$stmt_info->execute();
$result_info = $stmt_info->get_result();

if ($row_info = $result_info->fetch_assoc()) {
    $id = $row_info['id_mov'];
    $fecha_inicial = $row_info['fecha_inicial'];
    $hora_inicial = $row_info['hora_inicial'];
    $tiempo_inicial = strtotime($fecha_inicial . ' ' . $hora_inicial);
    $tiempo_actual = time();
    $diferencia_minutos = ($tiempo_actual - $tiempo_inicial) / 10;

    if ($diferencia_minutos < 1) {
        $segundos_restantes = ceil((1 - $diferencia_minutos) * 10);
        echo "<script>alert('Debe esperar al menos 1 minuto antes de subir la foto final. Faltan aproximadamente $segundos_restantes segundos.'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('No se encontr\u00f3 informaci\u00f3n de la primera parte.'); window.location.href='RegistroV.php';</script>";
    exit;
}
$stmt_info->close();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_segunda_parte'])) {
    $fecha_final = date('Y-m-d');
    $hora_final = date('H:i:s');

    if (!isset($_FILES['foto_final']) || $_FILES['foto_final']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error al subir la foto final.'); window.history.back();</script>";
        exit;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['foto_final']['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        echo "<script>alert('Formato de imagen no v\u00e1lido.'); window.history.back();</script>";
        exit;
    }

    $extension = pathinfo($_FILES['foto_final']['name'], PATHINFO_EXTENSION);
    $foto_final = uniqid('final_') . '.' . $extension;
    $ruta_foto_final = $upload_dir . '/' . $foto_final;

    if (!move_uploaded_file($_FILES['foto_final']['tmp_name'], $ruta_foto_final)) {
        echo "<script>alert('No se pudo guardar la imagen.'); window.history.back();</script>";
        exit;
    }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restricciones = [
        'carro' => [],
        'moto' => []
    ];
    
    // Procesar datos del carro
    foreach ($_POST['carro'] as $dia => $digitos) {
        $restricciones['carro'][(int)$dia] = array_map('intval', $digitos);
    }
    
    // Procesar datos de la moto
    foreach ($_POST['moto'] as $dia => $digitos) {
        $restricciones['moto'][(int)$dia] = array_map('intval', $digitos);
    }
    
    // Mostrar el array resultante
    echo "<pre>";
    echo "\$restricciones = " . var_export($restricciones, true) . ";";
    echo "</pre>";
    
    // También puedes guardarlo en un archivo o base de datos aquí
} else {
    header("Location: continuar_formulario.php");
    exit;
}
   $dia_semana = date('N');

// Obtener tipo de vehículo
 $sql_tipo = "SELECT tip_veh FROM vehiculos WHERE plac_veh = ?";
    $stmt_tipo = $conn->prepare($sql_tipo);
    $stmt_tipo->bind_param("s", $plac_veh);
    $stmt_tipo->execute();
    $result_tipo = $stmt_tipo->get_result();
    $tipo_vehiculo = $result_tipo->fetch_assoc()['tip_veh'];
    $stmt_tipo->close();

    if (strtolower($tipo_vehiculo) === 'moto') {
        preg_match('/\d+/', $plac_veh, $matches);
        $numeros = $matches[0];
        $digito = (int)substr($numeros, -1);
    } else {
        $digito = (int)substr($plac_veh, -1);
    }

    $restricciones = [
        'carro' => [
            1 => [3, 4],
            2 => [2, 8],
            3 => [5, 9],
            4 => [1, 7],
            5 => [0, 6]
        ],
        'moto' => [
            1 => [3, 4],
            2 => [2, 8],
            3 => [5, 9],
            4 => [1, 7],
            5 => [0, 6]
        ]
    ];

    $restriccion_actual = $restricciones[strtolower($tipo_vehiculo)][$dia_semana] ?? [];
    $tiene_pico_placa = in_array($digito, $restriccion_actual);
    $puntos_nuevos = $tiene_pico_placa ? 100 : 200;
    
    // Procesar datos del carro
    foreach ($_POST['carro'] as $dia => $digitos) {
        $restricciones['carro'][(int)$dia] = array_map('intval', $digitos);
    }
    
    // Procesar datos de la moto
    foreach ($_POST['moto'] as $dia => $digitos) {
        $restricciones['moto'][(int)$dia] = array_map('intval', $digitos);
    }

   



    $restriccion_actual = $restricciones[strtolower($tipo_vehiculo)][$dia_semana] ?? [];
    $tiene_pico_placa = in_array($digito, $restriccion_actual);
    $puntos_nuevos = $tiene_pico_placa ? 100 : 200;

    $sql_suma = "SELECT IFNULL(SUM(puntos), 0) FROM movilidad WHERE plac_veh = ? AND id_mov != ?";
    $stmt_suma = $conn->prepare($sql_suma);
    $stmt_suma->bind_param("si", $plac_veh, $id);
    $stmt_suma->execute();
    $stmt_suma->bind_result($puntos_previos);
    $stmt_suma->fetch();
    $stmt_suma->close();

    $puntos_totales = $puntos_previos + $puntos_nuevos;
    
// ✅ Sumar los nuevos puntos al valor actual del registro
$sql_update = "UPDATE movilidad 
               SET fecha_final = ?, 
                   hora_final = ?, 
                   foto_final = ?, 
                   puntos = puntos + ?
               WHERE id_mov = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("ssssi", $fecha_final, $hora_final, $ruta_foto_final, $puntos_nuevos, $id);




    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Registro finalizado. Puntos acumulados: $puntos_totales');
            window.location.href = 'RegistroV.php?plac_veh=" . urlencode($plac_veh) . "';
        </script>";
    } else {
        echo "<script>alert('❌ Error al actualizar puntos: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Registro de Movilidad</title>
    
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
            background: #e6f3d9a1;
            padding: 20px;
            border-radius: 0.9rem;
            max-width: 400px;
            margin: 60px auto;
            text-align: center;
            box-shadow: 0 0 10px #00FF00;
        }
        
        h2, h3 {
            color: black;
            font-family: 'Times New Roman', Times, serif;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: black;
        }

        input[type="text"], input[type="file"] {
            width: calc(100% - 20px);
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            border: none;
            background-color: #28a745;
            padding: 10px;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #218838;
        }

        .disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .puntos-totales {
            background-color: rgba(40, 167, 69, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .puntos-totales h4 {
            color: #28a745;
            margin: 0;
            font-size: 1.2em;
        }
        .frase-container2 {
            padding: 30px;
            max-width: 3050px;
            margin: 20px auto;
            animation: slideDown 0.8s ease-out;
        }

        .frase {
            background: rgba(255, 255, 255, 0.274);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-left: 5px solid #28a745;
        }

        .frase strong {
            color: #28a745;
            font-size: 1.5em;
            display: block;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .highlight {
            background: linear-gradient(120deg, #28a745 0%, #28a745 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            font-weight: bold;
            padding: 0 5px;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .frase-container2 {
                padding: 15px;
                margin: 10px;
            }
            .frase {
                padding: 20px;
            }
            .frase strong {
                font-size: 1.3em;
            }
        }

        @keyframes brillo {
            0% {
                box-shadow: 0 0 15px 5px #00ff00;
            }

            50% {
                box-shadow: 0 0 35px 15px #00ff00;
            }

            100% {
                box-shadow: 0 0 15px 5px #00ff00;
            }
        }

        .borde-verde {
            border: 3.5px solid #00ff00;
            box-shadow: 0 0 25px 10px #00ff00;
            animation: brillo 1s infinite ease-in-out;
        }
    </style>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        </head>
        <body>
            <!-- Add the back button -->
            <a href="RegistroV.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Devolver
            </a>
            
            <div class="container">
                <h2>Finalizar Registro de Movilidad</h2>
                
             
                <div class="info-section">
                    <p><strong>Placa:</strong> <?= htmlspecialchars($plac_veh) ?></p>
                    <p><strong>Fecha Inicial:</strong> <?= htmlspecialchars($fecha_inicial) ?></p>
                    <p><strong>Hora Inicial:</strong> <?= htmlspecialchars($hora_inicial) ?></p>
                </div>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="upload-section">
                        <label for="foto_final">
                            <i class="fas fa-camera"></i> Subir Foto Final
                        </label>
                        <input type="file" id="foto_final" name="foto_final" accept="image/*" required onchange="previewImage(this);">
                        <label class="custom-file-upload" for="foto_final">
                            Seleccionar Archivo
                        </label>
                        <div id="imagePreview" style="margin-top: 20px; display: none;">
                            <p><strong>Vista previa:</strong></p>
                            <img id="preview" src="#" alt="Vista previa de la foto" style="max-width: 100%; max-height: 300px; margin: 10px auto;">
                        </div>
                    </div>
                    <button type="submit" name="guardar_segunda_parte">
                        <i class="fas fa-save"></i> Guardar Segunda Parte
                    </button>
                </form>
            </div>
            <script>
                function previewImage(input) {
                    const preview = document.getElementById('preview');
                    const previewContainer = document.getElementById('imagePreview');
                    
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            previewContainer.style.display = 'block';
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
                .back-button {
                    position: fixed;
                    left: 20px;
                    top: 20px;
                    padding: 10px 20px;
                    background: #28a745;
                    color: white;
                    border: none;
                    border-radius: 25px;
                    cursor: pointer;
                    font-size: 16px;
                    box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
        
                .back-button:hover {
                    background: #218838;
                    transform: scale(1.05);
                }
            </style>

            
            <script>
                function previewImage(input) {
                    const preview = document.getElementById('preview');
                    const previewContainer = document.getElementById('imagePreview');
                    
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            previewContainer.style.display = 'block';
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <script>
                function actualizarHoraFinal() {
                    const ahora = new Date();
                    const fecha = ahora.toLocaleDateString();
                    const hora = ahora.toLocaleTimeString();
                    
                    document.querySelector('.info-section p:nth-child(2)').innerHTML = '<strong>Fecha Final:</strong> ' + fecha;
                    document.querySelector('.info-section p:nth-child(3)').innerHTML = '<strong>Hora Final:</strong> ' + hora;
                }
            
                // Actualizar cada segundo
                setInterval(actualizarHoraFinal, 1000);
                actualizarHoraFinal(); // Ejecutar inmediatamente
            </script>
