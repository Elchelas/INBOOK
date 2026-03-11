<?php
session_start();
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: home.php"); exit(); }

// Obtener información del libro y su carrera asociada
$stmt = $pdo->prepare("SELECT l.*, c.nombre_carrera FROM libros l 
                       LEFT JOIN carreras c ON l.carrera_id = c.id 
                       WHERE l.id = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch();

if (!$libro) { echo "Libro no encontrado."; exit(); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title><?php echo $libro['titulo']; ?> | Detalle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="top-bar">
        <div><b>ITSUR</b> | Detalle del Libro</div>
        <a href="home.php" style="color:white; text-decoration:none;">← Volver al inicio</a>
    </div>

    <div class="container" style="display: flex; gap: 40px; margin-top: 50px;">
        <div style="flex: 1; text-align: center;">
            <img src="../auth/ver_binario.php?t=libros&id=<?php echo $libro['id']; ?>" alt="Portada" 
                 style="width: 300px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
        </div>
        
        <div style="flex: 2;">
            <h1 style="color: var(--primary-color);"><?php echo $libro['titulo']; ?></h1>
            <p style="font-size: 1.2rem; color: #555;">Por: <b><?php echo $libro['autor']; ?></b></p>
            <hr>
            
            <div style="margin: 20px 0;">
                <p><strong>Categoría:</strong> <?php echo ucfirst($libro['categoria']); ?></p>
                <?php if($libro['categoria'] == 'academico'): ?>
                    <p><strong>Carrera:</strong> <?php echo $libro['nombre_carrera']; ?></p>
                    <p><strong>Semestre sugerido:</strong> <?php echo $libro['semestre_sugerido']; ?>°</p>
                <?php endif; ?>
            </div>

            <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 5px solid var(--primary-color);">
                <h4>Sinopsis / Descripción</h4>
                <p style="color: #666; line-height: 1.6;">
                    Este libro es una herramienta fundamental para el desarrollo académico en el área de 
                    <?php echo $libro['nombre_carrera'] ?? 'literatura general' ?>.
                </p>
            </div>

            <div style="margin-top: 20px;">
                <a href="agregar_favorito.php?id=<?php echo $libro['id']; ?>" 
                class="btn-primary" 
                style="background: #e83e8c; text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">
                ❤ Agregar a mi estante
                </a>
            </div>
        </div>
    </div>
</body>
</html>