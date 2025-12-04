<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>

<style>
/* Estilo para el botón de imprimir */
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

/* Para la vista previa de impresión */
.swal2-wide {
    max-width: 1000px !important;
}

/* Estilos para la vista de impresión */
@media print {
    .no-print {
        display: none !important;
    }
    body {
        font-size: 12pt;
        line-height: 1.5;
    }
}

p {
  text-align: left;
}

</style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contratos - Sistema Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <!-- jsPDF para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

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
                        <h1>Gestión de Contratos</h1>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-success float-right" onclick="abrirModalNuevoContrato()">
                            <i class="fas fa-plus"></i> Nuevo Contrato
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Todos los Contratos</h3>
                            </div>
                            <div class="card-body">
                                <table id="tablaContratos" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>N° Contrato</th>
                                            <th>Depositante</th>
                                            <th>CI/NIT</th>
                                            <th>Inicio - Fin</th>
                                            <th>Monto Mensual</th>
                                            <th>Items</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Nuevo Contrato -->
    <div class="modal fade" id="modalNuevoContrato">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Contrato</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoContrato">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Depositante *</label>
                                    <select class="form-control select2" id="id_depositante" style="width: 100%;" required>
                                        <option value="">Seleccionar depositante...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Gestión *</label>
                                    <input type="text" class="form-control" id="gestion" value="<?php echo date('Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Inicio *</label>
                                    <input type="date" class="form-control" id="fecha_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Fin *</label>
                                    <input type="date" class="form-control" id="fecha_fin" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monto Total Mensual (Bs.) *</label>
                                    <input type="number" class="form-control" id="monto_total_mes" step="0.01" required>
                                </div>
                            </div>
                         <!--   <div class="col-md-6">
                                <div class="form-group">
                                    <label>Testigos</label>
                                    <input type="text" class="form-control" id="testigos" placeholder="Nombres de testigos">
                                </div>
                            </div>
                        </div>
                        -->
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarContrato()">
                        <i class="fas fa-save"></i> Crear Contrato
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
// Variable global para la tabla
var tablaContratos;

$(document).ready(function() {
    console.log('=== MÓDULO CONTRATOS INICIADO ===');
    
    // Inicializar Select2
    $('.select2').select2();
    
    // Cargar depositantes en el select
    cargarDepositantes();
    
    // Inicializar DataTable
    inicializarTablaContratos();
    
    // Establecer fecha por defecto
    var hoy = new Date();
    var fechaFin = new Date();
    fechaFin.setFullYear(fechaFin.getFullYear() + 1);
    
    $('#fecha_inicio').val(hoy.toISOString().split('T')[0]);
    $('#fecha_fin').val(fechaFin.toISOString().split('T')[0]);
});

