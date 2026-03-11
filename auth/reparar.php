<?php
require_once '../config/db.php';
// Generamos el hash real para 'admin123'
$nuevo_hash = password_hash('admin123', PASSWORD_BCRYPT);

$sql = "UPDATE usuarios SET password = ? WHERE correo = 'admin@itsur.edu.mx'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nuevo_hash]);

echo "Base de datos actualizada. Intenta loguearte con: admin123";
?>