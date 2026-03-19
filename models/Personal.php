<?php
require_once __DIR__ . '/../config/Database.php';

class Personal {
    private $conn;
    private $table_name = "personal";

    public $id;
    public $nombre;
    public $apellidos;
    public $telefono;
    public $correo;
    public $usuario_id;
    public $departamento_id;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT p.*, u.usuario as nombre_usuario, d.nombre as nombre_departamento 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN usuarios u ON p.usuario_id = u.id 
                  LEFT JOIN departamentos d ON p.departamento_id = d.id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre=:nombre, apellidos=:apellidos, telefono=:telefono, correo=:correo, usuario_id=:usuario_id, departamento_id=:departamento_id";
        
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        
        // Handle optional usuario_id
        if(empty($this->usuario_id)){
            $this->usuario_id = null;
        } else {
            $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        }

        // Handle optional departamento_id
        if(empty($this->departamento_id)){
            $this->departamento_id = null;
        } else {
            $this->departamento_id = htmlspecialchars(strip_tags($this->departamento_id));
        }

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellidos", $this->apellidos);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":departamento_id", $this->departamento_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre=:nombre, apellidos=:apellidos, telefono=:telefono, correo=:correo, usuario_id=:usuario_id, departamento_id=:departamento_id 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        if(empty($this->usuario_id)){
            $this->usuario_id = null;
        } else {
            $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        }

        if(empty($this->departamento_id)){
            $this->departamento_id = null;
        } else {
            $this->departamento_id = htmlspecialchars(strip_tags($this->departamento_id));
        }

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellidos", $this->apellidos);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":departamento_id", $this->departamento_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>
