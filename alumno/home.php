<?php
session_start();
require_once '../config/db.php';

// Redirigir si no hay sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];

// 1. Cargamos datos del alumno (Arregla error de la línea 53 y 106)
$stmtUser = $pdo->prepare("SELECT u.*, c.nombre_carrera 
                           FROM usuarios u 
                           LEFT JOIN carreras c ON u.carrera_id = c.id 
                           WHERE u.id = ?");
$stmtUser->execute([$usuario_id]);
$alumno = $stmtUser->fetch();

// 2. Lógica de Búsqueda (Arregla error de la línea 70 y 72)
$resultados_busqueda = null;
$query_busqueda = $_GET['query'] ?? ''; // Evita el "Undefined array key"

if (!empty($query_busqueda)) {
    $busqueda = "%" . $query_busqueda . "%";
    $stmtB = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ?");
    $stmtB->execute([$busqueda, $busqueda]);
    $resultados_busqueda = $stmtB->fetchAll();
}

// 3. Libros Recomendados (Arregla error de la línea 111)
$carrera_id = $alumno['carrera_id'] ?? 0;
$semestre   = $alumno['semestre'] ?? 1;

$stmtRec = $pdo->prepare("SELECT * FROM libros WHERE carrera_id = ? AND semestre_sugerido = ?");
$stmtRec->execute([$carrera_id, $semestre]);
$libros = $stmtRec->fetchAll(); // Aquí se define $libros para el foreach
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Biblioteca ITSUR</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="top-bar">
    <div class="user-info">
        Hola, <b><?php echo htmlspecialchars($alumno['nombre']); ?></b>
        <br>
        <small>
            <?php echo htmlspecialchars($alumno['nombre_carrera']); ?> (Semestre <?php echo $alumno['semestre']; ?>)
        </small>
        <a href="../auth/logout.php">Salir</a>
    </div>
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
<?php if (!empty($query_busqueda)): ?>
    <div class="container">
        <h3>Resultados para: "<?php echo htmlspecialchars($query_busqueda); ?>"</h3>
        <div class="book-grid">
            <?php if ($resultados_busqueda): ?>
                <?php foreach($resultados_busqueda as $libro): ?>
                    <div class="book-card">
                        <img src="../auth/ver_binario.php?id=<?php echo $libro['id']; ?>" alt="Portada">
                        <p><b><?php echo htmlspecialchars($libro['titulo']); ?></b></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron libros que coincidan.</p>
            <?php endif; ?>
        </div>
    </div>
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
                <img src="../auth/ver_binario.php?t=libros&id=<?php echo $fav['id']; ?>" alt="Portada" style="width: 150px; height: 200px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <p><b><?php echo $fav['titulo']; ?></b></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<div class="container">
    <h3 class="section-title">
        Recomendados para <?php echo htmlspecialchars($alumno['nombre_carrera']); ?> 
        (Semestre <?php echo $alumno['semestre']; ?>)
    </h3>
    <div class="book-grid">
        <?php if (!empty($libros)): ?>
            <?php foreach ($libros as $libro): ?>
                <div class="book-card">
                    <a href="ver_libro.php?id=<?php echo $libro['id']; ?>">
                        <img src="../auth/ver_binario.php?id=<?php echo $libro['id']; ?>" alt="Portada">
                    </a>
                    <p><b><?php echo htmlspecialchars($libro['titulo']); ?></b></p>
                    <small><?php echo htmlspecialchars($libro['autor']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="padding: 20px; color: #666;">Aún no hay libros cargados para tu semestre actual.</p>
        <?php endif; ?>
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