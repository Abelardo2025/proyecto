<?php
class UbicacionModel {
    private $conn;
    private $table_name = "ubicaciones";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todas las ubicaciones
    public function leer() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY 
                      CASE piso 
                          WHEN 'PLANTA_BAJA' THEN 1
                          WHEN 'PRIMER_PISO' THEN 2
                          WHEN 'SEGUNDO_PISO' THEN 3
                          WHEN 'TERCER_PISO' THEN 4
                          ELSE 5
                      END, nombre_ubicacion";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer ubicaciones: " . $exception->getMessage());
        }
    }

    // Crear nueva ubicación
    public function crear($nombre, $piso, $tipo_espacio, $precio_base, $capacidad = '', $dimensiones = '', $caracteristicas = '') {
        try {
            // Verificar si ya existe el nombre
            $query_verificar = "SELECT id_ubicacion FROM " . $this->table_name . " WHERE nombre_ubicacion = :nombre";
            $stmt_verificar = $this->conn->prepare($query_verificar);
            $stmt_verificar->bindParam(":nombre", $nombre);
            $stmt_verificar->execute();
            
            if($stmt_verificar->rowCount() > 0) {
                throw new Exception("Ya existe una ubicación con ese nombre");
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre_ubicacion, piso, tipo_espacio, precio_base_mes, capacidad, dimensiones, caracteristicas) 
                     VALUES (:nombre, :piso, :tipo_espacio, :precio_base, :capacidad, :dimensiones, :caracteristicas)";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $nombre = htmlspecialchars(strip_tags($nombre));
            $piso = htmlspecialchars(strip_tags($piso));
            $tipo_espacio = htmlspecialchars(strip_tags($tipo_espacio));
            $precio_base = htmlspecialchars(strip_tags($precio_base));
            $capacidad = htmlspecialchars(strip_tags($capacidad));
            $dimensiones = htmlspecialchars(strip_tags($dimensiones));
            $caracteristicas = htmlspecialchars(strip_tags($caracteristicas));
            
            // Vincular parámetros
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":piso", $piso);
            $stmt->bindParam(":tipo_espacio", $tipo_espacio);
            $stmt->bindParam(":precio_base", $precio_base);
            $stmt->bindParam(":capacidad", $capacidad);
            $stmt->bindParam(":dimensiones", $dimensiones);
            $stmt->bindParam(":caracteristicas", $caracteristicas);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al crear ubicación: " . $exception->getMessage());
        }
    }

    // Obtener ubicación por ID
    public function leerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id_ubicacion = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer ubicación: " . $exception->getMessage());
        }
    }

    // Actualizar ubicación
    public function actualizar($id, $nombre, $piso, $tipo_espacio, $precio_base, $capacidad, $dimensiones, $caracteristicas, $estado) {
        try {
            // Verificar si el nombre ya existe en otro registro
            $query_verificar = "SELECT id_ubicacion FROM " . $this->table_name . " 
                               WHERE nombre_ubicacion = :nombre AND id_ubicacion != :id";
            $stmt_verificar = $this->conn->prepare($query_verificar);
            $stmt_verificar->bindParam(":nombre", $nombre);
            $stmt_verificar->bindParam(":id", $id);
            $stmt_verificar->execute();
            
            if($stmt_verificar->rowCount() > 0) {
                throw new Exception("Ya existe otra ubicación con ese nombre");
            }

            $query = "UPDATE " . $this->table_name . " 
                     SET nombre_ubicacion = :nombre, 
                         piso = :piso, 
                         tipo_espacio = :tipo_espacio, 
                         precio_base_mes = :precio_base,
                         capacidad = :capacidad,
                         dimensiones = :dimensiones,
                         caracteristicas = :caracteristicas,
                         estado = :estado
                     WHERE id_ubicacion = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $nombre = htmlspecialchars(strip_tags($nombre));
            $piso = htmlspecialchars(strip_tags($piso));
            $tipo_espacio = htmlspecialchars(strip_tags($tipo_espacio));
            $precio_base = htmlspecialchars(strip_tags($precio_base));
            $capacidad = htmlspecialchars(strip_tags($capacidad));
            $dimensiones = htmlspecialchars(strip_tags($dimensiones));
            $caracteristicas = htmlspecialchars(strip_tags($caracteristicas));
            $estado = htmlspecialchars(strip_tags($estado));
            
            // Vincular parámetros
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":piso", $piso);
            $stmt->bindParam(":tipo_espacio", $tipo_espacio);
            $stmt->bindParam(":precio_base", $precio_base);
            $stmt->bindParam(":capacidad", $capacidad);
            $stmt->bindParam(":dimensiones", $dimensiones);
            $stmt->bindParam(":caracteristicas", $caracteristicas);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al actualizar ubicación: " . $exception->getMessage());
        }
    }

    // Eliminar ubicación (cambiar estado)
    public function eliminar($id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET estado = 'MANTENIMIENTO' 
                     WHERE id_ubicacion = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al eliminar ubicación: " . $exception->getMessage());
        }
    }

    // Obtener ubicaciones disponibles
    public function leerDisponibles() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE estado = 'DISPONIBLE' 
                     ORDER BY piso, precio_base_mes";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer ubicaciones disponibles: " . $exception->getMessage());
        }
    }

    // Obtener ubicaciones por piso
    public function leerPorPiso($piso) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE piso = :piso 
                     ORDER BY nombre_ubicacion";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":piso", $piso);
            $stmt->execute();
            return $stmt;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer ubicaciones por piso: " . $exception->getMessage());
        }
    }

    // Cambiar estado de ubicación
    public function cambiarEstado($id, $estado) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET estado = :estado 
                     WHERE id_ubicacion = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al cambiar estado: " . $exception->getMessage());
        }
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'DISPONIBLE' THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN estado = 'OCUPADO' THEN 1 ELSE 0 END) as ocupadas,
                    SUM(CASE WHEN estado = 'MANTENIMIENTO' THEN 1 ELSE 0 END) as mantenimiento,
                    AVG(precio_base_mes) as precio_promedio
                FROM " . $this->table_name;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            throw new Exception("Error al obtener estadísticas: " . $exception->getMessage());
        }
    }
}
?>