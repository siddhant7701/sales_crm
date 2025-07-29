<?php
class Remark {
    private $conn;
    private $table_name = "remarks";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new remark
    public function create($student_id, $user_id, $remark_text) {
        $query = "INSERT INTO " . $this->table_name . " (student_id, user_id, remark_text, created_at) VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed: ' . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("iis", $student_id, $user_id, $remark_text);
        
        if ($stmt->execute()) {
            $result = $this->conn->insert_id;
            $stmt->close();
            return $result;
        } else {
            error_log('Remark Model Execute failed: ' . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Get remarks by student ID
    public function getByStudentId($student_id) {
        $query = "SELECT r.*, u.username as creator_username, u.display_name as creator_display_name 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.student_id = ? 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed (getByStudentId): ' . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $student_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            error_log('Remark Model Execute failed (getByStudentId): ' . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Find remark by ID
    public function findById($id) {
        $query = "SELECT r.*, u.username as creator_username, u.display_name as creator_display_name 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed (findById): ' . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $data = $result->fetch_assoc();
                $stmt->close();
                return $data;
            }
        } else {
            error_log('Remark Model Execute failed (findById): ' . $stmt->error);
        }
        
        $stmt->close();
        return null;
    }

    // Update a remark
    public function update($id, $remark_text) {
        $query = "UPDATE " . $this->table_name . " SET remark_text = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed (update): ' . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("si", $remark_text, $id);
        
        if ($stmt->execute()) {
            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        } else {
            error_log('Remark Model Execute failed (update): ' . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Delete a remark
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed (delete): ' . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        } else {
            error_log('Remark Model Execute failed (delete): ' . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Get recent remarks for dashboard
    public function getRecent($limit = 5) {
        $query = "SELECT r.*, s.name as student_name, u.username as creator_username 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN students s ON r.student_id = s.id 
                  LEFT JOIN users u ON r.user_id = u.id 
                  ORDER BY r.created_at DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log('Remark Model Prepare failed (getRecent): ' . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $limit);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            error_log('Remark Model Execute failed (getRecent): ' . $stmt->error);
            $stmt->close();
            return false;
        }
    }
}
?>
