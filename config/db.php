<?php
$host = "localhost";
$db_name = "biblioteca_itsur";
$username = "root"; // Cambia esto por tu usuario de hosting
$password = "1112019";     // Cambia esto por tu contraseña de hosting

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>