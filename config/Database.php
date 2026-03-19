<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = ""; // Assuming standard XAMPP or local setup without password initially
    private $database = "helpdesk_mvc_db";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->user, $this->password);
            $this->conn->exec("set names utf8");
            // Set error mode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
