<?php 
session_start(); 
$_SESSION['num_doc_usu'] = $num_doc_usu; 
require 'conexion_bd.php'; 
$num_doc_usu = $_SESSION['num_doc_usu']; 
$sql = "SELECT nom_usu, num_doc_usu, tel_usu, direc_usu FROM usuario WHERE num_doc_usu = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $num_doc_usu); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 
if ($resultado->num_rows > 0) { 
  $usuario = $resultado->fetch_assoc(); 
} else { 
  $usuario = [ 
    'nom_usu' => 'Usuario no encontrado', 
    'num_doc_usu' => 'No disponible', |
    'tel_usu' => 'No disponible', 
    'direc_usu' => 'No disponible' 
  ]; 
} 
$conn->close(); 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <title>Inicio de Usuario</title> 
  <link rel="stylesheet" href="inicio.css"> 
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 
  <style> 
    /* Estilos del modal */ 
    .modal { 
      display: none; /* Oculto por defecto */ 
      position: fixed; 
      z-index: 1000; 
      left: 0; 
      top: 0; 
      width: 100%; 
      height: 100%; 
      background-color: rgba(0, 0, 0, 0.5); 
      display: flex; 
      justify-content: center; 
      align-items: center; 
    } 
    .modal-content { 
      background-color: white; 
      padding: 20px; 
      border-radius: 10px; 
      width: 40%; 
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
      text-align: center; 
      position: relative; 
    } 
    .close { 
      position: absolute; 
      top: 10px; 
      right: 15px; 
      font-size: 24px; 
      cursor: pointer; 
    } 
  </style> 
</head> 
<body> 
  <header> 
    <nav> 
      <ul class="list"> 
        <img src="logo blanco.png" alt="observar el logo"> 
        <li><a href="https://wa.me/3202295786"><b>Contáctenos</b></a></li> 
      </ul> 
    </nav> 
  </header> 
  <br><br> 
  <div class="profile"> 
    <button onclick="abrirModal()"><img src="profil.png" alt="Perfil" width="120vw" height="120vw"></button> 
  </div> 
  <!-- Modal --> 
  <div id="miModal" class="modal"> 
    <div class="modal-content"> 
      <span class="close" onclick="cerrarModal()">&times;</span> 
      <h2>Mi Perfil</h2> 
      <p>Nombre: <?php echo $usuario['nom_usu']; ?></p> 
      <p>Número de documento: <?php echo $usuario['num_doc_usu']; ?></p> 
      <p>Teléfono: <?php echo $usuario['tel_usu']; ?></p> 
      <p>Dirección: <?php echo $usuario['direc_usu']; ?></p> 
    </div> 
  </div> 
  <script> 
    function abrirModal() { 
      document.getElementById("miModal").style.display = "flex"; 
    } 
    function cerrarModal() { 
      document.getElementById("miModal").style.display = "none"; 
    } 
    window.onclick = function(event) { 
      let modal = document.getElementById("miModal"); 
      if (event.target == modal) { 
        cerrarModal(); 
      } 
    } 
  </script> 
  <br> 
  <h1><b>BIENVENIDO A TU MOVILIDAD GRUPO ECOMOVI </b><br> 
  </h1> 
  <br> 
  <br>
```
[12:58 p.m., 14/3/2025] Sánchez: <br>
    <div class="container">
        <div class="card">
            <img src="Imgen/seguimiento.png" alt="Imagen 1" width="5PX" >
            <h3>VEHICULOS REGISTRADOS</h3>
            <a href="vehicregis.html"><button>Ir</button></a>
        </div>
        <div class="card">
            <img src="Imgen/recomp.avif" alt="Imagen 2"  >
            <h3>RECOMPENSAS</h3>
            <a href="RecomUsuario.html"><button>Ir</button></a>
        </div>
        <div class="card">
            <img src="Imgen/coche-blanco-vista-frontal-aislado-fondo-negro_6689-412.avif" alt="Imagen 3">
            <h3>REGISTRAR VEHICULOS</h3>
            <a href="RegistroV/RegistroV.html"><button>Ir</button></a>
        </div>
    </div>
 
   
        <div class="card4">
            <img src="Imgen/movilidad.jpg" alt="Imagen 4">
            <h3>MOVILIDAD</h3>
            <a href="Seguimiento de movilidad.html"><button>Ir</button></a>
            
        </div>
    

       
    </div>
<br>
<br>
<br>
<br>
</body>
</html>

  
   
    <footer class="footer2">
        <div class="fecha" id="fecha"></div>
    </footer>

    <script>
        // Mostrar la fecha al cargar la página
        function mostrarFecha() {
            const fecha = new Date();
            const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
            const fechaFormateada = fecha.toLocaleDateString('es-ES', opciones);
            document.getElementById('fecha').innerText = fechaFormateada;
        }
        window.onload = mostrarFecha;

        // Lógica para el modal
        const modal = document.getElementById("myModal");
        const openPopup = document.getElementById("openPopup");
        const closePopup = document.getElementsByClassName("close")[0];

        openPopup.onclick = function () {
            modal.style.display = "block";
        }

        closePopup.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>


    <footer>
        <div class="footer-container">
          <div class="info">
            <h3>Centro Administrativo:</h3>
            <p>Lunes a viernes de 7:30 a. m. a 12:00 a.m. y de 1:30 p. m. a 5:00 p. m.</p>
            <p>Sabado y domingo de 8:00 a.m. a 12 a.m.</p>
          </div>
          <div class="contacto">
            <h3>Correo institucional para la recepción de solicitudes de información:</h3>
            <p>atencion.ciudadana@ecomovi.gov.co</p>
      
          </div>
          <div class="redes-sociales">
            <h3>Redes Sociales</h3>
            <p>ECO-MOVI</p>
            <div class="social-icons">
              <a href="#"><img src="imagess/face.jpeg" alt="Facebook"></a>
              <a href="#"><img src="imagess/twiter.jpeg" alt="X"></a>
              <a href="#"><img src="imagess/insta.jpeg" alt="Instagram"></a>
              <a href="#"><img src="imagess/yout.jpeg" alt="YouTube"></a>
              <a href="#"><img src="imagess/tiktok.jpg" alt="TikTok"></a>
              <a href="https://wa.me/3202295786"><img src="imagess/WHATS.jpg" alt="Whatsapp"></a>
            </div>
      
          </div>
        </div>
      </footer>

     
    
</body>
</html>