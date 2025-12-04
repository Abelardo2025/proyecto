<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Obtener ID del contrato
$id_contrato = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id_contrato == 0) {
    header("Location: list.php");
    exit;
}

require_once '../../config/database.php';
require_once '../../models/ContratoModel.php';
require_once '../../models/DepositanteModel.php';

$database = new Database();
$db = $database->getConnection();
$contratoModel = new ContratoModel($db);
$depositanteModel = new DepositanteModel($db);

$contrato = $contratoModel->leerPorId($id_contrato);

if(!$contrato) {
    header("Location: list.php");
    exit;
}

// Obtener depositante para mostrar
$depositante = $depositanteModel->leerPorId($contrato['id_depositante']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Contrato <?php echo $contrato['numero_contrato']; ?> - Sistema Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Contrato</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="list.php">Contratos</a></li>
                            <li class="breadcrumb-item"><a href="ver.php?id=<?php echo $contrato['id_contrato']; ?>">
                                <?php echo substr($contrato['numero_contrato'], 0, 15) . '...'; ?>
                            </a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-edit"></i>
                                    Editar Contrato: <?php echo $contrato['numero_contrato']; ?>
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-<?php 
                                        echo $contrato['estado'] == 'VIGENTE' ? 'success' : 
                                             ($contrato['estado'] == 'FINALIZADO' ? 'secondary' : 'danger'); 
                                    ?>">
                                        <?php echo $contrato['estado']; ?>
                                    </span>
                                </div>
                            </div>
                            <form id="formEditarContrato">
                                <div class="card-body">
                                    <!-- Información no editable -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h4 class="card-title"><i class="fas fa-info-circle"></i> Información del Contrato</h4>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th width="150">N° Contrato:</th>
                                                            <td><?php echo htmlspecialchars($contrato['numero_contrato']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Depositante:</th>
                                                            <td>
                                                                <?php echo htmlspecialchars($contrato['nombre_completo']); ?><br>
                                                                <small class="text-muted">CI: <?php echo htmlspecialchars($contrato['ci_nit']); ?></small>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Fecha Creación:</th>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($contrato['fecha_creacion'])); ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4 class="card-title"><i class="fas fa-user"></i> Información del Depositante</h4>
                                                </div>
                                                <div class="card-body">
                                                    <?php if($depositante): ?>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th width="100">Nombre:</th>
                                                            <td><?php echo htmlspecialchars($depositante['nombre_completo']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>CI/NIT:</th>
                                                            <td><?php echo htmlspecialchars($depositante['ci_nit']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Teléfono:</th>
                                                            <td><?php echo htmlspecialchars($depositante['telefono'] ?: 'No registrado'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email:</th>
                                                            <td><?php echo htmlspecialchars($depositante['email'] ?: 'No registrado'); ?></td>
                                                        </tr>
                                                    </table>
                                                    <?php else: ?>
                                                    <p class="text-muted">Información del depositante no disponible</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Campos editables -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_inicio">Fecha Inicio *</label>
                                                <input type="date" class="form-control" id="fecha_inicio" 
                                                       value="<?php echo $contrato['fecha_inicio']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_fin">Fecha Fin *</label>
                                                <input type="date" class="form-control" id="fecha_fin" 
                                                       value="<?php echo $contrato['fecha_fin']; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gestion">Gestión *</label>
                                                <input type="text" class="form-control" id="gestion" 
                                                       value="<?php echo htmlspecialchars($contrato['gestion']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="monto_total_mes">Monto Mensual (Bs.) *</label>
                                                <input type="number" class="form-control" id="monto_total_mes" step="0.01"
                                                       value="<?php echo $contrato['monto_total_mes']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estado">Estado *</label>
                                                <select class="form-control" id="estado" required>
                                                    <option value="VIGENTE" <?php echo $contrato['estado'] == 'VIGENTE' ? 'selected' : ''; ?>>VIGENTE</option>
                                                    <option value="FINALIZADO" <?php echo $contrato['estado'] == 'FINALIZADO' ? 'selected' : ''; ?>>FINALIZADO</option>
                                                    <option value="RENOVADO" <?php echo $contrato['estado'] == 'RENOVADO' ? 'selected' : ''; ?>>RENOVADO</option>
                                                    <option value="CANCELADO" <?php echo $contrato['estado'] == 'CANCELADO' ? 'selected' : ''; ?>>CANCELADO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" rows="3" 
                                                  placeholder="Observaciones adicionales..."><?php echo htmlspecialchars($contrato['observaciones'] ?: ''); ?></textarea>
                                    </div>
                                    
                                    <!-- Advertencia para fechas -->
                                    <div class="alert alert-warning">
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> Advertencia</h5>
                                        <p>Al cambiar las fechas del contrato, los cobros mensuales existentes podrían necesitar recalculo.</p>
                                        <p class="mb-0"><strong>Fecha actual de fin:</strong> <?php echo date('d/m/Y', strtotime($contrato['fecha_fin'])); ?></p>
                                    </div>
                                    
                                    <!-- ID oculto -->
                                    <input type="hidden" id="id_contrato" value="<?php echo $contrato['id_contrato']; ?>">
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="ver.php?id=<?php echo $contrato['id_contrato']; ?>" class="btn btn-default">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../../includes/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('=== EDITAR CONTRATO INICIADO ===');
    
    // Validar que fecha fin sea mayor a fecha inicio
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        var inicio = new Date($('#fecha_inicio').val());
        var fin = new Date($('#fecha_fin').val());
        
        if(fin <= inicio) {
            Swal.fire('Error', 'La fecha fin debe ser mayor a la fecha inicio', 'error');
            $('#fecha_fin').val('');
        }
    });
    
    // Formulario de edición
    $('#formEditarContrato').on('submit', function(e) {
        e.preventDefault();
        guardarCambios();
    });
});

