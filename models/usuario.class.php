<?php
class Usuario {
    private $conn;
    private $table = "usuario";

    public $nombre;
    public $apellidos;
    public $fechanatal;
    public $alias;
    public $email;
    public $pass;
    public $foto;
    public $genero;
    public $paisnatal;
    public $nacionalidad;
    public $tipo;
    public $activo;
    public $tocken;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar() {
        $query = "CALL InsertarUsuario(:nombre, :apellidos, :fechanatal, :alias, :email, :pass, :foto, :genero, :paisnatal, :nacionalidad, :tipo)";
        $stmt = $this->conn->prepare($query);

        // Encriptar contraseña
        $this->pass = password_hash($this->pass, PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellidos", $this->apellidos);
        $stmt->bindParam(":fechanatal", $this->fechanatal);
        $stmt->bindParam(":alias", $this->alias);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":pass", $this->pass);
        $stmt->bindParam(":foto", $this->foto, PDO::PARAM_LOB);
        $stmt->bindParam(":genero", $this->genero, PDO::PARAM_INT);
        $stmt->bindParam(":paisnatal", $this->paisnatal);
        $stmt->bindParam(":nacionalidad", $this->nacionalidad);
        $stmt->bindParam(":tipo", $this->tipo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function iniciarSesion() {
        $query = "CALL IniciarSesion(:p_Alias)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":p_Alias", $this->alias);
    
        $stmt->execute();
    
        // Obtener los datos
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un array con id, alias, pass
    }

}
