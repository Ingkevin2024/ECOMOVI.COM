<!DOCTYPE html> 
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOMOVI - BUSCAR USUARIO</title>
    <style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('fondoauto.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: rgb(0, 0, 0);
}

nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 20px;
    padding-left: 5%;
    padding-right: 5%;
    font-weight: bold;
}

h1 {
    color: rgb(255, 255, 255);
    font-size: 45px;
    font-family: "Ribeye Marrow", serif;
}

ul li {
    display: inline-block;
    padding: 8px 28px;
}

a {
    color: rgb(255, 255, 255);
    text-decoration: none;
}

a:hover {
    color: rgb(255, 255, 255);
}

.search-container {
    text-align: center;
    margin: 30px auto;
}

#search-input {
    padding: 10px;
    width: 300px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#search-button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    background-color: #125c07bc;;
    color: rgb(226, 221, 221);
    border-radius: 5px;
    margin-left: 10px;
}

.user-info {
    margin: 30px auto;
    padding: 20px;
    border-radius: 0.6rem;
    background-color: #e6f3d97b;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    max-width: 800px;
}

.profile-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.profile-pic {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 2px solid #000000;
    object-fit: cover;
}

.vehicles {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.vehicle {
    border: 2px ;
    border-radius: 15px;
    background-color: rgba(255, 255, 255, 0.726); 
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.vehicle:hover {
    transform: scale(1.05);
}

.vehicle img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.vehicle-info {
    padding: 15px;
    text-align: left;
}

.vehicle-info p {
    margin: 5px 0;
    color: #333;
}

   </style>
</head>
<link rel="icon" href="logoecomovil.png" type="icon">

<body>
    <header>
        <nav>
            <h1>BUSCAR USUARIO</h1>
            <ul>
                <li><a href="paginap.php">INICIO</a></li>
            </ul>
        </nav>
    </header>

    <div class="search-container">
        <form method="POST" action="">
            <input type="text" name="identificacion" id="search-input" placeholder="Escribe el número de Identificación..." required>
            <button type="submit" id="search-button">BUSCAR</button>
        </form>
    </div>
    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servidor = "localhost";
    $usuario = "root";
    $contrasena = "";
    $baseDatos = "busqueda";


    $conn = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

  
    if ($conn->connect_error) {
        die("<p id='error-message'>Error de conexión: " . $conn->connect_error . "</p>");
    }

    
    $identificacion = $conn->real_escape_string($_POST["identificacion"]);

    
    $sql = "SELECT u.nombre AS userName, u.foto_perfil AS profilePic, 
                   v.marca AS marca, v.modelo AS modelo, v.placa AS placa, 
                   v.puntos AS puntos, v.imagen_vehiculo AS vehicleImg
            FROM usuario u
            LEFT JOIN usuario v ON u.identificacion = v.identificacion
            WHERE u.identificacion = '$identificacion'
            GROUP BY v.placa"; 

    
    $resultado = $conn->query($sql);

    if (!$resultado) {
        die("<p id='error-message'>Error en la consulta: " . $conn->error . "</p>");
    }

    if ($resultado->num_rows > 0) {
        echo "<div id='user-info' class='user-info'>";

        $profileDisplayed = false;
        $vehicles = []; 

        while ($fila = $resultado->fetch_assoc()) {
            if (!$profileDisplayed) {
                
                echo "<div class='profile-section'>
                        <img id='profile-pic' src='{$fila['profilePic']}' alt='Foto de perfil' class='profile-pic'>
                        <div>
                            <h2>Nombre: {$fila['userName']}</h2>
                            <p>Número de Identificación: $identificacion</p>
                        </div>
                      </div>";
                $profileDisplayed = true;
            }

          
            if (!empty($fila['marca']) && !empty($fila['modelo']) && !empty($fila['placa'])) {
                $vehicles[] = [
                    "marca" => $fila['marca'],
                    "modelo" => $fila['modelo'],
                    "placa" => $fila['placa'],
                    "puntos" => $fila['puntos'],
                    "vehicleImg" => $fila['vehicleImg']
                ];
            }
        }

        
        echo "<div class='vehicles' id='vehicles-container'>";
        foreach ($vehicles as $vehicle) {
            echo "<div class='vehicle'>
                    <img src='{$vehicle['vehicleImg']}' alt='Imagen de vehículo'>
                    <div class='vehicle-info'>
                        <p><strong>Marca:</strong> {$vehicle['marca']}</p>
                        <p><strong>Modelo:</strong> {$vehicle['modelo']}</p>
                        <p><strong>Placa:</strong> {$vehicle['placa']}</p>
                        <p><strong>Puntos:</strong> {$vehicle['puntos']}</p>
                    </div>
                  </div>";
        }
        echo "</div>";
        echo "<h1>" . count($vehicles) . " vehículo estan  registrado</h1>";
        echo "</div>";
    } else {
        echo "<p id='error-message'>⚠️ EL USUARIO NO ESTÁ REGISTRADO</p>";
    }

    
    $conn->close();
}
?>




</body>
</html>
