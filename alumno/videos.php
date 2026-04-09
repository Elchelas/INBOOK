<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['user_id'];

// 1. Obtener datos del alumno para filtrar contenido relevante
$stmtUser = $pdo->prepare("SELECT carrera_id, semestre FROM usuarios WHERE id = ?");
$stmtUser->execute([$user_id]);
$alumno = $stmtUser->fetch();

// 2. Consultar videos recomendados (Filtrados por carrera y semestre)
$stmtVid = $pdo->prepare("SELECT * FROM recursos_video WHERE carrera_id = ? AND semestre_sugerido = ? ORDER BY id DESC");
$stmtVid->execute([$alumno['carrera_id'], $alumno['semestre']]);
$videos = $stmtVid->fetchAll();

/**
 * Función auxiliar para extraer el ID de un video de YouTube
 */
function obtenerYoutubeID($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    return $vars['v'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoteca | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/videos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" class="back-link">
            <i class="bi bi-arrow-left-circle"></i> Regresar al Inicio
        </a>
    </div>
</div>

<div class="container" style="margin-top: 40px;">
    
    <header class="header-intro">
        <h2 class="section-title"><i class="bi bi-play-btn-fill"></i> Videoteca de Apoyo</h2>
        <p>Contenido multimedia seleccionado estratégicamente para tu formación en el <b><?php echo $alumno['semestre']; ?>° Semestre</b>.</p>
    </header>

    <?php if (count($videos) > 0): ?>
        <div class="video-grid">
            <?php foreach ($videos as $v): 
                $vidID = obtenerYoutubeID($v['url_youtube']); 
            ?>
                <div class="video-card">
                    <div class="video-container">
                        <?php if ($vidID): ?>
                            <iframe src="https://www.youtube.com/embed/<?php echo $vidID; ?>?rel=0" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        <?php else: ?>
                            <div class="video-error">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Enlace no válido</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="video-info">
                        <span class="badge-tag">Sugerencia Académica</span>
                        <h4><?php echo htmlspecialchars($v['titulo_video']); ?></h4>
                        <p><?php echo htmlspecialchars($v['descripcion'] ?? 'Material de refuerzo para tus materias actuales.'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-camera-video-off"></i>
            <h3>Aún no hay videos para tu semestre</h3>
            <p>Tus docentes están preparando material de apoyo. ¡Vuelve pronto!</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>