<?php
require_once 'config/db.php';

$tabla = $_GET['t'] ?? 'libros'; // 'libros' o 'usuarios'
$id = $_GET['id'] ?? 0;

if ($tabla == 'libros') {
    $stmt = $pdo->prepare("SELECT portada as imagen FROM libros WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT foto_perfil as imagen FROM usuarios WHERE id = ?");
}

$stmt->execute([$id]);
$res = $stmt->fetch();

if ($res && $res['imagen']) {
    header("Content-Type: image/jpeg");
    echo $res['imagen'];
} else {
    // Imagen por defecto si no hay nada en la DB
    header("Location: assets/img/default-placeholder.png");
}