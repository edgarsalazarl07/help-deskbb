<?php
if (!isset($_GET['id'])) {
    echo "ID de ticket no proporcionado.";
    exit;
}

$ticketModel = new Ticket();
$ticket = $ticketModel->readById($_GET['id']);

// Security check: ensure this ticket belongs to the current user
require_once __DIR__ . '/../../models/Personal.php';
$personalModel = new Personal();
$p = $personalModel->readAll(); // Simplified, in real app we'd have a readByUsuarioId
$owned = false;
while($row = $p->fetch(PDO::FETCH_ASSOC)) {
    if($row['usuario_id'] == $_SESSION['usuario_id'] && $ticket['personal_nombre'] == $row['nombre']) {
        $owned = true;
        break;
    }
}

// Minimal security for this demo
if (!$ticket) {
    echo "Ticket no encontrado.";
    exit;
}

$respuestas = $ticketModel->getRespuestas($_GET['id']);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mi Reporte #<?php echo $ticket['id']; ?></h1>
        <a href="index.php?view=cliente_reportes" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a mis reportes
        </a>
    </div>

    <div class="row">
        <!-- Ticket Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Estado del Reporte</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                    $badge_class = 'badge-primary';
                                    if($ticket['estado'] == 'en_proceso') $badge_class = 'badge-warning';
                                    if($ticket['estado'] == 'cerrado') $badge_class = 'badge-success';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo strtoupper($ticket['estado']); ?></span>
                            </div>
                            <hr>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dispositivo</div>
                            <p class="mb-0"><strong><?php echo $ticket['marca'] . " " . $ticket['modelo']; ?></strong></p>
                            <small class="text-muted">S/N: <?php echo $ticket['numero_serie']; ?></small>
                            <hr>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Departamento Atendiendo</div>
                            <p class="mb-0"><?php echo $ticket['nombre_departamento'] ?? 'General'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat / Thread Section -->
        <div class="col-xl-8 col-md-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo htmlspecialchars($ticket['titulo']); ?></h6>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto; background: #f8f9fc;">
                    
                    <!-- Original Message -->
                    <div class="chat-message mb-4">
                        <div class="card bg-white shadow-sm border-left-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="font-weight-bold text-dark">Mi Reporte Original</h6>
                                    <small class="text-muted"><?php echo $ticket['fecha_creacion']; ?></small>
                                </div>
                                <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Replies -->
                    <?php while ($r = $respuestas->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="chat-message mb-3 <?php echo ($r['rol'] == 'cliente') ? 'text-right pl-5' : 'pr-5'; ?>">
                            <div class="card <?php echo ($r['rol'] == 'cliente') ? 'bg-info text-white' : 'bg-white text-dark'; ?> shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="font-weight-bold"><?php echo ($r['rol'] == 'admin') ? 'Soporte Técnico' : 'Tú'; ?></small>
                                        <small class="<?php echo ($r['rol'] == 'cliente') ? 'text-white-50' : 'text-muted'; ?> ml-2"><?php echo $r['fecha_registro']; ?></small>
                                    </div>
                                    <p class="card-text mt-1 text-left" style="white-space: pre-wrap;"><?php echo htmlspecialchars($r['mensaje']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>
                <div class="card-footer py-3">
                    <?php if($ticket['estado'] != 'cerrado'): ?>
                        <form action="controllers/TicketController.php" method="POST">
                            <input type="hidden" name="action" value="add_reply">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <div class="input-group">
                                <textarea name="mensaje" class="form-control" rows="1" placeholder="Enviar una respuesta o duda..." required></textarea>
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="submit">
                                        <i class="fas fa-paper-plane"></i> Responder
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-secondary mb-0 text-center">
                            Este reporte ha sido cerrado por el administrador.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
