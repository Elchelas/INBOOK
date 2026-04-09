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

    $sql = "UPDATE libros SET titulo=?, autor=?, carrera_id=?, semestre_sugerido=?, categoria=?";
    $params = [$titulo, $autor, $carrera_id, $semestre, $categoria];

    if (!empty($_FILES['portada']['tmp_name'])) {
        $portada = file_get_contents($_FILES['portada']['tmp_name']);
        $sql .= ", portada=?";
        $params[] = $portada;
    }

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro | Admin INBOOK</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_forms.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-pencil-square"></i>
            <b>EDITOR DE CONTENIDO</b> | ITSUR
        </div>
        <a href="gestionar_libros.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <div class="container mt-5">
        <div class="admin-card">
            <div class="card-header-edit">
                <h2>Modificar Información</h2>
                <span class="badge-edit">Libro ID: #<?php echo $id; ?></span>
            </div>
            <p class="subtitle-edit">Estás editando: <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong></p>

            <form method="POST" enctype="multipart/form-data" class="custom-form">
                <div class="row">
                    <div class="form-group">
                        <label><i class="bi bi-bookmark-fill"></i> Título del Libro</label>
                        <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-person-badge"></i> Autor</label>
                        <input type="text" name="autor" class="form-control" value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group" style="flex: 2;">
                        <label><i class="bi bi-mortarboard-fill"></i> Carrera Destino</label>
                        <select name="carrera_id" class="form-select" required>
                            <option value="" <?php echo ($libro['carrera_id'] == null) ? 'selected' : ''; ?>>Acervo de uso común</option>
                            <?php foreach($carreras as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $libro['carrera_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-hash"></i> Semestre</label>
                        <input type="number" name="semestre_sugerido" class="form-control" value="<?php echo $libro['semestre_sugerido']; ?>" min="1" max="12" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="bi bi-collection"></i> Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="academico" <?php echo ($libro['categoria'] == 'academico') ? 'selected' : ''; ?>>📖 Académico</option>
                        <option value="literatura" <?php echo ($libro['categoria'] == 'literatura') ? 'selected' : ''; ?>>🎭 Literatura / Ocio</option>
                    </select>
                </div>

                <div class="edit-media-section">
                    <div class="current-preview">
                        <small>Portada Actual</small>
                        <img src="../auth/ver_binario.php?id=<?php echo $libro['id']; ?>" alt="Portada">
                    </div>
                    <div class="upload-new">
                        <label><i class="bi bi-image"></i> Remplazar Portada (Opcional)</label>
                        <input type="file" name="portada" class="form-control" accept="image/*">
                        
                        <label class="mt-3"><i class="bi bi-file-earmark-pdf"></i> Actualizar PDF (Opcional)</label>
                        <input type="file" name="archivo_pdf" class="form-control" accept="application/pdf">
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Aplicar Cambios
                    </button>
                    <a href="gestionar_libros.php" class="btn-cancel">Descartar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>