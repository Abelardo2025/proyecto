<?php
session_start();
// Verificar autenticación
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once '../config/database.php';
require_once '../models/DepositanteModel.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

// TEMPORAL: Para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $database = new Database();
    $db = $database->getConnection();
    $depositanteModel = new DepositanteModel($db);

    // LOG para ver qué acción se está ejecutando
    error_log("DepositanteController - Action: " . ($_GET['action'] ?? 'NONE') . ", POST: " . ($_POST['action'] ?? 'NONE'));

    // Manejar diferentes acciones
    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listar':
                $stmt = $depositanteModel->leer();
                $depositantes = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $row['fecha_registro'] = date('d/m/Y H:i', strtotime($row['fecha_registro']));
                    $depositantes[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $depositantes
                ]);
                break;

            case 'obtener':
                if(isset($_GET['id'])) {
                    $depositante = $depositanteModel->leerPorId($_GET['id']);
                    if($depositante) {
                        echo json_encode([
                            'success' => true,
                            'data' => $depositante
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Depositante no encontrado'
                        ]);
                    }
                }
                break;

            default:
                echo json_encode(['error' => 'Acción no válida']);
                break;
        }
        exit;
    } 
    
    // Manejar POST requests
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'crear':
                error_log("Creando depositante: " . print_r($_POST, true));
                
                if(empty($_POST['nombre_completo']) || empty($_POST['ci_nit'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Nombre y CI/NIT son obligatorios'
                    ]);
                    exit;
                }

                $result = $depositanteModel->crear(
                    $_POST['nombre_completo'],
                    $_POST['ci_nit'],
                    $_POST['telefono'] ?? '',
                    $_POST['email'] ?? '',
                    $_POST['direccion'] ?? ''
                );
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Depositante creado exitosamente' : 'Error al crear depositante'
                ]);
                break;

            case 'editar':
                error_log("Editando depositante: " . print_r($_POST, true));
                
                if(empty($_POST['id']) || empty($_POST['nombre_completo']) || empty($_POST['ci_nit'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Datos incompletos'
                    ]);
                    exit;
                }

                $result = $depositanteModel->actualizar(
                    $_POST['id'],
                    $_POST['nombre_completo'],
                    $_POST['ci_nit'],
                    $_POST['telefono'] ?? '',
                    $_POST['email'] ?? '',
                    $_POST['direccion'] ?? '',
                    $_POST['estado'] ?? 'ACTIVO'
                );
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Depositante actualizado exitosamente' : 'Error al actualizar depositante'
                ]);
                break;

            case 'eliminar':
                if(empty($_POST['id'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ID no proporcionado'
                    ]);
                    exit;
                }

                $result = $depositanteModel->eliminar($_POST['id']);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Depositante eliminado exitosamente' : 'Error al eliminar depositante'
                ]);
                break;

            default:
                echo json_encode(['error' => 'Acción POST no válida']);
                break;
        }
        exit;
    }

    // Si no hay acción válida
    echo json_encode(['error' => 'Solicitud no válida']);

} catch(Exception $e) {
    error_log("Error en DepositanteController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>