function inicializarTablaContratos() {
    console.log('Inicializando tabla de contratos...');
    
    tablaContratos = $('#tablaContratos').DataTable({
        "ajax": {
            "url": "../../controllers/ContratoController.php?action=listar",
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error cargando contratos:', error, thrown);
                Swal.fire('Error', 'Error al cargar contratos', 'error');
            }
        },
        "columns": [
            {"data": "numero_contrato"},
            {"data": "nombre_completo"},
            {"data": "ci_nit"},
            {
                "data": null,
                "render": function(data) {
                    // Usar las fechas formateadas del controlador
                    return (data.fecha_inicio_formatted || '') + ' - ' + (data.fecha_fin_formatted || '');
                }
            },
            {
                "data": "monto_total_mes",
                "render": function(data) {
                    return data ? 'Bs. ' + parseFloat(data).toFixed(2) : 'Bs. 0.00';
                }
            },
            {
                "data": "total_items",
                "render": function(data) {
                    return data ? data : '0';
                }
            },
            {
                "data": "estado",
                "render": function(data, type, row) {
                    var badge = '';
                    var dias = row.dias_restantes || 0;
                    
                    switch(data) {
                        case 'VIGENTE':
                            badge = '<span class="badge badge-success">VIGENTE</span>';
                            if(dias < 30) {
                                badge += ' <span class="badge badge-warning">' + dias + ' días</span>';
                            }
                            break;
                        case 'FINALIZADO':
                            badge = '<span class="badge badge-secondary">FINALIZADO</span>';
                            break;
                        case 'RENOVADO':
                            badge = '<span class="badge badge-info">RENOVADO</span>';
                            break;
                        case 'CANCELADO':
                            badge = '<span class="badge badge-danger">CANCELADO</span>';
                            break;
                        default:
                            badge = '<span class="badge badge-light">' + (data || '') + '</span>';
                    }
                    return badge;
                }
            },
            {
                "data": "id_contrato",
                "render": function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info mr-1" onclick="verContrato(${data})" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning mr-1" onclick="editarContrato(${data})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-success mr-1" onclick="imprimirContrato(${data})" title="Imprimir">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarContrato(${data})" title="Cancelar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
        `;
    }
}
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        "order": [[0, "desc"]],
        "initComplete": function() {
            console.log('DataTable de contratos cargado correctamente');
        }
    });
}

function cargarDepositantes() {
    $.ajax({
        url: "../../controllers/DepositanteController.php?action=listar",
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                var select = $('#id_depositante');
                select.empty();
                select.append('<option value="">Seleccionar depositante...</option>');
                
                response.data.forEach(function(depositante) {
                    if(depositante.estado === 'ACTIVO') {
                        select.append(
                            '<option value="' + depositante.id_depositante + '">' + 
                            depositante.nombre_completo + ' - ' + depositante.ci_nit + 
                            '</option>'
                        );
                    }
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cargar depositantes', 'error');
        }
    });
}

function abrirModalNuevoContrato() {
    $('#modalNuevoContrato').modal('show');
}

function guardarContrato() {
    // Validar campos requeridos
    var id_depositante = $('#id_depositante').val();
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();
    var gestion = $('#gestion').val();
    var monto_total_mes = $('#monto_total_mes').val();
    
    if(!id_depositante || !fecha_inicio || !fecha_fin || !gestion || !monto_total_mes) {
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
        action: 'crear',
        id_depositante: id_depositante,
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        gestion: gestion,
        monto_total_mes: monto_total_mes,
        observaciones: $('#observaciones').val()
        // SIN testigos
    };
    
    console.log('Enviando contrato:', datos);
    
    // Enviar por AJAX
    $.ajax({
        url: "../../controllers/ContratoController.php",
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                    $('#modalNuevoContrato').modal('hide');
                    tablaContratos.ajax.reload();
                    
                    // Limpiar formulario
                    $('#formNuevoContrato')[0].reset();
                    $('#fecha_inicio').val(new Date().toISOString().split('T')[0]);
                    
                    var fechaFin = new Date();
                    fechaFin.setFullYear(fechaFin.getFullYear() + 1);
                    $('#fecha_fin').val(fechaFin.toISOString().split('T')[0]);
                    
                    // Redirigir a ver contrato o agregar items
                    if(response.id_contrato) {
                        setTimeout(() => {
                            Swal.fire({
                                title: 'Contrato Creado',
                                text: '¿Desea agregar items al contrato ahora?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, agregar items',
                                cancelButtonText: 'No, más tarde'
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    // Aquí irá la función para agregar items
                                    verContrato(response.id_contrato);
                                }
                            });
                        }, 1000);
                    }
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
        }
    });
}

function verContrato(id) {
    console.log('Cargando contrato ID:', id);
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo información del contrato',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener datos del contrato
    $.ajax({
        url: "../../controllers/ContratoController.php?action=obtener&id=" + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if(response.success && response.data) {
                var contrato = response.data;
                
                // Crear contenido del modal
                var contenido = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Contrato</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>N° Contrato:</th>
                                    <td>${contrato.numero_contrato}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>${contrato.estado}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Inicio:</th>
                                    <td>${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Fin:</th>
                                    <td>${contrato.fecha_fin_formatted || contrato.fecha_fin}</td>
                                </tr>
                                <tr>
                                    <th>Monto Mensual:</th>
                                    <td>Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Información del Depositante</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Nombre:</th>
                                    <td>${contrato.nombre_completo}</td>
                                </tr>
                                <tr>
                                    <th>CI/NIT:</th>
                                    <td>${contrato.ci_nit}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono:</th>
                                    <td>${contrato.telefono || 'No registrado'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                
                if(contrato.observaciones) {
                    contenido += `
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Observaciones</h5>
                                <p>${contrato.observaciones}</p>
                            </div>
                        </div>
                    `;
                }
                
                // Mostrar modal con la información
                Swal.fire({
                    title: 'Detalle del Contrato',
                    html: contenido,
                    width: '800px',
                    showCloseButton: true,
                    showConfirmButton: false
                });
                
            } else {
                Swal.fire('Error', response.message || 'No se pudieron cargar los datos', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error cargando contrato:', error);
            Swal.fire('Error', 'Error al cargar el contrato: ' + error, 'error');
        }
    });
}

function imprimirContrato(id) {
    console.log('Generando contrato para imprimir ID:', id);
    
    // Mostrar opciones
    Swal.fire({
        title: 'Imprimir Contrato',
        html: `
            <div class="text-left">
                <p>Seleccione una opción:</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="opcionImprimir" id="opcionVista" value="vista" checked>
                    <label class="form-check-label" for="opcionVista">
                        <i class="fas fa-eye text-info"></i> Vista previa para imprimir
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="opcionImprimir" id="opcionPDF" value="pdf">
                    <label class="form-check-label" for="opcionPDF">
                        <i class="fas fa-file-pdf text-danger"></i> Descargar como PDF
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const opcion = document.querySelector('input[name="opcionImprimir"]:checked').value;
            return { opcion: opcion };
        }
    }).then((result) => {
        if(result.isConfirmed) {
            const opcion = result.value.opcion;
            
            if(opcion === 'pdf') {
                // Generar y descargar PDF
                generarPDFContrato(id);
            } else {
                // Vista previa HTML
                mostrarVistaPreviaContrato(id);
            }
        }
    });
}

