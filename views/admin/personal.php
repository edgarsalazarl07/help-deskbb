<?php
require_once __DIR__ . '/../../models/Personal.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../models/Departamento.php';

$personalModel = new Personal();
$personal = $personalModel->readAll();

$usuarioModel = new Usuario();
$usuarios = $usuarioModel->readAll();

$deptoModel = new Departamento();
$departamentos = $deptoModel->readAll();
?>

<div class="d-flex flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 mr-4">Catálogo de Personal</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevoPersonal">
        <i class="fas fa-plus"></i> Nuevo Registro
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
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Departamento</th>
                <th>Usuario Asociado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $personal->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['apellidos']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['correo']) ?></td>
                <td>
                    <?php if($row['nombre_departamento']): ?>
                        <span class="badge badge-info"><i class="fas fa-building"></i> <?= htmlspecialchars($row['nombre_departamento']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Externo / General</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['usuario_id']): ?>
                        <span class="badge badge-success"><i class="fas fa-link"></i> <?= htmlspecialchars($row['nombre_usuario']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Sin asignar</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" 
                        data-id="<?= $row['id'] ?>" 
                        data-nombre="<?= htmlspecialchars($row['nombre']) ?>" 
                        data-apellidos="<?= htmlspecialchars($row['apellidos']) ?>" 
                        data-telefono="<?= htmlspecialchars($row['telefono']) ?>" 
                        data-correo="<?= htmlspecialchars($row['correo']) ?>" 
                        data-usuario="<?= $row['usuario_id'] ?>" 
                        data-departamento="<?= $row['departamento_id'] ?>" 
                        data-toggle="modal" data-target="#modalEditarPersonal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="controllers/PersonalController.php?action=delete_personal&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Nuevo -->
<div class="modal fade" id="modalNuevoPersonal" tabindex="-1" aria-labelledby="modalNuevoPersonalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="controllers/PersonalController.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoPersonalLabel">Nuevo Personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_personal">
                    
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Departamento</label>
                        <select name="departamento_id" class="form-control">
                            <option value="">-- Seleccione Departamento --</option>
                            <?php 
                            $departamentos->execute();
                            while ($d = $departamentos->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Asociar Usuario del Sistema (Opcional)</label>
                        <select name="usuario_id" class="form-control">
                            <option value="">-- Sin asignar --</option>
                            <?php 
                            // Reset pointer
                            $usuarios->execute();
                            while ($u = $usuarios->fetch(PDO::FETCH_ASSOC)): 
                                if($u['usuario'] != 'admin'): // Dont list main admin for normal association usually
                            ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['usuario']) ?> (Puesto: <?= $u['rol'] ?>)</option>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                        </select>
                        <small class="form-text text-muted">Asociar un usuario permite que esta persona ingrese al portal para ver sus dispositivos.</small>
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
<div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="controllers/PersonalController.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPersonalLabel">Editar Personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_personal">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" id="edit_apellidos" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" id="edit_correo" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Departamento</label>
                        <select name="departamento_id" id="edit_departamento_id" class="form-control">
                            <option value="">-- Seleccione Departamento --</option>
                            <?php 
                            $departamentos->execute();
                            while ($d = $departamentos->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Asociar Usuario del Sistema (Opcional)</label>
                        <select name="usuario_id" id="edit_usuario_id" class="form-control">
                            <option value="">-- Sin asignar --</option>
                            <?php 
                            $usuarios->execute();
                            while ($u = $usuarios->fetch(PDO::FETCH_ASSOC)): 
                                if($u['usuario'] != 'admin'):
                            ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['usuario']) ?></option>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
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
            document.getElementById('edit_nombre').value = this.dataset.nombre;
            document.getElementById('edit_apellidos').value = this.dataset.apellidos;
            document.getElementById('edit_telefono').value = this.dataset.telefono;
            document.getElementById('edit_correo').value = this.dataset.correo;
            document.getElementById('edit_usuario_id').value = this.dataset.usuario || '';
            document.getElementById('edit_departamento_id').value = this.dataset.departamento || '';
        });
    });

    // Confirmation for delete
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: '¿Eliminar registro?',
                text: "No podrás revertir esto.",
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
