<?php
session_start();
// Validación de rango: Solo SuperAdmin del ITSUR puede entrar
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
    <title>Gestión de Libros | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .admin-table th { background: #212529; color: white; }
        .admin-table tr:hover { background: #f8f9fa; }
        .img-preview { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; }
        .btn-add { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="user-info">
        <b>ADMINISTRACIÓN DE ACERVO</b> | 
        <a href="dashboard.php" style="color:white; text-decoration:none;">Panel Principal</a>
    </div>
</div>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Inventario de Libros</h2>
        <a href="agregar_libro.php" class="btn-add">+ Agregar Nuevo Libro</a>
    </div>

    <?php if(isset($_GET['msg'])) echo "<p style='color:green; font-weight:bold;'>Acción realizada con éxito.</p>"; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Portada</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Carrera / Semestre</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($libros as $l): ?>
            <tr>
                <td>
                    <img src="../auth/ver_binario.php?id=<?php echo $l['id']; ?>" class="img-preview" alt="Miniatura">
                </td>
                <td><b><?php echo htmlspecialchars($l['titulo']); ?></b></td>
                <td><?php echo htmlspecialchars($l['autor']); ?></td>
                <td>
                    <small><?php echo htmlspecialchars($l['nombre_carrera'] ?? 'General'); ?></small><br>
                    <span class="badge"><?php echo $l['semestre_sugerido']; ?>° Semestre</span>
                </td>
                <td>
                    <span style="text-transform: capitalize;"><?php echo $l['categoria']; ?></span>
                </td>
                <td>
                    <a href="editar_libro.php?id=<?php echo $l['id']; ?>" style="color: #007bff; text-decoration: none; margin-right: 10px;">Editar</a>
                    <a href="gestionar_libros.php?eliminar=<?php echo $l['id']; ?>" 
                       style="color: #dc3545; text-decoration: none;" 
                       onclick="return confirm('¿Seguro que deseas eliminar este libro?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>