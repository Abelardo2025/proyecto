<?php
session_start();

// Para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Verificar autenticación
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once '../config/database.php';
require_once '../models/ContratoModel.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

try {
    $database = new Database();
    $db = $database->getConnection();
    $contratoModel = new ContratoModel($db);
    
    // Manejar GET requests - IMPORTANTE: Esto debe ejecutarse primero
    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listar':
                $stmt = $contratoModel->leer();
                $contratos = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Formatear fechas solo si existen
                    if(!empty($row['fecha_inicio'])) {
                        $fecha = DateTime::createFromFormat('Y-m-d', $row['fecha_inicio']);
                        if($fecha) {
                            $row['fecha_inicio_formatted'] = $fecha->format('d/m/Y');
                        }
                    }
                    
                    if(!empty($row['fecha_fin'])) {
                        $fecha = DateTime::createFromFormat('Y-m-d', $row['fecha_fin']);
                        if($fecha) {
                            $row['fecha_fin_formatted'] = $fecha->format('d/m/Y');
                            
                            // Calcular días restantes
                            $hoy = new DateTime();
                            if($hoy <= $fecha) {
                                $interval = $hoy->diff($fecha);
                                $row['dias_restantes'] = $interval->days;
                            } else {
                                $row['dias_restantes'] = 0;
                            }
                        }
                    }
                    
                    if(!empty($row['fecha_creacion'])) {
                        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_creacion']);
                        if($fecha) {
                            $row['fecha_creacion_formatted'] = $fecha->format('d/m/Y H:i');
                        }
                    }
                    
                    // Si no hay días restantes, establecer a 0
                    if(!isset($row['dias_restantes'])) {
                        $row['dias_restantes'] = 0;
                    }
                    
                    $contratos[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $contratos
                ]);
                exit;
                break;

            case 'obtener':
                if(isset($_GET['id'])) {
                    $contrato = $contratoModel->leerPorId($_GET['id']);
                    if($contrato) {
                        echo json_encode([
                            'success' => true,
                            'data' => $contrato
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Contrato no encontrado'
                        ]);
                    }
                }
                exit;
                break;

            case 'activos':
                $stmt = $contratoModel->leerActivos();
                $contratos = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if(!empty($row['fecha_fin'])) {
                        $fecha = DateTime::createFromFormat('Y-m-d', $row['fecha_fin']);
                        if($fecha) {
                            $row['fecha_fin_formatted'] = $fecha->format('d/m/Y');
                        }
                    }
                    $contratos[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $contratos
                ]);
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
                $required = ['id_depositante', 'fecha_inicio', 'fecha_fin', 'gestion', 'monto_total_mes'];
                foreach($required as $field) {
                    if(empty($_POST[$field])) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Faltan datos requeridos: ' . $field
                        ]);
                        exit;
                    }
                }

                $id_contrato = $contratoModel->crear(
                    $_POST['id_depositante'],
                    $_POST['fecha_inicio'],
                    $_POST['fecha_fin'],
                    $_POST['gestion'],
                    $_POST['monto_total_mes'],
                    $_POST['observaciones'] ?? '',
                    $_POST['testigos'] ?? ''
                );
                
                if($id_contrato) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Contrato creado exitosamente',
                        'id_contrato' => $id_contrato
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear contrato'
                    ]);
                }
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

                $result = $contratoModel->actualizar(
                    $_POST['id'],
                    $_POST['fecha_inicio'] ?? '',
                    $_POST['fecha_fin'] ?? '',
                    $_POST['gestion'] ?? '',
                    $_POST['monto_total_mes'] ?? '',
                    $_POST['observaciones'] ?? '',
                    $_POST['estado'] ?? 'VIGENTE'
                );
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Contrato actualizado exitosamente' : 'Error al actualizar contrato'
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

                $result = $contratoModel->cambiarEstado($_POST['id'], $_POST['estado']);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Estado actualizado exitosamente' : 'Error al cambiar estado'
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
    error_log("Error en ContratoController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>