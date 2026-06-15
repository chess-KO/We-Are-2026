<?php
class TokenSimple {
    private static $clave = 'clave_super_secreta_123';

    // 🟢 Generar token
    public static function generar($data) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'data' => $data,
            'exp' => time() + 3600 // expira en 1 hora
        ]));
        $firma = hash_hmac('sha256', "$header.$payload", self::$clave);
        return "$header.$payload.$firma";
    }

    // 🟢 Verificar token de cabecera Authorization
    public static function verificar() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token no proporcionado"]);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $partes = explode('.', $token);
        if (count($partes) !== 3) {
            http_response_code(401);
            echo json_encode(["message" => "Inicia Sesión para continuar"]);
            exit;
        }

        [$header, $payload, $firma] = $partes;
        $firmaVerificada = hash_hmac('sha256', "$header.$payload", self::$clave);

        if (!hash_equals($firmaVerificada, $firma)) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido"]);
            exit;
        }

        $data = json_decode(base64_decode($payload), true);

        if ($data['exp'] < time()) {
            // 🔥 Si el token expiró, eliminar la sesión automáticamente
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
                session_unset();
                session_destroy();
            }

            
            http_response_code(401);
            echo json_encode(["message" => "Sesión expirada"]);
            exit;
        }

        return $data['data'];
    }

    // 🟡 Verificación de sesión normal (para archivos PHP)
    public static function verificarSesion($soloAdmin = false) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['token'])) {
            header("Location: index.php");
            exit;
        }

        $token = $_SESSION['token'];
        $usuario = self::verificarTokenInterno($token);

        if ($soloAdmin && (!isset($usuario['Tipo']) || $usuario['Tipo'] != 1)) {
            header("Location: index.php");
            exit;
        }

        return $usuario;
    }

    // 🟣 Nueva: Verificación especial para admins en APIs
    public static function verificarAdmin() {
        $usuario = self::verificar(); // primero verifica el token

        if (!isset($usuario['Tipo']) || $usuario['Tipo'] != 1) {
            http_response_code(403);
            echo json_encode(["message" => "Acceso denegado: solo administradores"]);
            exit;
        }

        return $usuario;
    }

    // 🔵 Verificación de token guardado en sesión
    private static function verificarTokenInterno($token) {
        $partes = explode('.', $token);
        if (count($partes) !== 3) return null;

        [$header, $payload, $firma] = $partes;
        $firmaVerificada = hash_hmac('sha256', "$header.$payload", self::$clave);
        if (!hash_equals($firmaVerificada, $firma)) return null;

        $data = json_decode(base64_decode($payload), true);
        if ($data['exp'] < time()) return null;

        return $data['data'];
    }
}
