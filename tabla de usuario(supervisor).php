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
    $placa = $_POST["placa"];
    $puntos = $_POST["puntos"];
    $recompensa = $_POST["recompensa"];
    $accion = $_POST["accion"];

    if ($accion === "redimir") {
        $stmt = $conexion->prepare("UPDATE movilidad SET puntos = puntos - ? WHERE plac_veh = ?");
        $stmt->bind_param("is", $puntos, $placa);
        $stmt->execute();
        $stmt->close();

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

            // Aquí va el nuevo diseño del correo
            $mail->Body = "
            <div style='padding:40px; font-family:Arial, sans-serif; background:#f9f9f9;'>
                <div style='max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:20px; text-align:center;'>
                    <img src='[img]https://i.ibb.co/r2MRcCG4/logofinal.png[/img]' alt='EcoMovi Logo' style='height:60px; margin-bottom:20px;'/>
                    <h2 style='color:#27ae60; margin-bottom:10px;'>🌿 Notificación de EcoMovi</h2>
                    <p>Has redimido exitosamente tu recompensa. El vehículo con la placa <strong style='color:#27ae60;'>$placa</strong> ha utilizado sus puntos.</p>
                    <p style='font-style:italic; color:#555; margin-top:20px;'>Gracias por confiar en nosotros y ser parte de una movilidad más ecológica.</p>
                </div>
            </div>
            ";

            $mail->AltBody = strip_tags("Has redimido exitosamente tu recompensa. El vehículo con la placa $placa ha utilizado sus puntos.\n\nGracias por confiar en nosotros y ser parte de una movilidad más ecológica.");

            $mail->send();
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&placa=" . urlencode($placa) . "&recompensa=" . urlencode($recompensa));
        exit();
    }
}

    


$query = "SELECT c.fecha, v.plac_veh, r.nom_reco AS recompensa, r.puntos
    FROM canjeos c
    JOIN vehiculos v ON c.plac_veh = v.plac_veh
    JOIN recompensa r ON c.nom_reco = r.nom_reco";

if (isset($_GET['search_placa']) && !empty($_GET['search_placa'])) {
    $search_placa = $conexion->real_escape_string($_GET['search_placa']);
    $query .= " WHERE v.plac_veh LIKE '%$search_placa%'";
}

$query .= " ORDER BY c.fecha DESC";
$resultado = $conexion->query($query);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ECOMOVI - Tabla de Canjeos</title>
    <link rel="stylesheet" href="css/tabla-usuario.css">
    <style>
        .boton-confirmar {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .boton-confirmar:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        .modal { display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background: white; padding: 20px; margin: 10% auto; width: 400px; border-radius: 8px; }
        .close { float: right; font-size: 24px; cursor: pointer; }

        h2{
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body>
   <header>
    <nav>
        <h2 class="titulo-recompensas">TABLA DE RECOMPENSAS</h2>
        <a href="supervisor(principal).html">Inicio</a>
    </nav>
</header>


    <?php if (isset($_GET['success'])): ?>
        <div style="background:#d4edda; padding:10px; margin-bottom:20px;">
            ✅ Redención exitosa se a enviado al correo del usuario </strong></strong>
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
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $placa_redimida_get = $_GET['placa'] ?? '';
            $recompensa_redimida_get = $_GET['recompensa'] ?? '';

            while ($fila = $resultado->fetch_assoc()) {
                $placa = htmlspecialchars($fila['plac_veh']);
                $recompensa = htmlspecialchars($fila['recompensa']);
                $puntos = $fila['puntos'];

                $disabled = ($placa === $placa_redimida_get && $recompensa === $recompensa_redimida_get) ? "disabled" : "";

                echo "<tr>
                    <td>$placa</td>
                    <td>$recompensa</td>
                    <td>$puntos</td>
                    <td>
                        <button class='boton-confirmar' onclick='mostrarModal(\"$placa\", \"$recompensa\", \"$puntos\", this)' $disabled>Redimir</button>
                    </td>
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
            <form method="POST" onsubmit="return deshabilitarBotonTemporal();">
                <input type="hidden" name="placa" id="input-placa">
                <input type="hidden" name="recompensa" id="input-recompensa">
                <input type="hidden" name="puntos" id="input-puntos">
                <input type="hidden" name="accion" value="redimir">
              <button type="submit" class="boton-confirmar" id="confirmarBtnModal">Confirmar Redención</button>
            </form>
        </div>
    </div>
<script>
    let botonActivo = null;

    function mostrarModal(placa, recompensa, puntos, boton) {
        botonActivo = boton;
        document.getElementById("modal-placa").innerText = placa;
        document.getElementById("modal-recompensa").innerText = recompensa;
        document.getElementById("modal-puntos").innerText = puntos;
        document.getElementById("input-placa").value = placa;
        document.getElementById("input-recompensa").value = recompensa;
        document.getElementById("input-puntos").value = puntos;
        document.getElementById("modal").style.display = "block";
    }

    function cerrarModal() {
        document.getElementById("modal").style.display = "none";
    }

    function deshabilitarBotonTemporal() {
        if (botonActivo && !botonActivo.disabled) {
            botonActivo.disabled = true;

            const btnConfirmar = document.getElementById("confirmarBtnModal");
            if (btnConfirmar) {
                btnConfirmar.disabled = true;
                btnConfirmar.innerText = "Procesando...";
            }

            const placa = document.getElementById("input-placa").value;
            const recompensa = document.getElementById("input-recompensa").value;

            // Guardar varias redenciones en localStorage
            let redenciones = JSON.parse(localStorage.getItem("redenciones")) || [];
            redenciones.push({ placa, recompensa });
            localStorage.setItem("redenciones", JSON.stringify(redenciones));
        }

        cerrarModal();
        return true;
    }

    window.onload = function () {
        const redenciones = JSON.parse(localStorage.getItem("redenciones")) || [];

        redenciones.forEach(({ placa, recompensa }) => {
            document.querySelectorAll("table tbody tr").forEach(row => {
                const colPlaca = row.children[0]?.innerText.trim();
                const colRecompensa = row.children[1]?.innerText.trim();

                if (colPlaca === placa && colRecompensa === recompensa) {
                    const boton = row.querySelector(".boton-confirmar");
                    if (boton) {
                        boton.disabled = true;
                        boton.innerText = "Ya redimido";
                    }
                }
            });
        });
    };

    window.onclick = function(event) {
        const modal = document.getElementById("modal");
        if (event.target === modal) {
            cerrarModal();
        }
    };
</script>

</body>
</html>
