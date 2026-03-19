<?php
require_once __DIR__ . '/../../models/Ticket.php';

$ticketModel = new Ticket();
$tickets = $ticketModel->readAll();
?>

<div class="container-fluid py-4">
    <div class="container-glass p-4">
        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 text-dark font-weight-bold mr-4">Tickets Globales (Reportes)</h1>
        </div>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="table-responsive bg-white p-3 rounded shadow-sm">
            <table class="table table-hover table-bordered datatable responsive nowrap" style="width:100%">
                <thead class="bg-light text-secondary small text-uppercase font-weight-bold">
                    <tr>
                        <th data-priority="1">ID Ticket</th>
                        <th data-priority="2">Fecha Reporte</th>
                        <th data-priority="3">Solicitante</th>
                        <th data-priority="6">Departamento</th>
                        <th data-priority="7">Equipo</th>
                        <th data-priority="4">Problema</th>
                        <th data-priority="5">Estado</th>
                        <th data-priority="8">Acciones</th>
                    </tr>
                </thead>
        <tbody>
            <?php while ($row = $tickets->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td>#<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
                <td><?= htmlspecialchars($row['personal_nombre'] . ' ' . $row['personal_apellidos']) ?></td>
                <td><span class="badge badge-info"><?= $row['nombre_departamento'] ?? 'General' ?></span></td>
                <td>
                    <span class="font-weight-bold text-info"><?= htmlspecialchars($row['numero_serie']) ?></span><br>
                    <small><?= htmlspecialchars($row['marca']) ?> <?= htmlspecialchars($row['modelo']) ?></small>
                </td>
                <td><?= htmlspecialchars($row['titulo']) ?></td>
                <td>
                    <?php if($row['estado'] == 'abierto'): ?>
                        <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> Abierto</span>
                    <?php elseif($row['estado'] == 'en_proceso'): ?>
                        <span class="badge badge-warning text-dark"><i class="fas fa-spinner fa-spin"></i> En Proceso</span>
                    <?php else: ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Cerrado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?view=admin_ticket_detalle&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-search-plus"></i> Gestionar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'btn btn-success btn-sm mr-2 rounded shadow-sm',
                title: 'Reporte de Tickets HelpDesk'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                className: 'btn btn-danger btn-sm mr-2 rounded shadow-sm',
                title: 'Reporte de Tickets HelpDesk'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Imprimir',
                className: 'btn btn-info btn-sm rounded shadow-sm',
                title: 'Reporte de Tickets HelpDesk'
            }
        ],
        "language": datatableSpanish,
        "order": [[0, "desc"]]
    });
});
</script>
