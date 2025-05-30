<?php
session_start(); // Siempre al principio del archivo

$conn = new mysqli("localhost", "root", "", "ecomovi");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener la placa desde GET, POST o SESSION
$plac_veh = '';

if  (isset($_GET['id'])) {
    $plac_veh = $_GET ['id'];
}
    else {
    echo '<script>
        alert("Error: No se recibió una placa de vehículo. Por favor, seleccione un vehículo.");
        window.location.href = "RegistroV.php";
    </script>';
    exit;
}


$ya_registrado_hoy = false;

if (!empty($plac_veh)) {
    $fecha_hoy = date('Y-m-d');

    $query = "SELECT COUNT(*) as total FROM movilidad WHERE plac_veh = ? AND DATE(fecha_inicial) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $plac_veh, $fecha_hoy);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila['total'] > 0) {
        $ya_registrado_hoy = true;
    }
}

$deshabilitar_primera_parte = "";
if ($ya_registrado_hoy) {
    $deshabilitar_primera_parte = 'disabled';
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO DE MOVILIDAD</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
            background: #e6f3d9a1; /* Fondo semitransparente */
            padding: 25px;
            border-radius: 20px;

        /* Sombra luminiscente */
         box-shadow:
        0 0 10px #00ff00,
        0 0 20px #00ff00,
        0 0 30px #00ff00;

        }
    
        @keyframes glow {
    0% { box-shadow: 0 0 5px #00ff00; }
    50% { box-shadow: 0 0 20px #00ff00; }
    100% { box-shadow: 0 0 5px #00ff00; }
}

    .frase {
    animation: glow 2s infinite;
    }

        .frase strong {
            color:rgb(0, 0, 0);
            font-size: 1.5em;
            display: block;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .highlight {
            background:rgba(0, 0, 0);
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

                .back-button {
                    position: absolute;
                    left: calc(35% - 400px);
                    top: 100%;
                    transform: translateY(-50%);
                    padding: 12px 24px;
                    background: #28a745;
                    color: white;
                    border: none;
                    border-radius: 20px;
                    cursor: pointer;
                    font-size: 16px;
                    box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
                    transition: all 0.3s ease;
                    text-decoration: none;
                }
        
                .back-button:hover {
                    background: #218838;
                    transform: translateY(-50%) scale(1.05);
                }
        
                @media (max-width: 1200px) {
                    .back-button {
                        position: fixed;
                        left: 20px;
                    }
                }
    </style>
</head>
<body>
    <div class="frase-container2">
        <div class="frase">
            <strong style="font-family: 'Times New Roman', Times, serif;">Registro de Movilidad</strong><br><br>
            
            <b>Este apartado permitirá observar y confirmar su participación en esta alternativa sostenible. 
            <span class="highlight"> A mayor cantidad de registros, mayor adquisición de puntos y por lo tanto recompensas.</b></span><br><br>

            <b>En este formulario se ingresará información sobre el seguimiento de su movilidad, la cual 
            corresponde a los registros de pico y placa voluntario. Esta información se deberá diligenciar 
            en 2 momentos del día:</b><br><br>

            <strong style="font-family: 'Times New Roman', Times, serif;">Primer Momento:</strong> <b>Se solicitará información sobre el departamento, municipio y la primera foto del kilometraje
            de su vehículo. Suministrada la información se guardará la primera parte.</b><br><br>

            <strong style="font-family: 'Times New Roman', Times, serif;">Segundo Momento:</strong> <b>Se solicitará únicamente la foto del kilometraje final, y se finalizará guardando la 
            información completa.</b>
        </div>
    </div>

    <a href="RegistroV.php" class="back-button">← Volver</a>
    

    <div class="container">
        <h2>REGISTRO DE MOVILIDAD</h2>
    <?php if (!empty($id_veh)): ?>
    <div class="puntos">
        <h4>Puntos <?= htmlspecialchars($puntos ?? 0) ?></h4>
    </div>
    <?php endif; ?>

    <!-- FORMULARIO PRIMERA PARTE -->
         
    <form action="procesar_movilidad.php" method="POST" enctype="multipart/form-data">
    <label for="Departamento">Departamento:</label>
    <input type="text" name="Departamento" required 
           value="Antioquia" readonly>

    <label for="Municipio">Municipio:</label>
    <input type="text" name="Municipio" required 
           value="Medellín" readonly>

    <!-- En la sección del formulario -->
    <label for="plac_veh">Placa del Vehículo:</label>
        <input type="text" name="plac_veh" id="plac_veh" 
    value="<?= htmlspecialchars($plac_veh) ?>" readonly required>

        

    <label for="fecha_inicial">Fecha Inicial:</label>
    <input type="text" id="fecha_inicial" name="fecha_inicial" readonly required>

    <label for="hora_inicial">Hora Inicial:</label>
    <input type="text" id="hora_inicial" name="hora_inicial" readonly required>

    <label for="foto_inicial">Subir Foto Inicial:</label>
    <input type="file" name="foto_inicial" id="foto_inicial" accept="image/*" required onchange="previewImage(this, 'previewInicial')">
    
    <div id="previewContainer" style="margin-top: 15px; display: none;">
        <p><strong>Vista previa de la foto:</strong></p>
        <img id="previewInicial" src="#" alt="Vista previa" style="max-width: 100%; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    </div>

    <button type="submit" name="guardar_primera_parte" <?= $deshabilitar_primera_parte ?>>
        Guardar Primera Parte
    </button>
</form>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const container = document.getElementById('previewContainer');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
   

<script>
$(document).ready(function() {
    $("#plac_veh").autocomplete({
        source: "Seguimiento de movilidad.php",
        minLength: 1,
        select: function(event, ui) {
            $("input[name='plac_veh']").val(ui.item.value);
        }
    });

    function actualizarFechaHora() {
        var ahora = new Date();
        var fecha = ahora.toLocaleDateString("es-ES");
        var hora = ahora.toLocaleTimeString("es-ES");

        $('#fecha_inicial').val(fecha);
        $('#hora_inicial').val(hora);
        $('#fecha_final').val(fecha);
        $('#hora_final').val(hora);
    }

    actualizarFechaHora();
    setInterval(actualizarFechaHora, 10000);

});
</script>
