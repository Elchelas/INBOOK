<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $categoria = $_POST['categoria'];
    $carrera_id = !empty($_POST['carrera_id']) ? $_POST['carrera_id'] : null;
    $semestre = !empty($_POST['semestre']) ? $_POST['semestre'] : null;

    // VALIDACIÓN Y CARGA DE IMAGEN
    if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
        // Leemos el contenido del archivo temporal como una cadena de bits
        $datos_imagen = file_get_contents($_FILES['portada']['tmp_name']);

        $sql = "INSERT INTO libros (titulo, autor, categoria, carrera_id, semestre_sugerido, portada) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Usamos bindParam para asegurar que el BLOB se envíe correctamente
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $autor);
        $stmt->bindParam(3, $categoria);
        $stmt->bindParam(4, $carrera_id);
        $stmt->bindParam(5, $semestre);
        $stmt->bindParam(6, $datos_imagen, PDO::PARAM_LOB); // <--- Clave para imágenes
        
        if($stmt->execute()) {
            echo "<p style='color:green'>Libro guardado con éxito en la Base de Datos.</p>";
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título" required>
    <input type="file" name="portada" accept="image/*" required>
    <button type="submit">Subir Libro e Imagen</button>
</form>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Agregar Libro | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <a href="dashboard.php" class="btn btn-secondary mb-3">Volver</a>
        <div class="card shadow">
            <div class="card-header bg-dark text-white"><h5>Nuevo Libro</h5></div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Autor</label>
                            <input type="text" name="autor" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Categoría</label>
                            <select name="categoria" class="form-select" id="cat_select" required>
                                <option value="academico">Académico</option>
                                <option value="ocio">Ocio / Literatura</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Carrera (Solo académicos)</label>
                            <select name="carrera_id" class="form-select">
                                <option value="">N/A</option>
                                <?php foreach($carreras as $c): ?>
                                    <option value="<?=$c['id']?>"><?=$c['nombre_carrera']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Semestre</label>
                            <input type="number" name="semestre" class="form-control" min="1" max="10">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Imagen de Portada</label>
                        <input type="file" name="portada" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Libro</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>