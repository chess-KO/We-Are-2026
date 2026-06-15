<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "middleware.php";
require_once("../config/Database.class.php");
require_once("../models/usuario.class.php");

// Instancia de conexión
$database = new Database();
$db = $database->connect();

// Detectar método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        ///////////////////////////////////////////////////////////////////
        // ============================================================= //
        // --------------------- GESTION DE USUARIOS ------------------- //
        // ============================================================= //
        // ////////////////////////////////////////////////////////////////


        // ==========================================================
        // ---------------------REGISTRAR USUARIO -------------------
        // ==========================================================
        if($_POST['accion'] === 'registrarUsuario'){

             //si vienen datos por $_POST (form-data)
            if (
                isset($_POST['user_name'], $_POST['user_apellido'], $_POST['fecha_nac'], $_POST['user_name2'],
                $_POST['user_email'], $_POST['pass'], $_POST['gene'], $_POST['pais'], $_POST['naci']) 
      
                        
            ) {
                try {
                    //validar nombre y apellido 
                    $nombre = trim($_POST['user_name']);
                    $apellidos = trim($_POST['user_apellido']);
                    $pais = trim($_POST['pais']);
                    $naci = trim($_POST['naci']);

                    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {

                             echo json_encode([
                            "status" => "error",
                            "message" => "El nombre solo puede contener letras y espacios"
                             ]);
                              exit;
                    
                    }
                    // Letras, espacios, acentos y símbolos de idiomas (UTF-8)
                    if (!preg_match('/^[\p{L}\s]+$/u', $nombre)) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "El nombre solo puede contener letras y espacios"
                             ]);
                              exit;
                    }

                    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $apellidos)) {
                    echo json_encode([
                                    "status" => "error",
                                   "message" => "El apellido solo puede contener letras y espacios"
                                       ]);
                                        exit;
                               
                    }

                    //Validación de la edad
                    $fecha=new DateTime($_POST['fecha_nac']);
                    $fechaNacimiento = new DateTime($_POST['fecha_nac']);
                    $hoy = new DateTime();
                    $edad = $hoy->diff($fechaNacimiento)->y;

                    // No permitir fechas futuras
                    if ($fecha > $hoy) {
                   echo json_encode([
                                    "status" => "error",
                                   "message" => "No puedes ingresar con una fecha futura"
                                       ]);
                                        exit;
                    }
                    if ($edad <=12) {
                      echo json_encode([
                                    "status" => "error",
                                   "message" => "Debes de tener más de 12 años para registrarte"
                                       ]);
                                        exit;
                    }
                    //------Validación del correo 
                        $email = trim($_POST['user_email']);

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode([
                                    "status" => "error",
                                   "message" => "Dominio incorrecto"
                                       ]);
                                        exit;
                    }

                    // Validar que solo tenga un '@' y dominio correcto
                    $partes = explode('@', $email);
                    if (count($partes) != 2 || empty($partes[0]) || empty($partes[1])) {
                        echo json_encode([
                                    "status" => "error",
                                   "message" => "Dominio incorrecto, solo debe de tener un arroba"
                                       ]);
                                        exit;
              
                    }

                    // Verificar dominio
                    if (!preg_match('/^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $partes[1])) {
                        echo json_encode([
                                    "status" => "error",
                                   "message" => "Dominio incorrecto"
                                       ]);
                                        exit;
                    }



                
                    //Validar contraseña

                    $pass = trim($_POST['pass']);

                    $letrasValidas = preg_match('/[\p{L}]/u', $pass);
                    $mayuscula = preg_match('/[\p{Lu}Ñ]/u', $pass);
                    $minuscula = preg_match('/[\p{Ll}ñ]/u', $pass);
                    $numero = preg_match('/\d/', $pass);
                    $especial = preg_match('/[^\p{L}\p{N}\s]/u', $pass);
                    // Validar longitud mínima
                    if (strlen($pass) < 8) {
                        echo json_encode([
                                    "status" => "error",
                                   "message" => " La contraseña debe tener al menos 8 carácteres"
                                       ]);
                                        exit;
                    }
                    // Comprobar que la ñ/Ñ sea tratada como letra, no como símbolo
                    if (preg_match('/ñ|Ñ/', $pass)) {}



                    if (!$mayuscula) {
                            echo json_encode([
                                    "status" => "error",
                                   "message" => " La contraseña debe incluir al menos una letra mayúscula"
                            ]);
                                        exit;
             
                    }

                    if (!$minuscula) {
                            echo json_encode([
                                    "status" => "error",
                                   "message" => " La contraseña debe inlcuir una letra minúscula"
                                       ]);
                                        exit;
                    }

                    if (!$numero) {
                             echo json_encode([
                                    "status" => "error",
                                   "message" => " La contraseña debe incluir al menos un numero"
                                       ]);
                                        exit;
                    }

                    if (!$especial) {
                                   echo json_encode([
                                    "status" => "error",
                                   "message" => " La contraseña debe incluir al menos un carácter especial (por ejemplo: @, #, $, %, &)"
                     
                                       ]);
                                        exit;
                    }

                    if (empty($_POST['gene'])) {
                                   echo json_encode([
                                    "status" => "error",
                                   "message" => "Debes seleccionar un género"
                     
                                       ]);
                                        exit;
                    }
                    
                    if (empty($_POST['pais'])) {
                                   echo json_encode([
                                    "status" => "error",
                                   "message" => "Debes seleccionar un país"
                     
                                       ]);
                                        exit;
                    }

                    if (empty($_POST['naci'])) {
                                   echo json_encode([
                                    "status" => "error",
                                   "message" => "Debes seleccionar una nacionalidad"
                     
                                       ]);
                                        exit;
                    }

                    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $pais)) {
                    echo json_encode([
                                    "status" => "error",
                                   "message" => "El país solo puede contener letras y espacios"
                                       ]);
                                        exit;
                               
                    }

                    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $naci)) {
                    echo json_encode([
                                    "status" => "error",
                                   "message" => "La nacionalidad solo puede contener letras y espacios"
                                       ]);
                                        exit;
                               
                    }

                    // Encriptar contraseña
                    $hashedPass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

                    // Procesar imagen (opcional)
                    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Debes seleccionar una foto de perfil"
                        ]);
                        exit;
                    }

                    $foto = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $foto = file_get_contents($_FILES['image']['tmp_name']); // lectura binaria
                    }


                    // Procedimiento almacenado
                    $query = "CALL InsertarUsuario(
                        :Nombre, :Apellidos, :Fechanatal, :Alias, :Email, :Pass,
                        :Foto, :Genero, :Paisnatal, :Nacionalidad, :Tipo
                    )";

                    $stmt = $db->prepare($query);

                    // Asignar valores
                    $nombre        = trim($_POST['user_name']);
                    $apellidos     = trim($_POST['user_apellido']);
                    $fechanatal    = $_POST['fecha_nac'];
                    $alias         = trim($_POST['user_name2']);
                    $email         = trim($_POST['user_email']);
                    $genero        = ($_POST['gene'] === 'Masculino') ? 1 : 0; 
                    $paisnatal     = trim($_POST['pais']);
                    $nacionalidad  = trim($_POST['naci']);
                    $tipo          = 0; // valor por defecto o dinámico

                    // Vincular parámetros
                    $stmt->bindParam(":Nombre", $nombre);
                    $stmt->bindParam(":Apellidos", $apellidos);
                    $stmt->bindParam(":Fechanatal", $fechanatal);
                    $stmt->bindParam(":Alias", $alias);
                    $stmt->bindParam(":Email", $email);
                    $stmt->bindParam(":Pass", $hashedPass);
                    $stmt->bindParam(":Foto", $foto, PDO::PARAM_LOB);
                    $stmt->bindParam(":Genero", $genero, PDO::PARAM_INT);
                    $stmt->bindParam(":Paisnatal", $paisnatal);
                    $stmt->bindParam(":Nacionalidad", $nacionalidad);
                    $stmt->bindParam(":Tipo", $tipo, PDO::PARAM_INT);

                    // Ejecutar
                    if ($stmt->execute()) {
                          echo json_encode([
                  "status" => "success",
                  "message" => "Todo bien:)",

                    ]);
                       
            
                        exit();
                        
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error al registrar usuario"]);
                    }

                } catch (PDOException $e) {
                    $mensaje = $e->getMessage();

                    //  trigger lanzó SIGNAL 45000
                    if (str_contains($mensaje, 'correo electrónico ya está en uso')) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Ese correo ya está registrado, prueba con otro."
                        ]);
                        exit();
                    }

                    // Luego validar la edad
                    if (str_contains($mensaje, '13 años')) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "El usuario debe tener al menos 13 años para registrarse"
                        ]);
                        exit();
                    }

                    // Otros errores
                    file_put_contents("debug_registrarUsuario_error.txt", $e->getMessage());
                    echo json_encode(["message" => "Error en la base de datos"]);
                    exit();
                }
                
            } else {

                http_response_code(400);
                echo json_encode([
                "status" => "error",
                "message" => "Datos incompletos"
                ]);
                    
            }
            
        }

        // =============================================================================================
        // -----------------------------INICIAR SESIÓN ----------------------------------------------
        // =============================================================================================
        if($_POST['accion'] === 'iniciarSesion'){
            try {
                // Obtener alias y contraseña desde los parámetros GET
                if (!isset($_POST['user_name2']) || !isset($_POST['user_pass'])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Faltan parámetros: alias y pass"]);
                    exit;
                }

                $alias = trim($_POST['user_name2']);
                $pass = trim($_POST['user_pass']);

                // Buscar usuario
                $query = "CALL IniciarSesion(:Alias)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":Alias", $alias);
                $stmt->execute();

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!empty($usuario['Foto'])) {
                    $usuario['Foto'] = 'data:image/jpeg;base64,' . base64_encode($usuario['Foto']);
                }

                if ($usuario && password_verify($pass, $usuario['Pass'])) {
                    // No se envía la contraseña en la respuesta
                    unset($usuario['Pass']);
                    session_start();
                    $_SESSION['usuarioSesion'] = [
                        "Idusuario" => $usuario['Idusuario'],
                        "Alias" => $usuario['Alias'],
                        "Nombre" => $usuario['Nombre'],
                        "Apellidos" => $usuario['Apellidos'],
                        "Fechanatal" => $usuario['Fechanatal'],
                        "Email" => $usuario['Email'],
                        "Foto" => $usuario['Foto'],
                        "Genero" => $usuario['Genero'],
                        "Paisnatal" => $usuario['Paisnatal'],
                        "Nacionalidad" => $usuario['Nacionalidad'],
                        "Tipo" => $usuario['Tipo']
                    ];

                    // Al iniciar sesión correctamente:
                    $token = TokenSimple::generar([
                        "Idusuario" => $usuario['Idusuario'],
                        "Alias" => $usuario['Alias'],
                        "Tipo" => $usuario['Tipo'] //Importante
                    ]);
                    $_SESSION['token'] = $token;


                    echo json_encode([
                        "message" => "Inicio de sesión exitoso",
                        "token" => $token
                    ]);


                    //Redireccionamiento a index.php
                    header("Location: ../index.php");
                    exit();

                    //Debuggeo
                    http_response_code(200);
                    echo json_encode([
                    "message" => "Inicio de sesión exitoso",
                    "usuario" => $usuario   
                    ]);
                    

                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Usuario o contraseña incorrectos"]);
                }

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["message" => "Error al consultar usuario", "error" => $e->getMessage()]);
            }
        }


        // =============================================================================================
        // -----------------------------MODIFICAR USUARIO ----------------------------------------------
        // =============================================================================================
        if($_POST['accion'] === 'modificarUsuario'){
            
            $usuarioData = TokenSimple::verificar();

            if (isset($_POST['accion'])) {
                try {
                    
                    
                    if (
                        empty($_POST['Idusuario']) ||  
                         empty($_POST['correo']) ||
                        !isset($_POST['gene'])
                    ) {
                        http_response_code(400);
                        echo json_encode(["message" => "Faltan datos para actualizar el usuario"]);
                        break;
                    }

                    $idusuario   = (int) $_POST['Idusuario'];
                    $nombre      = trim($_POST['nombre']);
                    $apellidos   = trim($_POST['apellido']);
                    $alias       = trim($_POST['n_usuario']);
                    $fechanatal  = $_POST['fecha_nac'];
                    $paisnatal   = trim($_POST['pais']);
                    $nacionalidad = trim($_POST['nacionalidad']);
                    $email       = trim($_POST['correo']);
                    $genero      = ($_POST['gene'] === "Masculino") ? 1 : 0;
                    $pass        = trim($_POST['pass']);

                    //validar nombre y apellido 
                    $nombre = null;
                    if(!empty($_POST['nombre'])){
                        $nombre = trim($_POST['nombre']);
                        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {
                            echo json_encode(["success" => false, "message" => "El nombre solo puede contener letras y espacios."]);
                            exit;
                        }
                        // Letras, espacios, acentos y símbolos de idiomas (UTF-8)
                        if (!preg_match('/^[\p{L}\s]+$/u', $nombre)) {
                            echo json_encode(["success" => false, "message" => "El nombre solo puede contener letras y espacios (incluyendo acentos y caracteres de otros idiomas)."]);
                            exit;
                        }
                    }
                    
                    $apellidos = null;
                    if(!empty($_POST['apellido'])){
                        $apellidos = trim($_POST['apellido']);
                        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $apellidos)) {
                            echo json_encode(["success" => false, "message" => "El apellido solo puede contener letras y espacios."]);
                            exit;
                        }
                    }

                    $alias = null;
                    if(!empty($_POST['n_usuario'])){
                        $alias = trim($_POST['n_usuario']);
                        
                    }

                    

                    $fecha = null;

                    if(!empty($_POST['fecha_nac'])){

                        //Validación de la edad
                        $fecha=new DateTime($_POST['fecha_nac']);
                        $fechaNacimiento = new DateTime($_POST['fecha_nac']);
                        $hoy = new DateTime();
                        $edad = $hoy->diff($fechaNacimiento)->y;

                        // No permitir fechas futuras
                        if ($fecha > $hoy) {
                            echo json_encode(["success" => false, "message" => "La fecha de nacimiento no puede ser futura."]);
                        }
                        if ($edad < 13) {
                            echo json_encode(["success" => false, "message" => "Debes ser mayor de 13 años edad."]);
                            exit;
                        }
                    }
                    
                    

                    //------Validación del correo 
                    $email = null;
                    if(!empty($_POST['correo'])){
                        $email = trim($_POST['correo']);

                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            echo json_encode(["success" => false, "message" => "Formato de correo no válido."]);
                            exit;
                        }

                        // Validar que solo tenga un '@' y dominio correcto
                        $partes = explode('@', $email);
                        if (count($partes) != 2 || empty($partes[0]) || empty($partes[1])) {
                            echo json_encode(["success" => false, "message" => "El correo debe tener un solo @ y un dominio válido."]);
                            exit;
                        }

                        // Verificar dominio
                        if (!preg_match('/^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $partes[1])) {
                            echo json_encode(["success" => false, "message" => "El dominio del correo no es válido."]);
                            exit;
                        }
                    }



                    //Validar contraseña (si no es nula, se valida)

                    $pass = null;
                    if(!empty($_POST['pass'])){
                        $pass = trim($_POST['pass']);

                        $letrasValidas = preg_match('/[\p{L}]/u', $pass);
                        $mayuscula = preg_match('/[\p{Lu}Ñ]/u', $pass);
                        $minuscula = preg_match('/[\p{Ll}ñ]/u', $pass);
                        $numero = preg_match('/\d/', $pass);
                        $especial = preg_match('/[^\p{L}\p{N}\s]/u', $pass);
                        // Validar longitud mínima
                        if (strlen($pass) < 8) {
                            echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
                            exit;
                        }
                        // Comprobar que la ñ/Ñ sea tratada como letra, no como símbolo
                        if (preg_match('/ñ|Ñ/', $pass)) {}



                        if (!$mayuscula) {
                            echo json_encode(["success" => false, "message" => "La contraseña necesita una mayúscula."]);
                            exit;
                        }

                        if (!$minuscula) {
                            echo json_encode(["success" => false, "message" => "La contraseña necesita una minúscula."]);
                            exit;
                        }

                        if (!$numero) {
                            echo json_encode(["success" => false, "message" => "La contraseña necesita un número."]);
                            exit;
                        }

                        if (!$especial) {
                            echo json_encode(["success" => false, "message" => "La contraseña necesita un símbolo."]);
                            exit;
                        }
                        $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
                    }


                    $paisnatal=null;
                    if(!empty($_POST['pais'])){

                        $paisnatal = trim($_POST['pais']);
                        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $paisnatal)) {
                            echo json_encode(["success" => false, "message" => "El país solo puede contener letras y espacios."]);
                            exit;
                        }
                    }

                    $nacionalidad=null;
                    if(!empty($_POST['nacionalidad'])){

                        $nacionalidad = trim($_POST['nacionalidad']);
                        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', $nacionalidad)) {
                            echo json_encode(["success" => false, "message" => "La nacionalidad solo puede contener letras y espacios."]);
                            exit;
                        }
                    }
                    
                    //cargar foto
                    $foto = null;
                    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $foto = file_get_contents($_FILES['image']['tmp_name']);
                    } /*else {
                        http_response_code(400);
                        echo json_encode(["message" => "Error: no se recibió una imagen válida"]);
                        break;
                    }*/

                    

                    // Ejecutar procedimiento de modificación
                    $query = "CALL ModificarUsuario(:Idusuario, :Nombre, :Apellidos, :Fechanatal, :Alias, :Email, :Pass, :Foto, :Genero, :Paisnatal, :Nacionalidad)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(":Idusuario", $idusuario, PDO::PARAM_INT);
                    $stmt->bindParam(":Nombre", $nombre);
                    $stmt->bindParam(":Apellidos", $apellidos);
                    $stmt->bindParam(":Fechanatal", $fechanatal);
                    $stmt->bindParam(":Alias", $alias);
                    $stmt->bindParam(":Email", $email);
                    $stmt->bindParam(":Pass", $hashedPass);
                    $stmt->bindParam(":Foto", $foto, PDO::PARAM_LOB);
                    $stmt->bindParam(":Genero", $genero, PDO::PARAM_INT);
                    $stmt->bindParam(":Paisnatal", $paisnatal);
                    $stmt->bindParam(":Nacionalidad", $nacionalidad);

                    if ($stmt->execute()) {
                        // Obtener los nuevos datos desde la BD
                        
                        $select = $db->prepare("CALL TraerNuevosDatosUsuario(:id)");
                        $select->bindParam(":id", $idusuario, PDO::PARAM_INT);
                        $select->execute();
                        $usuarioActualizado = $select->fetch(PDO::FETCH_ASSOC);

                        if (!empty($usuarioActualizado['Foto'])) {
                            $usuarioActualizado['Foto'] = 'data:image/jpeg;base64,' . base64_encode($usuarioActualizado['Foto']);
                        }
                        
                        if ($usuarioActualizado) {
                            // Actualizar sesión
                            session_start();
                            $_SESSION['usuarioSesion'] = [
                                "Idusuario"     => $usuarioActualizado['Idusuario'],
                                "Alias"         => $usuarioActualizado['Alias'],
                                "Nombre"        => $usuarioActualizado['Nombre'],
                                "Apellidos"     => $usuarioActualizado['Apellidos'],
                                "Fechanatal"    => $usuarioActualizado['Fechanatal'],
                                "Email"         => $usuarioActualizado['Email'],
                                "Foto"          => $usuarioActualizado['Foto'],
                                "Genero"        => $usuarioActualizado['Genero'],
                                "Paisnatal"     => $usuarioActualizado['Paisnatal'],
                                "Nacionalidad"  => $usuarioActualizado['Nacionalidad'],
                                "Tipo"          => $usuarioActualizado['Tipo']
                            ];

                            
                            // Devolver JSON, no redirigir
                            echo json_encode([
                                "success" => true,
                                "message" => "Usuario actualizado correctamente",
                                "usuario" => $usuarioActualizado
                            ]);
                            exit;
                        }

                        http_response_code(200);
                        echo json_encode(["message" => "Usuario actualizado correctamente"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error al actualizar usuario"]);
                    }

                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(["message" => "Error en la BD", "error" => $e->getMessage()]);
                }
            }

        }



        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE CATEGORIAS ------------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////


        // =============================================================================================
        // ----------------------------- REGISTRAR CATEGORIA -------------------------------------------
        // =============================================================================================
        if($_POST['accion'] === 'crearCategoria'){

            $admin=TokenSimple::verificarAdmin();

            if ($_POST['accion'] === 'crearCategoria') {
                $nombre = trim($_POST['nombre']);
                $activo = 1;

                if (empty($nombre)) {
                    http_response_code(400);
                    echo json_encode(["message" => "El nombre de la categoría es obligatorio."]);
                    break;
                }

                $query = "CALL CrearCategoria(:Nombre, :Activo, @resultado)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":Nombre", $nombre);
                $stmt->bindParam(":Activo", $activo, PDO::PARAM_INT);
                $stmt->execute();

                // Obtener valor OUT
                $res = $db->query("SELECT @resultado")->fetchColumn();

                switch ($res) {
                    
                    case 1:
                    http_response_code(400);
                    echo json_encode(["message" => "Esta categoría ya existe, por favor ingresa otra."]);
                    break;

                    case 2:
                    http_response_code(400);
                    echo json_encode(["message" => "La categoría es demasiado larga. Máximo 30 caracteres."]);
                    break;

                    case 0:
                    echo json_encode(["message" => "Categoría creada exitosamente."]);
                    break;

                    default:
                    http_response_code(500);
                    echo json_encode(["message" => "Error desconocido al crear la categoría."]);
                }

            }
        }

        // =============================================================================================
        // ----------------------------- TRAER ID Y NOMBRE DE CATEGORIAS -------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'traerCategorias') {
            try {
                $stmt = $db->query("CALL TraerCategorias();");
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($categorias, JSON_UNESCAPED_UNICODE);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al obtener los mundiales",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- TRAER ID Y NOMBRE DE CATEGORIAS -------------------------------
        // =============================================================================================




        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE MUNDIALES -------------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////
        
        // =============================================================================================
        // ----------------------------- CREAR MUNDIAL -------------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'crearMundial') {

            $autorizacion=TokenSimple::verificarAdmin();
            try {

                // === 1. Recibir los datos del formulario ===
                $anio          = $_POST['anio'];
                $sede          = trim($_POST['sede']);
                $descripcion   = trim($_POST['descripcion']);
                $mascota       = trim($_POST['mascota']);
                $campeon       = trim($_POST['campeon']);
                $subcampeon    = trim($_POST['subcampeon']);
                $marcadorFinal = trim($_POST['marcador_final']);
                $final         = trim($_POST['final']);
                $liderGoleo    = trim($_POST['lider_goleo']);
                $lugar3        = trim($_POST['lugar_3']);
                $lugar4        = trim($_POST['lugar_4']);
                $activo        = 1;

                // === 2. Validar campos vacíos ===
                if (
                    empty($anio) || empty($sede) || empty($descripcion) || empty($mascota) ||
                    empty($campeon) || empty($subcampeon) || empty($marcadorFinal) ||
                    empty($final) || empty($liderGoleo) || empty($lugar3) || empty($lugar4)
                ) {
                    http_response_code(400);
                    echo json_encode(["message" => "Todos los campos son obligatorios."]);
                    exit;
                }

                // === Validar Año ===

                // Solo números
                if (!ctype_digit($anio)) {
                    http_response_code(400);
                    echo json_encode(["message" => "El año solo puede contener números."]);
                    exit;
                }

                // Convertir a int para comparar
                $anioInt = intval($anio);

                // Año >= 1930
                if ($anioInt < 1930) {
                    http_response_code(400);
                    echo json_encode(["message" => "El mundial no puede ser antes de 1930."]);
                    exit;
                }

                // Año exactamente 4 dígitos
                if (strlen($anio) !== 4) {
                    http_response_code(400);
                    echo json_encode(["message" => "El año debe tener exactamente 4 dígitos."]);
                    exit;
                }

                // === Validación longitudes ===

                $val60 = [$sede, $mascota, $campeon, $subcampeon, $liderGoleo, $lugar3, $lugar4, $final];

                foreach ($val60 as $v) {
                    if (strlen($v) > 60) {
                        http_response_code(400);
                        echo json_encode(["message" => "Los campos no pueden exceder 60 caracteres."]);
                        exit;
                    }
                }

                if (strlen($descripcion) > 500) {
                    http_response_code(400);
                    echo json_encode(["message" => "La descripción no puede exceder 500 caracteres."]);
                    exit;
                }

                // === Validación solo letras ===

                $soloLetras = [$sede, $mascota, $campeon, $subcampeon, $liderGoleo, $lugar3, $lugar4];

                foreach ($soloLetras as $campo) {
                    if (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $campo)) {
                        http_response_code(400);
                        echo json_encode(["message" => "Algunos campos solo deben contener letras."]);
                        exit;
                    }
                }

                // === Marcador Final (formato 3-2) ===
                if (!preg_match('/^[0-9]{1,2}-[0-9]{1,2}$/', $marcadorFinal)) {
                    http_response_code(400);
                    echo json_encode(["message" => "El marcador debe tener el formato 3-2 (solo números y un guion)."]);
                    exit;
                }

                // === Campeón, Subcampeón, 3er y 4to deben ser diferentes ===
                if (
                    $campeon === $subcampeon ||
                    $campeon === $lugar3 ||
                    $campeon === $lugar4 ||
                    $subcampeon === $lugar3 ||
                    $subcampeon === $lugar4 ||
                    $lugar3 === $lugar4
                ) {
                    http_response_code(400);
                    echo json_encode(["message" => "Campeón, Subcampeón, 3er lugar y 4to lugar deben ser diferentes."]);
                    exit;
                }

                // === 3. Leer archivos binarios (imágenes) ===
                if (!isset($_FILES['imagen_destacada']) || $_FILES['imagen_destacada']['error'] === UPLOAD_ERR_NO_FILE) {
                    http_response_code(400);
                    echo json_encode([
                        "status" => "error",
                        "message" => "Debes seleccionar una imagen destacada"
                    ]);
                    exit;
                }

                if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
                    http_response_code(400);
                    echo json_encode([
                        "status" => "error",
                        "message" => "Debes seleccionar un logo"
                    ]);
                    exit;
                }

                $foto = null;
                $logo = null;

                if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] === UPLOAD_ERR_OK) {
                    $foto = file_get_contents($_FILES['imagen_destacada']['tmp_name']);
                }

                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $logo = file_get_contents($_FILES['logo']['tmp_name']);
                }

                

                // === 4. Ejecutar Procedure ===

                $query = "CALL CrearMundial(
                    :Sede, :Anio, :Foto, :Logo, :Descripcion,
                    :Mascota, :Campeon, :Subcampeon, :Marcadorfinal,
                    :Final, :Lidergoleo, :Tercerlugar, :Cuartolugar, :Activo
                )";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':Sede', $sede);
                $stmt->bindParam(':Anio', $anioInt, PDO::PARAM_INT);
                $stmt->bindParam(':Foto', $foto, PDO::PARAM_LOB);
                $stmt->bindParam(':Logo', $logo, PDO::PARAM_LOB);
                $stmt->bindParam(':Descripcion', $descripcion);
                $stmt->bindParam(':Mascota', $mascota);
                $stmt->bindParam(':Campeon', $campeon);
                $stmt->bindParam(':Subcampeon', $subcampeon);
                $stmt->bindParam(':Marcadorfinal', $marcadorFinal);
                $stmt->bindParam(':Final', $final);
                $stmt->bindParam(':Lidergoleo', $liderGoleo);
                $stmt->bindParam(':Tercerlugar', $lugar3);
                $stmt->bindParam(':Cuartolugar', $lugar4);
                $stmt->bindParam(':Activo', $activo, PDO::PARAM_INT);

                // === 5. Ejecutar ===
                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Mundial creado exitosamente."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Error al crear el Mundial."]);
                }

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error en la base de datos",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- TRAER ID, Y NOMBRE DE MUNDIALES -------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'traerMundiales') {
            try {
                $stmt = $db->query("CALL TraerMundiales_Id_Nombre();");
                $mundiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($mundiales, JSON_UNESCAPED_UNICODE);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al obtener los mundiales",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- MODIFICAR MUNDIAL ---------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'modificarMundial') {

            // DEBUG: ver lo que llega
            file_put_contents("debug_mundial.txt",
                "POST:\n" . print_r($_POST, true) .
                "\nFILES:\n" . print_r($_FILES, true)
            );

            // 1. Verificación de admin
            $usuarioData = TokenSimple::verificarAdmin(); // lanza excepción si no es admin

            $idMundial = $_POST['Idmundial'] ?? null;
            if (!$idMundial) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "Idmundial faltante"]);
                exit;
            }

            // 2. Campos de texto
            $anio           = $_POST['Año'] ?? '';
            $sede           = $_POST['Sede'] ?? '';
            $descripcion    = $_POST['Descripcion'] ?? '';
            $mascota        = $_POST['Mascota'] ?? '';
            $campeon        = $_POST['Campeon'] ?? '';
            $subcampeon     = $_POST['Subcampeon'] ?? '';
            $marcadorFinal  = $_POST['MarcadorFinal'] ?? '';
            $final          = $_POST['Final'] ?? '';
            $liderGoleo     = $_POST['Lidergoleo'] ?? '';
            $tercerLugar    = $_POST['Tercerlugar'] ?? '';
            $cuartoLugar    = $_POST['Cuartolugar'] ?? '';

            // === 2. Validar campos vacíos ===
            if (
                empty($anio) || empty($sede) || empty($descripcion) || empty($mascota) ||
                empty($campeon) || empty($subcampeon) || empty($marcadorFinal) ||
                empty($final) || empty($liderGoleo) || empty($tercerLugar) || empty($cuartoLugar)
            ) {
                http_response_code(400);
                echo json_encode(["message" => "Todos los campos son obligatorios."]);
                exit;
            }


            // === Validar Año ===

            // Solo números
            if (!ctype_digit($anio)) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "El año solo puede tener números"]);
                exit;
            }

            // Convertir a int para comparar
            $anioInt = intval($anio);

            // Año >= 1930
            if ($anioInt < 1930) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "El año no puede ser menor a 1930"]);
                exit;
            }

            // Año exactamente 4 dígitos
            if (strlen($anio) !== 4) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "El año debe tener 4 dígitos"]);
                exit;
            }            

            // === Validación longitudes ===

            $val60 = [$sede, $mascota, $campeon, $subcampeon, $liderGoleo, $tercerLugar, $cuartoLugar, $final];

            foreach ($val60 as $v) {
                if (strlen($v) > 60) {
                    http_response_code(400);
                    echo json_encode(["estado" => "error", "mensaje" => "Los campos no pueden exceder 60 caracteres"]);
                    exit;
                }
            }

            if (strlen($descripcion) > 500) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "La descripcion no puede exceder los 500 caracteres"]);
                exit;
            }

            // === Validación solo letras ===

            $soloLetras = [$sede, $mascota, $campeon, $subcampeon, $liderGoleo, $tercerLugar, $cuartoLugar];

            foreach ($soloLetras as $campo) {
                if (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $campo)) {
                    http_response_code(400);
                    echo json_encode(["estado" => "error", "mensaje" => "Algunos campos solo deben contener letras"]);
                    exit;
                }
            }

            // === Marcador Final (formato 3-2) ===
            if (!preg_match('/^[0-9]{1,2}-[0-9]{1,2}$/', $marcadorFinal)) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "El marcador debe tener el formato 3-2 (solo números y un guion)."]);
                exit;
            }

            // === Campeón, Subcampeón, 3er y 4to deben ser diferentes ===
            if (
                $campeon === $subcampeon ||
                $campeon === $tercerLugar ||
                $campeon === $cuartoLugar ||
                $subcampeon === $tercerLugar ||
                $subcampeon === $cuartoLugar ||
                $tercerLugar === $cuartoLugar
            ) {
                http_response_code(400);
                echo json_encode(["estado" => "error", "mensaje" => "Campeón, Subcampeón, 3er lugar y 4to lugar deben ser diferentes."]);
                exit;
            }

            // 3. Archivos (BLOB)
            $foto = !empty($_FILES['Foto']['tmp_name']) ? file_get_contents($_FILES['Foto']['tmp_name']) : null;
            $logo = !empty($_FILES['Logo']['tmp_name']) ? file_get_contents($_FILES['Logo']['tmp_name']) : null;

            try {

                $query = "CALL ModificarMundial(
                    :p_Idmundial,
                    :p_Sede,
                    :p_Anio,
                    :p_Foto,
                    :p_Logo,
                    :p_Descripcion,
                    :p_Mascota,
                    :p_Campeon,
                    :p_Subcampeon,
                    :p_Marcadorfinal,
                    :p_Final,
                    :p_Lidergoleo,
                    :p_Tercerlugar,
                    :p_Cuartolugar
                )";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':p_Idmundial', $idMundial, PDO::PARAM_INT);
                $stmt->bindParam(':p_Sede', $sede);
                $stmt->bindParam(':p_Anio', $anio, PDO::PARAM_INT);
                $stmt->bindParam(':p_Foto', $foto, PDO::PARAM_LOB);
                $stmt->bindParam(':p_Logo', $logo, PDO::PARAM_LOB);
                $stmt->bindParam(':p_Descripcion', $descripcion);
                $stmt->bindParam(':p_Mascota', $mascota);
                $stmt->bindParam(':p_Campeon', $campeon);
                $stmt->bindParam(':p_Subcampeon', $subcampeon);
                $stmt->bindParam(':p_Marcadorfinal', $marcadorFinal);
                $stmt->bindParam(':p_Final', $final);
                $stmt->bindParam(':p_Lidergoleo', $liderGoleo);
                $stmt->bindParam(':p_Tercerlugar', $tercerLugar);
                $stmt->bindParam(':p_Cuartolugar', $cuartoLugar);


                if ($stmt->execute()) {
                    echo json_encode([ "estado" => "ok", "mensaje" => "Mundial actualizado correctamente", "id" => $idMundial ]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Error al crear el Mundial."]);
                }
            }
            catch (Exception $ex) {

                file_put_contents("debug_mundial_error.txt", $ex->getMessage());

                http_response_code(500);
                echo json_encode([
                    "estado" => "error",
                    "mensaje" => "Error al ejecutar el procedimiento",
                    "detalle" => $ex->getMessage()
                ]);
            }

            exit;
        }

        // =============================================================================================
        // ----------------------------- ELIMINAR MUNDIAL ----------------------------------------------
        // =============================================================================================        
        if ($_POST['accion'] === 'eliminarMundial') {

            file_put_contents("debug_eliminar.txt", print_r($_POST, true));

            try {
                // Verifica admin
                $usuarioData = TokenSimple::verificarAdmin();

                $idMundial = $_POST['Idmundial'] ?? null;

                if (!$idMundial) {
                    http_response_code(400);
                    echo json_encode([
                        "estado" => "error",
                        "mensaje" => "Idmundial faltante"
                    ]);
                    exit;
                }

                // Llamar al procedure
                $stmt = $db->prepare("CALL EliminarMundial(:id)");
                $stmt->bindParam(":id", $idMundial, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo json_encode([
                        "estado" => "ok",
                        "mensaje" => "Mundial eliminado"
                    ]);
                } else {
                    echo json_encode([
                        "estado" => "error",
                        "mensaje" => "No se pudo eliminar"
                    ]);
                }

            } catch (Exception $ex) {

                file_put_contents("debug_eliminar_error.txt", $ex->getMessage());

                http_response_code(500);
                echo json_encode([
                    "estado" => "error",
                    "mensaje" => "Error interno",
                    "detalle" => $ex->getMessage()
                ]);
            }

            exit;
        }



        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE PUBLICACIONES ---------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////


        // =============================================================================================
        // ----------------------------- CREAR PUBLICACION ---------------------------------------------
        // =============================================================================================

        if ($_POST['accion'] === 'crearPublicacion') {

            $autenticacion = TokenSimple::verificar();
            // --- DEBUG: ver todo lo que llega a la API ---
            file_put_contents("debug_post.txt", print_r($_POST, true));
            file_put_contents("debug_files.txt", print_r($_FILES, true));
            $titulo = trim($_POST['titulo']);
            $descripcion = trim($_POST['descripcion']);
            $idusuario = (int) $_POST['idusuario'];
            $idcategoria = (int) $_POST['categoria'];
            $idmundial = (int) $_POST['idmundial'];
            $fecha = date('Y-m-d H:i:s');
            $activo = 0;
            $fechaAprobacion = null; // o date() si la apruebas automáticamente

            // validar campos básicos
            if (empty($titulo) || empty($descripcion) || empty($idusuario) || empty($idcategoria) || empty($idmundial)) {
                http_response_code(400);
                echo json_encode(["message" => "Faltan campos obligatorios"]);
                exit;
            }

            // Determinar tipo de archivo
            $rutaArchivo = null;
            $contenidoArchivo = null;

            if (!empty($_FILES['archivo']['tmp_name'])) {
                $tipo = mime_content_type($_FILES['archivo']['tmp_name']);

                // Carpeta base física (para mover el archivo)
                $baseUploads = "../uploads/videos";

                // Crear carpeta base si no existe
                if (!is_dir($baseUploads)) {
                    mkdir($baseUploads, 0777, true);
                }

                // Crear carpeta del usuario (física)
                $carpetaUsuario = $baseUploads . "/" . $idusuario;
                if (!is_dir($carpetaUsuario)) {
                    mkdir($carpetaUsuario, 0777, true);
                }

                // Nombre del archivo con fecha y hora
                $timestamp = date("Ymd_His");
                $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $timestamp . "." . $extension;

                // Ruta física real donde se guardará
                $rutaDestino = $carpetaUsuario . "/" . $nombreArchivo;

                if (str_starts_with($tipo, 'video/')) {
                    // --- si es video, guardar físicamente ---
                    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
                        // Ruta física (para mover) y ruta web (para mostrar)
                        $rutaWeb = "uploads/videos/" . $idusuario . "/" . $nombreArchivo;

                        // Guardamos solo la ruta web en la BD
                        $rutaArchivo = $rutaWeb;
                        $contenidoArchivo = null;
                    } else {
                        throw new Exception("No se pudo mover el archivo de video");
                    }
                } else {
                    // --- si es imagen, guardamos blob ---
                    $contenidoArchivo = file_get_contents($_FILES['archivo']['tmp_name']);
                    $rutaArchivo = null;
                }
            }


            if (strlen($titulo) > 60) {
            http_response_code(400);
            echo json_encode(["message" => "El titulo es muy largo, no puede exceder 60 caracteres."]);
            exit;
            }
            
            if (strlen($descripcion) > 255) {
            http_response_code(400);
            echo json_encode(["message" => "La descripción no puede exceder 255 caracteres."]);
            exit;
            }

            try {
                // Insertar publicación
                $stmt = $db->prepare("
                    CALL CrearPublicacion(
                        :Titulo,
                        :Descripcion,
                        :Fecha,
                        :Fechaaprobacion,
                        :Activo,
                        :Idusuario,
                        :Idcategoria,
                        :Idmundial,
                        @out_id
                    )
                ");
                $stmt->bindParam(":Titulo", $titulo);
                $stmt->bindParam(":Descripcion", $descripcion);
                $stmt->bindParam(":Fecha", $fecha);
                $stmt->bindParam(":Fechaaprobacion", $fechaAprobacion);
                $stmt->bindParam(":Activo", $activo, PDO::PARAM_INT);
                $stmt->bindParam(":Idusuario", $idusuario, PDO::PARAM_INT);
                $stmt->bindParam(":Idcategoria", $idcategoria, PDO::PARAM_INT);
                $stmt->bindParam(":Idmundial", $idmundial, PDO::PARAM_INT);
                $stmt->execute();

                // obtener el valor OUT devuelto
                $row = $db->query("SELECT @out_id AS IdPublicacion")->fetch(PDO::FETCH_ASSOC);
                $idpublicacion = $row['IdPublicacion'] ?? 0;

                file_put_contents("debug_api.txt", "ID generado: $idpublicacion\n", FILE_APPEND);

                if (!$idpublicacion) {
                    throw new Exception("No se pudo obtener el ID de la publicación");
                }

                // Insertar multimedia (solo si hay archivo)
                if (!empty($_FILES['archivo']['tmp_name'])) {
                    $stmt2 = $db->prepare("CALL CrearMultimedia(:Ruta, :Archivo, :Idpublicacion)");
                    $stmt2->bindParam(":Ruta", $rutaArchivo);
                    $stmt2->bindParam(":Archivo", $contenidoArchivo, PDO::PARAM_LOB);
                    $stmt2->bindParam(":Idpublicacion", $idpublicacion, PDO::PARAM_INT);
                    $stmt2->execute();
                }

                http_response_code(201);
                echo json_encode([
                    "message" => "Publicación creada correctamente. Espera a que el administrador la apruebe.",
                    "idpublicacion" => $idpublicacion
                ]);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error en la BD",
                    "error" => $e->getMessage()
                ]);
                file_put_contents("debug_api.txt", "ERROR SQL: " . $e->getMessage() . "\n", FILE_APPEND);
            } catch (Exception $ex) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error interno",
                    "error" => $ex->getMessage()
                ]);
                file_put_contents("debug_api.txt", "ERROR PHP: " . $ex->getMessage() . "\n", FILE_APPEND);
            }
        }

        // =============================================================================================
        // --------------------------- PANEL ADMIN: PUBLICACIONES + CATEGORÍAS --------------------------
        // =============================================================================================
        if (isset($_POST['accion']) && $_POST['accion'] === 'listarPanelAdmin') {
            file_put_contents("debug_inicio_panel_admin.txt", " Entró a listarPanelAdmin\n", FILE_APPEND);

            $usuarioData = TokenSimple::verificarAdmin();

            try {
                //  Traer PUBLICACIONES PENDIENTES
                $stmt1 = $db->prepare("CALL TraerPublicaciones(:p_Activo)");
                $activo = 0; // pendientes
                $stmt1->bindParam(':p_Activo', $activo, PDO::PARAM_INT);
                $stmt1->execute();
                $pendientes = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                $stmt1->closeCursor();
                $db->query("DO 0"); //  Forzar limpieza del buffer

                foreach ($pendientes as &$p) {
                    if (!empty($p['Archivo'])) {
                        $p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                    }
                    if (!empty($p['FotoUsuario'])) {
                        $p['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']);
                    }
                }

                //   Traer CATEGORÍAS
                $stmt2 = $db->prepare("CALL TraerCategorias()");
                $stmt2->execute();
                $categorias = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                $stmt2->closeCursor();
                $db->query("DO 0"); //  limpiar buffer de nuevo

                echo json_encode([
                    "success" => true,
                    "publicaciones" => $pendientes,
                    "categorias" => $categorias
                ], JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Error al listar panel admin.",
                    "error" => $e->getMessage()
                ]);
                file_put_contents("debug_panel_admin.txt", " Error PDO: " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        // =============================================================================================
        // ----------------------------- APROBAR / RECHAZAR PUBLICACIONES ------------------------------
        // =============================================================================================
        if (isset($_POST['accion']) && $_POST['accion'] === 'actualizarEstadoPublicacion') {
            $usuario = TokenSimple::verificarAdmin();

            try {
                if (!isset($_POST['Idpublicacion']) || !isset($_POST['NuevoEstado'])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Faltan parámetros"]);
                    exit;
                }

                $id = (int)$_POST['Idpublicacion'];
                $estado = (int)$_POST['NuevoEstado'];

                $stmt = $db->prepare("CALL CambiarEstatusPublicacion(:p_Activo, :p_Idpublicacion)");
                $stmt->bindParam(':p_Activo', $estado, PDO::PARAM_INT);
                $stmt->bindParam(':p_Idpublicacion', $id, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                $db->query("DO 0");

                echo json_encode(["success" => true, "message" => "Estado de publicación actualizado correctamente"]);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Error al actualizar la publicación.",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- LISTAR SOLO CATEGORÍAS ----------------------------------------
        // =============================================================================================
        if (isset($_POST['accion']) && $_POST['accion'] === 'listarCategorias') {
            file_put_contents("debug_inicio_listar.txt", "Entró a listarCategorias\n", FILE_APPEND);

            try {
                $stmt = $db->prepare("CALL TraerCategorias()");
                $stmt->execute();
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $db->query("DO 0");

                echo json_encode([
                    "success" => true,
                    "categorias" => $categorias
                ], JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Error en la base de datos.",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- ACTUALIZAR / DESACTIVAR CATEGORÍAS ----------------------------
        // =============================================================================================
        if (isset($_POST['accion']) && $_POST['accion'] === 'actualizarCategoria') {
            // ======================== DEBUG INICIAL ========================
            file_put_contents("debug_actualizar_categoria.txt", "\n\n=== INICIO ===\n" . print_r($_POST, true), FILE_APPEND);

            // ======================== VALIDAR TOKEN ========================
            $usuario = TokenSimple::verificarAdmin(); // Solo admin autenticado
            if (!$usuario) {
                http_response_code(403);
                echo json_encode([
                    "success" => false,
                    "message" => "Acceso denegado: token inválido o ausente."
                ]);
                exit;
            }

            try {
                // ======================== VALIDAR PARÁMETROS ========================
                if (!isset($_POST['Idcategoria']) || !isset($_POST['Nombre']) || !isset($_POST['Activo'])) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Faltan parámetros obligatorios."
                    ]);
                    exit;
                }

                $id = (int)$_POST['Idcategoria'];
                $nombre = trim($_POST['Nombre']);
                $activo = (int)$_POST['Activo'];

                // ======================== COMPLETAR NOMBRE SI ESTÁ VACÍO ========================
                if ($nombre === "") {
                    $stmtName = $db->prepare("SELECT Nombre FROM categoria WHERE Idcategoria = :id");
                    $stmtName->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmtName->execute();
                    $nombre = $stmtName->fetchColumn() ?: 'Categoría';
                    $stmtName->closeCursor();
                    $db->query("DO 0");
                }

                // ======================== EJECUTAR PROCEDIMIENTO ========================
                $stmt = $db->prepare("CALL ActualizarCategoria(:p_IdCategoria, :p_Nombre, :p_Activo)");
                $stmt->bindParam(':p_IdCategoria', $id, PDO::PARAM_INT);
                $stmt->bindParam(':p_Nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':p_Activo', $activo, PDO::PARAM_INT);
                $stmt->execute();

                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $db->query("DO 0");

                // ======================== DEBUG DE RESULTADO ========================
                file_put_contents("debug_actualizar_categoria.txt", "Resultado del procedure:\n" . print_r($resultado, true), FILE_APPEND);

                // ======================== RESPUESTA JSON ========================
                echo json_encode([
                    "success" => true,
                    "message" => $resultado['Mensaje'] ?? (
                        $activo === 1
                            ? " Categoría actualizada correctamente."
                            : " Categoría desactivada correctamente."
                    )
                ], JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                file_put_contents("debug_actualizar_categoria.txt", "ERROR PDO: " . $e->getMessage() . "\n", FILE_APPEND);

                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Error al actualizar la categoría.",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- TRAER LAS PUBLICACIONES DEL USUARIO --------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'listarPublicacionesUsuario') {

            $usuarioData = TokenSimple::verificar(); // Usuario normal en sesión

            try {
                $query = "CALL TraerPublicacionesUsuario(:p_IdUsuario)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':p_IdUsuario', $usuarioData['Idusuario'], PDO::PARAM_INT);
                $stmt->execute();

                $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                file_put_contents("debug_api_publisUsuario.txt", print_r($publicaciones, true));

                // Convierte blobs a base64
                foreach ($publicaciones as &$p) {
                    if (!empty($p['Archivo'])) {
                        $p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                    }

                    if (!empty($p['FotoUsuario'])) {
                        $p['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']);
                    }
                }

                echo json_encode($publicaciones, JSON_UNESCAPED_UNICODE);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al obtener publicaciones del usuario",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- EDITAR PUBLICACION --------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'modificarPublicacion') {

            try {
                $user = TokenSimple::verificar();
                $idusuario = $user["Idusuario"];

                $idPublicacion = $_POST['Idpublicacion'];
                $titulo = $_POST['Titulo'];
                $descripcion = $_POST['Descripcion'];
                $idMundial = $_POST['Idmundial'];
                $idCategoria = $_POST['Idcategoria'];

                $rutaArchivo = null;
                $contenidoArchivo = null;


                // validar campos básicos
                if (empty($titulo) || empty($descripcion) || empty($idusuario) || empty($idCategoria) || empty($idMundial)) {
                    http_response_code(400);
                    echo json_encode(["message" => "Faltan campos obligatorios"]);
                    exit;
                }

                // Si llega multimedia nuevo
                if (!empty($_FILES['archivoNuevo']['tmp_name'])) {

                    $tipo = mime_content_type($_FILES['archivoNuevo']['tmp_name']);
                    $baseUploads = "../uploads/videos";

                    if (!is_dir($baseUploads)) mkdir($baseUploads, 0777, true);

                    $carpetaUsuario = $baseUploads . "/" . $idusuario;
                    if (!is_dir($carpetaUsuario)) mkdir($carpetaUsuario, 0777, true);

                    $timestamp = date("Ymd_His");
                    $ext = pathinfo($_FILES['archivoNuevo']['name'], PATHINFO_EXTENSION);
                    $nombreArchivo = "$timestamp.$ext";

                    $rutaDestino = $carpetaUsuario . "/" . $nombreArchivo;

                    if (str_starts_with($tipo, "video/")) {

                        if (!move_uploaded_file($_FILES['archivoNuevo']['tmp_name'], $rutaDestino)) {
                            throw new Exception("Error moviendo archivo");
                        }

                        $rutaArchivo = "uploads/videos/$idusuario/$nombreArchivo";

                    } else {
                        $contenidoArchivo = file_get_contents($_FILES['archivoNuevo']['tmp_name']);
                    }
                }
                else{
                    $rutaArchivo = null;
                    $contenidoArchivo = null;
                }
                file_put_contents("debug_api_publiModificada.txt", print_r($_FILES['archivoNuevo']['name'], true));

                // Llamar procedure
                $stmt = $db->prepare("CALL ModificarPublicacion(:p_Idpublicacion, :p_Idmundial, :p_Idcategoria, 
                :p_Titulo, :p_Descripcion, :p_Ruta, :p_Archivo)");

                $stmt->bindParam(":p_Idpublicacion", $idPublicacion, PDO::PARAM_INT);
                $stmt->bindParam(":p_Idmundial", $idMundial, PDO::PARAM_INT);
                $stmt->bindParam(":p_Idcategoria", $idCategoria, PDO::PARAM_INT);
                $stmt->bindParam(":p_Titulo", $titulo);
                $stmt->bindParam(":p_Descripcion", $descripcion);
                $stmt->bindParam(":p_Ruta", $rutaArchivo);
                $stmt->bindParam(":p_Archivo", $contenidoArchivo, PDO::PARAM_LOB);
                $stmt->execute();

                echo json_encode(["success" => true, "message" => "Publicación modificada correctamente"]);

                exit;

            } catch (Exception $e) {

                echo json_encode(["success" => false, "message" => $e->getMessage()]);
                exit;
            }
        }


        // =============================================================================================
        // ----------------------------- BUSQUEDA DE PUBLICACIONES -------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'buscarPublicaciones') {

            $busqueda = $_POST['texto'] ?? '';

            // Si está vacío, devolvemos todas las publicaciones
            if (trim($busqueda) === '') {
                $stmt = $db->query("CALL TraerPublicaciones()");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                exit;
            }

            try {
                $stmt = $db->prepare("CALL BusquedaPublicaciones(:p)");
                $stmt->bindParam(":p", $busqueda);
                $stmt->execute();

                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                file_put_contents("debug_api_resultadoBusqueda.txt", print_r($resultados, true));

                // Convierte blobs a base64
                foreach ($publicaciones as &$p) {
                    if (!empty($p['Archivo'])) {
                        $p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                    }

                    if (!empty($p['FotoUsuario'])) {
                        $p['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']);
                    }
                }

                echo json_encode($resultados);
                
            } catch (Exception $ex) {
                echo json_encode([
                    "estado" => "error",
                    "mensaje" => "Error ejecutando búsqueda",
                    "detalle" => $ex->getMessage()
                ]);
            }

            exit;
        }





        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE COMENTARIOS ------------------ //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////
        
        // =============================================================================================
        // ----------------------------- CREAR COMENTARIOS ---------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'insertarComentario') {

            $usuarioData = TokenSimple::verificar(); // Requiere token del usuario autenticado
            file_put_contents("debug_usuarioData.txt", print_r($usuarioData, true));

            try {
                $idUsuario = $usuarioData['Idusuario'] ?? null;
                $idPublicacion = $_POST['Idpublicacion'] ?? null;
                $mensaje = trim($_POST['Mensaje'] ?? '');

                if (!$idUsuario || !$idPublicacion || $mensaje === '') {
                    http_response_code(400);
                    echo json_encode(["message" => "Datos incompletos"]);
                    exit;
                }
                 if (empty($mensaje)) {
                http_response_code(400);
                echo json_encode(["message" => "No puedes mandar un comentario vacio"]);
                exit;
            }
            if (strlen($mensaje) > 255) {
            http_response_code(400);
            echo json_encode(["message" => "El comentario es muy largo no puede exceder 255 caracteres."]);
            exit;
            }
                
                $activo = 1;
                $stmt = $db->prepare("CALL CrearComentario(:p_Idpublicacion, :p_Idusuario, :p_Texto, :p_Activo)");
                $stmt->bindParam(':p_Idpublicacion', $idPublicacion, PDO::PARAM_INT);
                $stmt->bindParam(':p_Idusuario', $idUsuario, PDO::PARAM_INT);
                $stmt->bindParam(':p_Texto', $mensaje, PDO::PARAM_STR);
                $stmt->bindParam(':p_Activo', $activo, PDO::PARAM_INT);

                file_put_contents("debug_api_comentario.txt", 
                    "Idusuario: $idUsuario\nIdpublicacion: $idPublicacion\nMensaje: $mensaje\n", 
                    FILE_APPEND
                );
                $stmt->execute();

                http_response_code(201);
                echo json_encode(["message" => "Comentario agregado exitosamente"]);
            } catch (PDOException $e) {
                file_put_contents("debug_error_comentario.txt", $e->getMessage());
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al insertar comentario",
                    "error" => $e->getMessage()
                ]);
            
            }
        }

        // =============================================================================================
        // ----------------------------- LISTAR COMENTARIOS --------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'listarComentarios') {

            $idPublicacion = $_POST['Idpublicacion'] ?? null;

            if (!$idPublicacion) {
                http_response_code(400);
                echo json_encode(["message" => "Idpublicacion requerido"]);
                exit;
            }

            // ============================================================
            //  Intentar leer token SI EXISTE (comentarios visibles sin login)
            // ============================================================
            $usuarioLog = null;
            $headers = getallheaders();

            if (isset($headers['Authorization'])) {

                // Extraer token del header
                $auth = $headers['Authorization'];
                if (str_starts_with($auth, "Bearer ")) {
                    $token = substr($auth, 7);
                    
                    try {
                        

                        // VERIFICAR EL TOKEN CORRECTO 💥
                        $usuarioLog = TokenSimple::verificar($token);
                    
                    } catch (Exception $e) {
                        $usuarioLog = null;
                    }
                }
            }


            try {
                $stmt = $db->prepare("CALL TraerComentarios(:pub)");
                $stmt->bindParam(':pub', $idPublicacion, PDO::PARAM_INT);
                $stmt->execute();
                $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($comentarios as &$c) {

                    // Convertir la foto a base64
                    if (!empty($c['FotoUsuario'])) {
                        $c['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($c['FotoUsuario']);
                    }

                    // -------------------------------------------------------
                    //  QUIÉN ES EL DUEÑO DEL COMENTARIO
                    // -------------------------------------------------------
                    $c['esPropio'] = false;
                    if ($usuarioLog && $usuarioLog['Idusuario'] == $c['Idusuario']) {
                        $c['esPropio'] = true;
                    }

                    // -------------------------------------------------------
                    //  ¿ES ADMIN?
                    // -------------------------------------------------------
                    $c['esAdmin'] = false;
                    if ($usuarioLog && isset($usuarioLog['Tipo']) && $usuarioLog['Tipo'] == 1) {
                        $c['esAdmin'] = true;
                    }
                }
                file_put_contents("debug_listar_comentarios.txt", print_r($comentarios, true));

                echo json_encode($comentarios, JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al obtener comentarios",
                    "error"   => $e->getMessage()
                ]);
            }
        }

        //=============================================================================================
        //-----------------------------EDITAR COMENTARIOS---------------------------------------------
        //=============================================================================================
        if ($_POST['accion'] === 'editarComentario') {

            $usuario = TokenSimple::verificar(); 
            $idUsuarioSesion = $usuario['Idusuario'];

            $idComentario = $_POST['Idcomentario'] ?? null;
            $nuevoTexto   = trim($_POST['Comentario'] ?? '');

            if (!$idComentario || empty($nuevoTexto)) {
                http_response_code(400);
                echo json_encode(["message" => "Idcomentario y Comentario son obligatorios"]);
                exit;
            }
                   file_put_contents("debug_editar.txt", print_r($_POST, true));

            try {
                // TRAER UN COMENTARIO, NO TODOS
                $stmt = $db->prepare("CALL TraerComentarioPorId(:idc)");
                $stmt->bindParam(':idc', $idComentario, PDO::PARAM_INT);
                $stmt->execute();
                $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                if (!$comentario) {
                    http_response_code(404);
                    echo json_encode(["message" => "Comentario no encontrado"]);
                    exit;
                }

                if ($comentario['Idusuario'] != $idUsuarioSesion) {
                    http_response_code(403);
                    echo json_encode(["message" => "No puedes editar este comentario"]);
                    exit;
                }

                $stmt2 = $db->prepare("CALL EditarComentario(:idc, :idusuario, :texto)");
                $stmt2->bindParam(':idc', $idComentario, PDO::PARAM_INT);
                $stmt2->bindParam(':idusuario', $idUsuarioSesion, PDO::PARAM_INT);
                $stmt2->bindParam(':texto', $nuevoTexto, PDO::PARAM_STR);
                $stmt2->execute();

                echo json_encode(["message" => "Comentario editado correctamente"]);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al editar comentario",
                    "error" => $e->getMessage()
                ]);
            }
        }

        //=============================================================================================
        //-----------------------------ELIMINAR COMENTARIOS (ADMIN)------------------------------------
        //=============================================================================================
        if ($_POST['accion'] === 'eliminarComentario') {

                $usuario = TokenSimple::verificarAdmin(); // Solo admin

                $idComentario = $_POST['Idcomentario'] ?? null;
                

                if (!$idComentario) {
                    http_response_code(400);
                    echo json_encode(["message" => "Idcomentario requerido"]);
                    exit;
                }
            

                try {
                    $stmt = $db->prepare("CALL EliminarComentario(:idc)");
                    $stmt->bindParam(':idc', $idComentario, PDO::PARAM_INT);
                    $stmt->execute();

                    echo json_encode(["message" => "Comentario eliminado correctamente"]);

                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode([
                        "message" => "Error al eliminar comentario",
                        "error" => $e->getMessage()
                    ]);
                }
        }

        if ($_POST['accion'] === "listarComentariosAutor") {

            // validar token
            $user = TokenSimple::verificar(); 

            $Idusuario = $user['Idusuario'];

            $stmt = $pdo->prepare("CALL TraerComentarios_AutoresPublicacion(:id)");
            $stmt->bindParam(":id", $Idusuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // convertir foto BLOB a base64
            foreach ($comentarios as &$c) {
                if (!empty($c['FotoUsuario'])) {
                    $c['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($c['FotoUsuario']);
                }
            }

            file_put_contents("debug_comentariosPublicacionDeUsuario.txt",
                print_r($comentarios, true)
            );

            echo json_encode($comentarios);
            exit;
        }


        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE INTERACCIONES ---------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////

        // =============================================================================================
        // ----------------------------- CREAR LIKE ----------------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'toggleLike') {
            file_put_contents("debug_interaccion.txt", "=== TOGGLE LIKE ===\n", FILE_APPEND);
            file_put_contents("debug_interaccion.txt", print_r($_POST, true), FILE_APPEND);
            file_put_contents("debug_interaccion.txt", "Authorization: " . ($_SERVER['HTTP_AUTHORIZATION'] ?? 'NO TOKEN') . "\n", FILE_APPEND);

            $usuarioData = TokenSimple::verificar();
            file_put_contents("debug_interaccion.txt", print_r($usuarioData, true), FILE_APPEND);

            try {
                $idUsuario = $usuarioData['Idusuario'] ?? null;
                $idPublicacion = $_POST['Idpublicacion'] ?? null;

                if (!$idUsuario || !$idPublicacion) {
                    http_response_code(400);
                    echo json_encode(["message" => "Datos incompletos"]);
                    exit;
                }

                $stmt = $db->prepare("CALL ToggleLike(:p_Idpublicacion, :p_Idusuario)");
                $stmt->bindParam(':p_Idpublicacion', $idPublicacion, PDO::PARAM_INT);
                $stmt->bindParam(':p_Idusuario', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();

                // Retornar el total actualizado
                $countStmt = $db->prepare("
                    CALL CuentaLikesActualizada(:id)
                ");
                $countStmt->bindParam(':id', $idPublicacion, PDO::PARAM_INT);
                $countStmt->execute();
                $total = $countStmt->fetch(PDO::FETCH_ASSOC)['totalLikes'];

                echo json_encode(["message" => "Like actualizado", "totalLikes" => $total]);
            } catch (PDOException $e) {
                file_put_contents("debug_interaccionL.txt", "PDO ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                http_response_code(500);
                echo json_encode(["message" => "Error al actualizar like", "error" => $e->getMessage()]);
            }
        }


        // =============================================================================================
        // ----------------------------- REGISTRAR VISTA -----------------------------------------------
        // =============================================================================================
        if ($_POST['accion'] === 'registrarVista') {
            file_put_contents("debug_interaccion.txt", "=== REGISTRAR VISTA ===\n", FILE_APPEND);
            file_put_contents("debug_interaccion.txt", print_r($_POST, true), FILE_APPEND);
            file_put_contents("debug_interaccion.txt", "Authorization: " . ($_SERVER['HTTP_AUTHORIZATION'] ?? 'NO TOKEN') . "\n", FILE_APPEND);

            $usuarioData = TokenSimple::verificar();
            file_put_contents("debug_interaccion.txt", print_r($usuarioData, true), FILE_APPEND);

            try {
                $idUsuario = $usuarioData['Idusuario'] ?? null;
                $idPublicacion = $_POST['Idpublicacion'] ?? null;

                if (!$idUsuario || !$idPublicacion) {
                    http_response_code(400);
                    echo json_encode(["message" => "Datos incompletos"]);
                    exit;
                }

                $stmt = $db->prepare("CALL RegistrarVista(:p_Idpublicacion, :p_Idusuario)");
                $stmt->bindParam(':p_Idpublicacion', $idPublicacion, PDO::PARAM_INT);
                $stmt->bindParam(':p_Idusuario', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();

                // Contar vistas totales
                $countStmt = $db->prepare("
                    CALL CuentaVistasActualizada(:id)
                ");
                $countStmt->bindParam(':id', $idPublicacion, PDO::PARAM_INT);
                $countStmt->execute();
                $total = $countStmt->fetch(PDO::FETCH_ASSOC)['totalVistas'];

                echo json_encode(["message" => "Vista registrada", "totalVistas" => $total]);
            } catch (PDOException $e) {
                file_put_contents("debug_interaccionV.txt", "PDO ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                http_response_code(500);
                echo json_encode(["message" => "Error al registrar vista", "error" => $e->getMessage()]);
            }
        }
            
        if ($_POST['accion'] === 'traerEstadisticas') {
            file_put_contents("debug_stats.txt", print_r(getallheaders(), true));

            // =============================
            // LEER TOKEN DESDE EL HEADER
            // =============================
            $headers = getallheaders();
            $auth = $headers["Authorization"] ?? null;
       

            if (!$auth || !str_starts_with($auth, "Bearer ")) {
                http_response_code(401);
                echo json_encode(["message" => "Token no enviado"]);
                exit;
            }

            try {
                $usuarioData = TokenSimple::verificar();  // <-- SIN PARÁMETROS
            } catch (Exception $e) {
                file_put_contents("debug_token_error.txt", $e->getMessage());
                http_response_code(401);
                echo json_encode(["message" => "Token inválido"]);
                exit;
            }
            $idUsuario = $usuarioData['Idusuario'];

            try {
                $stmt = $db->prepare("CALL TraerEstadisticasUsuario(:u)");
                $stmt->bindParam(':u', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();

                $stats = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode($stats, JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error al obtener estadísticas",
                    "error" => $e->getMessage()
                ]);
            }
        }
        
        ///-----------usuario e intarccion-----------------
        if ($_POST['accion'] === 'listarUsuariosInteracciones') {
     
            $headers = getallheaders();
            $auth = $headers["Authorization"] ?? null;

            $usuario = TokenSimple::verificar();
            if (!$usuario) {
                echo json_encode(["message" => "Token inválido o expirado"]);
                exit;
            }

            $idUsuarioSesion = $usuario['Idusuario'];

            try {
                $query = $db->prepare("CALL TraerUsuariosQueInteractuaron(:p_Idusuario)");
                $query->bindParam(":p_Idusuario", $idUsuarioSesion, PDO::PARAM_INT);
                $query->execute();

                $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($usuarios);
            } catch (Exception $e) {
                echo json_encode(["error" => $e->getMessage()]);
            }
        }

        break;

    
    case 'GET':
        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE MUNDIALES -------------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////
        
        // =============================================================================================
        // ----------------------------- TRAER TODOS LOS MUNDIALES Y CATEGORÍAS ------------------------
        // =============================================================================================
        if (isset($_GET['accion']) && $_GET['accion'] === 'listarMundiales') {
            try {
                // Traer MUNDIALES
                $stmt = $db->prepare("CALL TraerMundiales()");
                $stmt->execute();
                $mundiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                // Convertir imágenes a base64
                foreach ($mundiales as &$m) {
                    $m['Foto'] = $m['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($m['Foto']) : null;
                    $m['Logo'] = $m['Logo'] ? 'data:image/jpeg;base64,' . base64_encode($m['Logo']) : null;
                }

                // Traer CATEGORÍAS
                $stmt2 = $db->prepare("CALL TraerCategorias()");
                $stmt2->execute();
                $categorias = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                $stmt2->closeCursor();

                // Devolver todo junto en un solo JSON
                echo json_encode([
                    "mundiales" => $mundiales,
                    "categorias" => $categorias
                ], JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error en la base de datos",
                    "error" => $e->getMessage()
                ]);
            }
        }

        /////////////////////////////////////////////////////////////////////
        // =============================================================== //
        // --------------------- GESTION DE PUBLICACIONES ---------------- //
        // =============================================================== //
        // //////////////////////////////////////////////////////////////////

        // =============================================================================================
        // ----------------------------- OBTENER PUBLICACIONES -----------------------------------------
        // =============================================================================================
        if (isset($_GET['accion']) && $_GET['accion'] === 'listarPublicaciones') {
            try {
                $query = "CALL TraerPublicaciones(:p_Activo)";

                $stmt = $db->prepare($query);
                $activo=1;
                $stmt->bindParam(':p_Activo', $activo, PDO::PARAM_INT);
                $stmt->execute();
                $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                
                // Convertir imagen blob o ruta si aplica
                foreach ($publicaciones as &$p) {
                    if (!empty($p['Archivo'])) {
                        //$p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                        $p['Archivo'] = $p['Archivo'] ? 'data:image/jpeg;base64,' . base64_encode($p['Archivo']) : null;
                    }
                    if (!empty($p['FotoUsuario'])) {
                        //$p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                        $p['FotoUsuario'] = $p['FotoUsuario'] ? 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']) : null;
                    }
                }

                //file_put_contents("debug_api_publicaciones.txt", print_r($publicaciones, true));
                //file_put_contents("debug_api_flow.txt", "Entró a listarPublicaciones y va a imprimir JSON\n", FILE_APPEND);
                echo json_encode($publicaciones, JSON_UNESCAPED_UNICODE);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Error en la BD",
                    "error" => $e->getMessage()
                ]);
            }
        }

        // =============================================================================================
        // ----------------------------- BUSQUEDA DE PUBLICACIONES -------------------------------------
        // =============================================================================================
        if ($_GET['accion'] === 'buscarPublicaciones') {

            $busqueda = $_GET['texto'] ?? '';

            // Si está vacío, devolvemos todas las publicaciones
            if (trim($busqueda) === '') {
                $stmt = $db->query("CALL TraerPublicaciones()");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                exit;
            }

            try {
                $stmt = $db->prepare("CALL BusquedaPublicaciones(:p)");
                $stmt->bindParam(":p", $busqueda);
                $stmt->execute();

                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                

                // Convierte blobs a base64
                foreach ($resultados as &$p) {
                    if (!empty($p['Archivo'])) {
                        $p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);
                    }

                    if (!empty($p['FotoUsuario'])) {
                        $p['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']);
                    }
                }
                
                echo json_encode($resultados);
                file_put_contents("debug_api_resultadoBusqueda.txt", print_r($resultados, true));

            } catch (Exception $ex) {
                echo json_encode([
                    "estado" => "error",
                    "mensaje" => "Error ejecutando búsqueda",
                    "detalle" => $ex->getMessage()
                ]);
            }

            exit;
        }

        if ($_GET['accion'] === 'filtrarPublicaciones') {

            $buscar = $_GET['buscar'] ?? '';
            $sede   = $_GET['sede'] ?? '';
            $orden  = $_GET['orden'] ?? '';

            $stmt = $db->prepare("CALL BusquedaPublicaciones(:p, :s, :o)");
            $stmt->bindValue(':p', $buscar ?: null);
            $stmt->bindValue(':s', $sede   ?: null);
            $stmt->bindValue(':o', $orden  ?: null);
            $stmt->execute();


            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convertir blobs
            foreach ($resultados as &$p) {
                if (!empty($p['Archivo']))
                    $p['Archivo'] = 'data:image/jpeg;base64,' . base64_encode($p['Archivo']);

                if (!empty($p['FotoUsuario']))
                    $p['FotoUsuario'] = 'data:image/jpeg;base64,' . base64_encode($p['FotoUsuario']);
            }

            echo json_encode($resultados);
            exit;
        }
        break;

    case 'PUT': 

        break;

    case 'DELETE':
        
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}
