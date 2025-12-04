<?php
class DepositanteModel {
    private $conn;
    private $table_name = "depositantes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todos los depositantes
    public function leer() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $exception) {
            throw new Exception("Error al leer depositantes: " . $exception->getMessage());
        }
    }

    // Crear nuevo depositante
    public function crear($nombre, $ci_nit, $telefono, $email, $direccion) {
        try {
            // Verificar si ya existe el CI/NIT
            $query_verificar = "SELECT id_depositante FROM " . $this->table_name . " WHERE ci_nit = :ci_nit";
            $stmt_verificar = $this->conn->prepare($query_verificar);
            $stmt_verificar->bindParam(":ci_nit", $ci_nit);
            $stmt_verificar->execute();
            
            if($stmt_verificar->rowCount() > 0) {
                throw new Exception("El CI/NIT ya está registrado");
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre_completo, ci_nit, telefono, email, direccion) 
                     VALUES (:nombre, :ci_nit, :telefono, :email, :direccion)";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $nombre = htmlspecialchars(strip_tags($nombre));
            $ci_nit = htmlspecialchars(strip_tags($ci_nit));
            $telefono = htmlspecialchars(strip_tags($telefono));
            $email = htmlspecialchars(strip_tags($email));
            $direccion = htmlspecialchars(strip_tags($direccion));
            
            // Vincular parámetros
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":ci_nit", $ci_nit);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":direccion", $direccion);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al crear depositante: " . $exception->getMessage());
        }
    }

    // Obtener depositante por ID
    public function leerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id_depositante = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
            
        } catch(PDOException $exception) {
            throw new Exception("Error al leer depositante: " . $exception->getMessage());
        }
    }

    // Actualizar depositante
    public function actualizar($id, $nombre, $ci_nit, $telefono, $email, $direccion, $estado) {
        try {
            // Verificar si el CI/NIT ya existe en otro registro
            $query_verificar = "SELECT id_depositante FROM " . $this->table_name . " 
                               WHERE ci_nit = :ci_nit AND id_depositante != :id";
            $stmt_verificar = $this->conn->prepare($query_verificar);
            $stmt_verificar->bindParam(":ci_nit", $ci_nit);
            $stmt_verificar->bindParam(":id", $id);
            $stmt_verificar->execute();
            
            if($stmt_verificar->rowCount() > 0) {
                throw new Exception("El CI/NIT ya está registrado en otro depositante");
            }

            $query = "UPDATE " . $this->table_name . " 
                     SET nombre_completo = :nombre, 
                         ci_nit = :ci_nit, 
                         telefono = :telefono, 
                         email = :email, 
                         direccion = :direccion,
                         estado = :estado
                     WHERE id_depositante = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $nombre = htmlspecialchars(strip_tags($nombre));
            $ci_nit = htmlspecialchars(strip_tags($ci_nit));
            $telefono = htmlspecialchars(strip_tags($telefono));
            $email = htmlspecialchars(strip_tags($email));
            $direccion = htmlspecialchars(strip_tags($direccion));
            $estado = htmlspecialchars(strip_tags($estado));
            
            // Vincular parámetros
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":ci_nit", $ci_nit);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":direccion", $direccion);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al actualizar depositante: " . $exception->getMessage());
        }
    }

    // Eliminar depositante (cambiar estado a INACTIVO)
    public function eliminar($id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET estado = 'INACTIVO' 
                     WHERE id_depositante = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al eliminar depositante: " . $exception->getMessage());
        }
    }

    // Eliminación física (opcional - solo si no hay relaciones)
    public function eliminarFisico($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id_depositante = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Error al eliminar depositante: " . $exception->getMessage());
        }
    }
}
?>