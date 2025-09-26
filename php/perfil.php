<?php
session_start();
include("conexion.php"); // tu archivo de conexión a BD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id']; // asumiendo que guardas el id en la sesión

    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha_nac'];
    $pais = $_POST['pais'];
    $nacionalidad = $_POST['nacionalidad'];
    $correo = $_POST['correo'];
    $pass = $_POST['pass'];
    $genero = $_POST['gene']; // 👈 corregido

    // Validar contraseña
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-zñ])(?=.*[\W_]).{8,}$/u', $pass)) {
        echo json_encode(["status" => "error", "message" => "La contraseña no cumple con los requisitos mínimos."]);
        exit;
    }

    // Encriptar contraseña
    $passHash = password_hash($pass, PASSWORD_BCRYPT);

    // Subir imagen
    $imagen = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $nombreImg = uniqid() . "_" . basename($_FILES['image']['name']);
        $rutaDestino = __DIR__ . "/uploads/" . $nombreImg; // guarda en /php/uploads/
        if (move_uploaded_file($_FILES['image']['tmp_name'], $rutaDestino)) {
            $imagen = "uploads/" . $nombreImg; // ruta relativa para BD
        }
    }

    // SQL
    $sql = "UPDATE usuarios 
            SET nombre=?, fecha_nac=?, pais=?, nacionalidad=?, correo=?, pass=?, genero=? " 
            . ($imagen ? ", foto=? " : "") . 
            "WHERE id=?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error en prepare: " . $conn->error]);
        exit;
    }

    if ($imagen) {
        $stmt->bind_param("ssssssssi", $nombre, $fecha, $pais, $nacionalidad, $correo, $passHash, $genero, $imagen, $id_usuario);
    } else {
        $stmt->bind_param("sssssssi", $nombre, $fecha, $pais, $nacionalidad, $correo, $passHash, $genero, $id_usuario);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Perfil actualizado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error en execute: " . $stmt->error]);
    }
}
?>