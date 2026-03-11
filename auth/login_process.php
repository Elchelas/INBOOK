<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    // Buscamos al usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch();

    if ($user) {
        // Intentamos verificar con Hash (estándar de seguridad)
        if (password_verify($password, $user['password'])) {
            $access = true;
        } 
        // Solo para emergencias/pruebas: si la DB tiene texto plano (quitar en producción)
        elseif ($password === $user['password']) {
            $access = true;
        } else {
            $access = false;
        }

        if ($access) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol']     = $user['rol'];
            $_SESSION['nombre']  = $user['nombre'];
            $_SESSION['carrera'] = $user['carrera_id'];
            $_SESSION['semestre'] = $user['semestre']; // ¡ESTA LÍNEA ES VITAL!

            if ($user['rol'] == 'superadmin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../alumno/home.php");
            }
            exit();
        }else {
            header("Location: ../index.php?error=invalid_credentials");
            exit();
        }
    }
    // Si llega aquí, falló algo
    header("Location: ../index.php?error=1");
    exit();
}