<?php
    class Database{
        private $host = 'localhost';
        private $user = 'root';
        private $password = '';
        private $database = 'pci_bdm';
        public $conn;

        public function connect(){
            $this->conn = null;
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->database,
                    $this->user,
                    $this->password
                );

                $this->conn->exec("set names utf8");
                
            } catch (PDOException $e){
                echo "Error de conexion: " . $e->getMessage();
            }
            
            return $this->conn;

        }
    }