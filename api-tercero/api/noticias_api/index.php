<?php

require_once __DIR__ . '/controllers/NoticiaController.php';

// Limpiamos la URL
$basePath =  '/Proyecto%20PCI/RespaldosProyecto/2/PCI-BDM/api-tercero/api/noticias_api';
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = str_replace($basePath, '', $requestUri);

// Enrutador
switch ($endpoint) {
    case '/ultimas':
        // Si el endpoint es /ultimas, llama al controlador
        $controller = new NoticiaController();
        $controller->manejarPeticionNoticias();
        break;

    default:
        // Para cualquier otra URL, devuelve un error 404
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint no encontrado.']);
        break;
}
?>