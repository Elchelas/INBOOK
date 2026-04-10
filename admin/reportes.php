<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php"); exit();
}
require_once '../config/db.php';

// Consulta optimizada para el nuevo enfoque de "Mapa de Calor de Interés"
$sql = "SELECT r.*, u.nombre as alumno, u.correo, l.titulo as libro, c.nombre_carrera
        FROM reportes_lectura r
        JOIN usuarios u ON r.usuario_id = u.id
        JOIN libros l ON r.libro_id = l.id
        LEFT JOIN carreras c ON u.carrera_id = c.id
        ORDER BY r.fecha_ultima_consulta DESC";
$reportes = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Interés | INBOOK</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_tables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-graph-up-arrow"></i>
            <b>MÉTRICAS DE CONSULTA</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-speedometer2"></i> Panel Principal
        </a>
    </div>

    <div class="container mt-5">
        <div class="table-header-actions">
            <div>
                <h2>Popularidad del Acervo</h2>
                <p class="subtitle">Libros con mayor impacto y frecuencia de consulta por los alumnos.</p>
            </div>
            <button onclick="window.print()" class="btn-add-new">
                <i class="bi bi-printer"></i> Exportar Listado
            </button>
        </div>

        <div class="table-responsive-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Estudiante / Contacto</th>
                        <th>Recurso Consultado</th>
                        <th class="text-center">Frecuencia</th>
                        <th>Último Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reportes as $r): 
                        // Lógica visual: Si tiene más de 5 visitas es "Hot"
                        $isTrending = ($r['total_consultas'] >= 5);
                    ?>
                    <tr>
                        <td>
                            <div class="title-cell">
                                <span class="book-title"><?php echo htmlspecialchars($r['alumno']); ?></span>
                                <span class="book-author" style="font-family: monospace; font-size: 0.8rem;">
                                    <?php echo htmlspecialchars($r['correo']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="linked-book" style="border: none; background: transparent; padding: 0;">
                                <i class="bi bi-bookmark-star-fill text-warning"></i>
                                <strong style="color: var(--itsur-blue-dark);">
                                    <?php echo htmlspecialchars($r['libro']); ?>
                                </strong>
                            </div>
                            <small class="text-muted"><?php echo htmlspecialchars($r['nombre_carrera'] ?? 'General'); ?></small>
                        </td>
                        <td class="text-center">
                            <span class="badge-consultas <?php echo $isTrending ? 'trending' : ''; ?>">
                                <i class="bi <?php echo $isTrending ? 'bi-fire' : 'bi-eye'; ?>"></i> 
                                <?php echo $r['total_consultas']; ?> visitas
                            </span>
                        </td>
                        <td>
                            <div class="date-cell">
                                <i class="bi bi-clock-history"></i> 
                                <?php echo date('d/m/Y H:i', strtotime($r['fecha_ultima_consulta'])); ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($reportes)): ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="bi bi-cloud-slash"></i>
                            <p>No se han registrado consultas en el acervo todavía.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>