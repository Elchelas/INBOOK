<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php"); exit();
}
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: gestionar_usuarios.php"); exit(); }

// Obtener datos del usuario actual
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();

$carreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $carrera = $_POST['carrera_id'];
    $semestre = $_POST['semestre'];
    
    if (!empty($_POST['new_password'])) {
        $pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET nombre=?, correo=?, carrera_id=?, semestre=?, password=? WHERE id=?";
        $params = [$nombre, $correo, $carrera, $semestre, $pass, $id];
    } else {
        $sql = "UPDATE usuarios SET nombre=?, correo=?, carrera_id=?, semestre=? WHERE id=?";
        $params = [$nombre, $correo, $carrera, $semestre, $id];
    }

    $stmtUpd = $pdo->prepare($sql);
    if($stmtUpd->execute($params)) {
        header("Location: gestionar_usuarios.php?msg=actualizado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario | Admin INBOOK</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_forms.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-person-gear"></i>
            <b>CONTROL DE USUARIOS</b> | ITSUR
        </div>
        <a href="gestionar_usuarios.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-people-fill"></i> Regresar
        </a>
    </div>

    <div class="container mt-5">
        <div class="admin-card">
            <div class="card-header-edit">
                <h2>Perfil de Usuario</h2>
                <span class="badge-role-user"><?php echo strtoupper($u['rol']); ?></span>
            </div>
            <p class="subtitle-edit">Editando a: <strong><?php echo htmlspecialchars($u['nombre']); ?></strong></p>

            <form method="POST" class="custom-form">
                <div class="row">
                    <div class="form-group">
                        <label><i class="bi bi-person-fill"></i> Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($u['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-envelope-at-fill"></i> Correo Institucional</label>
                        <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($u['correo']); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group" style="flex: 2;">
                        <label><i class="bi bi-mortarboard-fill"></i> Carrera</label>
                        <select name="carrera_id" class="form-select">
                            <option value="">N/A (Administrativo / General)</option>
                            <?php foreach($carreras as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $u['carrera_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-hash"></i> Semestre</label>
                        <input type="number" name="semestre" class="form-control" value="<?php echo $u['semestre']; ?>" placeholder="Ej: 6">
                    </div>
                </div>

                <div class="security-section">
                    <div class="security-header">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Seguridad y Acceso</span>
                    </div>
                    <div class="form-group mb-0">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                        <small class="text-warning-custom">
                            <i class="bi bi-exclamation-triangle"></i> Dejar en blanco si no desea cambiar la contraseña actual.
                        </small>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-all"></i> Actualizar Perfil
                    </button>
                    <a href="gestionar_usuarios.php" class="btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>