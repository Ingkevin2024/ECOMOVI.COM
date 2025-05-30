<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "administrador";

$conn = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["query"])) {
    $query = $conn->real_escape_string($_POST["query"]);
    $sql = "SELECT num_doc_usu, nom_usu, apelli_usu FROM usuarios WHERE num_doc_usu LIKE '%$query%' OR nom_usu LIKE '%$query%' LIMIT 10";
    $resultado = $conn->query($sql);

    $sugerencias = [];

    while ($fila = $resultado->fetch_assoc()) {
        $sugerencias[] = $fila;
    }

    echo json_encode($sugerencias);
}

$conn->close();
?>
