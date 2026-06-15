<?php
require_once __DIR__ . '/../daos/NewsDAO.php';
require_once __DIR__ . '/../models/NoticiaModel.php';

class NoticiaBusiness {
    private $newsDAO;

    public function __construct() {
        $this->newsDAO = new NewsDAO();
    }

    // Obtiene y formatea las últimas noticias
    public function getUltimasNoticias() {
        $resultadosFinales = [];
        
        // 1. Pide los datos crudos al DAO
        $datosCrudos = $this->newsDAO->getNoticiasFutbol();

        // 2. Lógica de Negocio: 
        //    Verifica que la respuesta sea exitosa y tenga artículos
        if (isset($datosCrudos['status']) && $datosCrudos['status'] === 'ok' && !empty($datosCrudos['articles'])) {
            
            // 3. Procesa y limpia los datos
            foreach ($datosCrudos['articles'] as $articulo) {
                $noticia = new NoticiaModel();
                $noticia->titulo = $articulo['title'];
                $noticia->descripcion = $articulo['description'];
                $noticia->url = $articulo['url'];
                $noticia->urlImagen = $articulo['urlToImage'];
                $noticia->fuente = $articulo['source']['name'];
                
                $resultadosFinales[] = $noticia;
            }
        }

        return $resultadosFinales;
    }
}
?>