<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta | ITSUR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-container">

    <div class="auth-card" style="max-width: 500px;">
        <h2>Registro</h2>
        <p>Completa tus datos para personalizar tu biblioteca</p>
        
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Correo Institucional</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 2;">
                    <label>Carrera</label>
                    <select name="carrera_id" class="form-control" required>
                        </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Semestre</label>
                    <input type="number" name="semestre" class="form-control" min="1" max="10" required>
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-primary">Crear mi cuenta</button>
        </form>

        <div style="margin-top: 1rem; font-size: 0.85rem;">
            <a href="../index.php" style="color: #777; text-decoration: none;">← Volver al Login</a>
        </div>
    </div>

</body>
</html>