function mostrarVistaPreviaContrato(id) {
    // Cargar datos del contrato
    $.ajax({
        url: "../../controllers/ContratoController.php?action=obtener&id=" + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                var contrato = response.data;
                
                // Generar HTML del contrato
                var htmlContrato = generarHTMLParaImpresion(contrato);
                
                // Crear ventana de impresión
                var ventanaImpresion = window.open('', '_blank', 'width=900,height=700');
                
                ventanaImpresion.document.write(`
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Contrato ${contrato.numero_contrato}</title>
                        <style>
                            /* Estilos para impresión */
                            @media print {
                                .no-print { display: none !important; }
                                body { margin: 0 !important; padding: 0 !important; }
                                .print-toolbar { display: none !important; }
                                @page { margin: 15mm; }
                            }
                            
                            /* Estilos para pantalla */
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 20px;
                                background: #f5f5f5;
                            }
                            
                            .print-toolbar {
                                background: #2c3e50;
                                color: white;
                                padding: 15px;
                                margin-bottom: 20px;
                                border-radius: 8px;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            }
                            
                            .print-toolbar h3 {
                                margin: 0;
                                font-size: 18px;
                            }
                            
                            .print-toolbar button {
                                background: #3498db;
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 5px;
                                cursor: pointer;
                                font-size: 14px;
                                margin-left: 10px;
                            }
                            
                            .print-toolbar button:hover {
                                background: #2980b9;
                            }
                            
                            .print-toolbar button.close-btn {
                                background: #e74c3c;
                            }
                            
                            .print-toolbar button.close-btn:hover {
                                background: #c0392b;
                            }
                        </style>
                    </head>
                    <body>
                        <!-- Barra de herramientas -->
                        <div class="print-toolbar no-print">
                            <h3>
                                <i class="fas fa-file-contract"></i>
                                Contrato: ${contrato.numero_contrato} - ${contrato.nombre_completo}
                            </h3>
                            <div>
                                <button onclick="window.print()">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                <button class="close-btn" onclick="window.close()">
                                    <i class="fas fa-times"></i> Cerrar
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contenido del contrato -->
                        ${htmlContrato}
                        
                        <!-- Font Awesome para íconos -->
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                        
                        <script>
                            // Atajos de teclado
                            document.addEventListener('keydown', function(e) {
                                // Ctrl+P para imprimir
                                if(e.ctrlKey && e.key === 'p') {
                                    e.preventDefault();
                                    window.print();
                                }
                                // Esc para cerrar
                                if(e.key === 'Escape') {
                                    window.close();
                                }
                            });
                            
                            // Enfocar botón de imprimir
                            window.onload = function() {
                                var printBtn = document.querySelector('button[onclick="window.print()"]');
                                if(printBtn) printBtn.focus();
                            };
                        <\/script>
                    </body>
                    </html>
                `);
                
                ventanaImpresion.document.close();
                
            } else {
                Swal.fire('Error', response.message || 'No se pudieron cargar los datos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cargando contrato:', error);
            Swal.fire('Error', 'Error al cargar el contrato: ' + error, 'error');
        }
    });
}

