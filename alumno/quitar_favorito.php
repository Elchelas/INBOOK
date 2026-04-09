<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { exit; }

$libro_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($libro_id) {
    $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND libro_id = ?");
    $stmt->execute([$user_id, $libro_id]);
}

// Redirigir de vuelta al estante con un mensaje sutil
header("Location: mi_estante.php?msg=eliminado");
exit();