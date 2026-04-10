<?php
require_once '../config/db.php';

// Consulta de carreras para el select
$stmtC = $pdo->query("SELECT id, nombre_carrera FROM carreras ORDER BY nombre_carrera ASC");
$carreras = $stmtC->fetchAll();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $categoria = $_POST['categoria'];
    
    // Lógica para Género y Datos Académicos
    $genero = ($categoria === 'ocio') ? $_POST['genero'] : null;
    $carrera_id = ($categoria === 'academico' && !empty($_POST['carrera_id'])) ? $_POST['carrera_id'] : null;
    $semestre = ($categoria === 'academico' && !empty($_POST['semestre'])) ? $_POST['semestre'] : null;

    if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
        $datos_imagen = file_get_contents($_FILES['portada']['tmp_name']);

        // Insertamos incluyendo la nueva columna 'genero'
        $sql = "INSERT INTO libros (titulo, autor, categoria, genero, carrera_id, semestre_sugerido, portada) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $autor);
        $stmt->bindParam(3, $categoria);
        $stmt->bindParam(4, $genero);
        $stmt->bindParam(5, $carrera_id);
        $stmt->bindParam(6, $semestre);
        $stmt->bindParam(7, $datos_imagen, PDO::PARAM_LOB);
        
        if($stmt->execute()) {
            $mensaje = "Libro guardado con éxito en la Base de Datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro | Panel Administrativo</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_forms.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-play-btn-fill"></i>
            <b>GESTIÓN MULTIMEDIA</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-house-door"></i> Panel Principal
        </a>
    </div>

    <div class="container mt-5">
        <?php if($mensaje): ?>
            <div class="alert-success-custom">
                <i class="bi bi-check-circle-fill"></i> <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <h2><i class="bi bi-book-half"></i> Registrar Nuevo Libro</h2>
            
            <form method="POST" enctype="multipart/form-data" class="custom-form">
                <div class="row">
                    <div class="form-group">
                        <label><i class="bi bi-tag-fill"></i> Título del Libro</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Estructura de Datos" required>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-person-fill"></i> Autor(es)</label>
                        <input type="text" name="autor" class="form-control" placeholder="Nombre del autor" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Categoría del Recurso</label>
                        <select name="categoria" id="categoriaSelect" class="form-control" onchange="toggleFields()" required>
                            <option value="academico">Académico (ITSUR)</option>
                            <option value="ocio">Ocio / Lectura Ligera</option>
                        </select>
                    </div>

                    <div class="form-group" id="generoGroup" style="display: none;">
                        <label>Género Literario</label>
                        <select name="genero" class="form-control">
                            <option value="">Selecciona un género...</option>
                            <option value="terror">Terror / Suspenso</option>
                            <option value="ciencia_ficcion">Ciencia Ficción</option>
                            <option value="aventura">Aventura</option>
                            <option value="romance">Romance</option>
                            <option value="fantasia">Fantasía</option>
                        </select>
                    </div>
                </div>

                <div id="academicFields" class="row">
                    <div class="form-group">
                        <label>Carrera Destino</label>
                        <select name="carrera_id" class="form-control">
                            <option value="">Todas las carreras...</option>
                            <?php foreach ($carreras as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre_carrera']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Semestre Sugerido</label>
                        <input type="number" name="semestre" class="form-control" min="1" max="12" placeholder="Ej: 4">
                    </div>
                </div>

                <div class="preview-box" onclick="document.getElementById('portada_input').click()">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: var(--itsur-blue);"></i>
                    <p style="margin: 10px 0; font-weight: 600;">Arrastra o selecciona la portada</p>
                    <input type="file" name="portada" id="portada_input" accept="image/*" style="display:none;" required>
                    <div id="file-name-display" style="margin-top: 10px; font-size: 0.85rem; color: var(--itsur-blue-dark);"></div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Guardar en Biblioteca
                    </button>
                    <a href="dashboard.php" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFields() {
            const categoria = document.getElementById('categoriaSelect').value;
            const generoGroup = document.getElementById('generoGroup');
            const academicFields = document.getElementById('academicFields');

            if (categoria === 'ocio') {
                generoGroup.style.display = 'block';
                academicFields.style.display = 'none';
            } else {
                generoGroup.style.display = 'none';
                academicFields.style.display = 'flex';
            }
        }

        document.getElementById('portada_input').addEventListener('change', function(e){
            if(e.target.files.length > 0){
                var fileName = e.target.files[0].name;
                document.getElementById('file-name-display').innerText = "Archivo listo: " + fileName;
            }
        });
    </script>
</body>
</html>