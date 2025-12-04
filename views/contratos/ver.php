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

$database = new Database();
$db = $database->getConnection();
$contratoModel = new ContratoModel($db);

$contrato = $contratoModel->leerPorId($id_contrato);

if(!$contrato) {
    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrato <?php echo $contrato['numero_contrato']; ?> - Sistema Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
                        <h1>Detalle del Contrato</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="list.php" class="btn btn-default float-right">
                            <i class="fas fa-arrow-left"></i> Volver a Contratos
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Información del contrato -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-contract"></i>
                                    Contrato: <?php echo $contrato['numero_contrato']; ?>
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
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-user"></i> Información del Depositante</h5>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="150">Nombre:</th>
                                                <td><?php echo htmlspecialchars($contrato['nombre_completo']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>CI/NIT:</th>
                                                <td><?php echo htmlspecialchars($contrato['ci_nit']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Teléfono:</th>
                                                <td><?php echo htmlspecialchars($contrato['telefono'] ?: 'No registrado'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td><?php echo htmlspecialchars($contrato['email'] ?: 'No registrado'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-calendar-alt"></i> Información del Contrato</h5>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="150">Fecha Inicio:</th>
                                                <td><?php echo date('d/m/Y', strtotime($contrato['fecha_inicio'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Fecha Fin:</th>
                                                <td><?php echo date('d/m/Y', strtotime($contrato['fecha_fin'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Gestión:</th>
                                                <td><?php echo htmlspecialchars($contrato['gestion']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Monto Mensual:</th>
                                                <td class="text-success font-weight-bold">
                                                    Bs. <?php echo number_format($contrato['monto_total_mes'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php 
                                            // Calcular días restantes
                                            $fecha_fin = new DateTime($contrato['fecha_fin']);
                                            $hoy = new DateTime();
                                            $dias_restantes = $hoy->diff($fecha_fin)->days;
                                            if($contrato['estado'] == 'VIGENTE' && $hoy <= $fecha_fin):
                                            ?>
                                            <tr>
                                                <th>Días Restantes:</th>
                                                <td>
                                                    <span class="badge badge-<?php echo $dias_restantes < 30 ? 'warning' : 'info'; ?>">
                                                        <?php echo $dias_restantes; ?> días
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>
                                
                                <?php if($contrato['observaciones']): ?>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h5><i class="fas fa-sticky-note"></i> Observaciones</h5>
                                        <div class="alert alert-info">
                                            <?php echo nl2br(htmlspecialchars($contrato['observaciones'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            Creado el: <?php echo date('d/m/Y H:i', strtotime($contrato['fecha_creacion'])); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button class="btn btn-sm btn-warning" onclick="editarContrato(<?php echo $contrato['id_contrato']; ?>)">
                                            <i class="fas fa-edit"></i> Editar Contrato
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="imprimirContrato(<?php echo $contrato['id_contrato']; ?>)">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Items del contrato -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-boxes"></i> Items Almacenados
                                </h3>
                                <div class="card-tools">
                                    <button class="btn btn-sm btn-primary" onclick="agregarItem()">
                                        <i class="fas fa-plus"></i> Agregar Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tablaItems" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descripción</th>
                                            <th>Tipo</th>
                                            <th>Ubicación</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Total Mensual</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se cargarán via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cobros del contrato -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-money-bill-wave"></i> Historial de Cobros
                                </h3>
                            </div>
                            <div class="card-body">
                                <table id="tablaCobros" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Mes/Año</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Fecha Pago</th>
                                            <th>Monto</th>
                                            <th>Recargo</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se cargarán via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal para agregar item -->
    <div class="modal fade" id="modalAgregarItem">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Item al Contrato</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarItem">
                        <input type="hidden" id="item_id_contrato" value="<?php echo $contrato['id_contrato']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Artículo *</label>
                                    <select class="form-control select2" id="item_tipo_articulo" style="width: 100%;" required>
                                        <option value="">Seleccionar tipo...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ubicación *</label>
                                    <select class="form-control select2" id="item_ubicacion" style="width: 100%;" required>
                                        <option value="">Seleccionar ubicación...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Descripción del Item *</label>
                                    <input type="text" class="form-control" id="item_descripcion" 
                                           placeholder="Ej: Sillas de madera, Archivo 1990-2000, Refrigerador..." required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cantidad</label>
                                    <input type="number" class="form-control" id="item_cantidad" value="1" min="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio Unitario Mensual (Bs.) *</label>
                                    <input type="number" class="form-control" id="item_precio" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="item_fecha_ingreso" 
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="item_observaciones" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarItem()">
                        <i class="fas fa-save"></i> Guardar Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales
var tablaItems, tablaCobros;
var idContrato = <?php echo $contrato['id_contrato']; ?>;

$(document).ready(function() {
    console.log('=== DETALLE CONTRATO INICIADO ===');
    
    // Inicializar Select2
    $('.select2').select2();
    
    // Cargar datos para los selects
    cargarTiposArticulos();
    cargarUbicaciones();
    
    // Inicializar tablas
    inicializarTablaItems();
    inicializarTablaCobros();
});

function cargarTiposArticulos() {
    // Aquí cargarías los tipos de artículos desde la base de datos
    // Por ahora datos de ejemplo
    var tipos = [
        {id: 1, nombre: "Muebles", precio: 50.00},
        {id: 2, nombre: "Electrodomésticos", precio: 80.00},
        {id: 3, nombre: "Documentos", precio: 30.00},
        {id: 4, nombre: "Ropa", precio: 25.00},
        {id: 5, nombre: "Herramientas", precio: 60.00}
    ];
    
    var select = $('#item_tipo_articulo');
    select.empty();
    select.append('<option value="">Seleccionar tipo...</option>');
    
    tipos.forEach(function(tipo) {
        select.append(
            '<option value="' + tipo.id + '" data-precio="' + tipo.precio + '">' + 
            tipo.nombre + ' (Bs. ' + tipo.precio + ')</option>'
        );
    });
    
    // Cuando se selecciona un tipo, cargar su precio
    select.on('change', function() {
        var selected = $(this).find('option:selected');
        var precio = selected.data('precio');
        if(precio) {
            $('#item_precio').val(precio);
        }
    });
}

function cargarUbicaciones() {
    // Aquí cargarías las ubicaciones disponibles
    // Por ahora datos de ejemplo
    var ubicaciones = [
        {id: 1, nombre: "PB-A1", piso: "Planta Baja", precio: 40.00},
        {id: 2, nombre: "PB-A2", piso: "Planta Baja", precio: 40.00},
        {id: 3, nombre: "PB-B1", piso: "Planta Baja", precio: 20.00},
        {id: 4, nombre: "P1-A1", piso: "1er Piso", precio: 30.00},
        {id: 5, nombre: "P1-B1", piso: "1er Piso", precio: 15.00},
        {id: 6, nombre: "P2-A1", piso: "2do Piso", precio: 50.00}
    ];
    
    var select = $('#item_ubicacion');
    select.empty();
    select.append('<option value="">Seleccionar ubicación...</option>');
    
    ubicaciones.forEach(function(ubicacion) {
        select.append(
            '<option value="' + ubicacion.id + '" data-precio="' + ubicacion.precio + '">' + 
            ubicacion.nombre + ' - ' + ubicacion.piso + ' (Bs. ' + ubicacion.precio + ')</option>'
        );
    });
    
    // Cuando se selecciona una ubicación, sumar su precio
    select.on('change', function() {
        var selected = $(this).find('option:selected');
        var precioUbicacion = selected.data('precio') || 0;
        var precioTipo = $('#item_tipo_articulo option:selected').data('precio') || 0;
        var precioTotal = parseFloat(precioTipo) + parseFloat(precioUbicacion);
        
        $('#item_precio').val(precioTotal.toFixed(2));
    });
}

function inicializarTablaItems() {
    tablaItems = $('#tablaItems').DataTable({
        "ajax": {
            "url": "../../controllers/ItemContratoController.php?action=listar&id_contrato=" + idContrato,
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error cargando items:', error);
                // Si el controlador no existe, mostrar mensaje
                $('#tablaItems tbody').html(`
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Módulo de items en desarrollo
                        </td>
                    </tr>
                `);
            }
        },
        "columns": [
            {"data": "id_item"},
            {"data": "descripcion_item"},
            {"data": "nombre_tipo"},
            {"data": "nombre_ubicacion"},
            {"data": "cantidad"},
            {
                "data": "precio_unitario_mes",
                "render": function(data) {
                    return 'Bs. ' + parseFloat(data).toFixed(2);
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var total = parseFloat(data.precio_unitario_mes) * parseInt(data.cantidad);
                    return 'Bs. ' + total.toFixed(2);
                }
            },
            {
                "data": "estado",
                "render": function(data) {
                    if(data === 'ALMACENADO') {
                        return '<span class="badge badge-success">ALMACENADO</span>';
                    } else if(data === 'RETIRADO') {
                        return '<span class="badge badge-secondary">RETIRADO</span>';
                    } else {
                        return '<span class="badge badge-warning">' + data + '</span>';
                    }
                }
            },
            {
                "data": "id_item",
                "render": function(data, type, row) {
                    var botones = '';
                    
                    if(row.estado === 'ALMACENADO') {
                        botones += `
                            <button class="btn btn-sm btn-warning mr-1" onclick="editarItem(${data})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger mr-1" onclick="retirarItem(${data})" title="Marcar como Retirado">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        `;
                    }
                    
                    botones += `
                        <button class="btn btn-sm btn-info" onclick="verMovimientos(${data})" title="Ver Movimientos">
                            <i class="fas fa-history"></i>
                        </button>
                    `;
                    
                    return botones;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        }
    });
}

function inicializarTablaCobros() {
    tablaCobros = $('#tablaCobros').DataTable({
        "ajax": {
            "url": "../../controllers/CobroController.php?action=listar&id_contrato=" + idContrato,
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error cargando cobros:', error);
                // Si el controlador no existe, mostrar mensaje
                $('#tablaCobros tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Módulo de cobros en desarrollo
                        </td>
                    </tr>
                `);
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data) {
                    return data.mes + '/' + data.anio;
                }
            },
            {"data": "fecha_vencimiento"},
            {
                "data": "fecha_pago",
                "render": function(data) {
                    return data || '-';
                }
            },
            {
                "data": "monto_total",
                "render": function(data) {
                    return 'Bs. ' + parseFloat(data).toFixed(2);
                }
            },
            {
                "data": "recargo",
                "render": function(data) {
                    return data > 0 ? 'Bs. ' + parseFloat(data).toFixed(2) : '-';
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var total = parseFloat(data.monto_total) + parseFloat(data.recargo || 0);
                    return 'Bs. ' + total.toFixed(2);
                }
            },
            {
                "data": "estado",
                "render": function(data, type, row) {
                    var badgeClass = '';
                    switch(data) {
                        case 'PAGADO': badgeClass = 'success'; break;
                        case 'PENDIENTE': badgeClass = 'warning'; break;
                        case 'VENCIDO': badgeClass = 'danger'; break;
                        case 'MORA': badgeClass = 'danger'; break;
                        default: badgeClass = 'secondary';
                    }
                    
                    var html = '<span class="badge badge-' + badgeClass + '">' + data + '</span>';
                    
                    if(row.dias_retraso > 0) {
                        html += ' <span class="badge badge-danger">' + row.dias_retraso + ' días</span>';
                    }
                    
                    return html;
                }
            },
            {
                "data": "id_cobro",
                "render": function(data, type, row) {
                    if(row.estado === 'PENDIENTE' || row.estado === 'VENCIDO') {
                        return `
                            <button class="btn btn-sm btn-success" onclick="registrarPago(${data})" title="Registrar Pago">
                                <i class="fas fa-check"></i>
                            </button>
                        `;
                    }
                    return '-';
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        }
    });
}

function agregarItem() {
    // Limpiar formulario
    $('#formAgregarItem')[0].reset();
    $('#item_fecha_ingreso').val(new Date().toISOString().split('T')[0]);
    
    // Mostrar modal
    $('#modalAgregarItem').modal('show');
}

function guardarItem() {
    // Validar campos
    var descripcion = $('#item_descripcion').val();
    var tipo = $('#item_tipo_articulo').val();
    var ubicacion = $('#item_ubicacion').val();
    var precio = $('#item_precio').val();
    
    if(!descripcion || !tipo || !ubicacion || !precio) {
        Swal.fire('Advertencia', 'Por favor complete todos los campos requeridos', 'warning');
        return;
    }
    
    // Preparar datos
    var datos = {
        action: 'crear',
        id_contrato: idContrato,
        descripcion_item: descripcion,
        id_tipo_articulo: tipo,
        id_ubicacion: ubicacion,
        cantidad: $('#item_cantidad').val() || 1,
        precio_unitario_mes: precio,
        fecha_ingreso: $('#item_fecha_ingreso').val(),
        observaciones: $('#item_observaciones').val()
    };
    
    console.log('Guardando item:', datos);
    
    // Mostrar mensaje de desarrollo
    Swal.fire({
        title: 'En desarrollo',
        text: 'Funcionalidad de agregar items en desarrollo',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
    
    // En una implementación real, harías:
    /*
    $.ajax({
        url: "../../controllers/ItemContratoController.php",
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#modalAgregarItem').modal('hide');
                tablaItems.ajax.reload();
                Swal.fire('¡Éxito!', response.message, 'success');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
    */
}

function editarItem(id) {
    Swal.fire('Información', 'Editar item ID: ' + id + ' - Funcionalidad en desarrollo', 'info');
}

function retirarItem(id) {
    Swal.fire({
        title: '¿Retirar Item?',
        text: '¿Está seguro de marcar este item como retirado?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, retirar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire('En desarrollo', 'Funcionalidad de retiro en desarrollo', 'info');
        }
    });
}

function verMovimientos(id) {
    Swal.fire('Información', 'Ver movimientos del item ID: ' + id + ' - Funcionalidad en desarrollo', 'info');
}

function registrarPago(id) {
    Swal.fire('Información', 'Registrar pago del cobro ID: ' + id + ' - Funcionalidad en desarrollo', 'info');
}

function editarContrato(id) {
    window.location.href = 'editar.php?id=' + id;
}

function imprimirContrato(id) {
    window.open('imprimir.php?id=' + id, '_blank');
}
</script>

</body>
</html>