<?php
// eliminar_vehiculo.php
include 'conexion.php';

if (isset($_GET['plac_veh'])) {
    $plac_veh = $_GET['plac_veh'];

    // Eliminar el vehículo específico
    $stmt = $conn->prepare("DELETE FROM vehiculos WHERE plac_veh = ?");
    $stmt->bind_param("s", $plac_veh); // Usar "s" para cadenas
    $stmt->execute();

    $stmt->close();
}

$conn->close();

// Redirigir después de eliminar
header('Location: RegistroV.php');
exit();
?>
