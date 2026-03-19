<?php
session_start();
require_once __DIR__ . '/controllers/UsuarioController.php';

// Valid views to prevent LFI
$valid_views = [
    'login',
    'admin_dashboard',
    'admin_usuarios',
    'admin_personal',
    'admin_equipos',
    'admin_asignaciones',
    'admin_tickets',
    'admin_departamentos',
    'admin_ticket_detalle',
    'cliente_dashboard',
    'cliente_dispositivos',
    'cliente_reportar',
    'cliente_reportes',
    'cliente_ticket_detalle'
];

// Load controllers
require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/PersonalController.php';
require_once __DIR__ . '/controllers/EquipoController.php';
require_once __DIR__ . '/controllers/AsignacionController.php';
require_once __DIR__ . '/controllers/DepartamentoController.php';
require_once __DIR__ . '/controllers/TicketController.php';

$view = isset($_GET['view']) && in_array($_GET['view'], $valid_views) ? $_GET['view'] : 'login';

// Check if user is logged in
$is_logged_in = isset($_SESSION['usuario_id']);
$user_role = $_SESSION['rol'] ?? null;

// Allow accessing login page even if logged out
if (!$is_logged_in && $view !== 'login') {
    $view = 'login';
}

// Restrict access
if ($is_logged_in && $view === 'login') {
    $view = ($user_role === 'admin') ? 'admin_dashboard' : 'cliente_dashboard';
}

// Basic rudimentary role restriction
if ($is_logged_in) {
    if ($user_role === 'cliente' && strpos($view, 'admin_') !== false) {
        $view = 'cliente_dashboard';
    }
}

// Include top boilerplate
require_once __DIR__ . '/views/includes/header.php';

// Include the view content based on internal mapping
$view_path = '';
switch($view) {
    case 'login':
        $view_path = __DIR__ . '/views/login.php';
        break;
    case 'admin_dashboard':
        $view_path = __DIR__ . '/views/admin/dashboard.php';
        break;
    case 'admin_usuarios':
        $view_path = __DIR__ . '/views/admin/usuarios.php';
        break;
    case 'admin_personal':
        $view_path = __DIR__ . '/views/admin/personal.php';
        break;
    case 'admin_equipos':
        $view_path = __DIR__ . '/views/admin/equipos.php';
        break;
    case 'admin_asignaciones':
        $view_path = __DIR__ . '/views/admin/asignaciones.php';
        break;
    case 'admin_tickets':
        $view_path = __DIR__ . '/views/admin/tickets_globales.php';
        break;
    case 'admin_departamentos':
        $view_path = __DIR__ . '/views/admin/departamentos.php';
        break;
    case 'admin_ticket_detalle':
        $view_path = __DIR__ . '/views/admin/ticket_detalle.php';
        break;
    case 'cliente_dashboard':
        $view_path = __DIR__ . '/views/cliente/dashboard.php';
        break;
    case 'cliente_dispositivos':
        $view_path = __DIR__ . '/views/cliente/mis_dispositivos.php';
        break;
    case 'cliente_reportar':
        $view_path = __DIR__ . '/views/cliente/reportar_incidencia.php';
        break;
    case 'cliente_reportes':
        $view_path = __DIR__ . '/views/cliente/mis_reportes.php';
        break;
    case 'cliente_ticket_detalle':
        $view_path = __DIR__ . '/views/cliente/ticket_detalle.php';
        break;
}

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    echo "<div class='container mt-5'><h1>View not found: " . htmlspecialchars($view) . "</h1></div>";
}

require_once __DIR__ . '/views/includes/footer.php';
?>
