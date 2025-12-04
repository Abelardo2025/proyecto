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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Depositantes - Sistema Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
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
                        <h1>Gestión de Depositantes</h1>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-success float-right" onclick="abrirModalAgregar()">
                            <i class="fas fa-plus"></i> Nuevo Depositante
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <table id="tablaDepositantes" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>CI/NIT</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal AGREGAR -->
    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Depositante</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre_completo" required>
                    </div>
                    <div class="form-group">
                        <label>CI/NIT *</label>
                        <input type="text" class="form-control" id="ci_nit" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="telefono">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea class="form-control" id="direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDepositante()">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Depositante</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" class="form-control" id="edit_nombre_completo" required>
                    </div>
                    <div class="form-group">
                        <label>CI/NIT *</label>
                        <input type="text" class="form-control" id="edit_ci_nit" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="edit_telefono">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit_email">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="edit_estado">
                            <option value="ACTIVO">ACTIVO</option>
                            <option value="INACTIVO">INACTIVO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea class="form-control" id="edit_direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarDepositante()">
                        <i class="fas fa-save"></i> Actualizar
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

<script>
// Variable global para la tabla
var tablaDepositantes;

$(document).ready(function() {
    console.log('=== SISTEMA INICIADO ===');
    
    // Inicializar DataTable
    inicializarTabla();
});

function inicializarTabla() {
    tablaDepositantes = $('#tablaDepositantes').DataTable({
        "ajax": {
            "url": "../../controllers/DepositanteController.php?action=listar",
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error cargando datos:', error);
                alert('Error al cargar datos: ' + error);
            }
        },
        "columns": [
            {"data": "id_depositante"},
            {"data": "nombre_completo"},
            {"data": "ci_nit"},
            {
                "data": "telefono",
                "render": function(data) {
                    return data || '-';
                }
            },
            {
                "data": "email",
                "render": function(data) {
                    return data || '-';
                }
            },
            {"data": "fecha_registro"},
            {
                "data": "estado",
                "render": function(data) {
                    return data === 'ACTIVO' 
                        ? '<span class="badge badge-success">ACTIVO</span>'
                        : '<span class="badge badge-danger">INACTIVO</span>';
                }
            },
            {
                "data": "id_depositante",
                "render": function(data) {
                    return `
                        <button class="btn btn-sm btn-warning mr-1" onclick="editarDepositante(${data})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarDepositante(${data})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        }
    });
}

// FUNCIÓN PARA ABRIR MODAL AGREGAR
function abrirModalAgregar() {
    console.log('Abriendo modal de agregar...');
    
    // Limpiar formulario
    $('#nombre_completo').val('');
    $('#ci_nit').val('');
    $('#telefono').val('');
    $('#email').val('');
    $('#direccion').val('');
    
    // Mostrar modal
    $('#modalAgregar').modal('show');
}

// FUNCIÓN PARA GUARDAR NUEVO DEPOSITANTE
function guardarDepositante() {
    console.log('=== GUARDANDO NUEVO DEPOSITANTE ===');
    
    // Obtener valores
    var nombre = $('#nombre_completo').val();
    var ci_nit = $('#ci_nit').val();
    var telefono = $('#telefono').val();
    var email = $('#email').val();
    var direccion = $('#direccion').val();
    
    // Validar campos requeridos
    if (!nombre || !ci_nit) {
        alert('❌ Nombre y CI/NIT son obligatorios');
        return;
    }
    
    // Preparar datos
    var datos = {
        action: 'crear',
        nombre_completo: nombre,
        ci_nit: ci_nit,
        telefono: telefono,
        email: email,
        direccion: direccion
    };
    
    console.log('Enviando datos:', datos);
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controllers/DepositanteController.php',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta:', response);
            
            if(response.success) {
                $('#modalAgregar').modal('hide');
                tablaDepositantes.ajax.reload();
                alert('✅ ' + response.message);
            } else {
                alert('❌ ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            alert('❌ Error de conexión: ' + error);
        }
    });
}

// FUNCIÓN PARA EDITAR DEPOSITANTE (COMPLETA)
function editarDepositante(id) {
    console.log('=== CARGANDO DATOS PARA EDITAR === ID:', id);
    
    // Cargar datos del depositante
    $.ajax({
        url: '../../controllers/DepositanteController.php?action=obtener&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Datos recibidos:', response);
            
            if(response.success && response.data) {
                var depositante = response.data;
                
                // Llenar el formulario de edición
                $('#edit_id').val(depositante.id_depositante);
                $('#edit_nombre_completo').val(depositante.nombre_completo);
                $('#edit_ci_nit').val(depositante.ci_nit);
                $('#edit_telefono').val(depositante.telefono || '');
                $('#edit_email').val(depositante.email || '');
                $('#edit_estado').val(depositante.estado);
                $('#edit_direccion').val(depositante.direccion || '');
                
                // Mostrar modal de edición
                $('#modalEditar').modal('show');
                
                console.log('Formulario de edición cargado correctamente');
            } else {
                alert('❌ Error: ' + (response.message || 'No se pudieron cargar los datos'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cargando datos:', error);
            alert('❌ Error al cargar datos del depositante');
        }
    });
}

// FUNCIÓN PARA ACTUALIZAR DEPOSITANTE
function actualizarDepositante() {
    console.log('=== ACTUALIZANDO DEPOSITANTE ===');
    
    // Obtener valores del formulario de edición
    var id = $('#edit_id').val();
    var nombre = $('#edit_nombre_completo').val();
    var ci_nit = $('#edit_ci_nit').val();
    var telefono = $('#edit_telefono').val();
    var email = $('#edit_email').val();
    var estado = $('#edit_estado').val();
    var direccion = $('#edit_direccion').val();
    
    console.log('Datos a actualizar:', {
        id: id,
        nombre: nombre,
        ci_nit: ci_nit,
        telefono: telefono,
        email: email,
        estado: estado,
        direccion: direccion
    });
    
    // Validar campos requeridos
    if (!nombre || !ci_nit) {
        alert('❌ Nombre y CI/NIT son obligatorios');
        return;
    }
    
    // Preparar datos
    var datos = {
        action: 'editar',
        id: id,
        nombre_completo: nombre,
        ci_nit: ci_nit,
        telefono: telefono,
        email: email,
        estado: estado,
        direccion: direccion
    };
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controllers/DepositanteController.php',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta actualización:', response);
            
            if(response.success) {
                // Cerrar modal y recargar tabla
                $('#modalEditar').modal('hide');
                tablaDepositantes.ajax.reload();
                alert('✅ ' + response.message);
            } else {
                alert('❌ ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error actualizando:', error);
            alert('❌ Error al actualizar depositante');
        }
    });
}

// FUNCIÓN PARA ELIMINAR DEPOSITANTE
function eliminarDepositante(id) {
    console.log('Eliminando depositante ID:', id);
    
    if(confirm('¿Estás seguro de eliminar este depositante? Se cambiará su estado a INACTIVO.')) {
        $.ajax({
            url: '../../controllers/DepositanteController.php',
            type: 'POST',
            data: {
                action: 'eliminar',
                id: id
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta eliminación:', response);
                if(response.success) {
                    tablaDepositantes.ajax.reload();
                    alert('✅ ' + response.message);
                } else {
                    alert('❌ ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error eliminando:', error);
                alert('Error al eliminar depositante');
            }
        });
    }
}
</script>

</body>
</html>