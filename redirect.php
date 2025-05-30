<?php
session_start();
if(isset($_GET['page']) && $_GET['page'] === 'search') {
    header('Location: buscar(admi).php');
    exit();
}
?>