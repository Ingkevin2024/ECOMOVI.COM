<?php
require_once('conexion.php');



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['descargar_pdf'])) {
    require_once('tcpdf/tcpdf.php');
    require_once('conexion.php');

    $identificacion = $_POST['identificacion'] ?? null;
    if (!$identificacion) {
        die('Cédula no proporcionada.');
    }

    // Obtener datos del usuario
    $sqlUsuario = "SELECT nom_usu, apell_usu, num_doc_usu FROM usuarios WHERE num_doc_usu = ?";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    if (!$stmtUsuario) die("Error al preparar la consulta del usuario.");

    $stmtUsuario->bind_param("s", $identificacion);
    $stmtUsuario->execute();
    $resUsuario = $stmtUsuario->get_result();
    if ($resUsuario->num_rows === 0) {
        die("Usuario no encontrado.");
    }
    $usuario = $resUsuario->fetch_assoc();

    // Obtener vehículos
    $sqlVehiculos = "SELECT plac_veh, mar_veh, model_veh ,tecno_m, soat, lin_veh FROM vehiculos WHERE num_doc_usu = ?";
    $stmtVehiculos = $conn->prepare($sqlVehiculos);
    if (!$stmtVehiculos) die("Error al preparar la consulta de vehículos.");

    $stmtVehiculos->bind_param("s", $identificacion);
    $stmtVehiculos->execute();
    $resVehiculos = $stmtVehiculos->get_result();
    $totalVehiculos = $resVehiculos->num_rows;

    // Crear clase PDF
    class MYPDF extends TCPDF {
        public function Header() {
            $this->SetY(10);
            $this->Image('logo blanco.png', 160, 8, 30);
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(34, 139, 34);
            $this->Cell(0, 10, 'Reporte de registro', 0, false, 'L');
            $this->SetFont('helvetica', '', 10);
          $this->SetXY(160, 30); // Se baja la fecha
            $this->Cell(0, 10, 'Fecha: ' . date('d/m/Y'), 0, false, 'L');
            $this->Ln(10);
        }

        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, '@ECOMOVI-' . date('Y'), 0, false, 'C');
        }
    }

    $pdf = new MYPDF();
    $pdf->AddPage();

    // Datos del usuario
    $pdf->Ln(25);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Write(0, 'DATOS DEL USUARIO', '', 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Write(0, 'Nombre: ' . ($usuario['nom_usu'] ?? 'N/A') . ' ' . ($usuario['apell_usu'] ?? 'N/A'), '', 0, 'L', true);
    $pdf->Write(0, 'Cédula: ' . ($usuario['num_doc_usu'] ?? 'N/A'), '', 0, 'L', true);

    $pdf->Ln(10);

    if ($totalVehiculos > 0) {
        $tbl = '
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr style="background-color:#8BC34A; color:white; text-align:center;">
                    <th width="33.33%">MARCA</th>
                    <th width="33.33%">PLACA</th>
                    <th width="33.33%">MODELO</th>
                </tr>
            </thead>
            <tbody>';
        while ($vehiculo = $resVehiculos->fetch_assoc()) {
            $tbl .= "<tr style='text-align:center; background-color:#F5F5F5;'>
                        <td>{$vehiculo['mar_veh']}</td>
                        <td>{$vehiculo['plac_veh']}</td>
                        <td>{$vehiculo['model_veh']}</td>
                    </tr>";
        }
        $tbl .= '</tbody></table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Write(0, 'HAY ' . $totalVehiculos . ' VEHICULOS REGISTRADOS', '', 0, 'C', true);
    } else {
        $pdf->SetTextColor(200, 0, 0);
        $pdf->Write(0, "⚠ No hay vehículos registrados para este usuario.", '', 0, 'L', true);
    }

    $nombre_archivo = "reporte_vehiculos_$identificacion.pdf";
    $pdf->Output($nombre_archivo, 'I');
    exit;
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ECOMOVI - REPORTE DE VEHÍCULOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style(editarvehiculo).css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
</head>
 <!-- Header con botón Volver -->

    <!-- Botón Volver -->
 <a href="paginaadministrador.html" class="inicio-btn">volver</a>


    
  </header>

  <header class="header-container">
      
        <h1  style="font-family: 'Times New Roman', Times, serif;font-weight: bold;">REPORTE DE VEHÍCULOS REGISTRADOS</h1>
    </header>
</header>

<style>
    .modal-content{
         background-color:rgba(238, 242, 235, 0.91);
    }
     .usuario-info{
        background-color: #e6f3d97e !important;
     }

    h5{
        font-family: 'Times New Roman', Times, serif;
    }
    h4{
        font-weight: bold;
         font-family: 'Times New Roman', Times, serif;
    }
    
</style>

    
<div class="container mt-4">
    <form method="POST" class="text-center">
        <input type="text" name="identificacion" placeholder="Ingrese número de identificación..." class="form-control d-inline w-50" required
               value="<?php echo isset($_POST['identificacion']) ? $_POST['identificacion'] : ''; ?>">
        <button type="submit" class="btn btn-success mt-2">Buscar</button>
    </form>

<?php
// Verifica que $conn esté definido y no sea null
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['descargar_pdf']) && !isset($_POST['editar_vehiculo'])) {

    // Verifica que $conn esté correctamente inicializada y no sea null
    if (isset($conn) && $conn !== null) {
        $identificacion = $conn->real_escape_string($_POST["identificacion"]);
        $sqlUsuario = "SELECT nom_usu, apell_usu, num_doc_usu FROM usuarios WHERE num_doc_usu = '$identificacion'";
        $resUsuario = $conn->query($sqlUsuario);

        if ($resUsuario->num_rows > 0) {
            $usuario = $resUsuario->fetch_assoc();
            echo "<div class='usuario-info bg-white p-4 mt-4 shadow rounded'>";
            echo "<h4 class=''>INFORMACIÓN DEL USUARIO</h4>";
            echo "<p><strong>Cédula:</strong> {$usuario['num_doc_usu']}</p>";
            echo "<p><strong>Nombre:</strong> {$usuario['nom_usu']} {$usuario['apell_usu']}</p>";

            $sqlVehiculos = "SELECT plac_veh, mar_veh, model_veh FROM vehiculos WHERE num_doc_usu = '$identificacion'";
            $resVehiculos = $conn->query($sqlVehiculos);

          if ($resVehiculos->num_rows > 0) {
    echo "<div class='contenedor-vehiculos'>"; // 👈 Contenedor con estilo

    echo "<h5 class='titulo-vehiculos'> Vehículos Registrados</h5>";

    echo "<table class='table table-bordered table-hover tabla-vehiculos'>"; // 👈 Clase adicional a la tabla
    echo "<thead class='encabezado-verde'>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>EDITAR</th>
            </tr>
          </thead>
          <tbody>";

    while ($vehiculo = $resVehiculos->fetch_assoc()) {
        echo "<tr>
                <td>{$vehiculo['plac_veh']}</td>
                <td>{$vehiculo['mar_veh']}</td>
                <td>{$vehiculo['model_veh']}</td>
                <td>
                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#modalEditar'
                            onclick=\"document.getElementById('placa_original').value='{$vehiculo['plac_veh']}';
                                     document.getElementById('plac_veh').value='{$vehiculo['plac_veh']}';
                                     document.getElementById('mar_veh').value='{$vehiculo['mar_veh']}';
                                     document.getElementById('model_veh').value='{$vehiculo['model_veh']}';\">
                        ✏ Editar
                    </button>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
    echo "<p class='total-vehiculos'>Total: <strong>{$resVehiculos->num_rows}</strong> vehículo(s)</p>";
    echo "</div>"; // 👈 Cierra contenedor
} else {
    echo "<p class='text-danger text-center fw-bold mt-3'>⚠ No hay vehículos registrados para este usuario.</p>";
}


            echo "<form method='POST'>";
            echo "<input type='hidden' name='identificacion' value='{$usuario['num_doc_usu']}'>";
            echo "<input type='hidden' name='descargar_pdf' value='1'>";
            echo "<button type='submit' class='pdf-btn'>📄 Descargar PDF</button>";
            echo "</form>";
            echo "</div>";
        } else {
            echo "<p class='text-danger text-center fw-bold mt-3'>⚠ Usuario no registrado.</p>";
        }
    } else {
        echo "<p class='text-danger text-center fw-bold mt-3'>⚠ Error en la conexión a la base de datos.</p>";
    }
}

// Verifica si se ha enviado el formulario para editar un vehículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_vehiculo'])) {

    if (isset($conn) && $conn !== null) {

        // Obtener los datos enviados desde el formulario de edición
        $placa_original = $conn->real_escape_string($_POST['placa_original']);
        $plac_veh = $conn->real_escape_string($_POST['plac_veh']);
        $mar_veh = $conn->real_escape_string($_POST['mar_veh']);
        $model_veh = $conn->real_escape_string($_POST['model_veh']);
        $tecno_m = $conn->real_escape_string($_POST['tecno_m']);
        $soat = $conn->real_escape_string($_POST['soat']);
        $lin_veh = $conn->real_escape_string($_POST['lin_veh']);

        // Actualizar la información del vehículo en la base de datos
        $sqlUpdate = "UPDATE vehiculos
                      SET plac_veh = '$plac_veh', mar_veh = '$mar_veh', model_veh = '$model_veh',
                          tecno_m = '$tecno_m', soat = '$soat', lin_veh = '$lin_veh'
                      WHERE plac_veh = '$placa_original'";

        if ($conn->query($sqlUpdate) === TRUE) {
            echo "<div class='alert alert-success'>Vehículo actualizado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar el vehículo: " . $conn->error . "</div>";
        }
    }
}

