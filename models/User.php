<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $display_name;
    public $profile_image;
    public $profile_image_type;
    public $password;
    public $role;
    public $location_id;
    public $is_super_admin;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Find a user by username
    public function findByUsername($username) {
        $query = "SELECT u.id, u.username, u.display_name, u.profile_image, u.profile_image_type, 
                  u.password, u.role, u.location_id, u.is_super_admin, l.name as location_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN locations l ON u.location_id = l.id 
                  WHERE u.username = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->display_name = $row['display_name'];
            $this->profile_image = $row['profile_image'];
            $this->profile_image_type = $row['profile_image_type'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->location_id = $row['location_id'];
            $this->is_super_admin = $row['is_super_admin'];
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Verify password using plain text comparison
    public function verifyPassword($input_password) {
        return $input_password === $this->password;
    }

    // Get all users with display names, images, and locations
    public function getAll() {
        $query = "SELECT u.id, u.username, u.display_name, 
                  CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as has_image,
                  u.profile_image_type, u.role, u.location_id, u.is_super_admin,
                  l.name as location_name, l.code as location_code
                  FROM " . $this->table_name . " u 
                  LEFT JOIN locations l ON u.location_id = l.id 
                  ORDER BY u.display_name ASC, u.username ASC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Find user by ID
    public function findById($id) {
        $query = "SELECT u.id, u.username, u.display_name, u.profile_image, u.profile_image_type, 
                  u.password, u.role, u.location_id, u.is_super_admin, l.name as location_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN locations l ON u.location_id = l.id 
                  WHERE u.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->display_name = $row['display_name'];
            $this->profile_image = $row['profile_image'];
            $this->profile_image_type = $row['profile_image_type'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->location_id = $row['location_id'];
            $this->is_super_admin = $row['is_super_admin'];
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Create a user with plain text password
    public function create($username, $display_name, $password, $role, $location_id = null, $profile_image_data = null, $profile_image_type = null) {
        $query = "INSERT INTO " . $this->table_name . " (username, display_name, password, role, location_id, profile_image, profile_image_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssiss", $username, $display_name, $password, $role, $location_id, $profile_image_data, $profile_image_type);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Update a user - password is optional
    public function update($id, $username, $display_name, $role, $location_id = null, $password = null, $profile_image_data = null, $profile_image_type = null) {
        // Build query dynamically based on what needs to be updated
        $fields = [];
        $types = "";
        $values = [];

        $fields[] = "username = ?";
        $types .= "s";
        $values[] = $username;

        $fields[] = "display_name = ?";
        $types .= "s";
        $values[] = $display_name;

        $fields[] = "role = ?";
        $types .= "s";
        $values[] = $role;

        $fields[] = "location_id = ?";
        $types .= "i";
        $values[] = $location_id;

        // Only update password if provided
        if ($password !== null && $password !== '') {
            $fields[] = "password = ?";
            $types .= "s";
            $values[] = $password;
        }

        // Only update profile image if provided
        if ($profile_image_data !== null) {
            $fields[] = "profile_image = ?";
            $fields[] = "profile_image_type = ?";
            $types .= "ss";
            $values[] = $profile_image_data;
            $values[] = $profile_image_type;
        }

        $types .= "i";
        $values[] = $id;

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Delete a user
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

    // Get users for autocomplete (username and display name)
    public function getUsersForAutocomplete($search_term = '') {
        if (!empty($search_term)) {
            $search_term = '%' . $search_term . '%';
            $query = "SELECT u.id, u.username, u.display_name, 
                      CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as has_image,
                      u.profile_image_type, l.name as location_name
                      FROM " . $this->table_name . " u 
                      LEFT JOIN locations l ON u.location_id = l.id
                      WHERE u.username LIKE ? OR u.display_name LIKE ? 
                      ORDER BY u.display_name ASC, u.username ASC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $search_term, $search_term);
        } else {
            $query = "SELECT u.id, u.username, u.display_name, 
                      CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as has_image,
                      u.profile_image_type, l.name as location_name
                      FROM " . $this->table_name . " u 
                      LEFT JOIN locations l ON u.location_id = l.id
                      ORDER BY u.display_name ASC, u.username ASC LIMIT 10";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
        return $users;
    }

    // Get profile image data
    public function getProfileImage($user_id) {
        $query = "SELECT profile_image, profile_image_type FROM " . $this->table_name . " WHERE id = ? AND profile_image IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row;
        }
        $stmt->close();
        return null;
    }

    // Get all locations
    public function getAllLocations() {
        $query = "SELECT id, name, code FROM locations WHERE status = 'active' ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>