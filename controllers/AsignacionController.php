<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Asignacion.php';

class AsignacionController {
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_asignacion') {
            $asignacionModel = new Asignacion();
            $asignacionModel->equipo_id = $_POST['equipo_id'];
            $asignacionModel->personal_id = $_POST['personal_id'];

            if($asignacionModel->create()){
                $_SESSION['success_msg'] = "Equipo asignado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al asignar el equipo.";
            }
            header("Location: ../index.php?view=admin_asignaciones");
            exit;
        }
    }

    public function devolver() {
        if (isset($_GET['id'])) {
            $asignacionModel = new Asignacion();
            $asignacionModel->id = $_GET['id'];

            if($asignacionModel->devolverEquipo()){
                $_SESSION['success_msg'] = "Equipo marcado como devuelto correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al devolver equipo.";
            }
            header("Location: ../index.php?view=admin_asignaciones");
            exit;
        }
    }
}

// Router
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];
    $controller = new AsignacionController();
    
    if ($action == 'create_asignacion') {
        $controller->create();
    } elseif ($action == 'devolver_equipo') {
        $controller->devolver();
    }
}
?>
