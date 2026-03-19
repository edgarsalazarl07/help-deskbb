<?php
require_once __DIR__ . '/../config/Database.php';

class Asignacion {
    private $conn;
    private $table_name = "asignaciones";

    public $id;
    public $equipo_id;
    public $personal_id;
    public $fecha_asignacion;
    public $fecha_devolucion;
    public $estado_asignacion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT a.*, 
                         e.marca, e.modelo, e.numero_serie, 
                         p.nombre, p.apellidos 
                  FROM " . $this->table_name . " a 
                  INNER JOIN equipos e ON a.equipo_id = e.id 
                  INNER JOIN personal p ON a.personal_id = p.id 
                  ORDER BY a.estado_asignacion ASC, a.fecha_asignacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET equipo_id=:equipo_id, personal_id=:personal_id, fecha_asignacion=:fecha_asignacion, estado_asignacion='activa'";
        
        $stmt = $this->conn->prepare($query);

        $this->equipo_id = htmlspecialchars(strip_tags($this->equipo_id));
        $this->personal_id = htmlspecialchars(strip_tags($this->personal_id));
        $this->fecha_asignacion = date('Y-m-d'); // Today's date

        $stmt->bindParam(":equipo_id", $this->equipo_id);
        $stmt->bindParam(":personal_id", $this->personal_id);
        $stmt->bindParam(":fecha_asignacion", $this->fecha_asignacion);

        if($stmt->execute()){
            // Update equipo status
            $this->actualizarEstadoEquipo($this->equipo_id, 'asignado');
            return true;
        }
        return false;
    }

    public function devolverEquipo() {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado_asignacion='devuelto', fecha_devolucion=:fecha_devolucion 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->fecha_devolucion = date('Y-m-d');

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":fecha_devolucion", $this->fecha_devolucion);

        if($stmt->execute()){
            // We need the equipo_id to update its status
            $query_eq = "SELECT equipo_id FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt_eq = $this->conn->prepare($query_eq);
            $stmt_eq->bindParam(":id", $this->id);
            $stmt_eq->execute();
            if($row = $stmt_eq->fetch(PDO::FETCH_ASSOC)) {
                $this->actualizarEstadoEquipo($row['equipo_id'], 'disponible');
            }
            return true;
        }
        return false;
    }
    
    // Helper to update equipment status when assigning/returning
    private function actualizarEstadoEquipo($equipo_id, $nuevo_estado) {
        $query = "UPDATE equipos SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":id", $equipo_id);
        $stmt->execute();
    }
}
?>
