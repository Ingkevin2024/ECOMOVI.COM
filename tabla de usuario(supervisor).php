<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$conexion = new mysqli("localhost", "root", "", "ecomovi");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $placa = trim($_POST["placa"]);
    $puntos = (int)$_POST["puntos"];
    $recompensa = $_POST["recompensa"];
    $accion = $_POST["accion"];

    if ($accion === "redimir") {
        // Actualiza puntos (si quieres que no reste puntos, comenta esta parte)
        $stmt = $conexion->prepare("UPDATE movilidad SET puntos = puntos - ? WHERE plac_veh = ?");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("is", $puntos, $placa);
        $stmt->execute();
        $stmt->close();

        // Marca la recompensa como redimida y pone fecha
        $stmtRedimir = $conexion->prepare("UPDATE canjeos SET redimido = 1, fecha_hora = NOW() WHERE plac_veh = ? AND nom_reco = ?");
        $stmtRedimir->bind_param("ss", $placa, $recompensa);
        $stmtRedimir->execute();
        $stmtRedimir->close();

        // Preparar envío de correo
        $mail = new PHPMailer(true);
        try {
            $stmtDoc = $conexion->prepare("SELECT num_doc_usu FROM vehiculos WHERE plac_veh = ?");
            $stmtDoc->bind_param("s", $placa);
            $stmtDoc->execute();
            $stmtDoc->bind_result($docUsuario);
            $stmtDoc->fetch();
            $stmtDoc->close();

            $emailUsuario = null;
            if ($docUsuario) {
                $stmtEmail = $conexion->prepare("SELECT email FROM usuarios WHERE num_doc_usu = ?");
                $stmtEmail->bind_param("s", $docUsuario);
                $stmtEmail->execute();
                $stmtEmail->bind_result($emailUsuario);
                $stmtEmail->fetch();
                $stmtEmail->close();
            }

            if (!$emailUsuario) {
                throw new Exception("No se pudo obtener el correo electrónico del propietario del vehículo.");
            }

            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sebastianleong15@gmail.com';
            $mail->Password = 'qcsd lfqg cpbg oxud';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sebastianleon123@outlook.com', 'EcoMovi');
            $mail->addAddress($emailUsuario, 'Usuario');

            $mail->isHTML(true);
            $mail->Subject = 'Recompensa redimida';
            $mail->Body = "
            <div style='padding:40px; font-family:Arial, sans-serif; background:#f9f9f9;'>
                <div style='max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:20px; text-align:center;'>
                    <img src='https://i.ibb.co/r2MRcCG4/logofinal.png' alt='EcoMovi Logo' style='height:60px; margin-bottom:20px;'/>
                    <h2 style='color:#27ae60; margin-bottom:10px;'> Notificación de EcoMovi</h2>
                    <p>Has redimido exitosamente tu recompensa. El vehículo con la placa <strong style='color:#27ae60;'>$placa</strong> ha utilizado sus puntos.</p>
                    <p style='font-style:italic; color:#555; margin-top:20px;'>Gracias por confiar en nosotros y ser parte de una movilidad más ecológica.</p>
                </div>
            </div>
            ";
            $mail->AltBody = "Has redimido exitosamente tu recompensa. El vehículo con la placa $placa ha utilizado sus puntos.";

            $mail->send();
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }

        // Redirigir para evitar reenvío de formulario y recargar la página
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    }
}

// Consulta para mostrar la tabla
$query = "SELECT c.fecha_hora, v.plac_veh, r.nom_reco AS recompensa, r.puntos, c.redimido
    FROM canjeos c
    JOIN vehiculos v ON c.plac_veh = v.plac_veh
    JOIN recompensa r ON c.nom_reco = r.nom_reco";

if (isset($_GET['search_placa']) && !empty($_GET['search_placa'])) {
    $search_placa = $conexion->real_escape_string($_GET['search_placa']);
    $query .= " WHERE v.plac_veh LIKE '%$search_placa%'";
}

