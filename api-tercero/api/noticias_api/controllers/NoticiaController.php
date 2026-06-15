<?php
require_once __DIR__ . '/../business/NoticiaBusiness.php';

class NoticiaController {

    private $business;

    public function __construct() {
        $this->business = new NoticiaBusiness();
    }

    public function manejarPeticionNoticias() {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $noticias = $this->business->getUltimasNoticias();
            echo json_encode($noticias);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener las noticias']);
        }
    }
}
?>