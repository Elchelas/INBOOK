<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// 1. Obtener datos del alumno (carrera y semestre)
$stmt = $pdo->prepare("SELECT u.*, c.nombre_carrera FROM usuarios u JOIN carreras c ON u.carrera_id = c.id WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$alumno = $stmt->fetch();
// ... después de obtener los datos del alumno ...
$resultados = null;
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $stmtS = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ?");
    $stmtS->execute([$search, $search]);
    $resultados = $stmtS->fetchAll();
}
// 2. Libros recomendados (Misma carrera y semestre)
$sqlRec = "SELECT * FROM libros WHERE carrera_id = ? AND semestre_sugerido = ? AND categoria = 'academico' LIMIT 5";
$stmtRec = $pdo->prepare($sqlRec);
$stmtRec->execute([$alumno['carrera_id'], $alumno['semestre']]);
$recomendados = $stmtRec->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Biblioteca ITSUR</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; }
        /* Header estilo eLibro */
        .top-bar { background-color: #212529; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-main { background-color: #2c2e31; color: #adb5bd; padding: 10px 20px; font-size: 0.9rem; }
        .nav-main span { margin-right: 20px; cursor: pointer; }
        
        /* Contenedores */
        .container { padding: 20px; }
        .section-title { border-bottom: 2px solid #6f42c1; padding-bottom: 5px; margin-bottom: 20px; }
        
        /* Tarjetas de Libros */
        .book-grid { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px; }
        .book-card { background: white; min-width: 150px; text-align: center; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 10px; }
        .book-card img { width: 100%; height: 200px; object-fit: cover; }
        .btn-ocio { background: #6f42c1; color: white; padding: 15px; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

<div class="top-bar">
    <div><b>ITSUR</b> | Instituto Tecnológico Superior del Sur de Guanajuato</div>
    <div>Hola, <?php echo $alumno['nombre']; ?> (<?php echo $alumno['nombre_carrera']; ?>) | <a href="../auth/logout.php" style="color:white;">Salir</a></div>
</div>

<div class="nav-main">
    <span>Inicio</span> <span>Colecciones</span> <span>Búsqueda Filtrada</span> <span>Mi Estante</span>
</div>
<div class="search-section" style="background: #f8f9fa; padding: 20px; text-align: center; border-bottom: 1px solid #ddd;">
    <form action="home.php" method="GET" style="max-width: 600px; margin: 0 auto; display: flex;">
        <input type="text" name="query" class="form-control" placeholder="Teclee aquí para realizar una búsqueda rápida..." 
               style="border-radius: 20px 0 0 20px; border: 1px solid #6f42c1; padding: 10px 20px; flex-grow: 1;">
        <button type="submit" style="border-radius: 0 20px 20px 0; border: none; background: #6f42c1; color: white; padding: 0 25px;">
            Buscar
        </button>
    </form>
</div>
<?php if ($resultados): ?>
    <div class="container">
        <h3 class="section-title">Resultados de tu búsqueda: "<?php echo htmlspecialchars($_GET['query']); ?>"</h3>
        <div class="book-grid">
            <?php foreach($libros as $libro): ?>
                <div class="book-card">
                    <img src="ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada" style="width: 150px; height: 200px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <p><b><?php echo $libro['titulo']; ?></b></p>
                    <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 12px;">Ver más</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <hr>
<?php endif; ?>
<?php
// Consulta para favoritos
$stmtFav = $pdo->prepare("SELECT l.* FROM libros l 
                          JOIN favoritos f ON l.id = f.libro_id 
                          WHERE f.usuario_id = ?");
$stmtFav->execute([$_SESSION['user_id']]);
$mis_favoritos = $stmtFav->fetchAll();
?>

<?php if($mis_favoritos): ?>
<div class="container">
    <h3 class="section-title">Mi Estante (Favoritos)</h3>
    <div class="book-grid">
        <?php foreach($mis_favoritos as $fav): ?>
            <div class="book-card">
                <img src="ver_binario.php?t=libros&id=<?php echo $fav['id']; ?>" alt="Portada" style="width: 150px; height: 200px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <p><b><?php echo $fav['titulo']; ?></b></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<div class="container">
    <h3 class="section-title">Recomendados para tu Carrera (Semestre <?php echo $alumno['semestre']; ?>)</h3>
    <div class="book-grid">
        <?php foreach($libros as $libro): ?>
            <div class="book-card">
                <img src="ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada" style="width: 150px; height: 200px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <p><b><?php echo $libro['titulo']; ?></b></p>
                <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 12px;">Ver más</a>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>
    
    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; margin-top: 20px;">
        <h3>¿Cansado de estudiar?</h3>
        <p>Descubre libros de literatura, misterio o ciencia ficción basados en tu personalidad.</p>
        <a href="test_ocio.php" class="btn-ocio">¡Recomiéndame algo de ocio!</a>
    </div>
</div>

</body>
</html>