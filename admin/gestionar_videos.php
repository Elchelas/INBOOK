<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php"); exit();
}

// Función para extraer el ID de YouTube
function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return $match[1] ?? null;
}

// Lógica de Eliminación
if (isset($_GET['eliminar'])) {
    $id_del = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM recursos_video WHERE id = ?");
    $stmt->execute([$id_del]);
    header("Location: gestionar_videos.php?res=eliminado"); exit();
}

$sql = "SELECT v.*, c.nombre_carrera, l.titulo as titulo_libro 
        FROM recursos_video v
        LEFT JOIN carreras c ON v.carrera_id = c.id
        LEFT JOIN libros l ON v.libro_id = l.id
        ORDER BY v.fecha_registro DESC";
$videos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Videos | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_tables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-play-btn-fill"></i>
            <b>BIBLIOTECA MULTIMEDIA</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-speedometer2"></i> Panel Principal
        </a>
    </div>

    <div class="container mt-5">
        <div class="table-header-actions">
            <div>
                <h2>Recursos de Video</h2>
                <p class="subtitle">Gestiona el material complementario alojado en YouTube.</p>
            </div>
            <a href="agregar_video.php" class="btn-add-new">
                <i class="bi bi-plus-circle"></i> Agregar Video
            </a>
        </div>

        <?php if(isset($_GET['res']) && $_GET['res'] == 'eliminado'): ?>
            <div class="alert-success-custom">
                <i class="bi bi-trash-fill"></i> El video ha sido removido del catálogo.
            </div>
        <?php endif; ?>

        <div class="table-responsive-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="140">Miniatura</th>
                        <th>Título y Enlace</th>
                        <th>Clasificación</th>
                        <th>Libro Vinculado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($videos as $v): 
                        $yt_id = getYoutubeId($v['url_youtube']);
                    ?>
                    <tr>
                        <td>
                            <div class="video-thumb-wrapper">
                                <img src="https://img.youtube.com/vi/<?php echo $yt_id; ?>/mqdefault.jpg" alt="YT Thumb">
                                <a href="<?php echo $v['url_youtube']; ?>" target="_blank" class="play-overlay">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="title-cell">
                                <span class="book-title"><?php echo htmlspecialchars($v['titulo_video']); ?></span>
                                <a href="<?php echo $v['url_youtube']; ?>" target="_blank" class="yt-link-small">
                                    <i class="bi bi-youtube"></i> Abrir enlace externo
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="academic-cell">
                                <span class="career-name"><?php echo htmlspecialchars($v['nombre_carrera'] ?? 'General'); ?></span>
                                <span class="semester-badge"><?php echo $v['semestre_sugerido']; ?>° Semestre</span>
                            </div>
                        </td>
                        <td>
                            <?php if($v['titulo_libro']): ?>
                                <div class="linked-book">
                                    <i class="bi bi-book-half"></i> 
                                    <span><?php echo htmlspecialchars($v['titulo_libro']); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Sin vínculo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="action-btns-group">
                                <a href="editar_video.php?id=<?php echo $v['id']; ?>" class="btn-action edit" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="gestionar_videos.php?eliminar=<?php echo $v['id']; ?>" 
                                   class="btn-action delete" 
                                   title="Eliminar"
                                   onclick="return confirm('¿Confirma la eliminación de este recurso multimedia?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($videos)): ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="bi bi-camera-video-off"></i>
                            <p>No hay videos registrados en el sistema.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>