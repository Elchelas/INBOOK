<?php
session_start();
require_once '../config/db.php';

// Verificación de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Consultar datos del alumno, incluyendo su carrera
$stmt = $pdo->prepare("SELECT u.*, c.nombre_carrera 
                       FROM usuarios u 
                       LEFT JOIN carreras c ON u.carrera_id = c.id 
                       WHERE u.id = ?");
$stmt->execute([$user_id]);
$alumno = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | INBOOK</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .profile-container { max-width: 900px; margin: 30px auto; background: #fff; border-radius: 15px; overflow: hidden; shadow: 0 5px 20px rgba(0,0,0,0.1); }
        
        /* Banner Dinámico */
        .profile-banner { 
            height: 250px; 
            background: #444 url('../auth/ver_binario.php?t=usuarios&id=<?php echo $user_id; ?>&c=fondo') no-repeat center center; 
            background-size: cover;
            position: relative;
        }

        /* Foto de Perfil sobre el Banner */
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #fff;
            position: absolute;
            bottom: -75px;
            left: 50px;
            background: #eee;
            object-fit: cover;
        }

        .profile-content { padding: 90px 50px 40px; }
        .profile-header { display: flex; justify-content: space-between; align-items: flex-start; }
        
        .profile-name h1 { margin: 0; color: #2d3436; font-size: 2rem; }
        .profile-info { color: #636e72; font-weight: 500; margin-top: 5px; }
        
        .profile-bio { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 5px solid #6f42c1; }
        .profile-bio h3 { margin-top: 0; color: #6f42c1; font-size: 1.1rem; }
        
        .btn-edit-profile { 
            background: #6f42c1; color: white; padding: 10px 20px; 
            border-radius: 25px; text-decoration: none; font-weight: bold;
            transition: 0.3s;
        }
        .btn-edit-profile:hover { background: #5a32a3; transform: scale(1.05); }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="user-info">
        <a href="home.php" style="color:white; text-decoration:none; margin-right:20px;">← Volver al Inicio</a>
        <b>SESIÓN: <?php echo htmlspecialchars($alumno['nombre']); ?></b>
    </div>
</div>

<div class="profile-container">
    <div class="profile-banner">
        <img src="../auth/ver_binario.php?t=usuarios&id=<?php echo $user_id; ?>&c=foto" class="profile-avatar" alt="Avatar">
    </div>

    <div class="profile-content">
        <div class="profile-header">
            <div class="profile-name">
                <h1><?php echo htmlspecialchars($alumno['nombre']); ?></h1>
                <div class="profile-info">
                    <span>🎓 <?php echo htmlspecialchars($alumno['nombre_carrera'] ?? 'Carrera no asignada'); ?></span> | 
                    <span>📖 <?php echo $alumno['semestre']; ?>° Semestre</span>
                </div>
            </div>
            <a href="editar_perfil.php" class="btn-edit-profile">Editar Perfil</a>
        </div>

        <div class="profile-bio">
            <h3>Sobre mí</h3>
            <p>
                <?php 
                echo !empty($alumno['descripcion']) 
                    ? nl2br(htmlspecialchars($alumno['descripcion'])) 
                    : "Este estudiante aún no ha redactado su biografía. ¡Cuéntanos algo sobre ti!"; 
                ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>