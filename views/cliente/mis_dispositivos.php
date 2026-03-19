<?php
require_once __DIR__ . '/../../config/Database.php';

$usuario_id = $_SESSION['usuario_id'];

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT a.id as asignacion_id, a.fecha_asignacion, 
                 e.marca, e.modelo, e.numero_serie, e.estado,
                 c.nombre as categoria_nombre
          FROM asignaciones a 
          INNER JOIN equipos e ON a.equipo_id = e.id 
          LEFT JOIN categorias_equipo c ON e.categoria_id = c.id
          INNER JOIN personal p ON a.personal_id = p.id 
          WHERE p.usuario_id = :usuario_id AND a.estado_asignacion = 'activa'
          ORDER BY a.fecha_asignacion DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Dispositivos Asignados</h1>
    <a href="index.php?view=cliente_reportar" class="btn btn-danger shadow-sm">
        <i class="fas fa-exclamation-triangle"></i> Reportar Falla
    </a>
</div>

<?php if($stmt->rowCount() == 0): ?>
<div class="alert alert-info border-info text-center mt-5 p-5">
    <i class="fas fa-info-circle fa-4x mb-3 text-info"></i>
    <h4>No tienes equipos asignados actualmente.</h4>
    <p>Si consideras que esto es un error, por favor contacta al administrador del sistema.</p>
</div>
<?php else: ?>

<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                <?php 
                $icon = 'fa-laptop';
                $cat = strtolower($row['categoria_nombre'] ?? '');
                if(strpos($cat, 'pc') !== false || strpos($cat, 'desktop') !== false) $icon = 'fa-desktop';
                if(strpos($cat, 'monitor') !== false) $icon = 'fa-tv';
                if(strpos($cat, 'tel') !== false) $icon = 'fa-phone-alt';
                if(strpos($cat, 'impre') !== false) $icon = 'fa-print';
                ?>
                <i class="fas <?= $icon ?> fa-4x text-primary mb-3"></i>
                <h5 class="card-title font-weight-bold"><?= htmlspecialchars($row['marca'] . ' ' . $row['modelo']) ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0"><i class="fas fa-barcode text-muted mr-2"></i> <strong>S/N:</strong> <?= htmlspecialchars($row['numero_serie']) ?></li>
                    <li class="list-group-item px-0"><i class="fas fa-tag text-muted mr-2"></i> <strong>Tipo:</strong> <?= htmlspecialchars($row['categoria_nombre'] ?? 'General') ?></li>
                    <li class="list-group-item px-0"><i class="fas fa-calendar-alt text-muted mr-2"></i> <strong>Asignado:</strong> <?= date('d/m/Y', strtotime($row['fecha_asignacion'])) ?></li>
                    <li class="list-group-item px-0">
                        <i class="fas fa-heartbeat text-muted mr-2"></i> <strong>Estado:</strong> 
                        <?php if($row['estado'] == 'en_reparacion'): ?>
                            <span class="badge badge-warning text-dark"><i class="fas fa-tools"></i> En Reparación</span>
                        <?php else: ?>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Operativo</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-white text-center border-top-0 pb-4">
                <a href="index.php?view=cliente_reportar&asignacion_id=<?= $row['asignacion_id'] ?>" class="btn btn-outline-danger btn-block rounded-pill">
                    <i class="fas fa-ticket-alt"></i> Generar Reporte de Falla
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php endif; ?>
