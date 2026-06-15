<?php
/*
require_once("../config/Database.class.php");

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Falta el parámetro id";
    exit;
}

$idusuario = intval($_GET['id']);

try {
    $database = new Database();
    $db = $database->connect();

    $query = "CALL VisualizarFotoPerfil(:id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $idusuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $foto = $row['Foto'];

        if (!empty($foto)) {
            
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($foto);

            header("Content-Type: $mime");
            echo $foto;
        } else {
            // Imagen por defecto si no hay foto
            header("Content-Type: image/png");
            readfile("../img/default.png");
        }
    } else {
        header("Content-Type: image/png");
        readfile("../img/default.png");
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Error en la base de datos: " . $e->getMessage();
}*/