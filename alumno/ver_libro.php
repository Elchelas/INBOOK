<?php
session_start();
require_once '../config/db.php';

// Verificación de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['user_id'];
$libro_id = $_GET['id'] ?? null;

if (!$libro_id) { header("Location: home.php"); exit(); }

// 1. Obtener información del libro
$stmt = $pdo->prepare("SELECT l.*, c.nombre_carrera FROM libros l 
                       LEFT JOIN carreras c ON l.carrera_id = c.id 
                       WHERE l.id = ?");
$stmt->execute([$libro_id]);
$libro = $stmt->fetch();

if (!$libro) { echo "Libro no encontrado."; exit(); }

// 2. Lógica de Reporte: Registrar que el alumno está consultando este libro
$sqlReporte = "INSERT INTO reportes_lectura (usuario_id, libro_id, total_consultas) 
               VALUES (?, ?, 1) 
               ON DUPLICATE KEY UPDATE 
               total_consultas = total_consultas + 1, 
               fecha_ultima_consulta = CURRENT_TIMESTAMP";
$stmtRep = $pdo->prepare($sqlReporte);
$stmtRep->execute([$user_id, $libro_id]);

// 3. Obtener video vinculado
$stmtVid = $pdo->prepare("SELECT id, url_youtube FROM recursos_video WHERE libro_id = ? LIMIT 1");
$stmtVid->execute([$libro_id]);
$video_vinculado = $stmtVid->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $libro['titulo']; ?> | INBOOK</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/ver_libro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-book-half"></i>
            <b>DETALLES DEL RECURSO</b>
        </div>
        <a href="home.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>

    <div class="container book-detail-container">
        <div class="book-grid">
            <div class="book-cover-section">
                <div class="sticky-cover">
                    <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada" class="main-cover">
                    <a href="agregar_favorito.php?id=<?php echo $libro['id']; ?>" class="btn-fav">
                        <i class="bi bi-heart-fill"></i> Agregar a mi estante
                    </a>
                </div>
            </div>
            
            <div class="book-info-section">
                <span class="category-badge <?php echo $libro['categoria']; ?>">
                    <?php echo ($libro['categoria'] == 'academico') ? 'Material Académico' : 'Literatura / Ocio'; ?>
                </span>
                
                <h1 class="book-title-main"><?php echo htmlspecialchars($libro['titulo']); ?></h1>
                <p class="book-author-main">Escrito por <span><?php echo htmlspecialchars($libro['autor']); ?></span></p>
                
                <div class="book-meta-grid">
                    <?php if($libro['categoria'] == 'academico'): ?>
                        <div class="meta-item">
                            <i class="bi bi-mortarboard"></i>
                            <div>
                                <small>Carrera</small>
                                <strong><?php echo htmlspecialchars($libro['nombre_carrera']); ?></strong>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-layers"></i>
                            <div>
                                <small>Nivel</small>
                                <strong><?php echo $libro['semestre_sugerido']; ?>° Semestre</strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="synopsis-card">
                    <h3><i class="bi bi-text-left"></i> Sinopsis</h3>
                    <p>
                        Este recurso digital ha sido seleccionado para fortalecer las competencias académicas 
                        en el área de <?php echo $libro['nombre_carrera'] ?? 'lectura general' ?>. 
                        Explora su contenido y utiliza los recursos multimedia asociados para un mejor aprendizaje.
                    </p>
                </div>

                <?php if ($video_vinculado): ?>
                    <?php 
                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $video_vinculado['url_youtube'], $match);
                        $videoID = $match[1] ?? null;
                    ?>
                    <div class="video-integration">
                        <p class="video-hint">Este libro incluye una explicación en video:</p>
                        <button onclick="toggleVideo(true)" class="btn-video-play">
                            <i class="bi bi-play-circle-fill"></i> REPRODUCIR VIDEO TUTORIAL
                        </button>
                    </div>

                    <div id="cinemaModal" class="video-modal" onclick="toggleVideo(false)">
                        <div class="modal-content-cinema" onclick="event.stopPropagation()">
                            <span class="close-cinema" onclick="toggleVideo(false)">&times;</span>
                            <div class="ratio ratio-16x9">
                                <iframe id="ytPlayer" src="" frameborder="0" allow="autoplay; fullscreen"></iframe>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="action-footer" style="display: flex; gap: 15px; margin-top: 30px;">
                    <button class="btn-read-now" onclick="alert('¡Gracias por tu interés! Este libro está disponible en la biblioteca física del ITSUR.')">
                        <i class="bi bi-info-circle"></i> Disponibilidad Física
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleVideo(show) {
            const modal = document.getElementById('cinemaModal');
            const player = document.getElementById('ytPlayer');
            const videoUrl = "https://www.youtube.com/embed/<?php echo $videoID; ?>?autoplay=1&rel=0";

            if (show) {
                player.src = videoUrl;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                player.src = "";
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") toggleVideo(false);
        });
    </script>
</body>
</html>