function crearHTMLParaPDF(contrato) {
    var fechaActual = new Date().toLocaleDateString('es-ES');
    
    return `
        <div id="pdf-contenido" style="font-family: Arial, sans-serif; padding: 20px; width: 210mm; background: white;">
            <!-- Logo y Encabezado -->
            <div style="text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 15px; margin-bottom: 20px;">
                <div style="display: inline-block; background: #2c3e50; color: white; padding: 10px 30px; border-radius: 5px;">
                    <h2 style="margin: 0; font-size: 24px;">CONTRATO DE DEPÓSITO</h2>
                </div>
                <h3 style="color: #2c3e50; margin: 10px 0 5px 0; font-size: 18px;">N° ${contrato.numero_contrato}</h3>
                <p style="color: #7f8c8d; margin: 0;">Fecha de emisión: ${fechaActual}</p>
            </div>
            
            <!-- Información de las partes -->
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    <i class="fas fa-users"></i> PARTES CONTRATANTES
                </h4>
                
                <div style="display: flex; justify-content: space-between; margin-top: 15px; gap: 20px;">
                    <!-- Depositante -->
                    <div style="flex: 1; border: 1px solid #3498db; border-radius: 8px; padding: 15px;">
                        <h5 style="color: #3498db; margin: 0 0 10px 0; font-size: 14px;">
                            <i class="fas fa-user"></i> DEPOSITANTE
                        </h5>
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="padding: 4px 0; width: 100px;"><strong>Nombre:</strong></td>
                                <td style="padding: 4px 0;">${contrato.nombre_completo}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>CI/NIT:</strong></td>
                                <td style="padding: 4px 0;">${contrato.ci_nit}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 4px 0;">${contrato.telefono || 'No registrado'}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Depósito -->
                    <div style="flex: 1; border: 1px solid #e74c3c; border-radius: 8px; padding: 15px;">
                        <h5 style="color: #e74c3c; margin: 0 0 10px 0; font-size: 14px;">
                            <i class="fas fa-warehouse"></i> DEPÓSITO
                        </h5>
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="padding: 4px 0; width: 100px;"><strong>Empresa:</strong></td>
                                <td style="padding: 4px 0;">DEPÓSITOS SEGUROS S.A.</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Dirección:</strong></td>
                                <td style="padding: 4px 0;">Av. Principal #123, Ciudad</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 4px 0;">+591 12345678</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>NIT:</strong></td>
                                <td style="padding: 4px 0;">123456789</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Datos del contrato -->
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    <i class="fas fa-file-contract"></i> DATOS DEL CONTRATO
                </h4>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #dee2e6; width: 150px;"><strong>Fecha Inicio:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Fecha Fin:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.fecha_fin_formatted || contrato.fecha_fin}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Monto Mensual:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">
                            <span style="font-size: 14px; color: #27ae60;">
                                Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Gestión:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.gestion}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Estado:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; 
                                   background-color: ${contrato.estado === 'VIGENTE' ? '#28a745' : contrato.estado === 'FINALIZADO' ? '#6c757d' : '#dc3545'}; 
                                   color: white;">
                                ${contrato.estado}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Términos y condiciones -->
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    <i class="fas fa-gavel"></i> TÉRMINOS Y CONDICIONES
                </h4>
                
                <div style="margin-top: 10px; font-size: 12px; line-height: 1.6;">
                    <p><strong>CLÁUSULA PRIMERA (OBJETO):</strong> El presente contrato tiene por objeto el depósito y almacenamiento de bienes muebles propiedad del depositante.</p>
                    
                    <p><strong>CLÁUSULA SEGUNDA (DURACIÓN):</strong> El contrato tendrá una vigencia desde el ${contrato.fecha_inicio_formatted || contrato.fecha_inicio} hasta el ${contrato.fecha_fin_formatted || contrato.fecha_fin}.</p>
                    
                    <p><strong>CLÁUSULA TERCERA (PAGO):</strong> El depositante se obliga a pagar mensualmente la cantidad de <strong>Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</strong> por concepto de almacenamiento. El pago se realizará dentro de los primeros 5 días de cada mes.</p>
                    
                    <p><strong>CLÁUSULA CUARTA (MORA):</strong> El incumplimiento en el pago generará un recargo del 10% mensual sobre el monto adeudado.</p>
                    
                    <p><strong>CLÁUSULA QUINTA (RESPONSABILIDAD):</strong> El depósito no se hace responsable por daños causados por caso fortuito o fuerza mayor.</p>
                    
                    <p><strong>CLÁUSULA SEXTA (RETIRO):</strong> Para la retirada de los bienes, el depositante deberá presentar este contrato y documento de identidad original.</p>
                </div>
            </div>
            
            <!-- Observaciones -->
            ${contrato.observaciones ? `
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    <i class="fas fa-sticky-note"></i> OBSERVACIONES ADICIONALES
                </h4>
                <div style="margin-top: 10px; padding: 12px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; font-size: 12px;">
                    ${contrato.observaciones}
                </div>
            </div>
            ` : ''}
            
            <!-- Firmas -->
            <div style="margin-top: 40px;">
                <div style="display: flex; justify-content: space-between; gap: 20px;">
                    <!-- Firma depositante -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 20px; margin-top: 60px;">
                            <p style="font-weight: bold; margin: 0; font-size: 13px;">___________________________________</p>
                            <p style="margin: 5px 0; font-size: 12px;"><strong>${contrato.nombre_completo}</strong></p>
                            <p style="margin: 0; color: #666; font-size: 11px;">DEPOSITANTE</p>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 11px;">C.I. ${contrato.ci_nit}</p>
                        </div>
                    </div>
                    
                    <!-- Firma depósito -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 20px; margin-top: 60px;">
                            <p style="font-weight: bold; margin: 0; font-size: 13px;">___________________________________</p>
                            <p style="margin: 5px 0; font-size: 12px;"><strong>ADMINISTRACIÓN GENERAL</strong></p>
                            <p style="margin: 0; color: #666; font-size: 11px;">DEPÓSITOS SEGUROS S.A.</p>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 11px;">NIT: 123456789</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pie de página -->
            <div style="margin-top: 50px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #95a5a6;">
                <p>Contrato generado automáticamente por el Sistema de Gestión de Depósitos</p>
                <p>Fecha de generación: ${fechaActual} | Documento: ${contrato.numero_contrato}</p>
                <p style="font-style: italic;">Este documento es válido sin firma manuscrita para fines de control interno.</p>
            </div>
        </div>
    `;
}

