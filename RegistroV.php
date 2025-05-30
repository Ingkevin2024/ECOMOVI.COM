<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$conn = new mysqli("localhost", "root", "", "ecomovi");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['num_doc_usu'])) {
    header('Location: inicar_sesion.php');
    exit();
}

$num_doc_usu = $_SESSION['num_doc_usu'];

$resultVehiculos = $conn->query("SELECT * FROM vehiculos WHERE num_doc_usu = '$num_doc_usu'");

$resultPuntos = $conn->query("
    SELECT plac_veh, COALESCE(SUM(puntos), 0) as total_puntos 
    FROM movilidad 
    WHERE fecha_final IS NOT NULL 
    GROUP BY plac_veh
");

function generarCodigoRedencion($length = 8) {
    return substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehiculo_id'], $_POST['recompensa_id'])) {
    $vehiculosId = $_POST['vehiculo_id'];
    $recompensaId = $_POST['recompensa_id'];

    $stmtPuntos = $conn->prepare("
        SELECT COALESCE(SUM(puntos), 0) as puntos_totales 
        FROM movilidad 
        WHERE plac_veh = ? AND fecha_final IS NOT NULL
    ");
    $stmtPuntos->bind_param("s", $vehiculosId);
    $stmtPuntos->execute();
    $resultadoPuntos = $stmtPuntos->get_result()->fetch_assoc();
    $puntosTotales = $resultadoPuntos['puntos_totales'];

    $stmtRecompensa = $conn->prepare("SELECT puntos, disponible, nom_reco FROM recompensa WHERE nom_reco = ?");
    $stmtRecompensa->bind_param("s", $recompensaId);
    $stmtRecompensa->execute();
    $recompensa = $stmtRecompensa->get_result()->fetch_assoc();

    if ($recompensa && $puntosTotales >= $recompensa['puntos'] && $recompensa['disponible'] > 0) {

        $stmtCorreo = $conn->prepare("SELECT email FROM usuarios WHERE num_doc_usu = ?");
        $stmtCorreo->bind_param("s", $num_doc_usu);
        $stmtCorreo->execute();
        $resultCorreo = $stmtCorreo->get_result()->fetch_assoc();
        $correoUsuario = $resultCorreo ? $resultCorreo['email'] : '';

        $stmtUpdateRecompensa = $conn->prepare("UPDATE recompensa SET disponible = disponible - 1 WHERE nom_reco = ?");
        $stmtUpdateRecompensa->bind_param("s", $recompensaId);
        $stmtUpdateRecompensa->execute();

        $stmtInsert = $conn->prepare("INSERT INTO canjeos (plac_veh, nom_reco, fecha) VALUES (?, ?, NOW())");
        $stmtInsert->bind_param("ss", $vehiculosId, $recompensaId);
        $stmtInsert->execute();

        // Envío de correo
        $mail = new PHPMailer(true);
        try {
            $codigo = generarCodigoRedencion();
            $fechaActual = date('Y-m-d H:i:s');
            $fechaVencimiento = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $urlMaps = 'https://maps.google.com?q=' . rand(1,100) . ',' . rand(1,100); // aleatorio para ejemplo

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ingkevinrivera25@gmail.com';
            $mail->Password = 'wttq tooj egmv ergj';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('ingkevinrivera25@gmail.com', 'EcoMovi');
            $mail->addAddress($correoUsuario);
            $mail->isHTML(true);
            $mail->Subject = '🎉 Redención de puntos confirmada - EcoMovi';

            $mail->Body = '
                <div style="max-width: 650px; margin: auto; padding: 20px; font-family: Arial, sans-serif; color: #333; background-color: #fff; border-radius: 10px;">
                    <div style="text-align: center;">
                       
                        <h2 style="color: green;">¡Redención Exitosa!</h2>
                        <p>Tu solicitud de redención de puntos ha sido completada con éxito.</p>
                        <p>Los puntos del vehículo con la placa <strong style="color: #d35400;">' . htmlspecialchars($vehiculosId) . '</strong> han sido utilizados correctamente para obtener tu recompensa <strong>"' . htmlspecialchars($recompensa['nom_reco']) . '"</strong>.</p>
                    </div>

                    <div style="background-color: #f0f0f0; padding: 15px; margin-top: 20px; border-radius: 8px;">
                        <p><strong>Descripción de la recompensa:</strong> ' . htmlspecialchars($recompensa['puntos']) . ' puntos</p>
                    </div>

                    <div style="background-color: #f0f0f0; padding: 15px; margin-top: 20px; border-radius: 8px;">
                        <p>Preséntate con el siguiente <strong>código de la recompensa</strong> para reclamar tu recompensa:</p>
                        <h2 style="color: green; text-align: center;">' . $codigo . '</h2>
                        <p style="text-align: center;">Ubicación de redención: <a href="' . $urlMaps . '" target="_blank">Ver en Google Maps</a></p>
                    </div>

                    <div style="background-color: #fff3cd; padding: 10px; margin-top: 20px; border-left: 4px solid #ffa500;">
                        <strong>Importante:</strong> Para poder reclamar tu recompensa, es indispensable presentar los documentos actualizados del vehículo. Asegúrate de tener válido el <strong style="color:#d35400;">SOAT</strong> y la <strong style="color:#d35400;">revisión técnico-mecánica</strong>. Estos documentos serán verificados en el momento de la entrega. Te recomendamos revisarlos con anticipación para evitar contratiempos.
                    </div>

                    <p style="margin-top: 20px;">Tienes <strong>12 horas</strong> a partir de este momento para hacerla efectiva.</p>
                    <p><strong>Fecha y hora de solicitud:</strong> ' . $fechaActual . '</p>
                    <p><strong>Fecha y hora de vencimiento:</strong> ' . $fechaVencimiento . '</p>

                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-top: 20px; border-left: 4px solid #f5c6cb;">
                        <strong>Nota:</strong> Si pasan las 12 horas y no has reclamado la recompensa, los puntos serán devueltos automáticamente.
                    </div>

                    <p style="text-align: center; margin-top: 30px;">Gracias por confiar en nosotros y ser parte de una movilidad más ecológica.</p>
                </div>';

            $mail->send();
        } catch (Exception $e) {
            $mensajeRedencion = "Recompensa redimida, pero error al enviar el correo: {$mail->ErrorInfo}";
        }

        $mensajeRedencion = "¡Recompensa redimida con éxito! Código enviado al correo.";
    } else {
        $mensajeRedencion = "No tienes suficientes puntos o la recompensa ya no está disponible.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?mensaje=" . urlencode($mensajeRedencion));
    exit();
}

$resultRecompensas = $conn->query("SELECT * FROM recompensa");
$recompensas = [];
while ($row = $resultRecompensas->fetch_assoc()) {
    $recompensas[] = $row;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EcoMovi - Redimir recompensa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        p {
            color: black;
        }

        h5 {
            color: black;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        .text-white {
            color: white;
        }

        .botoncito{
            position: fixed;
            left: 20px;
            top: 20px;
            z-index: 1000;
            padding: 15px 25px;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
        }

        .botoncito:hover {
            transform: translateY(-3px);
            color: white;
        }

        .card .btn {
            padding: 10px 20px;
            margin: 5px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 80%;
            display: block;
            margin: 10px auto;
        }

        .btn-success {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
        border: none;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-info {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        color: white !important;
        }

        .btn-warning {
        background: linear-gradient(45deg, #f1c40f, #f39c12);
        border: none;
        box-shadow: 0 4px 15px rgba(241, 196, 15, 0.3);
        color: white !important;
        }
        
        .btn-delete{
           background: linear-gradient(45deg,rgba(241, 30, 15, 0.93),rgb(243, 70, 18));
        border: none;
        box-shadow: 0 4px 15px rgba(241, 49, 15, 0.3);
        color: white !important;
        }

        .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .card {
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    border: none;
    padding: 25px 15px;
    background:  #e6f3d9a1 !important;
    transition: all 0.3s ease;
    min-height: 320px;
    display: flex;
    flex-direction: column;
}


        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card-info {
            margin-bottom: 20px;
          
        }

        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: auto;
        }

        .card .btn {
            padding: 12px 20px;
            margin: 0;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn i {
            font-size: 1.1em;
        }
    .list-group-item {
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 10px !important;
        border: 1px   ;
        background: rgba(255, 255, 255, 0.274);
        transition: all 0.3s ease;
    }
    .list-group-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .list-group-item img {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .list-group-item .btn {
        padding: 5px 15px;
        border-radius: 20px;
    }
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
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
:root {
  --anim-duration: 1s;
  --anim-easing: ease;
  --anim-fade-delay-step: 0.2s;
}

@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Aplicar animación fade-in-up al body */
body {
  animation: fade-in-up var(--anim-duration) var(--anim-easing) forwards;
}

/* Título con animación y retraso */
.titulo {
  opacity: 0;
  animation: fade-in-up var(--anim-duration) var(--anim-easing) forwards;
  animation-delay: calc(var(--anim-fade-delay-step) * 0.5);
}

/* Cartas inicialmente invisibles para animar */
.card {
  opacity: 0;
  animation: fade-in-up var(--anim-duration) var(--anim-easing) forwards;
}

/* Animación escalonada para las cartas */
.card:nth-child(1)  { animation-delay: calc(var(--anim-fade-delay-step) * 1); }
.card:nth-child(2)  { animation-delay: calc(var(--anim-fade-delay-step) * 2); }
.card:nth-child(3)  { animation-delay: calc(var(--anim-fade-delay-step) * 3); }
.card:nth-child(4)  { animation-delay: calc(var(--anim-fade-delay-step) * 4); }
.card:nth-child(5)  { animation-delay: calc(var(--anim-fade-delay-step) * 5); }
.card:nth-child(6)  { animation-delay: calc(var(--anim-fade-delay-step) * 6); }
.card:nth-child(7)  { animation-delay: calc(var(--anim-fade-delay-step) * 7); }
.card:nth-child(8)  { animation-delay: calc(var(--anim-fade-delay-step) * 8); }
.card:nth-child(9)  { animation-delay: calc(var(--anim-fade-delay-step) * 9); }
.card:nth-child(10) { animation-delay: calc(var(--anim-fade-delay-step) * 10); }

    </style>
</head>
<body>

<a href="iniusu.html" class="botoncito"><i class="fas fa-arrow-left"></i> Regresar</a>
<div class="container mt-5">
    <link rel="stylesheet" href="estilousuario.css">
    <link rel="icon" href="logo blanco.png" type="image/png">
<?php if (isset($_GET['mensaje'])): ?>
    <script>alert("<?= htmlspecialchars($_GET['mensaje']) ?>");</script>
<?php endif; ?>

<div class="container mt-5">
    <h1  style="font-family: 'Times New Roman', Times, serif; font-weight: bold;" class="text-center"> Consultar Vehículos Registrados</h1>
    <div class="row">
        <?php foreach ($resultVehiculos as $vehiculo): 
            $puntos = 0;
            if ($resultPuntos) {
                $resultPuntos->data_seek(0);
                while ($row = $resultPuntos->fetch_assoc()) {
                    if ($row['plac_veh'] === $vehiculo['plac_veh']) {
                        $puntos = $row['total_puntos'];
                        break;
                    }
                }
            }
        ?>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                   <b> <h4 class="card-title"><?= htmlspecialchars($vehiculo['mar_veh']) ?></h></b>
                     <b><p>
                                    Placa : <?= htmlspecialchars($vehiculo['plac_veh']) ?><br>
                                    Tipo : <?= htmlspecialchars($vehiculo['tip_veh']) ?><br>
                                    Puntos Totales : <?= $puntos ?>
                     </p></b>

                    <div class="buttons-container">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rewardsModal"
                                onclick="showRecompensas('<?= $vehiculo['plac_veh'] ?>')">
                            <i class="fas fa-gift"></i> Recompensa
                        </button>
                        <a href="seguimiento de movilidad.php?id=<?= $vehiculo['plac_veh'] ?>" class="btn btn-info">
                            <i class="fas fa-route"></i> Registrar Movilidad
                        </a>
                        <a href="continuar_formulario.php?plac_veh=<?= $vehiculo['plac_veh'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Completar registro

                       <a href="eliminar_veh.php?plac_veh=<?= $vehiculo['plac_veh'] ?>" 
                            class="btn btn-delete"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este vehículo?');">
                            <i class="fas fa-edit"></i> Eliminar vehículo
                            </a>

                        
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="rewardsModal" tabindex="-1" aria-labelledby="rewardsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="rewardsModalLabel">Selecciona una recompensa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="vehiculo_id" id="vehiculoId">
          <div class="list-group">
            <?php foreach ($recompensas as $recompensa): ?>
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                      <div class="d-flex align-items-center">
                         <img src="<?= htmlspecialchars($recompensa['imagen_url'] ?: 'placeholder.jpg') ?>"
                          alt="Imagen Recompensa"
                          style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                      <div>
                         <?= htmlspecialchars($recompensa['nom_reco']) ?> - <b>Puntos: <?= $recompensa['puntos'] ?></b>
                      </div>
                      </div>    
                        <input type="radio" name="recompensa_id" value="<?= htmlspecialchars($recompensa['nom_reco']) ?>" required>
                    </label>
             <?php endforeach;?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Canjear</button>
        </div>
       

      </form>
    </div>
  </div>
</div>

<script>
function showRecompensas(placa) {
    document.getElementById('vehiculoId').value = placa;
}
</script>

<style>
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .modal-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        background: linear-gradient(45deg, #4CAF50, #45a049);
    }

    .list-group-item {
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 10px !important;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .list-group-item img {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .list-group-item .btn {
        padding: 5px 15px;
        border-radius: 20px;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
