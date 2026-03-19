<?php
require_once __DIR__ . '/../config/Database.php';

class Ticket {
    private $conn;
    private $table_name = "tickets";

    public $id;
    public $asignacion_id;
    public $titulo;
    public $descripcion;
    public $estado; // abierto, en_proceso, cerrado
    public $departamento_id;
    public $fecha_creacion;
    public $fecha_cierre;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Read all for Admin view
    public function readAll() {
        $query = "SELECT t.*, 
                         e.marca, e.modelo, e.numero_serie, 
                         p.nombre as personal_nombre, p.apellidos as personal_apellidos,
                         d.nombre as nombre_departamento
                  FROM " . $this->table_name . " t 
                  INNER JOIN asignaciones a ON t.asignacion_id = a.id 
                  INNER JOIN equipos e ON a.equipo_id = e.id 
                  INNER JOIN personal p ON a.personal_id = p.id 
                  LEFT JOIN departamentos d ON t.departamento_id = d.id
                  ORDER BY CASE WHEN t.estado = 'abierto' THEN 1 WHEN t.estado = 'en_proceso' THEN 2 ELSE 3 END, t.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read specific user's tickets (for Client view)
    public function readByUsuario($usuario_id) {
        $query = "SELECT t.*, 
                         e.marca, e.modelo, e.numero_serie 
                  FROM " . $this->table_name . " t 
                  INNER JOIN asignaciones a ON t.asignacion_id = a.id 
                  INNER JOIN equipos e ON a.equipo_id = e.id 
                  INNER JOIN personal p ON a.personal_id = p.id 
                  WHERE p.usuario_id = :usuario_id 
                  ORDER BY t.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt;
    }

    public function readById($id) {
        $query = "SELECT t.*, e.marca, e.modelo, e.numero_serie, 
                         p.nombre as personal_nombre, p.apellidos as personal_apellidos,
                         d.nombre as nombre_departamento
                  FROM " . $this->table_name . " t 
                  INNER JOIN asignaciones a ON t.asignacion_id = a.id 
                  INNER JOIN equipos e ON a.equipo_id = e.id 
                  INNER JOIN personal p ON a.personal_id = p.id 
                  LEFT JOIN departamentos d ON t.departamento_id = d.id
                  WHERE t.id = :id 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET asignacion_id=:asignacion_id, titulo=:titulo, descripcion=:descripcion, 
                      departamento_id=:departamento_id, estado='abierto'";
        
        $stmt = $this->conn->prepare($query);

        $this->asignacion_id = htmlspecialchars(strip_tags($this->asignacion_id));
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->departamento_id = !empty($this->departamento_id) ? htmlspecialchars(strip_tags($this->departamento_id)) : null;

        $stmt->bindParam(":asignacion_id", $this->asignacion_id);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":departamento_id", $this->departamento_id);

        if($stmt->execute()){
            // Put equipment in 'reparacion' state automatically when ticket is opened
            $this->actualizarEstadoEquipo($this->asignacion_id, 'en_reparacion');
            return true;
        }
        return false;
    }

    public function updateEstado() {
        $fecha_cierre_part = ($this->estado == 'cerrado') ? ", fecha_cierre=CURRENT_TIMESTAMP" : ", fecha_cierre=NULL";
        $query = "UPDATE " . $this->table_name . " 
                  SET estado=:estado " . $fecha_cierre_part . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            if($this->estado == 'cerrado') {
                $this->actualizarEstadoEquipoFromTicket($this->id, 'asignado');
            }
            return true;
        }
        return false;
    }

    // Logic for Replies (Thread)
    public function getRespuestas($ticket_id) {
        $query = "SELECT r.*, u.usuario, u.rol 
                  FROM ticket_respuestas r 
                  INNER JOIN usuarios u ON r.usuario_id = u.id 
                  WHERE r.ticket_id = :ticket_id 
                  ORDER BY r.fecha_registro ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();
        return $stmt;
    }

    public function addRespuesta($ticket_id, $usuario_id, $mensaje) {
        $query = "INSERT INTO ticket_respuestas SET ticket_id=:ticket_id, usuario_id=:usuario_id, mensaje=:mensaje";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":mensaje", $mensaje);
        return $stmt->execute();
    }

    // Helper to update equipment when creating ticket
    private function actualizarEstadoEquipo($asignacion_id, $nuevo_estado) {
        $query_a = "SELECT equipo_id FROM asignaciones WHERE id = :asignacion_id LIMIT 1";
        $stmt_a = $this->conn->prepare($query_a);
        $stmt_a->bindParam(":asignacion_id", $asignacion_id);
        $stmt_a->execute();
        if($row = $stmt_a->fetch(PDO::FETCH_ASSOC)) {
            $query_e = "UPDATE equipos SET estado = :estado WHERE id = :id";
            $stmt_e = $this->conn->prepare($query_e);
            $stmt_e->bindParam(":estado", $nuevo_estado);
            $stmt_e->bindParam(":id", $row['equipo_id']);
            $stmt_e->execute();
        }
    }

    // Helper to update equipment when closing ticket
    private function actualizarEstadoEquipoFromTicket($ticket_id, $nuevo_estado) {
        $query = "SELECT a.equipo_id FROM tickets t INNER JOIN asignaciones a ON t.asignacion_id = a.id WHERE t.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $ticket_id);
        $stmt->execute();
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $query_e = "UPDATE equipos SET estado = :estado WHERE id = :id";
            $stmt_e = $this->conn->prepare($query_e);
            $stmt_e->bindParam(":estado", $nuevo_estado);
            $stmt_e->bindParam(":id", $row['equipo_id']);
            $stmt_e->execute();
        }
    }
}
?>
