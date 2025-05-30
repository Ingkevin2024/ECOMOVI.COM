<?php
$host = 'localhost'; // El host de la base de datos
$dbname = 'ecomovi'; // El nombre de la base de datos
$username = 'root'; // Tu nombre de usuario de MySQL
$password = ''; // Tu contraseña de MySQL

// Crear la conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configurar el modo de error de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
