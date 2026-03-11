<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db.php';

// Lógica para eliminar usuario
if (isset($_GET['eliminar'])) {
    $id_del = $_GET['eliminar'];
    // Evitar que el admin se borre a sí mismo por accidente
    if ($id_del == $_SESSION['user_id']) {
        header("Location: gestionar_usuarios.php?error=self");
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id_del]);
        header("Location: gestionar_usuarios.php?msg=borrado");
    }
    exit();
}

// Consultar todos los alumnos y sus carreras
$sql = "SELECT u.id, u.nombre, u.correo, u.rol, u.semestre, c.nombre_carrera 
        FROM usuarios u 
        LEFT JOIN carreras c ON u.carrera_id = c.id 
        ORDER BY u.rol DESC, u.nombre ASC";
$usuarios = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Admin ITSUR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: bold; }
        .badge-admin { background: #ffd700; color: #000; }
        .badge-alumno { background: #6f42c1; color: #fff; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--dark-bg); color: white; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><b>ADMIN</b> | Control de Usuarios</div>
        <a href="dashboard.php" style="color:white; text-decoration:none;">Volver al Panel</a>
    </div>

    <div class="container">
        <h2>Comunidad Universitaria</h2>
        <p>Listado de administradores y alumnos registrados en la plataforma.</p>

        <?php if(isset($_GET['error'])) echo "<p style='color:red;'><b>Error:</b> No puedes eliminar tu propia cuenta administrativa.</p>"; ?>
        <?php if(isset($_GET['msg'])) echo "<p style='color:green;'>Usuario eliminado correctamente.</p>"; ?>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Carrera / Semestre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><b><?php echo htmlspecialchars($u['nombre']); ?></b></td>
                    <td><?php echo htmlspecialchars($u['correo']); ?></td>
                    <td>
                        <span class="badge <?php echo ($u['rol']=='superadmin') ? 'badge-admin' : 'badge-alumno'; ?>">
                            <?php echo strtoupper($u['rol']); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $u['nombre_carrera'] ? $u['nombre_carrera'] . " (" . $u['semestre'] . "°)" : "N/A"; ?>
                    </td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" style="color: var(--primary-color); text-decoration: none; margin-right: 10px;">Editar</a>
                        
                        <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <a href="gestionar_usuarios.php?eliminar=<?php echo $u['id']; ?>" 
                            style="color: #dc3545; text-decoration: none;"
                            onclick="return confirm('¿Eliminar permanentemente?')">Eliminar</a>
                        <?php else: ?>
                            <small style="color: #bbb;">(Tú)</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>