<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../index.php"); exit();
}
require_once '../config/db.php';

// Lógica para eliminar usuario
if (isset($_GET['eliminar'])) {
    $id_del = $_GET['eliminar'];
    // Evitar que el superadmin se elimine a sí mismo por error
    if ($id_del != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id_del]);
        header("Location: gestionar_usuarios.php?msg=eliminado");
    } else {
        header("Location: gestionar_usuarios.php?msg=error_self");
    }
    exit();
}

// Consulta de usuarios con el nombre de su carrera
$sql = "SELECT u.*, c.nombre_carrera 
        FROM usuarios u 
        LEFT JOIN carreras c ON u.carrera_id = c.id 
        ORDER BY u.rol ASC, u.nombre ASC";
$usuarios = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | INBOOK ITSUR</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin_tables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="admin-body">

    <div class="top-bar">
        <div class="user-info">
            <i class="bi bi-people-fill"></i>
            <b>CONTROL DE ACCESO Y USUARIOS</b> | ITSUR
        </div>
        <a href="dashboard.php" class="btn-logout" style="border-color: var(--itsur-yellow); color: var(--itsur-yellow);">
            <i class="bi bi-speedometer2"></i> Panel Principal
        </a>
    </div>

    <div class="container mt-5">
        <div class="table-header-actions">
            <div>
                <h2>Directorio de Usuarios</h2>
                <p class="subtitle">Administra los perfiles de alumnos y personal administrativo.</p>
            </div>
            <a href="#" class="btn-add-new" style="opacity: 0.6; cursor: not-allowed;">
                <i class="bi bi-person-plus-fill"></i> Nuevo Registro (Próximamente)
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'eliminado'): ?>
                <div class="alert-success-custom">
                    <i class="bi bi-person-x"></i> Usuario removido del sistema correctamente.
                </div>
            <?php elseif($_GET['msg'] == 'error_self'): ?>
                <div class="alert-danger-custom" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="bi bi-exclamation-octagon"></i> No puedes eliminar tu propia cuenta de administrador.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="table-responsive-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Usuario / Correo</th>
                        <th>Rol</th>
                        <th>Carrera y Semestre</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td>
                            <div class="title-cell">
                                <span class="book-title"><?php echo htmlspecialchars($u['nombre']); ?></span>
                                <span class="book-author" style="font-family: monospace;">
                                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($u['correo']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="category-tag <?php echo $u['rol']; ?>" style="text-transform: uppercase; letter-spacing: 0.5px;">
                                <?php if($u['rol'] == 'superadmin'): ?>
                                    <i class="bi bi-shield-check"></i> Admin
                                <?php else: ?>
                                    <i class="bi bi-mortarboard"></i> Alumno
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <div class="academic-cell">
                                <span class="career-name"><?php echo htmlspecialchars($u['nombre_carrera'] ?? 'Acceso Administrativo'); ?></span>
                                <?php if($u['semestre']): ?>
                                    <span class="semester-badge"><?php echo $u['semestre']; ?>° Semestre</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="action-btns-group">
                                <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn-action edit" title="Editar Perfil">
                                    <i class="bi bi-person-gear"></i>
                                </a>
                                
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="gestionar_usuarios.php?eliminar=<?php echo $u['id']; ?>" 
                                   class="btn-action delete" 
                                   title="Eliminar Usuario"
                                   onclick="return confirm('¿Estás seguro de eliminar a este usuario? Esta acción no se puede deshacer.')">
                                    <i class="bi bi-person-dash-fill"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>