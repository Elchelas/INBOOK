<?php
require_once '../config/db.php';

// 1. Consultar las carreras para llenar el combobox dinámicamente
$stmtCarreras = $pdo->query("SELECT * FROM carreras ORDER BY nombre_carrera ASC");
$carreras = $stmtCarreras->fetchAll();

// 2. Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $carrera_id = $_POST['carrera_id'];
    $semestre = $_POST['semestre'];

    // Encriptar la contraseña (Hash)
    $passHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (nombre, correo, password, rol, carrera_id, semestre) 
                VALUES (?, ?, ?, 'alumno', ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $correo, $passHash, $carrera_id, $semestre])) {
            // Registro exitoso, redirigir al login
            header("Location: ../index.php?msg=registro_exitoso");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Error: El correo ya está registrado o hubo un problema con la base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | ITSUR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-container">

    <div class="auth-card" style="max-width: 500px;">
        <h2>Registro de Alumno</h2>
        <p>Completa tus datos para personalizar tu biblioteca</p>
        
        <?php if(isset($error)): ?>
            <p style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="form-group">
                <label>Correo Institucional</label>
                <input type="email" name="correo" class="form-control" placeholder="usuario@itsur.edu.mx" required>
            </div>

            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 2;">
                    <label>Carrera</label>
                    <select name="carrera_id" class="form-control" required>
                        <option value="">Selecciona tu carrera...</option>
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Semestre</label>
                    <input type="number" name="semestre" class="form-control" min="1" max="12" required>
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Crea una contraseña" required>
            </div>
            
            <button type="submit" class="btn-primary">Crear mi cuenta</button>
        </form>

        <div style="margin-top: 1.5rem; font-size: 0.85rem; text-align: center;">
            ¿Ya tienes cuenta? <a href="../index.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>