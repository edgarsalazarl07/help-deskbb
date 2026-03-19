<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $apellido_paterno;
    public $apellido_materno;
    public $nombre;
    public $fecha_nacimiento;
    public $sexo;
    public $telefono;
    public $correo;
    public $usuario;
    public $password_sha1;
    public $rol;
    public $ubicacion;
    public $activo;
    public $fecha_creacion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login() {
        $query = "SELECT id, usuario, rol, nombre, apellido_paterno FROM " . $this->table_name . " WHERE usuario = :usuario AND password_sha1 = :password_sha1 LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $this->usuario = htmlspecialchars(strip_tags($this->usuario));
        $this->password_sha1 = htmlspecialchars(strip_tags($this->password_sha1));

        $stmt->bindParam(':usuario', $this->usuario);
        $stmt->bindParam(':password_sha1', $this->password_sha1);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->rol = $row['rol'];
            $this->nombre = $row['nombre'];
            $this->apellido_paterno = $row['apellido_paterno'];
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT id, apellido_paterno, apellido_materno, nombre, fecha_nacimiento, sexo, telefono, correo, usuario, rol, ubicacion, activo, fecha_creacion FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET 
                  apellido_paterno=:apellido_paterno, 
                  apellido_materno=:apellido_materno, 
                  nombre=:nombre, 
                  fecha_nacimiento=:fecha_nacimiento, 
                  sexo=:sexo, 
                  telefono=:telefono, 
                  correo=:correo, 
                  usuario=:usuario, 
                  password_sha1=:password_sha1, 
                  rol=:rol, 
                  ubicacion=:ubicacion,
                  activo=:activo";
        
        $stmt = $this->conn->prepare($query);

        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->usuario = htmlspecialchars(strip_tags($this->usuario));
        $this->password_sha1 = htmlspecialchars(strip_tags($this->password_sha1));
        $this->rol = htmlspecialchars(strip_tags($this->rol));
        $this->ubicacion = htmlspecialchars(strip_tags($this->ubicacion));

        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":usuario", $this->usuario);
        $stmt->bindParam(":password_sha1", $this->password_sha1);
        $stmt->bindParam(":rol", $this->rol);
        $stmt->bindParam(":ubicacion", $this->ubicacion);
        $this->activo = $this->activo ?? 1;
        $stmt->bindParam(":activo", $this->activo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update() {
        $password_clause = !empty($this->password_sha1) ? ", password_sha1=:password_sha1" : "";
        $query = "UPDATE " . $this->table_name . " SET 
                  apellido_paterno=:apellido_paterno, 
                  apellido_materno=:apellido_materno, 
                  nombre=:nombre, 
                  fecha_nacimiento=:fecha_nacimiento, 
                  sexo=:sexo, 
                  telefono=:telefono, 
                  correo=:correo, 
                  usuario=:usuario, 
                  rol=:rol, 
                  ubicacion=:ubicacion,
                  activo=:activo" . $password_clause . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->usuario = htmlspecialchars(strip_tags($this->usuario));
        $this->rol = htmlspecialchars(strip_tags($this->rol));
        $this->ubicacion = htmlspecialchars(strip_tags($this->ubicacion));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":usuario", $this->usuario);
        $stmt->bindParam(":rol", $this->rol);
        $stmt->bindParam(":ubicacion", $this->ubicacion);
        $stmt->bindParam(":activo", $this->activo);
        $stmt->bindParam(":id", $this->id);
        
        if(!empty($this->password_sha1)){
            $this->password_sha1 = htmlspecialchars(strip_tags($this->password_sha1));
            $stmt->bindParam(":password_sha1", $this->password_sha1);
        }

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function toggleStatus() {
        $query = "UPDATE " . $this->table_name . " SET activo = (1 - activo) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND usuario != 'admin'"; // Prevent deleting main admin
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
