<?php
require_once __DIR__ . '/../../models/Asignacion.php';
require_once __DIR__ . '/../../models/Equipo.php';
require_once __DIR__ . '/../../models/Personal.php';

$asignacionModel = new Asignacion();
$asignaciones = $asignacionModel->readAll();

$equipoModel = new Equipo();
// Solo traer equipos disponibles para asignar
$queryEquipos = "SELECT id, marca, modelo, numero_serie FROM equipos WHERE estado = 'disponible' ORDER BY id DESC";
$database = new Database();
$conn = $database->getConnection();
$stmtEquipos = $conn->prepare($queryEquipos);
$stmtEquipos->execute();
$equipos_disponibles = $stmtEquipos;

$personalModel = new Personal();
$personal = $personalModel->readAll();
?>

<div class="d-flex flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 mr-4">Control de Asignaciones</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevaAsignacion">
        <i class="fas fa-plus"></i> Nueva Asignación
    </button>
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

<div class="datatable-wrapper">
    <table class="table table-striped table-bordered datatable" style="width:100%">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Equipo (Serie/Marca/Modelo)</th>
                <th>Asignado A</th>
                <th>Fecha Asignación</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $asignaciones->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <span class="font-weight-bold text-primary"><?= htmlspecialchars($row['numero_serie']) ?></span><br>
                    <small><?= htmlspecialchars($row['marca']) ?> <?= htmlspecialchars($row['modelo']) ?></small>
                </td>
                <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['fecha_asignacion'])) ?></td>
                <td>
                    <?= $row['fecha_devolucion'] ? date('d/m/Y', strtotime($row['fecha_devolucion'])) : '<span class="text-muted">N/A</span>' ?>
                </td>
                <td>
                    <?php if($row['estado_asignacion'] == 'activa'): ?>
                        <span class="badge badge-success">Activa</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Devuelto</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['estado_asignacion'] == 'activa'): ?>
                    <a href="controllers/AsignacionController.php?action=devolver_equipo&id=<?= $row['id'] ?>" class="btn btn-sm btn-info btn-devolver">
                        <i class="fas fa-undo"></i> Registrar Devolución
                    </a>
                    <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check"></i> Completado</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Nueva Asignación -->
<div class="modal fade" id="modalNuevaAsignacion" tabindex="-1" aria-labelledby="modalNuevaAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="controllers/AsignacionController.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaAsignacionLabel">Asignar Equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_asignacion">
                    
                    <div class="form-group">
                        <label>Seleccionar Equipo Disponible <span class="text-danger">*</span></label>
                        <select name="equipo_id" class="form-control" required>
                            <option value="">-- Elija un equipo disponible --</option>
                            <?php 
                            while ($eq = $equipos_disponibles->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $eq['id'] ?>">
                                    [<?= htmlspecialchars($eq['numero_serie']) ?>] - <?= htmlspecialchars($eq['marca']) ?> <?= htmlspecialchars($eq['modelo']) ?>
                                </option>
                            <?php 
                            endwhile; 
                            ?>
                        </select>
                        <?php if($equipos_disponibles->rowCount() == 0): ?>
                            <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-triangle"></i> No hay equipos disponibles actualmente.</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Seleccionar Personal <span class="text-danger">*</span></label>
                        <select name="personal_id" class="form-control" required>
                            <option value="">-- Elija a la persona --</option>
                            <?php 
                            while ($p = $personal->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?> (ID: <?= $p['id'] ?>)
                                </option>
                            <?php 
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> La fecha de asignación se registrará automáticamente como el día de hoy.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" <?= ($equipos_disponibles->rowCount() == 0) ? 'disabled' : '' ?>>Guardar Asignación</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    if($.fn.DataTable) {
        $('.datatable').DataTable({
            "responsive": true,
            "language": datatableSpanish
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Confirmation for return
    const devolverBtns = document.querySelectorAll('.btn-devolver');
    devolverBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: '¿Confirmar devolución?',
                text: "El equipo pasará a estar 'disponible' nuevamente en el catálogo.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar devolución',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});
</script>
