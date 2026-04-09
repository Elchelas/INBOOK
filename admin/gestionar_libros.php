<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// Lógica para eliminar un libro
if (isset($_GET['eliminar'])) {
    $id_del = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->execute([$id_del]);
    header("Location: gestionar_libros.php?msg=eliminado");
    exit();
}

// Consulta de libros con el nombre de su carrera
$sql = "SELECT l.*, c.nombre_carrera 
        FROM libros l 
        LEFT JOIN carreras c ON l.carrera_id = c.id 
        ORDER BY l.id DESC";
$libros = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_tables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-bookshelf"></i>
            <b>INVENTARIO BIBLIOGRÁFICO</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-speedometer2"></i> Volver al Panel
        </a>
    </div>

    <div class="container mt-5">
        <div class="table-header-actions">
            <div>
                <h2>Acervo Digital</h2>
                <p class="subtitle">Administra los títulos disponibles en la plataforma.</p>
            </div>
            <a href="agregar_libro.php" class="btn-add-new">
                <i class="bi bi-plus-lg"></i> Registrar Nuevo Libro
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert-success-custom animate-slide-in">
                <i class="bi bi-check-circle-fill"></i> Operación completada con éxito.
            </div>
        <?php endif; ?>

        <div class="table-responsive-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="80">Portada</th>
                        <th>Información del Título</th>
                        <th>Origen Académico</th>
                        <th>Clasificación</th>
                        <th width="150" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($libros as $l): ?>
                    <tr>
                        <td>
                            <div class="img-wrapper">
                                <img src="../auth/ver_binario.php?id=<?php echo $l['id']; ?>" alt="Miniatura">
                            </div>
                        </td>
                        <td>
                            <div class="title-cell">
                                <span class="book-title"><?php echo htmlspecialchars($l['titulo']); ?></span>
                                <span class="book-author"><?php echo htmlspecialchars($l['autor']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="academic-cell">
                                <span class="career-name"><?php echo htmlspecialchars($l['nombre_carrera'] ?? 'Uso General'); ?></span>
                                <span class="semester-badge"><?php echo $l['semestre_sugerido']; ?>° Semestre</span>
                            </div>
                        </td>
                        <td>
                            <span class="category-tag <?php echo $l['categoria']; ?>">
                                <?php echo ($l['categoria'] == 'academico') ? '<i class="bi bi-journal-bookmark"></i> Académico' : '<i class="bi bi-controller"></i> Ocio'; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-btns-group">
                                <a href="editar_libro.php?id=<?php echo $l['id']; ?>" class="btn-action edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="gestionar_libros.php?eliminar=<?php echo $l['id']; ?>" 
                                   class="btn-action delete" 
                                   title="Eliminar"
                                   onclick="return confirm('¿Confirma que desea eliminar permanentemente este libro del sistema?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>