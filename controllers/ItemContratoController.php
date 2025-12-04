<?php
session_start();
header('Content-Type: application/json');

// Simulación de datos para desarrollo
if(isset($_GET['action']) && $_GET['action'] == 'listar' && isset($_GET['id_contrato'])) {
    $items = [
        [
            'id_item' => 1,
            'descripcion_item' => 'Sillas de madera',
            'nombre_tipo' => 'Muebles',
            'nombre_ubicacion' => 'PB-A1',
            'cantidad' => 4,
            'precio_unitario_mes' => 50.00,
            'estado' => 'ALMACENADO'
        ],
        [
            'id_item' => 2,
            'descripcion_item' => 'Archivo documentos 1990-2000',
            'nombre_tipo' => 'Documentos',
            'nombre_ubicacion' => 'P2-A1',
            'cantidad' => 1,
            'precio_unitario_mes' => 30.00,
            'estado' => 'ALMACENADO'
        ]
    ];
    
    echo json_encode(['success' => true, 'data' => $items]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida']);
?>