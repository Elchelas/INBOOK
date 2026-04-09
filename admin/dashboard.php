<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// Conteos rápidos
$totalLibros = $pdo->query("SELECT count(*) FROM libros")->fetchColumn();
$totalAlumnos = $pdo->query("SELECT count(*) FROM usuarios WHERE rol='alumno'")->fetchColumn();
$totalVideos = $pdo->query("SELECT count(*) FROM recursos_video")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-dashboard">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-speedometer2"></i>
            <b>PANEL DE CONTROL</b> | SuperAdmin
        </div>
        <div class="top-bar-actions">
            <span class="badge-role">Acceso Total</span>
            <a href="../auth/logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="welcome-text">Bienvenido, <?php echo explode(' ', $_SESSION['nombre'] ?? 'Administrador')[0]; ?></h2>
        <p class="subtitle">Estado actual de la biblioteca digital ITSUR</p>

        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-bookshelf"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalLibros; ?></h3>
                    <p>Libros</p>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalAlumnos; ?></h3>
                    <p>Alumnos</p>
                </div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-icon"><i class="bi bi-play-btn-fill"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalVideos; ?></h3>
                    <p>Recursos Video</p>
                </div>
            </div>
        </div>

        <div class="admin-sections mt-5">
            <div class="management-card">
                <h3><i class="bi bi-journal-plus"></i> Gestión de Biblioteca</h3>
                <div class="action-list">
                    <a href="agregar_libro.php" class="action-item primary">
                        <i class="bi bi-plus-circle-fill"></i> Nuevo Libro
                    </a>
                    <a href="gestionar_libros.php" class="action-item">
                        <i class="bi bi-pencil-square"></i> Editar Catálogo
                    </a>
                    <a href="reportes.php" class="action-item">
                        <i class="bi bi-bar-chart-line-fill"></i> Ver Reportes
                    </a>
                </div>
            </div>

            <div class="management-card">
                <h3><i class="bi bi-camera-video-fill"></i> Multimedia e Interacción</h3>
                <div class="action-list">
                    <a href="agregar_video.php" class="action-item primary">
                        <i class="bi bi-plus-circle-fill"></i> Nuevo Video
                    </a>
                    <a href="gestionar_videos.php" class="action-item">
                        <i class="bi bi-youtube"></i> Gestionar Videos
                    </a>
                    <a href="gestionar_usuarios.php" class="action-item">
                        <i class="bi bi-person-gear"></i> Control de Alumnos
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>