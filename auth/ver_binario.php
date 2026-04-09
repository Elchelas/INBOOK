<?php
require_once '../config/db.php';

$tabla = $_GET['t'] ?? 'libros'; 
$id    = $_GET['id'] ?? 0;
$col   = $_GET['c'] ?? 'foto'; // 'foto' o 'fondo' para usuarios

if ($tabla == 'libros') {
    // Para libros siempre usamos la portada
    $stmt = $pdo->prepare("SELECT portada as imagen FROM libros WHERE id = ?");
} else {
    // Para usuarios, elegimos entre foto de perfil o fondo
    $columna = ($col == 'fondo') ? 'fondo_perfil' : 'foto_perfil';
    $stmt = $pdo->prepare("SELECT $columna as imagen FROM usuarios WHERE id = ?");
}

$stmt->execute([$id]);
$res = $stmt->fetch();

if ($res && $res['imagen']) {
    header("Content-Type: image/jpeg");
    echo $res['imagen'];
    exit();
} else {
    // Imagen por defecto según el contexto
    $img_default = ($col == 'fondo') ? 'default-banner.png' : 'default-placeholder.png';
    header("Location: ../assets/img/" . $img_default);
    exit();
}
?>