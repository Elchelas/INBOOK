<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

// 1. Cargar carreras para el filtro
$carreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC")->fetchAll();

// 2. Lógica de Búsqueda Dinámica
$resultados = [];
$busqueda_activa = false;

// Verificamos si hay algún parámetro en la URL
if (!empty($_GET)) {
    $busqueda_activa = true;
    
    // Base de la consulta con JOIN para mostrar el nombre de la carrera en los resultados
    $sql = "SELECT l.*, c.nombre_carrera 
            FROM libros l 
            LEFT JOIN carreras c ON l.carrera_id = c.id 
            WHERE 1=1";
    $params = [];

    if (!empty($_GET['titulo'])) {
        $sql .= " AND l.titulo LIKE ?";
        $params[] = "%" . $_GET['titulo'] . "%";
    }
    if (!empty($_GET['autor'])) {
        $sql .= " AND l.autor LIKE ?";
        $params[] = "%" . $_GET['autor'] . "%";
    }
    if (!empty($_GET['carrera_id'])) {
        $sql .= " AND l.carrera_id = ?";
        $params[] = $_GET['carrera_id'];
    }
    if (!empty($_GET['semestre'])) {
        $sql .= " AND l.semestre_sugerido = ?";
        $params[] = $_GET['semestre'];
    }
    if (!empty($_GET['categoria'])) {
        $sql .= " AND l.categoria = ?";
        $params[] = $_GET['categoria'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Avanzada | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/busqueda_alumno.css">
    <link rel="stylesheet" href="../assets/css/home_alumno.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" style="color:white; text-decoration:none;">
            <i class="bi bi-arrow-left-circle"></i> Volver al Inicio
        </a>
    </div>
</div>

<div class="hero-search" style="padding: 40px 5% 80px;">
    <h1 style="color: white; margin-bottom: 10px;">Encuentra tu próximo recurso</h1>
    <p style="color: rgba(255,255,255,0.8);">Filtra por carrera, semestre o autor para una búsqueda precisa.</p>
</div>

<div class="container">
    <div class="filter-panel">
        <form method="GET" action="busqueda.php">
            <div class="filter-grid">
                <div class="form-group">
                    <label><i class="bi bi-fonts"></i> Título del Libro</label>
                    <input type="text" name="titulo" class="form-control" placeholder="Ej: Estructuras de Datos" value="<?php echo htmlspecialchars($_GET['titulo'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><i class="bi bi-person-badge"></i> Autor</label>
                    <input type="text" name="autor" class="form-control" placeholder="Ej: Robert Lafore" value="<?php echo htmlspecialchars($_GET['autor'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><i class="bi bi-mortarboard"></i> Carrera</label>
                    <select name="carrera_id" class="form-control">
                        <option value="">Todas las carreras</option>
                        <?php foreach($carreras as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo (isset($_GET['carrera_id']) && $_GET['carrera_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="bi bi-123"></i> Semestre</label>
                    <select name="semestre" class="form-control">
                        <option value="">Cualquier semestre</option>
                        <?php for($i=1; $i<=10; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['semestre']) && $_GET['semestre'] == $i) ? 'selected' : ''; ?>>
                                Semestre <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-search-advanced">
                <i class="bi bi-search"></i> Aplicar Filtros Inteligentes
            </button>
        </form>
    </div>

    <div class="results-section" style="margin-top: 40px; margin-bottom: 60px;">
        <?php if ($busqueda_activa): ?>
            <h3 class="results-count">
                <i class="bi bi-journal-check"></i> 
                Resultados encontrados: <b><?php echo count($resultados); ?></b>
            </h3>
            
            <div class="book-shelf">
                <?php foreach ($resultados as $libro): ?>
                    <div class="book-item">
                        <a href="ver_libro.php?id=<?php echo $libro['id']; ?>" class="cover-wrapper">
                            <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada">
                            <div class="hover-info"><i class="bi bi-eye"></i> Detalles</div>
                        </a>
                        <div class="book-data">
                            <span class="b-title"><?php echo htmlspecialchars($libro['titulo']); ?></span>
                            <span class="b-author"><?php echo htmlspecialchars($libro['autor']); ?></span>
                            <small style="color: var(--itsur-blue); font-weight: bold; font-size: 0.7rem; display: block; margin-top: 5px;">
                                <?php echo htmlspecialchars($libro['nombre_carrera'] ?? 'General'); ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($resultados) == 0): ?>
                <div class="empty-state-home" style="padding: 60px; background: #f9f9f9; border-radius: 15px; border: 2px dashed #ddd;">
                    <i class="bi bi-search-heart" style="font-size: 3rem; color: #ccc;"></i>
                    <p style="margin-top: 15px;">No encontramos libros con esos criterios específicos.</p>
                    <a href="busqueda.php" style="color: var(--itsur-blue); font-weight: bold;">Limpiar todos los filtros</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; color: #999; margin-top: 80px; padding: 40px;">
                <i class="bi bi-sliders" style="font-size: 4rem; opacity: 0.2;"></i>
                <p style="font-size: 1.1rem; margin-top: 20px;">Utiliza el panel superior para filtrar el catálogo por tus necesidades académicas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>