<?php
session_start();
require_once '../config/db.php';

// Verificación de Rango
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php"); exit();
}

$msg = "";

// 1. Procesar el Registro del Video
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_guardar'])) {
    $titulo = $_POST['titulo'];
    $url = $_POST['url'];
    $carrera = $_POST['carrera_id'];
    $semestre = $_POST['semestre'];
    $libro_id = !empty($_POST['libro_id']) ? $_POST['libro_id'] : null;

    $stmt = $pdo->prepare("INSERT INTO recursos_video (titulo_video, url_youtube, carrera_id, semestre_sugerido, libro_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$titulo, $url, $carrera, $semestre, $libro_id])) {
        $msg = "Video registrado correctamente en el acervo multimedia.";
    }
}

// 2. Cargar Datos para los Selects
$carreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC")->fetchAll();
$libros = $pdo->query("SELECT id, titulo FROM libros ORDER BY titulo ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Videos | Admin INBOOK</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_forms.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-play-btn-fill"></i>
            <b>GESTIÓN MULTIMEDIA</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-house-door"></i> Panel Principal
        </a>
    </div>

    <div class="container mt-5">
        <?php if($msg): ?>
            <div class="alert-success-custom">
                <i class="bi bi-check-all"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <h2><i class="bi bi-youtube"></i> Vincular Recurso de Video</h2>
            <p>Integre tutoriales o clases magistrales de YouTube al ecosistema bibliotecario.</p>
            
            <form method="POST" class="custom-form">
                <div class="row">
                    <div class="form-group">
                        <label><i class="bi bi-fonts"></i> Título del Recurso</label>
                        <input type="text" name="titulo" class="form-control" required placeholder="Ej: Fundamentos de Redes">
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-link-45deg"></i> Enlace de YouTube</label>
                        <input type="url" name="url" id="yt_url" class="form-control" required placeholder="https://www.youtube.com/watch?v=..." oninput="updatePreview()">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label><i class="bi bi-mortarboard"></i> Carrera</label>
                        <select name="carrera_id" class="form-select" required>
                            <?php foreach($carreras as $c): ?>
                                <option value="<?=$c['id']?>"><?=$c['nombre_carrera']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 0.5;">
                        <label><i class="bi bi-calendar3"></i> Semestre</label>
                        <input type="number" name="semestre" class="form-control" min="1" max="12" required placeholder="1-12">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="bi bi-bookmark-plus"></i> Vincular a un Libro Específico (Opcional)</label>
                    <select name="libro_id" class="form-select">
                        <option value="">Ninguno (Video de interés general)</option>
                        <?php foreach($libros as $l): ?>
                            <option value="<?=$l['id']?>"><?=htmlspecialchars($l['titulo'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="video_preview_container" class="preview-box" style="display: none; background: #000; border: none; padding: 0; overflow: hidden;">
                    <img id="yt_thumb" src="" style="width: 100%; height: 250px; object-fit: cover; opacity: 0.6;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; text-align: center;">
                        <i class="bi bi-play-circle-fill" style="font-size: 3rem; color: var(--itsur-yellow);"></i>
                        <p style="font-weight: bold; margin-top: 10px;">Vista previa del contenido detectada</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" name="btn_guardar" class="btn-save">
                        <i class="bi bi-plus-circle"></i> Registrar Video
                    </button>
                    <a href="gestionar_videos.php" class="btn-cancel">Ver todos los videos</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePreview() {
            const urlInput = document.getElementById('yt_url').value;
            const previewContainer = document.getElementById('video_preview_container');
            const thumbImg = document.getElementById('yt_thumb');
            
            const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = urlInput.match(regExp);

            if (match && match[2].length == 11) {
                const videoId = match[2];
                thumbImg.src = `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`;
                previewContainer.style.display = 'block';
                previewContainer.style.position = 'relative';
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>