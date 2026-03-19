<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
            $usuarioModel = new Usuario();
            $usuarioModel->usuario = $_POST['usuario'] ?? '';
            $usuarioModel->password_sha1 = sha1($_POST['password'] ?? ''); // User requested SHA1 explicitly

            header('Content-Type: application/json');
            if ($usuarioModel->login()) {
                $_SESSION['usuario_id'] = $usuarioModel->id;
                $_SESSION['usuario'] = $usuarioModel->usuario;
                $_SESSION['rol'] = $usuarioModel->rol;

                $redirect = ($usuarioModel->rol === 'admin') ? "index.php?view=admin_dashboard" : "index.php?view=cliente_dashboard";
                
                echo json_encode([
                    "status" => "success",
                    "message" => "¡Bienvenido, " . $usuarioModel->usuario . "!",
                    "redirect" => $redirect
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Usuario o contraseña incorrectos."
                ]);
            }
            exit;
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_user') {
            $usuarioModel = new Usuario();
            $usuarioModel->apellido_paterno = $_POST['apellido_paterno'] ?? '';
            $usuarioModel->apellido_materno = $_POST['apellido_materno'] ?? '';
            $usuarioModel->nombre = $_POST['nombre'] ?? '';
            $usuarioModel->fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
            $usuarioModel->sexo = $_POST['sexo'] ?? '';
            $usuarioModel->telefono = $_POST['telefono'] ?? '';
            $usuarioModel->correo = $_POST['correo'] ?? '';
            $usuarioModel->usuario = $_POST['usuario'] ?? '';
            $usuarioModel->password_sha1 = sha1($_POST['password'] ?? '');
            $usuarioModel->rol = $_POST['rol'] ?? 'cliente';
            $usuarioModel->ubicacion = $_POST['ubicacion'] ?? '';

            header('Content-Type: application/json');
            if($usuarioModel->create()){
                echo json_encode(["status" => "success", "message" => "Usuario creado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al crear el usuario. Posible duplicado."]);
            }
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_user') {
            $usuarioModel = new Usuario();
            $usuarioModel->id = $_POST['id'];
            $usuarioModel->apellido_paterno = $_POST['apellido_paterno'] ?? '';
            $usuarioModel->apellido_materno = $_POST['apellido_materno'] ?? '';
            $usuarioModel->nombre = $_POST['nombre'] ?? '';
            $usuarioModel->fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
            $usuarioModel->sexo = $_POST['sexo'] ?? '';
            $usuarioModel->telefono = $_POST['telefono'] ?? '';
            $usuarioModel->correo = $_POST['correo'] ?? '';
            $usuarioModel->usuario = $_POST['usuario'] ?? '';
            $usuarioModel->rol = $_POST['rol'] ?? 'cliente';
            $usuarioModel->ubicacion = $_POST['ubicacion'] ?? '';

            if(!empty($_POST['password'])) {
                $usuarioModel->password_sha1 = sha1($_POST['password']);
            }

            header('Content-Type: application/json');
            if($usuarioModel->update()){
                echo json_encode(["status" => "success", "message" => "Usuario actualizado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar el usuario."]);
            }
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->id = $_GET['id'];

            if($usuarioModel->delete()){
                $_SESSION['success_msg'] = "Usuario eliminado correctamente.";
            } else {
                $_SESSION['error_msg'] = "Error al eliminar. No se puede eliminar el superadministrador.";
            }
            header("Location: ../index.php?view=admin_usuarios");
            exit;
        }
    }

    public function toggleStatus() {
        if (isset($_REQUEST['id'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->id = $_REQUEST['id'];

            header('Content-Type: application/json');
            if($usuarioModel->toggleStatus()){
                echo json_encode(["status" => "success", "message" => "Estado de usuario actualizado."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al cambiar el estado."]);
            }
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../index.php?view=login");
        exit;
    }
}

// Simple router for controller actions
$action = $_REQUEST['action'] ?? null;
if ($action) {
    $controller = new UsuarioController();
    switch ($action) {
        case 'login':
            $controller->procesarLogin();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'create_user':
            $controller->create();
            break;
        case 'update_user':
            $controller->update();
            break;
        case 'delete_user':
            $controller->delete();
            break;
        case 'toggle_status':
            $controller->toggleStatus();
            break;
    }
}
?>
