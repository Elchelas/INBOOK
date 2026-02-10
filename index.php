<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Biblioteca ITSUR</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-container">

    <div class="auth-card">
        <img src="assets/img/logo_inbook.svg" alt="Logo" style="width: 300px; margin-bottom: 1rem; display: block; margin-left: 6vw; margin-right: auto;">
        <h2>¡Bienvenido!</h2>
        <p>Inicia sesión para acceder a tus libros</p>
        
        <?php if(isset($_GET['msg'])) echo "<p style='color:green'>Registrado con éxito. Inicia sesión.</p>"; ?>

        <?php if(isset($_GET['error'])): ?>
            <p style="color: red;">Correo o contraseña incorrectos.</p>
        <?php endif; ?>
        
        <form action="auth/login_process.php" method="POST">
            <div class="form-group">
                <label>Correo Institucional</label>
                <input type="email" name="correo" class="form-control" placeholder="ejemplo@itsur.edu.mx" required>
            </div>
            
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-primary">Entrar al Sistema</button>
        </form>

        <div style="margin-top: 1.5rem; font-size: 0.85rem;">
            ¿No tienes cuenta? <a href="auth/register.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>