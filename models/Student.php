<?php
class Student {
    private $conn;
    private $table_name = "students";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all students with optional filtering
    public function getAll($filters = []) {
        $query = "SELECT s.*, u.username as creator_name, u.display_name as creator_display_name 
                  FROM " . $this->table_name . " s 
                  LEFT JOIN users u ON s.assigned_to = u.id";
        
        $conditions = [];
        $params = [];
        $types = "";

        // Apply filters
        if (!empty($filters['stage'])) {
            $conditions[] = "s.stage = ?";
            $params[] = $filters['stage'];
            $types .= "s";
        }

        if (!empty($filters['status'])) {
            $conditions[] = "s.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['branch'])) {
            $conditions[] = "s.branch = ?";
            $params[] = $filters['branch'];
            $types .= "s";
        }

        if (!empty($filters['mode'])) {
            $conditions[] = "s.mode = ?";
            $params[] = $filters['mode'];
            $types .= "s";
        }

        if (!empty($filters['source'])) {
            $conditions[] = "s.source = ?";
            $params[] = $filters['source'];
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(s.name LIKE ? OR s.email LIKE ? OR s.mobile LIKE ? OR s.course LIKE ?)";
            $search_term = "%" . $filters['search'] . "%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= "ssss";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY s.created_at DESC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $result = $this->conn->query($query);
            if ($result === false) {
                die('Query failed: ' . htmlspecialchars($this->conn->error));
            }
        }

        return $result;
    }

    // Create a student from form data
    public function create($data, $created_by) {
        $query = "INSERT INTO " . $this->table_name . " (
            name, mobile, email, course, location, mode, source, branch, 
            stage, status, assigned_to
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_presales', ?)";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        
        $stmt->bind_param("sssssssssi", 
            $data['name'], 
            $data['mobile'], 
            $data['email'], 
            $data['course'], 
            $data['location'], 
            $data['mode'], 
            $data['source'], 
            $data['branch'], 
            $data['stage'], 
            $created_by
        );
        
        if ($stmt->execute()) {
            $student_id = $this->conn->insert_id;
            $stmt->close();
            return $student_id;
        }
        $stmt->close();
        return false;
    }

    // Create a student from a row of data (for CSV/Excel)
    public function createFromRow($data) {
        if (empty($data[0]) || empty($data[2])) { // Name and email are required
            return false;
        }
        
        $student_data = [
            'name' => $data[0],
            'mobile' => $data[1],
            'email' => $data[2],
            'course' => $data[3] ?? '',
            'location' => $data[4] ?? '',
            'mode' => $data[5] ?? 'online',
            'source' => $data[6] ?? 'website',
            'branch' => $data[7] ?? 'nagpur',
            'stage' => $data[8] ?? 'warm'
        ];
        
        return $this->create($student_data, $data[9]);
    }

    // Find student by ID
    public function findById($id) {
        $query = "SELECT s.*, u.username as creator_name, u.display_name as creator_display_name 
                  FROM " . $this->table_name . " s 
                  LEFT JOIN users u ON s.assigned_to = u.id 
                  WHERE s.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
        $stmt->close();
        return null;
    }

    // Update student data
    public function update($student_id, $data) {
        $fields = [];
        $params = [];
        $types = "";

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
            $types .= "s";
        }

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $student_id;
        $types .= "i";

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        }
        $stmt->close();
        return false;
    }

    // Update student status
    public function updateStatus($student_id, $new_status) {
        $query = "UPDATE " . $this->table_name . " SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("si", $new_status, $student_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Update student stage
    public function updateStage($student_id, $new_stage) {
        $query = "UPDATE " . $this->table_name . " SET stage = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("si", $new_stage, $student_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Set demo details
    public function setDemoDetails($student_id, $demo_date, $demo_time, $demo_pass) {
        $query = "UPDATE " . $this->table_name . " SET demo_date = ?, demo_time = ?, demo_pass = ?, status = 'demo_scheduled', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("sssi", $demo_date, $demo_time, $demo_pass, $student_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Set payment details
    public function setPaymentDetails($student_id, $payment_amount, $payment_date, $payment_method) {
        $query = "UPDATE " . $this->table_name . " SET payment_amount = ?, payment_date = ?, payment_method = ?, status = 'payment_received', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("dssi", $payment_amount, $payment_date, $payment_method, $student_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Grant full access
    public function grantFullAccess($student_id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'closed_finance', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Delete a student
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Check if email exists
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        
        if ($exclude_id) {
            $stmt->bind_param("si", $email, $exclude_id);
        } else {
            $stmt->bind_param("s", $email);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Check if mobile exists
    public function mobileExists($mobile, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE mobile = ?";
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        
        if ($exclude_id) {
            $stmt->bind_param("si", $mobile, $exclude_id);
        } else {
            $stmt->bind_param("s", $mobile);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
?>