function generarHTMLContrato(contrato) {
    var fechaActual = new Date().toLocaleDateString('es-ES');
    
    return `
        <div id="contenido-contrato" style="font-family: Arial, sans-serif; padding: 20px;">
            <!-- Encabezado -->
            <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
                <h2 style="color: #2c3e50; margin: 0;">CONTRATO DE DEPÓSITO</h2>
                <h3 style="color: #7f8c8d; margin: 5px 0;">N° ${contrato.numero_contrato}</h3>
                <p style="color: #666; margin: 5px 0;">Fecha: ${fechaActual}</p>
            </div>
            
            <!-- Información de las partes -->
            <div style="margin-bottom: 30px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 5px;">PARTES CONTRATANTES</h4>
                
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div style="width: 48%;">
                        <h5 style="color: #3498db; margin-bottom: 10px;">DEPOSITANTE</h5>
                        <table style="width: 100%;">
                            <tr>
                                <td style="padding: 5px 0;"><strong>Nombre:</strong></td>
                                <td style="padding: 5px 0;">${contrato.nombre_completo}</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;"><strong>CI/NIT:</strong></td>
                                <td style="padding: 5px 0;">${contrato.ci_nit}</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 5px 0;">${contrato.telefono || 'No registrado'}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="width: 48%;">
                        <h5 style="color: #3498db; margin-bottom: 10px;">DEPÓSITO</h5>
                        <table style="width: 100%;">
                            <tr>
                                <td style="padding: 5px 0;"><strong>Razón Social:</strong></td>
                                <td style="padding: 5px 0;">DEPÓSITOS SEGUROS S.A.</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;"><strong>Dirección:</strong></td>
                                <td style="padding: 5px 0;">Av. Principal #123</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 5px 0;">+591 12345678</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Términos del contrato -->
            <div style="margin-bottom: 30px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 5px;">TÉRMINOS DEL CONTRATO</h4>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Fecha Inicio:</strong></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Fecha Fin:</strong></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">${contrato.fecha_fin_formatted || contrato.fecha_fin}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Monto Mensual:</strong></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Gestión:</strong></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">${contrato.gestion}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Estado:</strong></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">
                            <span class="badge ${contrato.estado === 'VIGENTE' ? 'badge-success' : contrato.estado === 'FINALIZADO' ? 'badge-secondary' : 'badge-danger'}">
                                ${contrato.estado}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Cláusulas -->
            <div style="margin-bottom: 30px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 5px;">CLÁUSULAS</h4>
                
                <ol style="margin-top: 15px; padding-left: 20px;">
                    <li style="margin-bottom: 10px;">
                        <p>
                        El depositante se obliga a pagar mensualmente la cantidad de <strong>Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</strong> por concepto de almacenamiento.
                        <p>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <p>
                        El contrato tiene vigencia desde el ${contrato.fecha_inicio_formatted || contrato.fecha_inicio} hasta el ${contrato.fecha_fin_formatted || contrato.fecha_fin}.
                        <p>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <p>
                        El incumplimiento en el pago mensual generará recargos del 10% sobre el monto adeudado.
                        <p>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <p>
                        El depósito no se hace responsable por daños causados por fuerza mayor.
                        </p>
                    </li>
                    <li>
                        <p>
                        Para la retira de los bienes, el depositante deberá presentar este contrato y documento de identidad.
                        <p>
                    </li>
                </ol>
            </div>
            
            <!-- Observaciones -->
            ${contrato.observaciones ? `
            <div style="margin-bottom: 30px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 5px;">OBSERVACIONES ADICIONALES</h4>
                <p style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #3498db;">
                    ${contrato.observaciones}
                </p>
            </div>
            ` : ''}
            
            <!-- Firmas -->
            <div style="margin-top: 50px;">
                <div style="display: flex; justify-content: space-between;">
                    <div style="width: 45%; text-align: center;">
                        <div style="border-top: 1px solid #333; padding-top: 10px; margin-top: 60px;">
                            <strong>DEPOSITANTE</strong><br>
                            ${contrato.nombre_completo}<br>
                            CI: ${contrato.ci_nit}
                        </div>
                    </div>
                    
                    <div style="width: 45%; text-align: center;">
                        <div style="border-top: 1px solid #333; padding-top: 10px; margin-top: 60px;">
                            <strong>ABEL PEREZ AGUILAR</strong><br>
                            ADMINISTRADOR<br>
                            DEPÓSITO CONTINENTAL
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pie de página -->
            <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
                <p>Documento generado el ${fechaActual} - Sistema de Gestión de Depósitos</p>
            </div>
        </div>
    `;
}

