<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Consultar los libros favoritos con JOIN para obtener datos completos
$sql = "SELECT l.*, f.fecha_agregado 
        FROM libros l 
        INNER JOIN favoritos f ON l.id = f.libro_id 
        WHERE f.usuario_id = ? 
        ORDER BY f.fecha_agregado DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$mis_libros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Estante | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/estante.css">
    <link rel="stylesheet" href="../assets/css/home_alumno.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" style="color:white; text-decoration:none;">
            <i class="bi bi-arrow-left-circle"></i> Regresar al Inicio
        </a>
    </div>
</div>

<div class="estante-header">
    <div class="container">
        <h1><i class="bi bi-bookmarks-fill"></i> Mi Estante Personal</h1>
        <p>Gestiona tu colección de lecturas guardadas y recursos de consulta rápida.</p>
    </div>
</div>

<div class="container">
    <?php if (count($mis_libros) > 0): ?>
        <div class="book-shelf"> <?php foreach ($mis_libros as $libro): ?>
                <div class="book-item">
                    <div class="cover-wrapper">
                        <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada">
                        <div class="hover-info">
                            <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" style="color:white; text-decoration:none;">
                                <i class="bi bi-eye"></i> Leer
                            </a>
                        </div>
                    </div>
                    <div class="book-data">
                        <span class="b-title"><?php echo htmlspecialchars($libro['titulo']); ?></span>
                        <span class="b-author"><?php echo htmlspecialchars($libro['autor']); ?></span>
                        
                        <div class="actions-estante">
                            <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="btn-read" style="display:block; text-align:center; margin-top:10px;">
                                <i class="bi bi-book"></i> Abrir
                            </a>
                            <a href="quitar_favorito.php?id=<?php echo $libro['id']; ?>" 
                               class="btn-remove-fav" 
                               onclick="return confirm('¿Deseas quitar este libro de tu estante?')">
                                <i class="bi bi-trash"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-journal-x" style="font-size: 4rem; color: #ddd;"></i>
            <h3 style="margin-top:20px; color:#555;">Tu estante está esperando ser llenado</h3>
            <p style="color:#888;">Aún no has guardado ningún libro en tus favoritos.</p>
            <a href="home.php" class="btn-primary" style="display:inline-block; margin-top:25px; padding: 12px 30px;">
                <i class="bi bi-search"></i> Explorar Catálogo
            </a>
        </div>
    <?php endif; ?>
</div>

<footer style="text-align: center; padding: 60px; color: #bbb; font-size: 0.85rem;">
    &copy; <?php echo date('Y'); ?> INBOOK ITSUR - Tu biblioteca digital.
</footer>

</body>
</html>