// Cerrar la conexión correctamente si está definida
if (isset($conn) && $conn !== null) {
    $conn->close();
}
?>

</div>

<!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalEditarLabel">Editar Vehículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="placa_original" id="placa_original">

        <div class="mb-3">
          <label for="plac_veh" class="form-label">Placa</label>
          <input type="text" name="plac_veh" id="plac_veh" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="mar_veh" class="form-label">Marca</label>
          <input type="text" name="mar_veh" id="mar_veh" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="model_veh" class="form-label">Modelo</label>
          <input type="text" name="model_veh" id="model_veh" class="form-control" required>
        </div>

        <!-- Nuevo Campo: Tecnomecánica -->
        <div class="mb-3">
          <label for="tecno_m" class="form-label">Tecnomecánica</label>
          <input type="text" name="tecno_m" id="tecno_m" class="form-control" required>
        </div>

        <!-- Nuevo Campo: SOAT -->
        <div class="mb-3">
          <label for="soat" class="form-label">SOAT</label>
          <input type="text" name="soat" id="soat" class="form-control" required>
        </div>

        <!-- Nuevo Campo: Línea del Vehículo -->
        <div class="mb-3">
          <label for="lin_veh" class="form-label">Línea del Vehículo</label>
          <input type="text" name="lin_veh" id="lin_veh" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="editar_vehiculo" class="btn btn-success">Guardar Cambios</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
