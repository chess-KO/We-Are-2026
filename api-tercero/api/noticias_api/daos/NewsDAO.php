<?php
class NewsDAO {

    private $baseUrl = 'https://newsapi.org/v2/everything';
    private $apiKey = '';
    private $cacheFile = __DIR__ . '/cache_noticias.json'; // guarda el caché junto al DAO
    private $cacheTime = 86400; // 24 horas en segundos

    public function getNoticiasFutbol() {

        //  Verificar si hay caché reciente
        if ($this->hayCacheValido()) {
            $dataCache = file_get_contents($this->cacheFile);
            return json_decode($dataCache, true);
             if ($this->modoDebug) {
                error_log("[NewsDAO] Datos cargados desde caché: " . date('d-m-Y H:i:s', filemtime($this->cacheFile)));
            }

            return $data;
        }

        //  Si no hay caché o ya expiró, hace la petición a la API
        $query = urlencode('futbol OR "copa del mundo"');
        $url = "{$this->baseUrl}?q={$query}&language=es&sortBy=publishedAt&pageSize=10&apiKey={$this->apiKey}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: MundialesPHP/1.0']);
        $respuestaJson = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($respuestaJson, true);

        //  Guardar respuesta en caché si fue exitosa
        if (isset($data['status']) && $data['status'] === 'ok') {
            file_put_contents($this->cacheFile, json_encode($data));
               if ($this->modoDebug) {
                error_log("[NewsDAO]  Datos obtenidos de la API y guardados en caché: " . date('d-m-Y H:i:s'));
            }
        }

        return $data;
    }

    private function hayCacheValido() {
        // Devuelve true si el archivo existe y fue modificado hace menos de 24 horas
        return file_exists($this->cacheFile) && (time() - filemtime($this->cacheFile)) < $this->cacheTime;
    }
}
?>
