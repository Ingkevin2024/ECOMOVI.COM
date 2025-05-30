<?php

$servername = "localhost";
$username = "root"; // Cambia esto según tu configuración
$password = ""; // Cambia esto si tienes contraseña
$dbname = "ecomovi";

$conn = new mysqli($servername, $username, $password, $dbname);


$plac_veh = $_POST['plac_veh'];
$tip_veh = $_POST['tip_veh'];
$tarj_prop_veh = $_POST['tarj_prop_veh'];
$tecno_m = $_POST['tecno_m'];
$foto_1= $_POST['foto_1'];
$SOAT= $_POST['SOAT'];
$foto_2= $_POST['foto_2'];
$mar_veh= $_POST['mar_veh'];
$lin_veh= $_POST['lin_veh'];
$color_veh= $_POST['color_veh'];
$num_motor_veh= $_POST['num_motor_veh'];
$clase_veh= $_POST['clase_veh'];
$combus_veh= $_POST['combus_veh'];
$capaci_veh= $_POST['capaci_veh'];
$num_chasis_veh= $_POST['num_chasis_veh'];
$model_veh= $_POST['model_veh'];


$sql= "INSERT INTO vehiculo (plac_veh, tip_veh, tarj_prop_veh,
tecno_m, foto_1, SOAT, foto_2, mar_veh, lin_veh, color_veh, num_motor_veh, clase_veh, 
combus_veh, capaci_veh, num_chasis_veh, model_veh) VALUES 
('$plac_veh','$tip_veh','$tarj_prop_veh', '$tecno_m', 
'$foto_1', '$SOAT', '$foto_2', '$mar_veh', '$lin_veh', '$color_veh', 
'$num_motor_veh', '$clase_veh', '$combus_veh', '$capaci_veh', '$num_chasis_veh',
'$model_veh')";

if ($conn->query($sql) === TRUE) {
    echo "Vehiculo registrado exitosamente";
 } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
 }
 
 // Cerrar la conexión
 $conn->close()

?>