<?php
class Location {
    private $conn;
    private $table_name = "locations";

    public $id;
    public $name;
    public $code;
    public $address;
    public $phone;
    public $email;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all locations
    public function getAll() {
        $query = "SELECT l.*, 
                  (SELECT COUNT(*) FROM users WHERE location_id = l.id) as user_count,
                  (SELECT COUNT(*) FROM students WHERE location_id = l.id) as student_count
                  FROM " . $this->table_name . " l 
                  ORDER BY l.name ASC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Find location by ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->code = $row['code'];
            $this->address = $row['address'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Create a new location
    public function create($name, $code, $address = null, $phone = null, $email = null, $status = 'active') {
        $query = "INSERT INTO " . $this->table_name . " (name, code, address, phone, email, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssss", $name, $code, $address, $phone, $email, $status);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Update a location
    public function update($id, $name, $code, $address = null, $phone = null, $email = null, $status = 'active') {
        $query = "UPDATE " . $this->table_name . " SET name = ?, code = ?, address = ?, phone = ?, email = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssi", $name, $code, $address, $phone, $email, $status, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Delete a location
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Check if location code exists (excluding a specific ID for updates)
    public function codeExists($code, $exclude_id = null) {
        if ($exclude_id) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE code = ? AND id != ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $code, $exclude_id);
        } else {
            $query = "SELECT id FROM " . $this->table_name . " WHERE code = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $code);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Check if location name exists (excluding a specific ID for updates)
    public function nameExists($name, $exclude_id = null) {
        if ($exclude_id) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE name = ? AND id != ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $name, $exclude_id);
        } else {
            $query = "SELECT id FROM " . $this->table_name . " WHERE name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $name);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Check if location has associated users or students
    public function hasAssociatedRecords($id) {
        // Check users
        $query = "SELECT COUNT(*) as count FROM users WHERE location_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $user_count = $row['count'];
        $stmt->close();

        // Check students
        $query = "SELECT COUNT(*) as count FROM students WHERE location_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $student_count = $row['count'];
        $stmt->close();

        return ($user_count > 0 || $student_count > 0);
    }

    // Get active locations for dropdown
    public function getActiveLocations() {
        $query = "SELECT id, name, code FROM " . $this->table_name . " WHERE status = 'active' ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Get location statistics
    public function getLocationStats($id) {
        $stats = [
            'users' => 0,
            'students' => 0,
            'active_students' => 0,
            'pending_students' => 0
        ];

        // Users count
        $query = "SELECT COUNT(*) as count FROM users WHERE location_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['users'] = $row['count'];
        }
        $stmt->close();

        // Total students
        $query = "SELECT COUNT(*) as count FROM students WHERE location_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['students'] = $row['count'];
        }
        $stmt->close();

        // Active students
        $query = "SELECT COUNT(*) as count FROM students WHERE location_id = ? AND status IN ('payment_received', 'closed_finance')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['active_students'] = $row['count'];
        }
        $stmt->close();

        // Pending students
        $query = "SELECT COUNT(*) as count FROM students WHERE location_id = ? AND status = 'pending_presales'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['pending_students'] = $row['count'];
        }
        $stmt->close();

        return $stats;
    }
}
?>