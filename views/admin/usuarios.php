<?php
require_once __DIR__ . '/../../models/Usuario.php';
$usuarioModel = new Usuario();
$usuarios = $usuarioModel->readAll();

function calcularEdad($fecha_nacimiento) {
    if (empty($fecha_nacimiento)) return '-';
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento);
    return $edad->y;
}
?>


<div class="container-fluid py-4">
    <div class="container-glass p-4">
        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 text-dark font-weight-bold mr-4">Administrar Usuarios</h1>
            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalNuevoUsuario">
                <i class="fas fa-user-plus mr-2"></i> Agregar Usuario
            </button>
        </div>

        <div class="table-responsive bg-white p-3 rounded shadow-sm">
            <table class="table table-hover table-bordered datatable responsive nowrap" style="width:100%">
                <thead class="bg-light text-secondary small text-uppercase font-weight-bold">
                    <tr>
                        <th class="dtr-control" data-priority="1">Apellido paterno</th>
                        <th data-priority="2">Apellido materno</th>
                        <th data-priority="3">Nombre</th>
                        <th data-priority="4">Edad</th>
                        <th class="none">Sexo</th>
                        <th data-priority="5">Telefono</th>
                        <th data-priority="6">Correo</th>
                        <th data-priority="7">Usuario</th>
                        <th data-priority="8">Ubicacion</th>
                        <th class="none">Reset Password</th>
                        <th class="none" data-priority="11">Cambiar Rol</th>
                        <th class="none">Activar</th>
                        <th class="none">Editar</th>
                        <th class="none">Eliminar</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php while ($row = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td class="dtr-control"><?= htmlspecialchars($row['apellido_paterno'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['apellido_materno'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
                        <td class="text-center"><?= $row['fecha_nacimiento'] ?? '' ?></td>
                        <td class="text-center"><?= $row['sexo'] ?? '-' ?></td>
                        <td><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['correo'] ?? '') ?></td>
                        <td><span class="badge badge-light p-2 border"><?= htmlspecialchars($row['usuario']) ?></span></td>
                        <td><?= htmlspecialchars($row['ubicacion'] ?? '') ?></td>
                        <td>
                            <button class="btn btn-sm btn-success btn-reset-pass" data-id="<?= $row['id'] ?>">Cambiar password</button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-change-rol" data-id="<?= $row['id'] ?>" data-rol="<?= $row['rol'] ?>">Cambiar Rol</button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-teal btn-toggle-status" data-id="<?= $row['id'] ?>">
                                <?= ($row['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white btn-edit" 
                                data-id="<?= $row['id'] ?>" 
                                data-ap="<?= htmlspecialchars($row['apellido_paterno'] ?? '') ?>"
                                data-am="<?= htmlspecialchars($row['apellido_materno'] ?? '') ?>"
                                data-nom="<?= htmlspecialchars($row['nombre'] ?? '') ?>"
                                data-fn="<?= $row['fecha_nacimiento'] ?? '' ?>"
                                data-sx="<?= $row['sexo'] ?? '' ?>"
                                data-tel="<?= htmlspecialchars($row['telefono'] ?? '') ?>"
                                data-em="<?= htmlspecialchars($row['correo'] ?? '') ?>"
                                data-usr="<?= htmlspecialchars($row['usuario']) ?>" 
                                data-rol="<?= $row['rol'] ?>" 
                                data-ub="<?= htmlspecialchars($row['ubicacion'] ?? '') ?>"
                                data-toggle="modal" data-target="#modalEditarUsuario">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </td>
                        <td>
                            <?php if ($row['usuario'] !== 'admin'): ?>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formNuevoUsuario">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white">
                    <h5 class="modal-title font-weight-bold">Agregar nuevo usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create_user">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Apellido paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Apellido materno</label>
                            <input type="text" name="apellido_materno" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Sexo</label>
                            <select name="sexo" class="form-control" required>
                                <option value="">Selecciona...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Telefono</label>
                            <input type="text" name="telefono" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Usuario</label>
                            <input type="text" name="usuario" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Rol de usuario</label>
                        <select name="rol" class="form-control" required>
                            <option value="cliente">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Ubicacion</label>
                        <textarea name="ubicacion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4">Agregar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formEditarUsuario">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white">
                    <h5 class="modal-title font-weight-bold">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Apellido paterno</label>
                            <input type="text" name="apellido_paterno" id="edit_ap" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Apellido materno</label>
                            <input type="text" name="apellido_materno" id="edit_am" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Nombre</label>
                            <input type="text" name="nombre" id="edit_nom" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="edit_fn" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Sexo</label>
                            <select name="sexo" id="edit_sx" class="form-control" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Telefono</label>
                            <input type="text" name="telefono" id="edit_tel" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Correo</label>
                            <input type="email" name="correo" id="edit_em" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Usuario</label>
                            <input type="text" name="usuario" id="edit_usr" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Nueva Password (Opcional)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Rol</label>
                        <select name="rol" id="edit_rol" class="form-control" required>
                            <option value="cliente">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Ubicacion</label>
                        <textarea name="ubicacion" id="edit_ub" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fill edit modal
    $('.btn-edit').on('click', function() {
        const d = $(this).data();
        $('#edit_id').val(d.id);
        $('#edit_ap').val(d.ap);
        $('#edit_am').val(d.am);
        $('#edit_nom').val(d.nom);
        $('#edit_fn').val(d.fn);
        $('#edit_sx').val(d.sx);
        $('#edit_tel').val(d.tel);
        $('#edit_em').val(d.em);
        $('#edit_usr').val(d.usr);
        $('#edit_rol').val(d.rol);
        $('#edit_ub').val(d.ub);
    });

    // Handle AJAX actions
    const handleAJAX = (formId, successMsg) => {
        $(formId).on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'controllers/UsuarioController.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        Swal.fire('¡Éxito!', response.message || successMsg, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });
    };

    handleAJAX('#formNuevoUsuario', 'Usuario creado correctamente.');
    handleAJAX('#formEditarUsuario', 'Usuario actualizado correctamente.');

    // Status toggle
    $('.btn-toggle-status').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'controllers/UsuarioController.php',
            type: 'POST',
            data: { action: 'toggle_status', id: id },
            success: function(response) {
                if(response.status === 'success') {
                    Swal.fire('Listo', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });

    // Delete
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el usuario permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controllers/UsuarioController.php',
                    type: 'GET',
                    data: { action: 'delete_user', id: id },
                    success: function() {
                        Swal.fire('Eliminado', 'Usuario eliminado.', 'success').then(() => location.reload());
                    }
                });
            }
        });
    });
    
    // Quick actions
    $('.btn-reset-pass').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Reset Password',
            input: 'password',
            inputLabel: 'Nueva contraseña',
            showCancelButton: true
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'controllers/UsuarioController.php',
                    type: 'POST',
                    data: { action: 'update_user', id: id, password: result.value },
                    success: function() {
                        Swal.fire('Listo', 'Contraseña actualizada.', 'success');
                    }
                });
            }
        });
    });

    $('.btn-change-rol').on('click', function() {
        const id = $(this).data('id');
        const currentRol = $(this).data('rol');
        const newRol = currentRol === 'admin' ? 'cliente' : 'admin';
        Swal.fire({
            title: '¿Cambiar rol?',
            text: `El usuario será cambiado a ${newRol}`,
            icon: 'question',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controllers/UsuarioController.php',
                    type: 'POST',
                    data: { action: 'update_user', id: id, rol: newRol },
                    success: function() {
                        Swal.fire('Cambiado', 'Rol actualizado.', 'success').then(() => location.reload());
                    }
                });
            }
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
