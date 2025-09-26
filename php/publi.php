<?php
require_once "conexion.php"; // tu archivo de conexión a la BD
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debes iniciar sesión para publicar');
          window.location.href='../login.html';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST['titulo'] ?? '';
    $mundial = $_POST['mundial'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $usuario_id = $_SESSION['id_usuario'];

    
    $archivoRuta = null;
    if (!empty($_FILES['archivo']['name'])) {
        $dir = "../uploads/publicaciones/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $archivoRuta = $dir . time() . "_" . basename($_FILES['archivo']['name']);
        move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoRuta);
    }

   
    $stmt = $conn->prepare("INSERT INTO publicaciones 
        (usuario_id, titulo, mundial, categoria, archivo, descripcion, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("isssss", $usuario_id, $titulo, $mundial, $categoria, $archivoRuta, $descripcion);

    if ($stmt->execute()) {
        echo "<script>
            alert('Publicación creada con éxito ');
            window.location.href='../inicio.html';
        </script>";
    } else {
        echo "<script>
            alert('Error al crear publicación');
            window.location.href='../inicio.html';
        </script>";
    }
}
?>