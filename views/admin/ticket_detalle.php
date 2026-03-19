<?php
if (!isset($_GET['id'])) {
    echo "ID de ticket no proporcionado.";
    exit;
}

$ticketModel = new Ticket();
$ticket = $ticketModel->readById($_GET['id']);

if (!$ticket) {
    echo "Ticket no encontrado.";
    exit;
}

$respuestas = $ticketModel->getRespuestas($_GET['id']);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle del Ticket #<?php echo $ticket['id']; ?></h1>
        <a href="index.php?view=admin_tickets" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver al listado
        </a>
    </div>

    <div class="row">
        <!-- Ticket Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Estado Actual</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                    $badge_class = 'badge-primary';
                                    if($ticket['estado'] == 'en_proceso') $badge_class = 'badge-warning';
                                    if($ticket['estado'] == 'cerrado') $badge_class = 'badge-success';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo strtoupper($ticket['estado']); ?></span>
                            </div>
                            <hr>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Información del Equipo</div>
                            <p class="mb-0"><strong><?php echo $ticket['marca'] . " " . $ticket['modelo']; ?></strong></p>
                            <small class="text-muted">S/N: <?php echo $ticket['numero_serie']; ?></small>
                            <hr>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Reportado por</div>
                            <p class="mb-0"><?php echo $ticket['personal_nombre'] . " " . $ticket['personal_apellidos']; ?></p>
                            <hr>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Departamento Asignado</div>
                            <p class="mb-0"><?php echo $ticket['nombre_departamento'] ?? 'Sin asignar'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat / Thread Section -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Hilo de Conversación: <?php echo htmlspecialchars($ticket['titulo']); ?></h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalStatus">
                            <i class="fas fa-exchange-alt"></i> Cambiar Estado
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto; background: #f8f9fc;">
                    
                    <!-- Main Description (Initial Message) -->
                    <div class="chat-message mb-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="font-weight-bold text-dark"><?php echo $ticket['personal_nombre']; ?> (Cliente)</h6>
                                    <small class="text-muted"><?php echo $ticket['fecha_creacion']; ?></small>
                                </div>
                                <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Replies -->
                    <?php while ($r = $respuestas->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="chat-message mb-3 <?php echo ($r['rol'] == 'admin') ? 'text-right pl-5' : 'pr-5'; ?>">
                            <div class="card <?php echo ($r['rol'] == 'admin') ? 'bg-primary text-white' : 'bg-white text-dark'; ?> shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="font-weight-bold"><?php echo ($r['rol'] == 'admin') ? 'Soporte Técnico' : htmlspecialchars($r['usuario']); ?></small>
                                        <small class="<?php echo ($r['rol'] == 'admin') ? 'text-white-50' : 'text-muted'; ?> ml-2"><?php echo $r['fecha_registro']; ?></small>
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
                                <textarea name="mensaje" class="form-control" rows="1" placeholder="Escribe una respuesta..." required></textarea>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i> Enviar
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-secondary mb-0 text-center">
                            Este ticket está cerrado. Para nueva comunicación, por favor reabra el ticket o cree uno nuevo.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Status -->
<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="controllers/TicketController.php" method="POST">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nuevo Estado</label>
                        <select name="estado" class="form-control">
                            <option value="abierto" <?php echo $ticket['estado'] == 'abierto' ? 'selected' : ''; ?>>Abierto (Pendiente)</option>
                            <option value="en_proceso" <?php echo $ticket['estado'] == 'en_proceso' ? 'selected' : ''; ?>>En Proceso (Reparación)</option>
                            <option value="cerrado" <?php echo $ticket['estado'] == 'cerrado' ? 'selected' : ''; ?>>Cerrado (Resuelto)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>
