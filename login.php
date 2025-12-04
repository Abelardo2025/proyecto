<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Depósitos - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-box {
            width: 360px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-logo a {
            color: #fff;
            font-size: 35px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>🏢 Sistema</b>Depósitos
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Ingresa tus credenciales</p>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    Usuario o contraseña incorrectos
                </div>
            <?php endif; ?>

            <form action="controllers/AuthController.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Usuario" name="username" value="admin" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Contraseña" name="password" value="admin123" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block" name="login">Ingresar al Sistema</button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <strong>Credenciales por defecto:</strong><br>
                    Usuario: admin<br>
                    Contraseña: admin123
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>