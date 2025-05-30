<?php
session_start();
session_destroy();
header("Location: INICIOSESION.html");
exit();
?>