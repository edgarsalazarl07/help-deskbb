<?php
$deptoModel = new Departamento();
$deptos = $deptoModel->readAll();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 mr-4">Gestión de Departamentos</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalAddDepto">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Departamento
        </button>
    </div>

    <?php if(isset($_SESSION['success_msg'])): ?>
        <script>
            Swal.fire('¡Éxito!', '<?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>', 'success');
        </script>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_msg'])): ?>
        <script>
            Swal.fire('Error', '<?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>', 'error');
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Departamentos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $deptos->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick='editDepto(<?php echo json_encode($row); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="controllers/DepartamentoController.php?action=delete_departamento&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger btn-sm btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
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

<!-- Modal Add -->
<div class="modal fade" id="modalAddDepto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="controllers/DepartamentoController.php" method="POST">
            <input type="hidden" name="action" value="create_departamento">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Departamento</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Departamento</label>
                        <input type="text" name="nombre" class="form-control" required>
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEditDepto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="controllers/DepartamentoController.php" method="POST">
            <input type="hidden" name="action" value="update_departamento">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Departamento</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Departamento</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
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
$(document).ready(function() {
    $('.datatable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
    });

    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el departamento permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});

function editDepto(data) {
    $('#edit_id').val(data.id);
    $('#edit_nombre').val(data.nombre);
    $('#modalEditDepto').modal('show');
}
</script>
