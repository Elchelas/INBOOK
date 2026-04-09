<?php
session_start();
require_once '../config/db.php';

// 1. Consultar las carreras para llenar el combobox dinámicamente
$stmtCarreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC");
$carreras = $stmtCarreras->fetchAll();

// 2. Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $carrera_id = $_POST['carrera_id'];
    $semestre = $_POST['semestre'];

    // Encriptación segura
    $passHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (nombre, correo, password, rol, carrera_id, semestre) 
                VALUES (?, ?, ?, 'alumno', ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $correo, $passHash, $carrera_id, $semestre])) {
            header("Location: ../index.php?status=registered");
            exit();
        }
    } catch (PDOException $e) {
        $error = "El correo ya está vinculado a una cuenta existente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Únete a INBOOK | ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h2>Registro de Alumno</h2>
            <p>Crea tu cuenta institucional para acceder a la biblioteca</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="auth-alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label><i class="bi bi-person"></i> Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Centaugri Valdez" required>
            </div>

            <div class="form-group">
                <label><i class="bi bi-envelope-at"></i> Correo Institucional</label>
                <input type="email" name="correo" class="form-control" placeholder="s21010101@itsur.edu.mx" required>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label><i class="bi bi-mortarboard"></i> Carrera</label>
                    <div class="select-wrapper">
                        <select name="carrera_id" class="form-control" required>
                            <option value="" disabled selected>Selecciona tu carrera...</option>
                            <?php foreach ($carreras as $c): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group flex-1">
                    <label><i class="bi bi-123"></i> Semestre</label>
                    <input type="number" name="semestre" class="form-control" min="1" max="12" placeholder="1-12" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="bi bi-shield-lock"></i> Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
            </div>
            
            <button type="submit" class="btn-auth">
                <span>Crear mi cuenta</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="../index.php">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>