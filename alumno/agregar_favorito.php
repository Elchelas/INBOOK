<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $libro_id = $_GET['id'];
    $usuario_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT IGNORE INTO favoritos (usuario_id, libro_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $libro_id]);
}

// Redirigir de vuelta a donde estaba
header("Location: " . $_SERVER['HTTP_REFERER']);
?>