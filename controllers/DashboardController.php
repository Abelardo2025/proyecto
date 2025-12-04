<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if($_GET['action'] == 'estadisticas') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Consulta para estadísticas del dashboard
    $query = "SELECT * FROM vista_dashboard";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'total_clientes' => $row['total_clientes'] ?? 0,
        'total_contratos' => $row['total_contratos'] ?? 0,
        'ingreso_mensual' => number_format($row['ingreso_mensual_proyectado'] ?? 0, 2),
        'ubicaciones_ocupadas' => $row['total_ubicaciones_ocupadas'] ?? 0
    ]);
}
?>