function imprimirHTMLContrato(htmlContrato) {
    // Crear ventana de impresión
    var ventanaImpresion = window.open('', '_blank');
    
    ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Contrato de Depósito</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
            <style>
                @media print {
                    body { margin: 0; padding: 0; }
                    .no-print { display: none !important; }
                    @page { margin: 20mm; }
                }
                .print-header {
                    display: none;
                }
                @media print {
                    .print-header {
                        display: block;
                        text-align: center;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 2px solid #000;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-header no-print">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button class="btn btn-secondary" onclick="window.close()">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
            ${htmlContrato}
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"><\/script>
            <script>
                $(document).ready(function() {
                    // Auto-imprimir si se desea
                    // setTimeout(() => window.print(), 1000);
                });
            <\/script>
        </body>
        </html>
    `);
    
    ventanaImpresion.document.close();
}

function verContrato(id) {
    // Redirigir a la página de detalle
    window.location.href = 'ver.php?id=' + id;
}

function editarContrato(id) {
    window.location.href = 'editar.php?id=' + id;
}

function formatDate(dateString) {
    if(!dateString) return '';
    
    // Si ya está en formato dd/mm/yyyy
    if(dateString.includes('/')) {
        return dateString;
    }
    
    // Si está en formato YYYY-MM-DD
    var date = new Date(dateString);
    if(isNaN(date.getTime())) {
        return dateString;
    }
    
    var day = String(date.getDate()).padStart(2, '0');
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var year = date.getFullYear();
    
    return day + '/' + month + '/' + year;
}

// ============================================
// FUNCIONES PARA PDF (AGREGAR AL FINAL DEL SCRIPT)
// ============================================

function generarPDFContrato(id) {
    Swal.fire({
        title: 'Generando PDF...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: "../../controllers/ContratoController.php?action=obtener&id=" + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if(response.success && response.data) {
                var contrato = response.data;
                var fechaActual = new Date().toLocaleDateString('es-ES');
                
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF();
                
                // Configurar
                let y = 20;
                const margen = 20;
                
                // Título
                pdf.setFontSize(16);
                pdf.setTextColor(0, 0, 128);
                pdf.text("CONTRATO DE DEPÓSITO", 105, y, { align: 'center' });
                y += 10;
                
                pdf.setFontSize(12);
                pdf.setTextColor(128, 0, 0);
                pdf.text("N° " + contrato.numero_contrato, 105, y, { align: 'center' });
                y += 15;
                
                // Línea
                pdf.setDrawColor(0, 0, 0);
                pdf.line(margen, y, 190, y);
                y += 10;
                
                // Información básica
                pdf.setFontSize(11);
                pdf.setTextColor(0, 0, 0);
                
                pdf.text("Fecha de emisión: " + fechaActual, margen, y);
                y += 7;
                
                pdf.text("Fecha Inicio: " + (contrato.fecha_inicio_formatted || contrato.fecha_inicio), margen, y);
                y += 7;
                
                pdf.text("Fecha Fin: " + (contrato.fecha_fin_formatted || contrato.fecha_fin), margen, y);
                y += 7;
                
                pdf.text("Gestión: " + contrato.gestion, margen, y);
                y += 7;
                
                pdf.text("Monto Mensual: Bs. " + parseFloat(contrato.monto_total_mes || 0).toFixed(2), margen, y);
                y += 7;
                
                pdf.text("Estado: " + contrato.estado, margen, y);
                y += 12;
                
                // Depositante
                pdf.setFontSize(12);
                pdf.setTextColor(0, 0, 128);
                pdf.text("DEPOSITANTE:", margen, y);
                y += 7;
                
                pdf.setFontSize(11);
                pdf.setTextColor(0, 0, 0);
                
                pdf.text("Nombre: " + contrato.nombre_completo, margen + 5, y);
                y += 7;
                
                pdf.text("CI/NIT: " + contrato.ci_nit, margen + 5, y);
                y += 7;
                
                if(contrato.telefono) {
                    pdf.text("Teléfono: " + contrato.telefono, margen + 5, y);
                    y += 7;
                }
                y += 10;
                
                // Firmas
                pdf.setFontSize(11);
                pdf.text("___________________________", margen, 200);
                pdf.text(contrato.nombre_completo, margen + 40, 205, { align: 'center' });
                pdf.text("DEPOSITANTE", margen + 40, 210, { align: 'center' });
                
                pdf.text("___________________________", margen + 100, 200);
                pdf.text("ADMINISTRACIÓN", margen + 140, 205, { align: 'center' });
                pdf.text("DEPÓSITOS SEGUROS S.A.", margen + 140, 210, { align: 'center' });
                
                // Guardar
                const nombreArchivo = 'Contrato_' + contrato.numero_contrato + '.pdf';
                pdf.save(nombreArchivo);
                
                Swal.fire({
                    title: '¡Listo!',
                    html: 'PDF <strong>' + nombreArchivo + '</strong> generado',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                
            } else {
                Swal.fire('Error', response.message || 'Error al obtener datos', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}
function crearHTMLParaPDF(contrato) {
    var fechaActual = new Date().toLocaleDateString('es-ES');
    
    return `
        <div id="pdf-contenido" style="font-family: Arial, sans-serif; padding: 20px; width: 210mm; background: white;">
            <!-- Logo y Encabezado -->
            <div style="text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 15px; margin-bottom: 20px;">
                <div style="display: inline-block; background: #2c3e50; color: white; padding: 10px 30px; border-radius: 5px;">
                    <h2 style="margin: 0; font-size: 24px;">CONTRATO DE DEPÓSITO</h2>
                </div>
                <h3 style="color: #2c3e50; margin: 10px 0 5px 0; font-size: 18px;">N° ${contrato.numero_contrato}</h3>
                <p style="color: #7f8c8d; margin: 0;">Fecha de emisión: ${fechaActual}</p>
            </div>
            
            <!-- Información de las partes -->
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    PARTES CONTRATANTES
                </h4>
                
                <div style="display: flex; justify-content: space-between; margin-top: 15px; gap: 20px;">
                    <!-- Depositante -->
                    <div style="flex: 1; border: 1px solid #3498db; border-radius: 8px; padding: 15px;">
                        <h5 style="color: #3498db; margin: 0 0 10px 0; font-size: 14px;">DEPOSITANTE</h5>
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="padding: 4px 0; width: 100px;"><strong>Nombre:</strong></td>
                                <td style="padding: 4px 0;">${contrato.nombre_completo}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>CI/NIT:</strong></td>
                                <td style="padding: 4px 0;">${contrato.ci_nit}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 4px 0;">${contrato.telefono || 'No registrado'}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Depósito -->
                    <div style="flex: 1; border: 1px solid #e74c3c; border-radius: 8px; padding: 15px;">
                        <h5 style="color: #e74c3c; margin: 0 0 10px 0; font-size: 14px;">DEPÓSITO</h5>
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="padding: 4px 0; width: 100px;"><strong>Empresa:</strong></td>
                                <td style="padding: 4px 0;">DEPÓSITOS SEGUROS S.A.</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Dirección:</strong></td>
                                <td style="padding: 4px 0;">Av. Principal #123, Ciudad</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Teléfono:</strong></td>
                                <td style="padding: 4px 0;">+591 12345678</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Datos del contrato -->
            <div style="margin-bottom: 25px;">
                <h4 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 8px; font-size: 16px;">
                    DATOS DEL CONTRATO
                </h4>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #dee2e6; width: 150px;"><strong>Fecha Inicio:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Fecha Fin:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.fecha_fin_formatted || contrato.fecha_fin}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Monto Mensual:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Gestión:</strong></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;">${contrato.gestion}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Firmas -->
            <div style="margin-top: 40px;">
                <div style="display: flex; justify-content: space-between; gap: 20px;">
                    <!-- Firma depositante -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 20px; margin-top: 60px;">
                            <p style="font-weight: bold; margin: 0; font-size: 13px;">___________________________________</p>
                            <p style="margin: 5px 0; font-size: 12px;"><strong>${contrato.nombre_completo}</strong></p>
                            <p style="margin: 0; color: #666; font-size: 11px;">DEPOSITANTE</p>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 11px;">C.I. ${contrato.ci_nit}</p>
                        </div>
                    </div>
                    
                    <!-- Firma depósito -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 20px; margin-top: 60px;">
                            <p style="font-weight: bold; margin: 0; font-size: 13px;">___________________________________</p>
                            <p style="margin: 5px 0; font-size: 12px;"><strong>ADMINISTRACIÓN GENERAL</strong></p>
                            <p style="margin: 0; color: #666; font-size: 11px;">DEPÓSITOS SEGUROS S.A.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function eliminarContrato(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "¿Desea cancelar este contrato?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, mantener'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: "../../controllers/ContratoController.php",
                type: 'POST',
                data: {
                    action: 'cambiar_estado',
                    id: id,
                    estado: 'CANCELADO'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire('Cancelado!', response.message, 'success');
                        tablaContratos.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al cancelar contrato', 'error');
                }
            });
        }
    });
}

