<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Personal.php';

class PersonalController {
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_personal') {
            $personalModel = new Personal();
            $personalModel->nombre = $_POST['nombre'] ?? '';
            $personalModel->apellidos = $_POST['apellidos'] ?? '';
            $personalModel->telefono = $_POST['telefono'] ?? '';
            $personalModel->correo = $_POST['correo'] ?? '';
            $personalModel->usuario_id = !empty($_POST['usuario_id']) ? $_POST['usuario_id'] : null;
            $personalModel->departamento_id = !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null;

            if($personalModel->create()){
                $_SESSION['success_msg'] = "Personal registrado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al registrar el personal.";
            }
            header("Location: ../index.php?view=admin_personal");
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_personal') {
            $personalModel = new Personal();
            $personalModel->id = $_POST['id'];
            $personalModel->nombre = $_POST['nombre'] ?? '';
            $personalModel->apellidos = $_POST['apellidos'] ?? '';
            $personalModel->telefono = $_POST['telefono'] ?? '';
            $personalModel->correo = $_POST['correo'] ?? '';
            $personalModel->usuario_id = !empty($_POST['usuario_id']) ? $_POST['usuario_id'] : null;
            $personalModel->departamento_id = !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null;

            if($personalModel->update()){
                $_SESSION['success_msg'] = "Personal actualizado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al actualizar el personal.";
            }
            header("Location: ../index.php?view=admin_personal");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $personalModel = new Personal();
            $personalModel->id = $_GET['id'];

            if($personalModel->delete()){
                $_SESSION['success_msg'] = "Personal eliminado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al eliminar. Posiblemente existan equipos asignados a esta persona.";
            }
            header("Location: ../index.php?view=admin_personal");
            exit;
        }
    }
}

// Router
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];
    $controller = new PersonalController();
    
    if ($action == 'create_personal') {
        $controller->create();
    } elseif ($action == 'update_personal') {
        $controller->update();
    } elseif ($action == 'delete_personal') {
        $controller->delete();
    }
}
?>
