<?php
$conn = new mysqli("localhost", "root", "", "ecomovi");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$placa = $_GET['placa'] ?? '';
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM canjeos WHERE plac_veh = ? AND fecha >= NOW() - INTERVAL 12 HOUR");
$stmt->bind_param("s", $placa);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();

header('Content-Type: application/json');
echo json_encode(['tiene_canjeo_activo' => $resultado['total'] > 0]);