$query .= " ORDER BY c.fecha_hora DESC";
$resultado = $conexion->query($query);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="logo blanco.png" type="icon">
    <meta charset="UTF-8">
    <title>ECOMOVI - Tabla de Canjeos</title>
    <link rel="stylesheet" href="css/tabla-usuario.css">
    <style>
       .boton-confirmar {
           background-color: #28a745;
           color: white;
           padding: 10px 20px;
           border: none;
           border-radius: 10px;
           font-weight: 700;
           font-size: 16px;
           cursor: pointer;
           transition: background-color 0.3s ease;
       }
       .boton-confirmar:hover:not(:disabled) {
           background-color: #218838;
       }
       .boton-confirmar:disabled {
           background-color: #a50000;
           cursor: not-allowed;
           opacity: 0.7;
       }

       .modal {
           display: none;
           position: fixed;
           left: 0; top: 0;
           width: 100%; height: 100%;
           background: rgba(255, 255, 255, 0.2);
           backdrop-filter: blur(4px);
           z-index: 1000;
       }

       .modal-content {
           background: white;
           padding: 30px 25px;
           margin: 8% auto;
           width: 420px;
           max-width: 90%;
           border: 5px solid #28a745;
           box-shadow: 0 0 20px #28a745;
           border-radius: 12px;
           position: relative;
           font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
           color: black;
       }

       .modal-content h3{
        font-family: 'Times New Roman', Times, serif;
       }
       .close {
           position: absolute;
           right: 20px;
           top: 20px;
           font-size: 28px;
           font-weight: bold;
           color: #333;
           cursor: pointer;
           transition: color 0.3s ease;
       }
       .close:hover {
           color: #28a745;
       }

       h2 {
           font-family: 'Georgia', serif;
           font-weight: 600;
           font-size: 28px;
           margin-bottom: 15px;
           color: #222;
       }
    </style>
</head>
<body>
<header>
    <nav>
        <h2 class="titulo-recompensas">TABLA DE RECOMPENSAS</h2>
        <a href="paginaadministrador.html">Inicio</a>
    </nav>
</header>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#d4edda; padding:10px; margin-bottom:20px;">
         Redención exitosa se ha enviado al correo del usuario
    </div>
<?php endif; ?>

<form method="GET" class="search-form" style="margin-bottom: 20px;">
    <input type="text" name="search_placa" placeholder="Buscar por placa..." style="padding: 8px; width: 200px; margin-right: 10px;">
    <button type="submit" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">
        Buscar
    </button>
</form>

<table border="1">
    <thead>
        <tr>
            <th>Placa</th>
            <th>Recompensa</th>
            <th>Puntos</th>
            <th>Fecha de Redención</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php
        while ($fila = $resultado->fetch_assoc()) {
            $placa = htmlspecialchars($fila['plac_veh']);
            $recompensa = htmlspecialchars($fila['recompensa']);
            $puntos = $fila['puntos'];
            $redimido = isset($fila['redimido']) ? (int)$fila['redimido'] : 0;

            // Mostrar la fecha solo si fue redimido
            $fecha_hora = ($redimido && $fila['fecha_hora']) 
                ? date("d/m/Y h:i A", strtotime($fila['fecha_hora'])) 
                : "—";

            if ($redimido === 1) {
                // Botón deshabilitado si ya redimido
                $boton = "<button class='boton-confirmar' disabled style='background-color:#a50000; cursor:not-allowed;'>Redimido</button>";
            } else {
                // Botón habilitado si no redimido
                $boton = "<button class='boton-confirmar' onclick='mostrarModal(\"$placa\", \"$recompensa\", \"$puntos\", this)'>Redimir</button>";
            }

            echo "<tr>
                <td>$placa</td>
                <td>$recompensa</td>
                <td>$puntos</td>
                <td>$fecha_hora</td>
                <td>$boton</td>
            </tr>";
        }
    ?>
    </tbody>
</table>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Confirmación de Redención</h3>
        <p>Placa: <span id="modal-placa"></span></p>
        <p>Recompensa: <span id="modal-recompensa"></span></p>
        <p>Puntos: <span id="modal-puntos"></span></p>
        <form method="POST" onsubmit="return cerrarModal();">
            <input type="hidden" name="placa" id="input-placa">
            <input type="hidden" name="recompensa" id="input-recompensa">
            <input type="hidden" name="puntos" id="input-puntos">
            <input type="hidden" name="accion" value="redimir">
            <button type="submit" class="boton-confirmar" id="confirmarBtnModal">Confirmar Redención</button>
        </form>
    </div>
</div>

<script>
function mostrarModal(placa, recompensa, puntos, btn) {
    document.getElementById('modal-placa').textContent = placa;
    document.getElementById('modal-recompensa').textContent = recompensa;
    document.getElementById('modal-puntos').textContent = puntos;

    document.getElementById('input-placa').value = placa;
    document.getElementById('input-recompensa').value = recompensa;
    document.getElementById('input-puntos').value = puntos;

    document.getElementById('modal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modal').style.display = 'none';
    // Se permite el submit para enviar el formulario
    return true;
}

// Cerrar modal al hacer clic fuera de la caja
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        cerrarModal();
    }
};
</script>
</body>
</html>
