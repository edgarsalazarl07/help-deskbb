<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Ticket.php';

class TicketController {
    
    // Client or Admin opens a ticket
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_ticket') {
            $ticketModel = new Ticket();
            $ticketModel->asignacion_id = $_POST['asignacion_id'];
            $ticketModel->titulo = $_POST['titulo'];
            $ticketModel->descripcion = $_POST['descripcion'];
            $ticketModel->departamento_id = !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null;

            if($ticketModel->create()){
                $_SESSION['success_msg'] = "Ticket reportado correctamente. Nuestro equipo lo revisará a la brevedad.";
            } else {
                $_SESSION['error_msg'] = "Error al crear el ticket.";
            }
            
            if($_SESSION['rol'] == 'admin') {
                header("Location: ../index.php?view=admin_tickets");
            } else {
                header("Location: ../index.php?view=cliente_reportes");
            }
            exit;
        }
    }

    // Update ticket status (Admin)
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
            $ticketModel = new Ticket();
            $ticketModel->id = $_POST['ticket_id'];
            $ticketModel->estado = $_POST['estado'];

            if($ticketModel->updateEstado()){
                $_SESSION['success_msg'] = "Estado del ticket actualizado.";
            } else {
                $_SESSION['error_msg'] = "Error al actualizar el estado.";
            }
            header("Location: ../index.php?view=admin_ticket_detalle&id=" . $ticketModel->id);
            exit;
        }
    }

    // Add a reply to the thread
    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_reply') {
            $ticketModel = new Ticket();
            $ticket_id = $_POST['ticket_id'];
            $usuario_id = $_SESSION['usuario_id'];
            $mensaje = $_POST['mensaje'];

            if($ticketModel->addRespuesta($ticket_id, $usuario_id, $mensaje)){
                $_SESSION['success_msg'] = "Mensaje enviado.";
            } else {
                $_SESSION['error_msg'] = "Error al enviar el mensaje.";
            }
            
            if($_SESSION['rol'] == 'admin') {
                header("Location: ../index.php?view=admin_ticket_detalle&id=" . $ticket_id);
            } else {
                header("Location: ../index.php?view=cliente_ticket_detalle&id=" . $ticket_id);
            }
            exit;
        }
    }
}

// Router
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];
    $controller = new TicketController();
    
    if ($action == 'create_ticket') {
        $controller->create();
    } elseif ($action == 'update_status') {
        $controller->updateStatus();
    } elseif ($action == 'add_reply') {
        $controller->addReply();
    }
}
?>
