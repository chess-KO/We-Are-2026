<?php
require_once "conexion.php"; 
session_start();

// Verificar que llegaron los datos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre     = $_POST['user_name'] ?? '';
    $apellido   = $_POST['user_apellido'] ?? '';
    $fecha_nac  = $_POST['fecha_nac'] ?? '';
    $email      = $_POST['user_email'] ?? '';
    $username   = $_POST['user_name2'] ?? '';
    $pass       = $_POST['pass'] ?? '';
    $passc      = $_POST['passc'] ?? '';
    $genero     = $_POST['gene'] ?? '';
    $pais       = $_POST['pais'] ?? '';
    $nacionalidad = $_POST['naci'] ?? '';

    // Validar contraseñas
    if ($pass !== $passc) {
        echo "<script>alert('Las contraseñas no coinciden');
              window.location.href='../registro.html';</script>";
        exit();
    }

    // Encriptar contraseña
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Subir imagen si se envió
    $fotoPerfil = null;
    if (!empty($_FILES['image']['name'])) {
        $dir = "../uploads/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fotoPerfil = $dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $fotoPerfil);
    }

    // Insertar en la BD
    $stmt = $conn->prepare("INSERT INTO usuarios 
        (nombre, apellido, fecha_nac, email, username, password, genero, pais, nacionalidad, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", 
        $nombre, $apellido, $fecha_nac, $email, $username, $passHash, $genero, $pais, $nacionalidad, $fotoPerfil
    );

    if ($stmt->execute()) {
        // Guardar sesión y redirigir
        $_SESSION['usuario'] = $username;
        $_SESSION['id_usuario'] = $stmt->insert_id;

        echo "<script>alert('Bienvenido');
              window.location.href='../inicio.html';</script>";
        exit();
    } else {
        echo "<script>alert('Error al registrar usuario');
              window.location.href='../registro.html';</script>";
        exit();
    }
}
?>