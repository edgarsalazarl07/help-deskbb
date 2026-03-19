<?php
require_once __DIR__ . '/../config/Database.php';

class Equipo {
    private $conn;
    private $table_name = "equipos";

    public $id;
    public $marca;
    public $modelo;
    public $numero_serie;
    public $categoria_id;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT e.*, c.nombre as categoria_nombre 
                  FROM " . $this->table_name . " e 
                  LEFT JOIN categorias_equipo c ON e.categoria_id = c.id 
                  ORDER BY e.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readCategorias() {
        $query = "SELECT id, nombre FROM categorias_equipo ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET marca=:marca, modelo=:modelo, numero_serie=:numero_serie, categoria_id=:categoria_id, estado=:estado";
        
        $stmt = $this->conn->prepare($query);

        $this->marca = htmlspecialchars(strip_tags($this->marca));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->numero_serie = htmlspecialchars(strip_tags($this->numero_serie));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        $stmt->bindParam(":marca", $this->marca);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":numero_serie", $this->numero_serie);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":estado", $this->estado);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET marca=:marca, modelo=:modelo, numero_serie=:numero_serie, categoria_id=:categoria_id, estado=:estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->marca = htmlspecialchars(strip_tags($this->marca));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->numero_serie = htmlspecialchars(strip_tags($this->numero_serie));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":marca", $this->marca);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":numero_serie", $this->numero_serie);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":estado", $this->estado);
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
