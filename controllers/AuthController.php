<?php
session_start();

// Para depuración - quitar en producción
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validación simple
    if($username === 'admin' && $password === 'admin123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        // Redirigir al dashboard
        header("Location: ../index.php");
        exit();
    } else {
        // Redirigir al login con error
        header("Location: ../login.php?error=1");
        exit();
    }
}

if(isset($_GET['logout'])) {
    // Destruir la sesión
    session_destroy();
    
    // Redirigir al login
    header("Location: ../login.php");
    exit();
}

// Si se accede directamente al controlador sin acción
header("Location: ../login.php");
exit();
?>