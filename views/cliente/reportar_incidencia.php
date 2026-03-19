<?php
require_once __DIR__ . '/../../config/Database.php';

$usuario_id = $_SESSION['usuario_id'];
$asignacion_id_preselected = $_GET['asignacion_id'] ?? '';

$database = new Database();
$conn = $database->getConnection();

// Get active assignments for this client
$query = "SELECT a.id, e.marca, e.modelo, e.numero_serie 
          FROM asignaciones a 
          INNER JOIN equipos e ON a.equipo_id = e.id 
          INNER JOIN personal p ON a.personal_id = p.id 
          WHERE p.usuario_id = :usuario_id AND a.estado_asignacion = 'activa'";

$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reportar Incidencia</h1>
    <a href="index.php?view=cliente_reportes" class="btn btn-secondary">
        <i class="fas fa-history"></i> Ver Mis Reportes
    </a>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Nuevo Ticket de Soporte</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if($stmt->rowCount() == 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> No tienes dispositivos activos asignados. No puedes crear un reporte de avería en este momento.
                    </div>
                <?php else: ?>
                
                <form action="controllers/TicketController.php" method="POST">
                    <input type="hidden" name="action" value="create_ticket">
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Equipo que presenta la falla <span class="text-danger">*</span></label>
                        <select name="asignacion_id" class="form-control" required>
                            <option value="">-- Selecciona el dispositivo --</option>
                            <?php 
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                $selected = ($row['id'] == $asignacion_id_preselected) ? 'selected' : '';
                            ?>
                                <option value="<?= $row['id'] ?>" <?= $selected ?>>
                                    [S/N: <?= htmlspecialchars($row['numero_serie']) ?>] - <?= htmlspecialchars($row['marca'] . ' ' . $row['modelo']) ?>
                                </option>
                            <?php 
                            endwhile; 
                            ?>
                        </select>
                        <small class="form-text text-muted">Asegúrate de seleccionar el equipo físico correcto.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Departamento al que escala el reporte <span class="text-danger">*</span></label>
                        <select name="departamento_id" class="form-control" required>
                            <option value="">-- Seleccione Departamento --</option>
                            <?php 
                            require_once __DIR__ . '/../../models/Departamento.php';
                            $deptoModel = new Departamento();
                            $deptosList = $deptoModel->readAll();
                            while ($d = $deptosList->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Asunto o Tipo de Problema <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required placeholder="Ej: No enciende, Pantalla rota, Lentitud extrema...">
                        <small class="form-text text-muted">Escribe un título corto y descriptivo.</small>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Descripción Detallada <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control" rows="5" required placeholder="Describe paso a paso lo que ocurre, desde cuándo sucede y si muestra algún código de error..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-block btn-lg">
                        <i class="fas fa-paper-plane"></i> Enviar Reporte a Soporte Técnico
                    </button>
                </form>
                
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