function generarHTMLParaImpresion(contrato) {
    var fechaActual = new Date().toLocaleDateString('es-ES');
    
    return `
        <div style="font-family: Arial, sans-serif; max-width: 210mm; margin: 0 auto; padding: 20px;">
            <!-- Encabezado -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="display: inline-block; background: #2c3e50; color: white; padding: 15px 40px; border-radius: 8px;">
                    <h1 style="margin: 0; font-size: 28px; font-weight: bold;">CONTRATO DE DEPÓSITO</h1>
                    <h2 style="margin: 5px 0 0 0; font-size: 20px; color: #ecf0f1;">N° ${contrato.numero_contrato}</h2>
                </div>
                <p style="color: #7f8c8d; margin-top: 10px;">Fecha de emisión: ${fechaActual}</p>
            </div>
            
            <!-- Información de las partes - ESTILO INLINE -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 8px; margin-bottom: 20px;">
                    PARTES CONTRATANTES
                </h3>
                
                <!-- Contenedor de 2 columnas -->
                <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px;">
                    <!-- Columna Izquierda - Depositante -->
                    <div style="flex: 1; border: 2px solid #3498db; border-radius: 10px; overflow: hidden;">
                        <div style="background: #3498db; color: white; padding: 12px; font-weight: bold;">
                            DEPOSITANTE
                        </div>
                        <div style="padding: 15px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 8px 0; width: 120px; font-weight: bold;">Nombre:</td>
                                    <td style="padding: 8px 0;">${contrato.nombre_completo}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">CI/NIT:</td>
                                    <td style="padding: 8px 0;">${contrato.ci_nit}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Teléfono:</td>
                                    <td style="padding: 8px 0;">${contrato.telefono || 'No registrado'}</td>
                                </tr>
                                ${contrato.direccion_depositante ? `
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Dirección:</td>
                                    <td style="padding: 8px 0;">${contrato.direccion_depositante}</td>
                                </tr>
                                ` : ''}
                            </table>
                        </div>
                    </div>
                    
                    <!-- Columna Derecha - Depósito -->
                    <div style="flex: 1; border: 2px solid #e74c3c; border-radius: 10px; overflow: hidden;">
                        <div style="background: #e74c3c; color: white; padding: 12px; font-weight: bold;">
                            DEPÓSITO
                        </div>
                        <div style="padding: 15px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 8px 0; width: 120px; font-weight: bold;">Empresa:</td>
                                    <td style="padding: 8px 0;">DEPÓSITOS SEGUROS S.A.</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Dirección:</td>
                                    <td style="padding: 8px 0;">Av. Principal #123, Ciudad</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">Teléfono:</td>
                                    <td style="padding: 8px 0;">+591 12345678</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-weight: bold;">NIT:</td>
                                    <td style="padding: 8px 0;">123456789</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Datos del contrato -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #2c3e50; border-bottom: 3px solid #2ecc71; padding-bottom: 8px; margin-bottom: 20px;">
                    DATOS DEL CONTRATO
                </h3>
                
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd; width: 180px; font-weight: bold;">Fecha Inicio:</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Fecha Fin:</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">${contrato.fecha_fin_formatted || contrato.fecha_fin}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Monto Mensual:</td>
                        <td style="padding: 12px; border: 1px solid #ddd; color: #27ae60; font-weight: bold;">
                            Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Gestión:</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">${contrato.gestion}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Estado:</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; 
                                   background-color: ${contrato.estado === 'VIGENTE' ? '#28a745' : contrato.estado === 'FINALIZADO' ? '#6c757d' : '#dc3545'}; 
                                   color: white;">
                                ${contrato.estado}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Términos y condiciones -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #2c3e50; border-bottom: 3px solid #9b59b6; padding-bottom: 8px; margin-bottom: 20px;">
                    TÉRMINOS Y CONDICIONES
                </h3>
                
                <div style="padding-left: 20px;">
                    <ol style="margin: 0; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            El depositante se obliga a pagar mensualmente la cantidad de 
                            <strong>Bs. ${parseFloat(contrato.monto_total_mes || 0).toFixed(2)}</strong> 
                            por concepto de almacenamiento.
                        </li>
                        <li style="margin-bottom: 10px;">
                            El contrato tiene vigencia desde el 
                            <strong>${contrato.fecha_inicio_formatted || contrato.fecha_inicio}</strong> 
                            hasta el 
                            <strong>${contrato.fecha_fin_formatted || contrato.fecha_fin}</strong>.
                        </li>
                        <li style="margin-bottom: 10px;">
                            El pago debe realizarse dentro de los primeros 5 días de cada mes.
                        </li>
                        <li style="margin-bottom: 10px;">
                            El incumplimiento en el pago generará recargos del 10% mensual sobre el monto adeudado.
                        </li>
                        <li style="margin-bottom: 10px;">
                            El depósito no se hace responsable por daños causados por caso fortuito o fuerza mayor.
                        </li>
                        <li>
                            Para la retirada de los bienes, el depositante deberá presentar este contrato y documento de identidad original.
                        </li>
                    </ol>
                </div>
            </div>
            
            <!-- Observaciones -->
            ${contrato.observaciones ? `
            <div style="margin-bottom: 30px;">
                <h3 style="color: #2c3e50; border-bottom: 3px solid #f39c12; padding-bottom: 8px; margin-bottom: 20px;">
                    OBSERVACIONES ADICIONALES
                </h3>
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-left: 5px solid #f39c12; padding: 15px; border-radius: 5px;">
                    ${contrato.observaciones}
                </div>
            </div>
            ` : ''}
            
            <!-- Firmas -->
            <div style="margin-top: 60px;">
                <div style="display: flex; justify-content: space-between; gap: 30px;">
                    <!-- Firma depositante -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 30px; margin-top: 80px;">
                            <p style="margin: 0; font-weight: bold; font-size: 16px;">${contrato.nombre_completo}</p>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">DEPOSITANTE</p>
                            <p style="margin: 0; color: #999; font-size: 13px;">C.I. ${contrato.ci_nit}</p>
                        </div>
                    </div>
                    
                    <!-- Firma depósito -->
                    <div style="flex: 1; text-align: center;">
                        <div style="border-top: 2px solid #2c3e50; padding-top: 30px; margin-top: 80px;">
                            <p style="margin: 0; font-weight: bold; font-size: 16px;">ADMINISTRACIÓN GENERAL</p>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">DEPÓSITOS SEGUROS S.A.</p>
                            <p style="margin: 0; color: #999; font-size: 13px;">NIT: 123456789</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pie de página -->
            <div style="margin-top: 70px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #95a5a6; font-size: 12px;">
                <p style="margin: 5px 0;">Documento generado automáticamente por el Sistema de Gestión de Depósitos</p>
                <p style="margin: 5px 0;">Fecha de generación: ${fechaActual} | Contrato N°: ${contrato.numero_contrato}</p>
                <p style="margin: 5px 0; font-style: italic;">Este documento es válido sin firma manuscrita para fines de control interno.</p>
            </div>
        </div>
    `;
}
</script>

</body>
</html>