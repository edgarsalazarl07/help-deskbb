<?php
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Equipo.php';

$usuario_id = $_SESSION['usuario_id'];

// Get total tickets specifically for this user
$ticketModel = new Ticket();
$mis_tickets = $ticketModel->readByUsuario($usuario_id);

$abiertos = 0;
$cerrados = 0;
while ($row = $mis_tickets->fetch(PDO::FETCH_ASSOC)) {
    if($row['estado'] == 'abierto') $abiertos++;
    if($row['estado'] == 'cerrado') $cerrados++;
}

// Get assigned equipment
$query_equipos = "SELECT count(*) as total 
                  FROM asignaciones a 
                  INNER JOIN personal p ON a.personal_id = p.id 
                  WHERE p.usuario_id = :usuario_id AND a.estado_asignacion = 'activa'";
$database = new Database();
$conn = $database->getConnection();
$stmt = $conn->prepare($query_equipos);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$equipos_asignados = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

?>

<div class="container-glass p-5 text-center mt-5">
    <h1 class="display-4 text-primary font-weight-bold">Inicio - Mi Panel</h1>
    <p class="lead">¡Hola, <?= htmlspecialchars($_SESSION['usuario']) ?>! Bienvenido a tu portal de soporte técnico.</p>
    <hr class="my-4">
    
    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow hover-scale p-3">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4>¿Tienes un problema?</h4>
                    <p class="text-muted">Reporta una nueva incidencia de forma rápida.</p>
                    <a href="index.php?view=cliente_reportar" class="btn btn-danger btn-lg mt-2">Crear Ticket</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow hover-scale p-3">
                <div class="card-body">
                    <i class="fas fa-search fa-3x text-info mb-3"></i>
                    <h4>Seguimiento</h4>
                    <p class="text-muted">Consulta el estado de tus reportes activos.</p>
                    <a href="index.php?view=cliente_reportes" class="btn btn-info btn-lg mt-2">Ver Mis Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>
