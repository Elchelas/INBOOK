<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $genero = $_POST['genero'];
    $usuario_id = $_SESSION['user_id'];

    // Guardar o actualizar preferencia usando la sintaxis de duplicados de MySQL
    $stmt = $pdo->prepare("INSERT INTO preferencias (usuario_id, genero_favorito) 
                           VALUES (?, ?) ON DUPLICATE KEY UPDATE genero_favorito = ?");
    $stmt->execute([$usuario_id, $genero, $genero]);
    
    header("Location: recomendaciones_ocio.php?msg=preferencias_actualizadas");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Ocio | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/test_ocio.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" class="back-link">
            <i class="bi bi-arrow-left-circle"></i> Volver al inicio
        </a>
    </div>
</div>

<div class="container test-container">
    <div class="test-card">
        <div class="test-header">
            <div class="icon-circle">
                <i class="bi bi-controller"></i>
            </div>
            <h2>¿Qué te apetece leer hoy?</h2>
            <p>Responde esta pregunta y personalizaremos tu rincón de lectura fuera de las clases.</p>
        </div>

        <form method="POST" class="test-form">
            <div class="question-box">
                <label class="form-label">
                    <i class="bi bi-magic"></i> Si pudieras vivir en un mundo ficticio, ¿cuál elegirías?
                </label>
                
                <div class="select-wrapper">
                    <select name="genero" class="form-control custom-select" required>
                        <option value="" disabled selected>Selecciona tu destino...</option>
                        <option value="terror">Un castillo abandonado lleno de misterios (Terror)</option>
                        <option value="ciencia_ficcion">Una estación espacial en el año 3000 (Sci-Fi)</option>
                        <option value="aventura">Una selva buscando tesoros perdidos (Aventura)</option>
                        <option value="romance">Una ciudad europea clásica (Romance)</option>
                        <option value="fantasia">Un reino de dragones y hechicería (Fantasía)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Guardar mis gustos</span>
                <i class="bi bi-arrow-right-short"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>