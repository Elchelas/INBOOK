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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel Admin | ITSUR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Panel de Control - SuperAdmin</span>
        <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Libros en Sistema</h5>
                    <p class="card-text fs-2"><?php echo $totalLibros; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Alumnos Registrados</h5>
                    <p class="card-text fs-2"><?php echo $totalAlumnos; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Acciones Rápidas</div>
        <div class="card-body">
            <a href="agregar_libro.php" class="btn btn-outline-primary">Añadir Nuevo Libro</a>
            <a href="gestionar_usuarios.php" class="btn btn-outline-secondary">Gestionar Alumnos</a>
            <a href="reportes.php" class="btn btn-outline-info">Ver Reportes de Lectura</a>
        </div>
    </div>
</div>

</body>
</html>