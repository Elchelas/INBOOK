<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

$usuario_id = $_SESSION['user_id'];

// 1. Consultar el género favorito guardado tras el test
$stmtPref = $pdo->prepare("SELECT genero_favorito FROM preferencias WHERE usuario_id = ?");
$stmtPref->execute([$usuario_id]);
$pref = $stmtPref->fetch();

// Si no ha hecho el test, lo enviamos a resolverlo
if (!$pref) {
    header("Location: test_ocio.php");
    exit();
}

$genero = $pref['genero_favorito'];

// 2. Buscar libros de ocio que coincidan con ese género
// Se busca en una columna 'genero' (recomendado) o 'categoria_especifica'
$stmtLibros = $pdo->prepare("SELECT * FROM libros 
                             WHERE categoria = 'ocio' 
                             AND (genero = ? OR titulo LIKE ? OR autor LIKE ?)
                             ORDER BY RAND() LIMIT 8"); // RAND() para que siempre vea algo nuevo
$stmtLibros->execute([$genero, "%$genero%", "%$genero%"]);
$librosOcio = $stmtLibros->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu momento de Ocio | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/ocio.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" class="back-link">
            <i class="bi bi-house-door"></i> Regresar al Inicio
        </a>
    </div>
</div>

<div class="ocio-hero">
    <div class="container">
        <div class="hero-content">
            <span class="badge-pref">Basado en tu personalidad</span>
            <h1>Especialmente para ti: <span><?php echo ucfirst($genero); ?></span></h1>
            <p>Sabemos que después de las clases necesitas un respiro. Aquí tienes lecturas que encajan con tu estilo.</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="recommendations-wrapper">
        <?php if(count($librosOcio) > 0): ?>
            <div class="ocio-grid">
                <?php foreach($librosOcio as $libro): ?>
                    <div class="ocio-item">
                        <div class="ocio-cover">
                            <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada">
                            <div class="ocio-overlay">
                                <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="btn-play">
                                    <i class="bi bi-book-half"></i> Leer Ahora
                                </a>
                            </div>
                        </div>
                        <div class="ocio-info">
                            <h4><?php echo htmlspecialchars($libro['titulo']); ?></h4>
                            <p><?php echo htmlspecialchars($libro['autor']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-ocio">
                <i class="bi bi-emoji-smile-upside-down"></i>
                <h3>¡Vaya! Por ahora no hay libros de <?php echo $genero; ?></h3>
                <p>Nuestros bibliotecarios están añadiendo contenido nuevo cada semana. ¡Vuelve pronto!</p>
                <a href="home.php" class="btn-return">Explorar material académico</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="test-footer">
        <p>¿Tus gustos han cambiado?</p>
        <a href="test_ocio.php" class="btn-retry">Repetir el Test de Personalidad</a>
    </div>
</div>

</body>
</html>