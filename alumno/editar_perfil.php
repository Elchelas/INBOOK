<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['user_id'];
$mensaje = "";

// 1. Cargar datos actuales del alumno
$stmt = $pdo->prepare("SELECT u.*, c.nombre_carrera 
                       FROM usuarios u 
                       LEFT JOIN carreras c ON u.carrera_id = c.id 
                       WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

/**
 * 2. PROCESAR ACTUALIZACIÓN
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $semestre = $_POST['semestre'];

    // Lógica para la foto si se subió una nueva
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto_binaria = file_get_contents($_FILES['foto']['tmp_name']);
        $sql = "UPDATE usuarios SET nombre = ?, semestre = ?, foto = ? WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([$nombre, $semestre, $foto_binaria, $user_id]);
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, semestre = ? WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([$nombre, $semestre, $user_id]);
    }
    
    $mensaje = "¡Perfil actualizado con éxito!";
    // Recargar datos actualizados
    header("Refresh:2");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="top-bar">
    <div class="user-brand">
        <a href="home.php" style="color:white; text-decoration:none;">
            <i class="bi bi-house-door"></i> Regresar al Inicio
        </a>
    </div>
</div>

<div class="container" style="max-width: 800px; margin-top: 50px;">
    
    <?php if($mensaje): ?>
        <div class="alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="profile-card">
        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="profile-header">
                <div class="avatar-upload">
                    <div class="avatar-edit">
                        <input type='file' name="foto" id="imageUpload" accept=".png, .jpg, .jpeg" />
                        <label for="imageUpload"><i class="bi bi-pencil"></i></label>
                    </div>
                    <div class="avatar-preview">
                        <img id="imagePreview" src="../auth/ver_binario.php?t=usuarios&id=<?php echo $user_id; ?>&c=foto" alt="Foto Perfil">
                    </div>
                </div>
                <h2><?php echo htmlspecialchars($user['nombre']); ?></h2>
                <span class="badge-role">Estudiante ITSUR</span>
            </div>

            <div class="profile-body">
                <div class="info-grid">
                    <div class="form-group">
                        <label><i class="bi bi-person"></i> Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="bi bi-envelope"></i> Correo Institucional</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['correo']); ?>" disabled>
                        <small>El correo no puede ser modificado.</small>
                    </div>

                    <div class="form-group">
                        <label><i class="bi bi-mortarboard"></i> Carrera</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['nombre_carrera']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label><i class="bi bi-123"></i> Semestre Actual</label>
                        <select name="semestre" class="form-control">
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($user['semestre'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>° Semestre
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="actions" style="margin-top: 30px; text-align: center;">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Vista previa de la imagen antes de subirla
    document.getElementById("imageUpload").onchange = function () {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("imagePreview").src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    };
</script>

</body>
</html>