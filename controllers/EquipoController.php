<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Equipo.php';

class EquipoController {
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_equipo') {
            $equipoModel = new Equipo();
            $equipoModel->marca = $_POST['marca'] ?? '';
            $equipoModel->modelo = $_POST['modelo'] ?? '';
            $equipoModel->numero_serie = $_POST['numero_serie'] ?? '';
            $equipoModel->categoria_id = $_POST['categoria_id'] ?? null;
            $equipoModel->estado = $_POST['estado'] ?? 'disponible';

            if($equipoModel->create()){
                $_SESSION['success_msg'] = "Equipo registrado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al registrar el equipo. Posible número de serie duplicado.";
            }
            header("Location: ../index.php?view=admin_equipos");
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_equipo') {
            $equipoModel = new Equipo();
            $equipoModel->id = $_POST['id'];
            $equipoModel->marca = $_POST['marca'] ?? '';
            $equipoModel->modelo = $_POST['modelo'] ?? '';
            $equipoModel->numero_serie = $_POST['numero_serie'] ?? '';
            $equipoModel->categoria_id = $_POST['categoria_id'] ?? null;
            $equipoModel->estado = $_POST['estado'] ?? 'disponible';

            if($equipoModel->update()){
                $_SESSION['success_msg'] = "Equipo actualizado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al actualizar el equipo.";
            }
            header("Location: ../index.php?view=admin_equipos");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $equipoModel = new Equipo();
            $equipoModel->id = $_GET['id'];

            if($equipoModel->delete()){
                $_SESSION['success_msg'] = "Equipo eliminado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al eliminar. Posiblemente esté asignado actualmente.";
            }
            header("Location: ../index.php?view=admin_equipos");
            exit;
        }
    }
}

// Router
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];
    $controller = new EquipoController();
    
    if ($action == 'create_equipo') {
        $controller->create();
    } elseif ($action == 'update_equipo') {
        $controller->update();
    } elseif ($action == 'delete_equipo') {
        $controller->delete();
    }
}
?>
