<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// 1. Estadísticas Generales
$total_libros = $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
$total_alumnos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'alumno'")->fetchColumn();
$total_favoritos = $pdo->query("SELECT COUNT(*) FROM favoritos")->fetchColumn();

// 2. Libros más populares (Top 5 en favoritos)
$sql_top = "SELECT l.titulo, l.autor, COUNT(f.id) as conteo 
            FROM libros l 
            JOIN favoritos f ON l.id = f.libro_id 
            GROUP BY l.id 
            ORDER BY conteo DESC LIMIT 5";
$top_libros = $pdo->query($sql_top)->fetchAll();

// 3. Distribución por Carrera
$sql_carreras = "SELECT c.nombre_carrera, COUNT(u.id) as total_alumnos 
                 FROM carreras c 
                 LEFT JOIN usuarios u ON c.id = u.carrera_id 
                 GROUP BY c.id";
$repo_carreras = $pdo->query($sql_carreras)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Admin ITSUR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { color: var(--primary-color); font-size: 2rem; margin: 10px 0; }
        .report-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #555; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><b>ADMIN</b> | Reportes y Estadísticas</div>
        <a href="dashboard.php" style="color:white; text-decoration:none;">Volver al Panel</a>
    </div>

    <div class="container">
        <h2>Resumen del Sistema</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <p>Total Libros</p>
                <h3><?php echo $total_libros; ?></h3>
            </div>
            <div class="stat-card">
                <p>Alumnos Activos</p>
                <h3><?php echo $total_alumnos; ?></h3>
            </div>
            <div class="stat-card">
                <p>Libros en Estantes</p>
                <h3><?php echo $total_favoritos; ?></h3>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="report-section" style="flex: 1;">
                <h3>Libros más Populares</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Guardado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($top_libros as $libro): ?>
                        <tr>
                            <td><?php echo $libro['titulo']; ?></td>
                            <td><b><?php echo $libro['conteo']; ?> alumnos</b></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-section" style="flex: 1;">
                <h3>Usuarios por Carrera</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Carrera</th>
                            <th>Alumnos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($repo_carreras as $c): ?>
                        <tr>
                            <td><?php echo $c['nombre_carrera']; ?></td>
                            <td><?php echo $c['total_alumnos']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>