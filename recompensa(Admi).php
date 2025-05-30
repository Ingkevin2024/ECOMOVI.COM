<?php  
$host = 'localhost';
$usuario = 'root'; 
$contraseña = ''; 
$base_datos = 'ecomovi';

$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$uploads_dir = 'uploads';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];

    // First, modify the POST handling for 'agregar' action
    if ($accion === 'agregar') {
        $nom_reco = $_POST['nom_reco'];
        $descripcion = $_POST['descripcion'];
        $puntos = $_POST['puntos'];
        $disponible = $_POST['disponible']; // Changed this line to use the actual value
    
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = $_FILES['imagen']['name'];
            $ruta = $uploads_dir . '/' . basename($imagen);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    
            $stmt = $conexion->prepare("INSERT INTO recompensa (nom_reco, descripcion, puntos, imagen_url, disponible) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisi", $nom_reco, $descripcion, $puntos, $ruta, $disponible);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Similarly for the 'editar' action
    elseif ($accion === 'editar') {
        $nom_reco_original = $_POST['nom_reco_original']; // Add this line
        $nom_reco = $_POST['nom_reco'];
        $descripcion = $_POST['descripcion'];
        $puntos = $_POST['puntos'];
        $disponible = $_POST['disponible'];
    
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = $_FILES['imagen']['name'];
            $ruta = $uploads_dir . '/' . basename($imagen);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    
            $stmt = $conexion->prepare("UPDATE recompensa SET nom_reco = ?, descripcion = ?, puntos = ?, imagen_url = ?, disponible = ? WHERE nom_reco = ?");
            $stmt->bind_param("ssisis", $nom_reco, $descripcion, $puntos, $ruta, $disponible, $nom_reco_original);
        } else {
            $stmt = $conexion->prepare("UPDATE recompensa SET nom_reco = ?, descripcion = ?, puntos = ?, disponible = ? WHERE nom_reco = ?");
            $stmt->bind_param("ssiis", $nom_reco, $descripcion, $puntos, $disponible, $nom_reco_original);
        }
        $stmt->execute();
        $stmt->close();
    } elseif ($accion === 'eliminar') {
        $nom_reco = $_POST['nom_reco'];

        $stmt = $conexion->prepare("SELECT imagen_url FROM recompensa WHERE nom_reco = ?");
        $stmt->bind_param("s", $nom_reco);
        $stmt->execute();
        $stmt->bind_result($imagen_url);
        $stmt->fetch();
        $stmt->close();

        if ($imagen_url && file_exists($imagen_url)) {
            unlink($imagen_url);
        }

        $stmt = $conexion->prepare("DELETE FROM recompensa WHERE nom_reco = ?");
        $stmt->bind_param("s", $nom_reco);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$resultado = $conexion->query("SELECT * FROM recompensa");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ECOMOVI - Recompensas</title>
    <link rel="stylesheet" href="recompensa(admi).css">
</head>
<body>

<style>

.recompensa{
     background-color:  #e6f3d9a1 !important;
}
</style>
<header>
    <nav>
        <img class="logo" src="logo blanco.png" alt="Eco-Movi" width="150px">
        <h2  style="font-family: 'Times New Roman', Times, serif;" class="titulo-recompensas">Recompensa</h2>
        <a href="paginaadministrador.html">Inicio</a>
    </nav>
</header>

<div class="container">
    <?php while ($row = $resultado->fetch_assoc()) {
        $nom_reco_id = htmlspecialchars($row['nom_reco'], ENT_QUOTES);
    ?>
        <div class="recompensa">
            <img src="<?= htmlspecialchars($row['imagen_url']) ?>" alt="Imagen de recompensa">
            <h3><?= htmlspecialchars($row['nom_reco']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['descripcion'])) ?></p>
            <p><?= $row['puntos'] ?> puntos</p>
            <p><strong>Cantidad disponible:</strong> <?= htmlspecialchars($row['disponible']) ?></p>
            <button onclick="mostrarModalEditar('<?= $nom_reco_id ?>')">Editar</button>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="nom_reco" value="<?= $nom_reco_id ?>">
                <button type="submit" onclick="return confirm('¿Está seguro de eliminar esta recompensa?');">Eliminar</button>
            </form>
        </div>

        <div id="modalEditar<?= $nom_reco_id ?>" class="modal">
            <div class="modal-contenido">
                <span class="cerrar" onclick="cerrarModalEditar('<?= $nom_reco_id ?>')">&times;</span>
                <h2>Editar Recompensa</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="nom_reco_original" value="<?= htmlspecialchars($row['nom_reco']) ?>">
                    <input type="text" name="nom_reco" value="<?= htmlspecialchars($row['nom_reco']) ?>" required>
                    <textarea name="descripcion" required style="width: 100%; min-height: 100px; margin-bottom: 10px; padding: 8px;"><?= htmlspecialchars($row['descripcion']) ?></textarea>
                    <input type="number" name="puntos" value="<?= $row['puntos'] ?>" min="0" required>
                    <input type="number" name="disponible" value="<?= $row['disponible'] ?>" placeholder="Cantidad disponible (máx. 200)" min="0" max="200" required oninput="this.value = Math.abs(this.value)">
                    <p>Imagen actual:</p>
                    <img src="<?= htmlspecialchars($row['imagen_url']) ?>" alt="Imagen actual" width="100">
                    <input type="file" name="imagen" accept="image/*">
                    <button type="submit">Guardar</button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<button class="botonre" onclick="mostrarModalAgregar()">Agregar Recompensa</button>

<div id="modalAgregar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalAgregar()">&times;</span>
        <h2>Agregar Recompensa</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="nom_reco" placeholder="Nombre de la recompensa" required>
            <textarea name="descripcion" placeholder="Descripción detallada de la recompensa" style="width: 100%; min-height: 100px; margin-bottom: 10px; padding: 8px;" required></textarea>
            <input type="number" name="puntos" placeholder="Puntos requeridos" min="0" required>
            <input type="number" name="disponible" placeholder="Cantidad disponible (máx. 200)" min="0" max="200" required oninput="this.value = Math.abs(this.value)">
            <input type="file" name="imagen" accept="image/*" required>
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<script>
function mostrarModalEditar(nomReco) {
    document.getElementById('modalEditar' + nomReco).style.display = 'block';
}
function cerrarModalEditar(nomReco) {
    document.getElementById('modalEditar' + nomReco).style.display = 'none';
}
function mostrarModalAgregar() {
    document.getElementById('modalAgregar').style.display = 'block';
}
function cerrarModalAgregar() {
    document.getElementById('modalAgregar').style.display = 'none';
}
</script>

</body>
</html>
