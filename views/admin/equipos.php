<?php
require_once __DIR__ . '/../../models/Equipo.php';

$equipoModel = new Equipo();
$equipos = $equipoModel->readAll();
$categorias = $equipoModel->readCategorias();
?>

<div class="d-flex flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 mr-4">Catálogo de Equipos</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevoEquipo">
        <i class="fas fa-plus"></i> Nuevo Equipo
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
                <th>Número de Serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $equipos->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><strong><?= htmlspecialchars($row['numero_serie']) ?></strong></td>
                <td><?= htmlspecialchars($row['marca']) ?></td>
                <td><?= htmlspecialchars($row['modelo']) ?></td>
                <td><?= htmlspecialchars($row['categoria_nombre'] ?? 'Sin Categoría') ?></td>
                <td>
                    <?php 
                    $badgeClass = 'badge-secondary';
                    switch($row['estado']) {
                        case 'disponible': $badgeClass = 'badge-success'; break;
                        case 'asignado': $badgeClass = 'badge-primary'; break;
                        case 'en_reparacion': $badgeClass = 'badge-warning'; break;
                        case 'baja': $badgeClass = 'badge-danger'; break;
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $row['estado'])) ?></span>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" 
                        data-id="<?= $row['id'] ?>" 
                        data-marca="<?= htmlspecialchars($row['marca']) ?>" 
                        data-modelo="<?= htmlspecialchars($row['modelo']) ?>" 
                        data-numero_serie="<?= htmlspecialchars($row['numero_serie']) ?>" 
                        data-categoria="<?= $row['categoria_id'] ?>" 
                        data-estado="<?= $row['estado'] ?>"
                        data-toggle="modal" data-target="#modalEditarEquipo">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="controllers/EquipoController.php?action=delete_equipo&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Nuevo -->
<div class="modal fade" id="modalNuevoEquipo" tabindex="-1" aria-labelledby="modalNuevoEquipoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="controllers/EquipoController.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoEquipoLabel">Nuevo Equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_equipo">
                    
                    <div class="form-group">
                        <label>Número de Serie <span class="text-danger">*</span></label>
                        <input type="text" name="numero_serie" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-control" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            <?php 
                            $categorias->execute();
                            while ($c = $categorias->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php 
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="disponible">Disponible</option>
                            <option value="asignado">Asignado</option>
                            <option value="en_reparacion">En Reparación</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarEquipo" tabindex="-1" aria-labelledby="modalEditarEquipoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="controllers/EquipoController.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEquipoLabel">Editar Equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_equipo">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label>Número de Serie <span class="text-danger">*</span></label>
                        <input type="text" name="numero_serie" id="edit_numero_serie" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" id="edit_marca" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" id="edit_modelo" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria_id" id="edit_categoria_id" class="form-control" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            <?php 
                            $categorias->execute(); // Reset cursor
                            while ($c = $categorias->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php 
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" id="edit_estado" class="form-control" required>
                            <option value="disponible">Disponible</option>
                            <option value="asignado">Asignado</option>
                            <option value="en_reparacion">En Reparación</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fill edit modal
    const editBtns = document.querySelectorAll('.btn-edit');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_numero_serie').value = this.dataset.numero_serie;
            document.getElementById('edit_marca').value = this.dataset.marca;
            document.getElementById('edit_modelo').value = this.dataset.modelo;
            document.getElementById('edit_categoria_id').value = this.dataset.categoria;
            document.getElementById('edit_estado').value = this.dataset.estado;
        });
    });

    // Confirmation for delete
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: '¿Eliminar equipo?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    // Initialize DataTable for this view
    if($.fn.DataTable) {
        $('.datatable').DataTable({
            "responsive": true,
            "language": datatableSpanish
        });
    }
});
</script>
