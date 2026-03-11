<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: gestionar_libros.php"); exit(); }

// 1. Obtener datos actuales del libro
$stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch();

$carreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $carrera_id = $_POST['carrera_id'];
    $semestre = $_POST['semestre_sugerido'];
    $categoria = $_POST['categoria'];

    // Iniciar la consulta de actualización
    $sql = "UPDATE libros SET titulo=?, autor=?, carrera_id=?, semestre_sugerido=?, categoria=?";
    $params = [$titulo, $autor, $carrera_id, $semestre, $categoria];

    // 2. ¿Se subió una nueva portada?
    if (!empty($_FILES['portada']['tmp_name'])) {
        $portada = file_get_contents($_FILES['portada']['tmp_name']);
        $sql .= ", portada=?";
        $params[] = $portada;
    }

    // 3. ¿Se subió un nuevo PDF?
    if (!empty($_FILES['archivo_pdf']['tmp_name'])) {
        $pdf = file_get_contents($_FILES['archivo_pdf']['tmp_name']);
        $sql .= ", archivo_pdf=?";
        $params[] = $pdf;
    }

    $sql .= " WHERE id=?";
    $params[] = $id;

    $stmtUpdate = $pdo->prepare($sql);
    if ($stmtUpdate->execute($params)) {
        header("Location: gestionar_libros.php?msg=editado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Libro | Admin ITSUR</title>
    <link rel="stylesheet" href="../assets/css/editar_libro.css">
</head>
<body class="auth-container">
    <div class="auth-card" >
        <h2>Editar Información del Libro</h2>
        <p>Modificando: <b><?php echo htmlspecialchars($libro['titulo']); ?></b></p>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Título del Libro</label>
                <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
            </div>

            <div class="form-group">
                <label>Autor</label>
                <input type="text" name="autor" class="form-control" value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
            </div>

            <div class="row">
                <div class="form-group" style="flex: 2;">
                    <label>Carrera Destino</label>
                    <select name="carrera_id" class="form-control" required>
                        <option value="" <?php echo ($libro['carrera_id'] == null) ? 'selected' : ''; ?>>
                            Acervo de uso común
                        </option>

                        <?php foreach($carreras as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $libro['carrera_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Semestre</label>
                    <input type="number" name="semestre_sugerido" class="form-control" value="<?php echo $libro['semestre_sugerido']; ?>" min="1" max="12" required>
                </div>
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" class="form-control">
                    <option value="academico" <?php echo ($libro['categoria'] == 'academico') ? 'selected' : ''; ?>>Académico</option>
                    <option value="literatura" <?php echo ($libro['categoria'] == 'literatura') ? 'selected' : ''; ?>>Literatura / Ocio</option>
                </select>
            </div>

            <div class="portada">
                <label>Actualizar Portada (Opcional)</label>
                <input type="file" name="portada" class="form-control" accept="image/*">
                <small>Vista actual:</small><br>
                <img src="../auth/ver_binario.php?id=<?php echo $libro['id']; ?>">
            </div>

            <div class="guardar">
                <button type="submit" class="btn-primary" >Guardar Cambios</button>
                <a href="gestionar_libros.php" class="btn-primary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>