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
    <title>Ubicaciones - Sistema Depósitos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <style>
        .piso-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 10px;
        }
        .piso-pb { background-color: #3498db; color: white; }
        .piso-p1 { background-color: #2ecc71; color: white; }
        .piso-p2 { background-color: #9b59b6; color: white; }
        .piso-p3 { background-color: #e74c3c; color: white; }
        
        .tipo-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 10px;
        }
        .tipo-bodega { background-color: #f39c12; color: white; }
        .tipo-estante { background-color: #1abc9c; color: white; }
        .tipo-caja { background-color: #34495e; color: white; }
        .tipo-especial { background-color: #d35400; color: white; }
        
        .estado-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 10px;
        }
        .estado-disponible { background-color: #28a745; color: white; }
        .estado-ocupado { background-color: #dc3545; color: white; }
        .estado-mantenimiento { background-color: #ffc107; color: #212529; }
        .estado-reservado { background-color: #17a2b8; color: white; }
        
        .map-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .piso-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .piso-header {
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
        }
        .ubicacion-item {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ubicacion-item:last-child {
            border-bottom: none;
        }
    </style>
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
                        <h1>Gestión de Ubicaciones</h1>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-success float-right" onclick="abrirModalAgregar()">
                            <i class="fas fa-plus"></i> Nueva Ubicación
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Estadísticas -->
                <div class="row" id="estadisticasContainer">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="totalUbicaciones">0</h3>
                                <p>Total Ubicaciones</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 id="disponibles">0</h3>
                                <p>Disponibles</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="ocupadas">0</h3>
                                <p>Ocupadas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3 id="precioPromedio">0</h3>
                                <p>Precio Promedio (Bs.)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pestañas -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header p-0">
                                <ul class="nav nav-tabs" id="tabsUbicaciones" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-lista" data-toggle="tab" href="#lista" role="tab">
                                            <i class="fas fa-list"></i> Lista Completa
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-mapa" data-toggle="tab" href="#mapa" role="tab">
                                            <i class="fas fa-map"></i> Vista por Pisos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-disponibles" data-toggle="tab" href="#disponibles" role="tab">
                                            <i class="fas fa-check"></i> Disponibles
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <!-- Tab 1: Lista Completa -->
                                    <div class="tab-pane fade show active" id="lista" role="tabpanel">
                                        <table id="tablaUbicaciones" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Piso</th>
                                                    <th>Tipo</th>
                                                    <th>Precio Base</th>
                                                    <th>Capacidad</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Tab 2: Vista por Pisos -->
                                    <div class="tab-pane fade" id="mapa" role="tabpanel">
                                        <div id="mapaPisos">
                                            <p class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando mapa de ubicaciones...
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab 3: Disponibles -->
                                    <div class="tab-pane fade" id="disponibles" role="tabpanel">
                                        <table id="tablaDisponibles" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Piso</th>
                                                    <th>Tipo</th>
                                                    <th>Precio Base</th>
                                                    <th>Capacidad</th>
                                                    <th>Dimensiones</th>
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
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Agregar Ubicación -->
    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Ubicación</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formAgregar">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre de la Ubicación *</label>
                                    <input type="text" class="form-control" id="nombre_ubicacion" 
                                           placeholder="Ej: PB-A1, P1-B2, P2-CF1" required>
                                    <small class="text-muted">Identificador único para la ubicación</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Piso *</label>
                                    <select class="form-control" id="piso" required>
                                        <option value="">Seleccionar piso...</option>
                                        <option value="PLANTA_BAJA">Planta Baja</option>
                                        <option value="PRIMER_PISO">1er Piso</option>
                                        <option value="SEGUNDO_PISO">2do Piso</option>
                                        <option value="TERCER_PISO">3er Piso</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Espacio *</label>
                                    <select class="form-control" id="tipo_espacio" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="BODEGA">Bodega</option>
                                        <option value="ESTANTE">Estante</option>
                                        <option value="CAJA_FUERTE">Caja Fuerte</option>
                                        <option value="AREA_ESPECIAL">Área Especial</option>
                                        <option value="PASILLO">Pasillo</option>
                                        <option value="OFICINA">Oficina</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio Base Mensual (Bs.) *</label>
                                    <input type="number" class="form-control" id="precio_base_mes" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Capacidad</label>
                                    <input type="text" class="form-control" id="capacidad" 
                                           placeholder="Ej: Grande, Mediana, 500kg">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Dimensiones</label>
                                    <input type="text" class="form-control" id="dimensiones" 
                                           placeholder="Ej: 4x4m, 2x1m">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Características</label>
                            <textarea class="form-control" id="caracteristicas" rows="3" 
                                      placeholder="Ej: Climatizado, acceso controlado, iluminación natural..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarUbicacion()">
                        <i class="fas fa-save"></i> Guardar Ubicación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Ubicación -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Ubicación</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="edit_id">
                        <!-- Mismo formulario que agregar, pero con datos precargados -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarUbicacion()">
                        <i class="fas fa-save"></i> Actualizar Ubicación
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