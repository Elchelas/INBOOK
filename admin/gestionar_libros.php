<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// Lógica para eliminar libro
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    $stmtDel = $pdo->prepare("DELETE FROM libros WHERE id = ?");
    $stmtDel->execute([$id_eliminar]);
    header("Location: gestionar_libros.php?msg=eliminado");
}

// Obtener todos los libros
$libros = $pdo->query("SELECT l.*, c.nombre_carrera FROM libros l LEFT JOIN carreras c ON l.carrera_id = c.id ORDER BY l.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Gestionar Inventario | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: var(--dark-bg); color: white; }
        .img-tabla { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; }
        .btn-delete { background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; }
        .btn-delete:hover { background: #a71d2a; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><b>ADMIN</b> | Gestión de Biblioteca</div>
        <a href="dashboard.php" style="color:white;">Volver al Panel</a>
    </div>

    <div class="container">
        <h2>Inventario de Libros</h2>
        <p>Aquí puedes ver, editar o eliminar los libros almacenados en la base de datos.</p>

        <?php if(isset($_GET['msg'])) echo "<p style='color:red; font-weight:bold;'>Libro eliminado correctamente.</p>"; ?>

        <table>
            <thead>
                <tr>
                    <th>Portada</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Carrera / Semestre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($libros as $l): ?>
                <tr>
                    <td>
                        <img src="../ver_portada.php?id=<?php echo $l['id']; ?>" class="img-tabla">
                    </td>
                    <td><?php echo $l['titulo']; ?></td>
                    <td><?php echo $l['autor']; ?></td>
                    <td><?php echo ucfirst($l['categoria']); ?></td>
                    <td>
                        <?php echo $l['nombre_carrera'] ?? 'N/A'; ?> 
                        <?php echo $l['semestre_sugerido'] ? "({$l['semestre_sugerido']}°)" : ""; ?>
                    </td>
                    <td>
                        <a href="gestionar_libros.php?eliminar=<?php echo $l['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('¿Estás seguro de eliminar este libro? Esta acción no se puede deshacer.')">
                           Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>