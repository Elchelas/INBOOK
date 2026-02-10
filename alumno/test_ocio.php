<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $genero = $_POST['genero'];
    $usuario_id = $_SESSION['user_id'];

    // Guardar o actualizar preferencia
    $stmt = $pdo->prepare("INSERT INTO preferencias (usuario_id, genero_favorito) 
                           VALUES (?, ?) ON DUPLICATE KEY UPDATE genero_favorito = ?");
    $stmt->execute([$usuario_id, $genero, $genero]);
    
    header("Location: home.php?msg=preferencias_guardadas");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Test de Ocio | ITSUR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 600px; border-top: 5px solid #6f42c1;">
            <div class="card-body">
                <h2 class="text-center">¿Qué te apetece leer hoy?</h2>
                <p class="text-muted text-center">Queremos recomendarte algo fuera de las clases.</p>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label"><b>Si pudieras vivir en un mundo ficticio, ¿cuál elegirías?</b></label>
                        <select name="genero" class="form-select" required>
                            <option value="">Selecciona una opción...</option>
                            <option value="terror">Un castillo abandonado lleno de misterios (Terror/Suspenso)</option>
                            <option value="ciencia_ficcion">Una estación espacial en el año 3000 (Ciencia Ficción)</option>
                            <option value="aventura">Una selva buscando tesoros perdidos (Aventura)</option>
                            <option value="romance">Una ciudad europea clásica (Romance)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn w-100 text-white" style="background: #6f42c1;">Guardar mis gustos</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>