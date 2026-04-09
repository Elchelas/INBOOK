<?php
session_start();
// Función para limpiar la URL y sacar el ID de YouTube
function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return $match[1] ?? null;
}

$url = $_GET['url'] ?? null;
$id = getYoutubeId($url);

if (!$id) { echo "Video no válido."; exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reproductor Educativo | INBOOK</title>
    <style>
        body { background: #000; margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; color: white; font-family: sans-serif; }
        .video-box { width: 80%; height: 70%; box-shadow: 0 0 50px rgba(111, 66, 193, 0.5); }
        .back { margin-top: 20px; color: #fff; text-decoration: none; border: 1px solid #fff; padding: 10px 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="video-box">
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?php echo $id; ?>?rel=0&autoplay=1" frameborder="0" allowfullscreen></iframe>
    </div>
    <a href="javascript:history.back()" class="back">Volver al Libro</a>
</body>
</html>