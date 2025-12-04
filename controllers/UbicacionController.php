<?php
session_start();

// Para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Verificar autenticación
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require_once '../config/database.php';
require_once '../models/UbicacionModel.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

try {
    $database = new Database();
    $db = $database->getConnection();
    $ubicacionModel = new UbicacionModel($db);
    
    // Manejar GET requests
    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listar':
                $stmt = $ubicacionModel->leer();
                $ubicaciones = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Traducir piso para mejor visualización
                    $piso_traduccion = [
                        'PLANTA_BAJA' => 'Planta Baja',
                        'PRIMER_PISO' => '1er Piso',
                        'SEGUNDO_PISO' => '2do Piso',
                        'TERCER_PISO' => '3er Piso'
                    ];
                    $row['piso_display'] = $piso_traduccion[$row['piso']] ?? $row['piso'];
                    
                    // Traducir tipo de espacio
                    $tipo_traduccion = [
                        'ESTANTE' => 'Estante',
                        'CAJA_FUERTE' => 'Caja Fuerte',
                        'AREA_ESPECIAL' => 'Área Especial',
                        'BODEGA' => 'Bodega',
                        'PASILLO' => 'Pasillo',
                        'OFICINA' => 'Oficina'
                    ];
                    $row['tipo_espacio_display'] = $tipo_traduccion[$row['tipo_espacio']] ?? $row['tipo_espacio'];
                    
                    $ubicaciones[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $ubicaciones
                ]);
                exit;
                break;

            case 'obtener':
                if(isset($_GET['id'])) {
                    $ubicacion = $ubicacionModel->leerPorId($_GET['id']);
                    if($ubicacion) {
                        echo json_encode([
                            'success' => true,
                            'data' => $ubicacion
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Ubicación no encontrada'
                        ]);
                    }
                }
                exit;
                break;

            case 'disponibles':
                $stmt = $ubicacionModel->leerDisponibles();
                $ubicaciones = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $ubicaciones[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $ubicaciones
                ]);
                exit;
                break;

            case 'estadisticas':
                $estadisticas = $ubicacionModel->obtenerEstadisticas();
                echo json_encode([
                    'success' => true,
                    'data' => $estadisticas
                ]);
                exit;
                break;

            case 'por_piso':
                if(isset($_GET['piso'])) {
                    $stmt = $ubicacionModel->leerPorPiso($_GET['piso']);
                    $ubicaciones = [];
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $ubicaciones[] = $row;
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $ubicaciones
                    ]);
                }
                exit;
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción GET no válida']);
                exit;
                break;
        }
    }
    
    // Manejar POST requests
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'crear':
                // Validar datos requeridos
                $required = ['nombre_ubicacion', 'piso', 'tipo_espacio', 'precio_base_mes'];
                foreach($required as $field) {
                    if(empty($_POST[$field])) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Faltan datos requeridos: ' . $field
                        ]);
                        exit;
                    }
                }

                $result = $ubicacionModel->crear(
                    $_POST['nombre_ubicacion'],
                    $_POST['piso'],
                    $_POST['tipo_espacio'],
                    $_POST['precio_base_mes'],
                    $_POST['capacidad'] ?? '',
                    $_POST['dimensiones'] ?? '',
                    $_POST['caracteristicas'] ?? ''
                );
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Ubicación creada exitosamente' : 'Error al crear ubicación'
                ]);
                exit;
                break;

            case 'actualizar':
                if(empty($_POST['id'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ID no proporcionado'
                    ]);
                    exit;
                }

                $result = $ubicacionModel->actualizar(
                    $_POST['id'],
                    $_POST['nombre_ubicacion'],
                    $_POST['piso'],
                    $_POST['tipo_espacio'],
                    $_POST['precio_base_mes'],
                    $_POST['capacidad'] ?? '',
                    $_POST['dimensiones'] ?? '',
                    $_POST['caracteristicas'] ?? '',
                    $_POST['estado'] ?? 'DISPONIBLE'
                );
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Ubicación actualizada exitosamente' : 'Error al actualizar ubicación'
                ]);
                exit;
                break;

            case 'cambiar_estado':
                if(empty($_POST['id']) || empty($_POST['estado'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Datos incompletos'
                    ]);
                    exit;
                }

                $result = $ubicacionModel->cambiarEstado($_POST['id'], $_POST['estado']);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Estado actualizado exitosamente' : 'Error al cambiar estado'
                ]);
                exit;
                break;

            case 'eliminar':
                if(empty($_POST['id'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ID no proporcionado'
                    ]);
                    exit;
                }

                $result = $ubicacionModel->eliminar($_POST['id']);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Ubicación marcada como en mantenimiento' : 'Error al eliminar ubicación'
                ]);
                exit;
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción POST no válida']);
                exit;
                break;
        }
    }

    // Si no hay acción válida
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida']);

} catch(Exception $e) {
    error_log("Error en UbicacionController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>