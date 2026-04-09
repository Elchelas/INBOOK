<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Biblioteca ITSUR</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/auth.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo-main">
                <img src="assets/img/logo_inbook.svg" alt="INBOOK Logo" style="width: 200px; height: auto; margin: 0 auto; display: block;">
            </div>
            <h2>¡Qué bueno verte!</h2>
            <p>Inicia sesión para acceder a tu material de estudio.</p>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="auth-alert error-alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Correo o contraseña incorrectos.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
            <div class="auth-alert success-alert">
                <i class="bi bi-check-circle-fill"></i> Registro exitoso. ¡Inicia sesión!
            </div>
        <?php endif; ?>
        
        <form action="auth/login_process.php" method="POST" class="auth-form">
            <div class="form-group">
                <label><i class="bi bi-envelope-at"></i> Correo Institucional</label>
                <input type="email" name="correo" class="form-control" placeholder="usuario@itsur.edu.mx" required>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label><i class="bi bi-key"></i> Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-auth">
                <span>Entrar al Sistema</span>
                <i class="bi bi-box-arrow-in-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            ¿Aún no eres parte? <a href="auth/register.php">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>