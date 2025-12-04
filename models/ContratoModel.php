<?php
class ContratoModel {
    private $conn;
    private $table_name = "contratos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todos los contratos con información del depositante
    public function leer() {
        try {
            $query = "
                SELECT 
                    c.*,
                    d.nombre_completo,
                    d.ci_nit,
                    d.telefono,
                    COUNT(i.id_item) as total_items,
                    SUM(i.precio_unitario_mes) as total_mensual_items
                FROM " . $this->table_name . " c
                LEFT JOIN depositantes d ON c.id_depositante = d.id_depositante
                LEFT JOIN items_contrato i ON c.id_contrato = i.id_contrato AND i.estado = 'ALMACENADO'
                GROUP BY c.id_contrato
                ORDER BY c.fecha_creacion DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer contratos: " . $exception->getMessage());
        }
    }

    // Crear nuevo contrato
    public function crear($id_depositante, $fecha_inicio, $fecha_fin, $gestion, $monto_total_mes, $observaciones = '') {
        try {
            // Generar número de contrato automático
            $numero_contrato = $this->generarNumeroContrato();
        
            $query = "INSERT INTO " . $this->table_name . " 
                    (id_depositante, numero_contrato, fecha_inicio, fecha_fin, gestion, monto_total_mes, observaciones) 
                    VALUES (:id_depositante, :numero_contrato, :fecha_inicio, :fecha_fin, :gestion, :monto_total_mes, :observaciones)";
            
            $stmt = $this->conn->prepare($query);
        
            // Limpiar datos
            $id_depositante = htmlspecialchars(strip_tags($id_depositante));
            $fecha_inicio = htmlspecialchars(strip_tags($fecha_inicio));
            $fecha_fin = htmlspecialchars(strip_tags($fecha_fin));
            $gestion = htmlspecialchars(strip_tags($gestion));
            $monto_total_mes = htmlspecialchars(strip_tags($monto_total_mes));
            $observaciones = htmlspecialchars(strip_tags($observaciones));
        
            // Vincular parámetros
            $stmt->bindParam(":id_depositante", $id_depositante);
            $stmt->bindParam(":numero_contrato", $numero_contrato);
            $stmt->bindParam(":fecha_inicio", $fecha_inicio);
            $stmt->bindParam(":fecha_fin", $fecha_fin);
            $stmt->bindParam(":gestion", $gestion);
            $stmt->bindParam(":monto_total_mes", $monto_total_mes);
            $stmt->bindParam(":observaciones", $observaciones);
        
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        
        } catch(PDOException $exception) {
            throw new Exception("Error al crear contrato: " . $exception->getMessage());
        }
    }

    // Generar número de contrato automático
    private function generarNumeroContrato() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE YEAR(fecha_creacion) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $numero = $row['total'] + 1;
        return "CON-" . $year . "-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    // Obtener contrato por ID con toda la información
    public function leerPorId($id) {
        try {
            $query = "
                SELECT 
                    c.*,
                    d.nombre_completo,
                    d.ci_nit,
                    d.telefono,
                    d.email,
                    d.direccion as direccion_depositante
                FROM " . $this->table_name . " c
                JOIN depositantes d ON c.id_depositante = d.id_depositante
                WHERE c.id_contrato = :id
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer contrato: " . $exception->getMessage());
        }
    }

    // Actualizar contrato
// Actualizar contrato - MODIFICAR para incluir más campos
// Actualizar contrato - VERIFICAR PARÁMETROS
public function actualizar($id, $fecha_inicio, $fecha_fin, $gestion, $monto_total_mes, $observaciones, $estado) {
    try {
        $query = "UPDATE " . $this->table_name . " 
                 SET fecha_inicio = :fecha_inicio,
                     fecha_fin = :fecha_fin, 
                     gestion = :gestion,
                     monto_total_mes = :monto_total_mes, 
                     observaciones = :observaciones,
                     estado = :estado
                 WHERE id_contrato = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $fecha_inicio = htmlspecialchars(strip_tags($fecha_inicio));
        $fecha_fin = htmlspecialchars(strip_tags($fecha_fin));
        $gestion = htmlspecialchars(strip_tags($gestion));
        $monto_total_mes = htmlspecialchars(strip_tags($monto_total_mes));
        $observaciones = htmlspecialchars(strip_tags($observaciones));
        $estado = htmlspecialchars(strip_tags($estado));
        
        // Vincular parámetros
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":gestion", $gestion);
        $stmt->bindParam(":monto_total_mes", $monto_total_mes);
        $stmt->bindParam(":observaciones", $observaciones);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
        
    } catch(PDOException $exception) {
        throw new Exception("Error al actualizar contrato: " . $exception->getMessage());
    }
}

    // Cambiar estado del contrato
    public function cambiarEstado($id, $estado) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET estado = :estado 
                     WHERE id_contrato = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al cambiar estado: " . $exception->getMessage());
        }
    }

    // Obtener contratos por depositante
    public function leerPorDepositante($id_depositante) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE id_depositante = :id_depositante 
                     ORDER BY fecha_inicio DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_depositante", $id_depositante);
            $stmt->execute();
            
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer contratos: " . $exception->getMessage());
        }
    }

    // Obtener contratos activos
    public function leerActivos() {
        try {
            $query = "SELECT c.*, d.nombre_completo 
                     FROM " . $this->table_name . " c
                     JOIN depositantes d ON c.id_depositante = d.id_depositante
                     WHERE c.estado = 'VIGENTE' 
                     ORDER BY c.fecha_fin ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer contratos activos: " . $exception->getMessage());
        }
    }
}
?>