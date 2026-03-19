<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk System</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/custom.css?v=1.2">
    
    <link rel="stylesheet" href="public/css/style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        const datatableSpanish = {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "print": "Imprimir"
            }
        };
    </script>
</head>
<body class="">

<?php if (isset($_SESSION['usuario_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-light navbar-light-custom mb-4">
    <a class="navbar-brand" href="index.php">
        <img src="public/img/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top">
        Help - Desk
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item <?= $view == 'admin_dashboard' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=admin_dashboard">Inicio</a>
                </li>
                <li class="nav-item <?= $view == 'admin_usuarios' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=admin_usuarios">Usuarios</a>
                </li>
                <li class="nav-item <?= $view == 'admin_asignaciones' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=admin_asignaciones">Asignacion</a>
                </li>
                <li class="nav-item <?= $view == 'admin_tickets' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=admin_tickets">Reportes</a>
                </li>
            <?php else: ?>
                <li class="nav-item <?= $view == 'cliente_dashboard' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=cliente_dashboard">Inicio</a>
                </li>
                <li class="nav-item <?= $view == 'cliente_dispositivos' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=cliente_dispositivos">Mis dispositivos</a>
                </li>
                <li class="nav-item <?= $view == 'cliente_reportes' ? 'active' : '' ?>">
                    <a class="nav-link" href="index.php?view=cliente_reportes">Mis Reportes</a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle font-weight-bold text-primary" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['usuario']) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="navbarDropdown">
                    <div class="dropdown-header">Sesión Activa</div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="controllers/UsuarioController.php?action=logout">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid mb-5 <?php echo ($_SESSION['rol'] === 'admin') ? '' : 'container'; ?>">
<?php else: ?>
<div class="container">
<?php endif; ?>
