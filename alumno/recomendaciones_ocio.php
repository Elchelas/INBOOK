<?php
session_start();
require_once '../config/db.php';

$usuario_id = $_SESSION['user_id'];

// 1. Consultar qué género eligió el alumno en el test
$stmtPref = $pdo->prepare("SELECT genero_favorito FROM preferencias WHERE usuario_id = ?");
$stmtPref->execute([$usuario_id]);
$pref = $stmtPref->fetch();

if (!$pref) {
    header("Location: test_ocio.php"); // Si no ha hecho el test, mandarlo allá
    exit();
}

// 2. Buscar libros de ocio que coincidan con ese género
// Nota: Para que esto funcione, tus libros de ocio en la DB deben tener 
// el nombre del género en la columna 'autor' o una nueva columna 'genero'
$genero = $pref['genero_favorito'];
$stmtLibros = $pdo->prepare("SELECT * FROM libros WHERE categoria = 'ocio' AND (titulo LIKE ? OR autor LIKE ?)");
$stmtLibros->execute(["%$genero%", "%$genero%"]);
$librosOcio = $stmtLibros->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Tu momento de Ocio | ITSUR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="top-bar">
        <div><b>ITSUR</b> | Recomendaciones de Ocio</div>
        <a href="home.php" style="color:white; text-decoration:none;">Volver</a>
    </div>

    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="color: var(--primary-color);">Seleccionado para ti: <?php echo ucfirst($genero); ?></h2>
            <p>Basado en tu test de personalidad, creemos que estos libros te encantarán.</p>
        </div>

        <div class="book-grid">
            <?php if(count($librosOcio) > 0): ?>
                <?php foreach($librosOcio as $libro): ?>
                    <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="book-card">
                            <img src="ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada" style="width: 150px; height: 200px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <p><b><?php echo $libro['titulo']; ?></b></p>
                            <small><?php echo $libro['autor']; ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                    <p>Aún no tenemos libros de <b><?php echo $genero; ?></b>, pero estamos trabajando en ello.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="test_ocio.php" style="color: var(--primary-color);">¿Quieres cambiar tus preferencias? Repite el test aquí.</a>
        </div>
    </div>
</body>
</html>