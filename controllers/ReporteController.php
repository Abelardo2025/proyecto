<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';
require_once '../models/ContratoModel.php';

if(isset($_GET['action']) && $_GET['action'] == 'contrato_pdf' && isset($_GET['id'])) {
    // Aquí iría la generación de PDF usando TCPDF, Dompdf, etc.
    // Por ahora redirigimos a vista HTML
    header("Location: ../views/contratos/imprimir.php?id=" . $_GET['id']);
    exit;
}
?>