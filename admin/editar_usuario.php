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

$carreras = $pdo->query("SELECT * FROM carreras")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $carrera = $_POST['carrera_id'];
    $semestre = $_POST['semestre'];
    
    // Si el admin escribió algo en el campo password, se actualiza, si no, se queda igual
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
    <title>Editar Usuario | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-container">
    <div class="auth-card" style="max-width: 600px;">
        <h2>Editar Perfil de Usuario</h2>
        <p>Modifica los datos de <b><?php echo htmlspecialchars($u['nombre']); ?></b></p>

        <form method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $u['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" class="form-control" value="<?php echo $u['correo']; ?>" required>
            </div>
            
            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 2;">
                    <label>Carrera</label>
                    <select name="carrera_id" class="form-control">
                        <option value="">N/A (Admin)</option>
                        <?php foreach($carreras as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $u['carrera_id']) ? 'selected' : ''; ?>>
                                <?php echo $c['nombre_carrera']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Semestre</label>
                    <input type="number" name="semestre" class="form-control" value="<?php echo $u['semestre']; ?>">
                </div>
            </div>

            <div class="form-group" style="background: #fdf2f2; padding: 10px; border-radius: 8px; border: 1px solid #f8d7da;">
                <label>Nueva Contraseña (Dejar en blanco para no cambiar)</label>
                <input type="password" name="new_password" class="form-control" placeholder="••••••••">
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary" style="flex: 1;">Guardar Cambios</button>
                <a href="gestionar_usuarios.php" class="btn-primary" style="flex: 1; background: #6c757d; text-align: center; text-decoration: none;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>