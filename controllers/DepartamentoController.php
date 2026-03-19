<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Departamento.php';

class DepartamentoController {
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_departamento') {
            $depto = new Departamento();
            $depto->nombre = $_POST['nombre'] ?? '';

            if($depto->create()){
                $_SESSION['success_msg'] = "Departamento creado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al crear el departamento.";
            }
            header("Location: ../index.php?view=admin_departamentos");
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_departamento') {
            $depto = new Departamento();
            $depto->id = $_POST['id'];
            $depto->nombre = $_POST['nombre'] ?? '';

            if($depto->update()){
                $_SESSION['success_msg'] = "Departamento actualizado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al actualizar el departamento.";
            }
            header("Location: ../index.php?view=admin_departamentos");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $depto = new Departamento();
            $depto->id = $_GET['id'];

            if($depto->delete()){
                $_SESSION['success_msg'] = "Departamento eliminado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al eliminar. Verifique que no haya personal o tickets asociados.";
            }
            header("Location: ../index.php?view=admin_departamentos");
            exit;
        }
    }
}

// Router
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];
    $controller = new DepartamentoController();
    
    if ($action == 'create_departamento') {
        $controller->create();
    } elseif ($action == 'update_departamento') {
        $controller->update();
    } elseif ($action == 'delete_departamento') {
        $controller->delete();
    }
}
?>
