<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

$usuario_id = $_SESSION['user_id'];

// 1. Cargar datos del alumno
$stmtUser = $pdo->prepare("SELECT u.*, c.nombre_carrera 
                           FROM usuarios u 
                           LEFT JOIN carreras c ON u.carrera_id = c.id 
                           WHERE u.id = ?");
$stmtUser->execute([$usuario_id]);
$alumno = $stmtUser->fetch();

// 2. Lógica de Búsqueda Rápida
$query_busqueda = $_GET['query'] ?? '';
$resultados_busqueda = null;

if (!empty($query_busqueda)) {
    $busqueda = "%" . $query_busqueda . "%";
    $stmtB = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ? LIMIT 8");
    $stmtB->execute([$busqueda, $busqueda]);
    $resultados_busqueda = $stmtB->fetchAll();
}

// 3. Sistema de Recomendaciones y Filtros
$carrera_id_filtro = $_GET['carrera'] ?? null;
$filtro_ocio = $_GET['filtro'] ?? null;

if ($carrera_id_filtro) {
    // Filtrar por una carrera específica
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE carrera_id = ? ORDER BY titulo ASC");
    $stmt->execute([$carrera_id_filtro]);
} elseif ($filtro_ocio == 'ocio') {
    // Filtrar por libros de uso común (Literatura, etc)
    $stmt = $pdo->query("SELECT * FROM libros WHERE categoria != 'academico' ORDER BY RAND() LIMIT 12");
} else {
    // Recomendados por carrera y semestre del alumno (Default)
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE carrera_id = ? AND semestre_sugerido = ?");
    $stmt->execute([$alumno['carrera_id'], $alumno['semestre']]);
}
$libros_principales = $stmt->fetchAll();

// 4. Cargar "Mi Estante" (Favoritos)
$stmtFav = $pdo->prepare("SELECT l.* FROM libros l 
                          JOIN favoritos f ON l.id = f.libro_id 
                          WHERE f.usuario_id = ? LIMIT 6");
$stmtFav->execute([$usuario_id]);
$mis_favoritos = $stmtFav->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/home_alumno.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <i class="bi bi-book-half"></i> <span>INBOOK <b>ITSUR</b></span>
    </div>
    <div class="user-profile-zone">
        <span class="welcome-text">Hola, <b><?php echo explode(' ', $alumno['nombre'])[0]; ?></b></span>
        <a href="mi_perfil.php" class="avatar-link">
            <img src="../auth/ver_binario.php?t=usuarios&id=<?php echo $usuario_id; ?>&c=foto" class="mini-avatar">
        </a>
        <a href="../auth/logout.php" class="logout-icon" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>

<nav class="nav-main-tabs">
    <a href="home.php" class="active"><i class="bi bi-house-door"></i> Inicio</a>
    <a href="colecciones.php"><i class="bi bi-collection"></i> Colecciones</a>
    <a href="busqueda.php"><i class="bi bi-search"></i> Búsqueda Avanzada</a>
    <a href="mi_estante.php"><i class="bi bi-bookmark-heart"></i> Mi Estante</a>
</nav>

<div class="hero-search">
    <form action="home.php" method="GET" class="search-box-home">
        <input type="text" name="query" placeholder="Busca libros por título o autor..." value="<?php echo htmlspecialchars($query_busqueda); ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="container main-content">

    <?php if (!empty($query_busqueda)): ?>
        <section class="home-section search-results">
            <h3 class="section-title"><i class="bi bi-stars"></i> Resultados para "<?php echo htmlspecialchars($query_busqueda); ?>"</h3>
            <div class="book-shelf">
                <?php if ($resultados_busqueda): ?>
                    <?php foreach($resultados_busqueda as $libro): ?>
                        <div class="book-item">
                            <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="cover-wrapper">
                                <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada">
                                <div class="hover-info"><i class="bi bi-eye"></i> Ver más</div>
                            </a>
                            <div class="book-data">
                                <span class="b-title"><?php echo htmlspecialchars($libro['titulo']); ?></span>
                                <span class="b-author"><?php echo htmlspecialchars($libro['autor']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="bi bi-search-heart"></i>
                        <p>No encontramos nada similar. ¡Prueba con otra palabra!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if($mis_favoritos && empty($query_busqueda)): ?>
    <section class="home-section favorites-shelf">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-heart-fill text-danger"></i> Mi Estante Personal</h3>
            <a href="mi_estante.php">Ver todo</a>
        </div>
        <div class="book-shelf small">
            <?php foreach($mis_favoritos as $fav): ?>
                <div class="book-item tiny">
                    <a href="ver_libro.php?id=<?php echo $fav['id']; ?>">
                        <img src="../auth/ver_binario.php?t=libros&id=<?php echo $fav['id']; ?>" alt="Portada">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="home-section">
        <h3 class="section-title">
            <?php 
                if($filtro_ocio) echo "<i class='bi bi-controller'></i> Tiempo de Ocio";
                else echo "<i class='bi bi-mortarboard'></i> Recomendados para " . htmlspecialchars($alumno['nombre_carrera']);
            ?>
        </h3>
        
        <div class="book-shelf">
            <?php if (!empty($libros_principales)): ?>
                <?php foreach ($libros_principales as $libro): ?>
                    <div class="book-item">
                        <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="cover-wrapper">
                            <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada">
                            <div class="hover-info"><i class="bi bi-eye"></i> Detalles</div>
                        </a>
                        <div class="book-data">
                            <span class="b-title"><?php echo htmlspecialchars($libro['titulo']); ?></span>
                            <span class="b-author"><?php echo htmlspecialchars($libro['autor']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state-home">
                    <i class="bi bi-emoji-frown"></i>
                    <p>No hay libros registrados para tu semestre aún.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if(!$filtro_ocio): ?>
    <div class="ocio-banner">
        <div class="ocio-content">
            <h3>¿Un respiro del estudio?</h3>
            <p>Explora nuestro catálogo de novelas, ciencia ficción y desarrollo personal.</p>
            <a href="../alumno/recomendaciones_ocio.php" class="btn-ocio-link">Ver Lecturas de Ocio</a>
        </div>
        <img src="../assets/img/reading_relax.svg" alt="Relax" class="ocio-img">
    </div>
    <?php endif; ?>

</div>

</body>
</html>