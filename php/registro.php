<?php
// -------------------------------
// PARTE PHP: CONEXIÓN Y REGISTRO
// -------------------------------
require_once "../config/Database.class.php";
require_once "../models/usuario.class.php";
session_start();

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = new Database();
    $db = $database->connect();

    $usuario = new Usuario($db);

    // Asignar los valores del formulario a las propiedades de la clase
    $usuario->nombre       = $_POST['user_name'] ?? '';
    $usuario->apellidos    = $_POST['user_apellido'] ?? '';
    $usuario->fechanatal   = $_POST['fecha_nac'] ?? '';
    $usuario->alias        = $_POST['user_name2'] ?? '';
    $usuario->email        = $_POST['user_email'] ?? '';
    $usuario->pass         = $_POST['pass'] ?? '';
    $usuario->genero       = ($_POST['gene'] === "Masculino") ? 1 : 0;
    $usuario->paisnatal    = $_POST['pais'] ?? '';
    $usuario->nacionalidad = $_POST['naci'] ?? '';
    $usuario->tipo         = 0; // 0 = usuario normal por defecto

    // Leer la imagen
    if (!empty($_FILES['image']['tmp_name'])) {
        $usuario->foto = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        $usuario->foto = null;
    }

    // Intentar registrar con el Stored Procedure
    if ($usuario->registrar()) {
        $_SESSION['usuario'] = $usuario->alias;
        echo "<script>alert('Usuario registrado correctamente');
              window.location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al registrar usuario');
              window.location.href='registro.php';</script>";
        exit;
    }
}
?>
