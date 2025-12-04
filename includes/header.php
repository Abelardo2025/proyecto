<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Inicio</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i>
                <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuario'; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">Mi Cuenta</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a href="controllers/AuthController.php?logout=true" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>
</nav>