<?php
session_start();
session_unset();
session_destroy();

// Redirigir al HTML inicial o al login
header("Location: ../index.php"); 
exit();
?>