function guardarCambios() {
    // Validar campos
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();
    var gestion = $('#gestion').val();
    var monto_total_mes = $('#monto_total_mes').val();
    var estado = $('#estado').val();
    
    if(!fecha_inicio || !fecha_fin || !gestion || !monto_total_mes || !estado) {
        Swal.fire('Advertencia', 'Por favor complete todos los campos requeridos', 'warning');
        return;
    }
    
    // Validar fechas
    var inicio = new Date(fecha_inicio);
    var fin = new Date(fecha_fin);
    
    if(fin <= inicio) {
        Swal.fire('Error', 'La fecha fin debe ser mayor a la fecha inicio', 'error');
        return;
    }
    
    // Preparar datos
    var datos = {
        action: 'actualizar',
        id: $('#id_contrato').val(),
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        gestion: gestion,
        monto_total_mes: monto_total_mes,
        estado: estado,
        observaciones: $('#observaciones').val()
    };
    
    console.log('Enviando datos:', datos);
    
    // Mostrar confirmación
    Swal.fire({
        title: '¿Guardar cambios?',
        html: `
            <div class="text-left">
                <p>Se actualizarán los siguientes datos:</p>
                <ul>
                    <li><strong>Fecha Fin:</strong> ${fecha_fin}</li>
                    <li><strong>Monto Mensual:</strong> Bs. ${parseFloat(monto_total_mes).toFixed(2)}</li>
                    <li><strong>Estado:</strong> ${estado}</li>
                </ul>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar cambios',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                $.ajax({
                    url: "../../controllers/ContratoController.php",
                    type: 'POST',
                    data: datos,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta:', response);
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        resolve({ success: false, message: 'Error de conexión: ' + error });
                    }
                });
            });
        }
    }).then((result) => {
        if(result.isConfirmed) {
            const response = result.value;
            
            if(response.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p>${response.message}</p>
                            <p class="text-muted">Redirigiendo al detalle del contrato...</p>
                        </div>
                    `,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    willClose: () => {
                        // Redirigir al detalle del contrato
                        window.location.href = 'ver.php?id=' + $('#id_contrato').val();
                    }
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}
</script>

</body>
</html>