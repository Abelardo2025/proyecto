<?php
session_start();
header('Content-Type: application/json');

// Simulación de datos para desarrollo
if(isset($_GET['action']) && $_GET['action'] == 'listar' && isset($_GET['id_contrato'])) {
    $cobros = [
        [
            'id_cobro' => 1,
            'mes' => 12,
            'anio' => 2024,
            'fecha_vencimiento' => '10/12/2024',
            'fecha_pago' => '05/12/2024',
            'monto_total' => 150.00,
            'recargo' => 0,
            'estado' => 'PAGADO',
            'dias_retraso' => 0
        ],
        [
            'id_cobro' => 2,
            'mes' => 1,
            'anio' => 2025,
            'fecha_vencimiento' => '10/01/2025',
            'fecha_pago' => null,
            'monto_total' => 150.00,
            'recargo' => 15.00,
            'estado' => 'PENDIENTE',
            'dias_retraso' => 5
        ]
    ];
    
    echo json_encode(['success' => true, 'data' => $cobros]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida']);
?>