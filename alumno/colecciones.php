<?php
session_start();
require_once '../config/db.php';

// Protección de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
    exit();
}

/**
 * 1. OBTENCIÓN DE DATOS
 */

// Consultar carreras que tienen libros asignados (INNER JOIN para evitar categorías vacías)
$sqlCarreras = "SELECT c.id, c.nombre_carrera, COUNT(l.id) as total_libros 
                FROM carreras c 
                INNER JOIN libros l ON c.id = l.carrera_id 
                GROUP BY c.id 
                ORDER BY c.nombre_carrera ASC";
$colecciones = $pdo->query($sqlCarreras)->fetchAll();

// Consultar total de libros que NO son académicos (Ocio/Literatura)
// Usamos la columna 'categoria' para distinguir el material recreativo
$sqlOcio = "SELECT COUNT(*) FROM libros WHERE categoria != 'academico'";
$totalOcio = $pdo->query($sqlOcio)->fetchColumn();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colecciones | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/colecciones.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

    <div class="top-bar">
        <div class="user-brand">
            <a href="home.php" class="back-link">
                <i class="bi bi-arrow-left-circle"></i> 
                <span>Volver al Inicio</span>
            </a>
        </div>
    </div>

    <div class="container" style="margin-top: 40px;">
        
        <header class="header-intro">
            <h2 class="section-title">
                <i class="bi bi-collection-fill"></i> Explorar Colecciones
            </h2>
            <p>
                Navega por el acervo bibliográfico organizado por especialidad académica y áreas de interés general.
            </p>
        </header>

        <div class="grid-colecciones">
            
            <?php if($totalOcio > 0): ?>
            <a href="home.php?filtro=ocio" class="card-coleccion ocio-card">
                <div class="card-content">
                    <i class="bi bi-stars"></i>
                    <h3>Interés General y Ocio</h3>
                    <p>Novelas, literatura, ciencia ficción y desarrollo personal para tus momentos de descanso.</p>
                </div>
                <div class="badge-count">
                    <i class="bi bi-bookmark-star"></i> 
                    <?php echo $totalOcio; ?> Títulos
                </div>
            </a>
            <?php endif; ?>

            <?php foreach($colecciones as $col): ?>
            <a href="home.php?carrera=<?php echo $col['id']; ?>" class="card-coleccion">
                <div class="card-content">
                    <i class="bi bi-mortarboard-fill"></i>
                    <h3><?php echo htmlspecialchars($col['nombre_carrera']); ?></h3>
                    <p>Recursos bibliográficos especializados y material de apoyo para esta ingeniería o licenciatura.</p>
                </div>
                <div class="badge-count">
                    <i class="bi bi-bookshelf"></i> 
                    <?php echo $col['total_libros']; ?> Libros
                </div>
            </a>
            <?php endforeach; ?>

            <?php if(empty($colecciones) && $totalOcio == 0): ?>
            <div class="empty-state-container" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <i class="bi bi-archive" style="font-size: 4rem; color: #ccc;"></i>
                <p style="color: #888; margin-top: 20px;">Aún no se han categorizado libros en el sistema.</p>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <footer style="text-align: center; padding: 40px; color: #aaa; font-size: 0.8rem;">
        &copy; <?php echo date('Y'); ?> INBOOK - Instituto Tecnológico Superior del Sur de Guanajuato
    </footer>

</body>
</html>