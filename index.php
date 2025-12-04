<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Control de Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard Principal</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="total-clientes">0</h3>
                                <p>Clientes Activos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="views/depositantes/list.php" class="small-box-footer">
                                Más info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 id="total-contratos">0</h3>
                                <p>Contratos Vigentes</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <a href="views/contratos/list.php" class="small-box-footer">
                                Más info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="ingreso-mensual">0</h3>
                                <p>Ingreso Mensual (Bs)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <a href="views/cobros/list.php" class="small-box-footer">
                                Más info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3 id="ubicaciones-ocupadas">0</h3>
                                <p>Ubicaciones Ocupadas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <a href="views/ubicaciones/list.php" class="small-box-footer">
                                Más info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información de bienvenida -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Bienvenido al Sistema de Control de Depósitos</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info-circle"></i> Sistema en Desarrollo</h5>
                                    <p>Bienvenido <strong><?php echo $_SESSION['username']; ?></strong>! El sistema está funcionando correctamente.</p>
                                    <p>Puedes navegar por los diferentes módulos usando el menú lateral.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Módulos Disponibles:</h5>
                                        <ul>
                                            <li>📊 Dashboard</li>
                                            <li>👥 Gestión de Depositantes</li>
                                            <li>📝 Contratos</li>
                                            <li>🏢 Ubicaciones</li>
                                            <li>📦 Artículos</li>
                                            <li>💰 Cobros Mensuales</li>
                                            <li>📈 Reportes</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Acciones Rápidas:</h5>
                                        <div class="list-group">
                                            <a href="views/depositantes/list.php" class="list-group-item list-group-item-action">
                                                <i class="fas fa-user-plus mr-2"></i> Registrar Nuevo Depositante
                                            </a>
                                            <a href="views/contratos/list.php" class="list-group-item list-group-item-action">
                                                <i class="fas fa-file-signature mr-2"></i> Crear Nuevo Contrato
                                            </a>
                                            <a href="views/ubicaciones/list.php" class="list-group-item list-group-item-action">
                                                <i class="fas fa-map-marker-alt mr-2"></i> Ver Ubicaciones Disponibles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
// Simular datos del dashboard
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('total-clientes').textContent = '12';
    document.getElementById('total-contratos').textContent = '8';
    document.getElementById('ingreso-mensual').textContent = '1,250.00';
    document.getElementById('ubicaciones-ocupadas').textContent = '15';
});
</script>
</body>
</html>