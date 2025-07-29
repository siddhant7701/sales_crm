<?php
require_once BASE_PATH . '/models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
            header("location: index.php?action=login");
            exit;
        }
    }

    private function checkLoggedInAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }
    }

    private function handleImageUpload($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [null, null];
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("File size too large. Maximum 5MB allowed.");
        }

        // Read file content as binary data
        $image_data = file_get_contents($file['tmp_name']);
        if ($image_data === false) {
            throw new Exception("Failed to read uploaded image.");
        }

        return [$image_data, $file['type']];
    }

    public function index() {
        $this->checkAdminAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $users = $this->user->getAll();
        require_once BASE_PATH . '/views/user/index.php';
    }

    public function create() {
        $this->checkAdminAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $username_err = $display_name_err = $password_err = $role_err = $location_err = $success_msg = $error_msg = "";

        // Get locations for dropdown
        $locations = $this->user->getAllLocations();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST["username"]);
            $display_name = trim($_POST["display_name"]);
            $password = trim($_POST["password"]);
            $role = trim($_POST["role"]);
            $location_id = !empty($_POST["location_id"]) ? intval($_POST["location_id"]) : null;

            if (empty($username)) { $username_err = "Please enter a username."; }
            if (empty($display_name)) { $display_name_err = "Please enter a display name."; }
            if (empty($password)) { $password_err = "Please enter a password."; }
            if (empty($role)) { $role_err = "Please select a role."; }
            if ($role !== 'online' && empty($location_id)) { $location_err = "Please select a location."; }

            if (empty($username_err) && empty($display_name_err) && empty($password_err) && empty($role_err) && empty($location_err)) {
                try {
                    $image_data = null;
                    $image_type = null;
                    
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        list($image_data, $image_type) = $this->handleImageUpload($_FILES['profile_image']);
                    }

                    if ($this->user->create($username, $display_name, $password, $role, $location_id, $image_data, $image_type)) {
                        $success_msg = "User created successfully!";
                        $username = $display_name = $password = $role = $location_id = "";
                    } else {
                        $error_msg = "Error creating user. Username might already exist.";
                    }
                } catch (Exception $e) {
                    $error_msg = $e->getMessage();
                }
            }
        }
        require_once BASE_PATH . '/views/user/create.php';
    }

    public function edit() {
        $this->checkAdminAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $username_err = $display_name_err = $role_err = $location_err = $success_msg = $error_msg = "";
        $user_id = $_GET['id'] ?? null;

        if (!$user_id || !$this->user->findById($user_id)) {
            header("location: index.php?action=users");
            exit;
        }

        // Get locations for dropdown
        $locations = $this->user->getAllLocations();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST["username"]);
            $display_name = trim($_POST["display_name"]);
            $password = trim($_POST["password"]); // Optional now
            $role = trim($_POST["role"]);
            $location_id = !empty($_POST["location_id"]) ? intval($_POST["location_id"]) : null;

            if (empty($username)) { $username_err = "Please enter a username."; }
            if (empty($display_name)) { $display_name_err = "Please enter a display name."; }
            if (empty($role)) { $role_err = "Please select a role."; }
            if ($role !== 'online' && empty($location_id)) { $location_err = "Please select a location."; }

            if (empty($username_err) && empty($display_name_err) && empty($role_err) && empty($location_err)) {
                try {
                    $image_data = null;
                    $image_type = null;
                    
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        list($image_data, $image_type) = $this->handleImageUpload($_FILES['profile_image']);
                    }

                    // Pass password only if it's not empty
                    $password_to_update = !empty($password) ? $password : null;

                    if ($this->user->update($user_id, $username, $display_name, $role, $location_id, $password_to_update, $image_data, $image_type)) {
                        $success_msg = "User updated successfully!";
                        $this->user->findById($user_id); // Refresh user data
                    } else {
                        $error_msg = "Error updating user. Username might already exist.";
                    }
                } catch (Exception $e) {
                    $error_msg = $e->getMessage();
                }
            }
        }
        require_once BASE_PATH . '/views/user/edit.php';
    }

    public function delete() {
        $this->checkAdminAccess();
        $user_id = $_GET['id'] ?? null;

        if ($user_id && $this->user->findById($user_id)) {
            if ($this->user->delete($user_id)) {
                $_SESSION['success_message'] = "User deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Error deleting user.";
            }
        } else {
            $_SESSION['error_message'] = "User not found.";
        }
        header("location: index.php?action=users");
        exit;
    }

    public function profileEdit() {
        $this->checkLoggedInAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $username_err = $display_name_err = $success_msg = $error_msg = "";
        $user_id = $_SESSION['id'];

        if (!$this->user->findById($user_id)) {
            $_SESSION['error_message'] = "Your user profile could not be found.";
            header("location: index.php?action=dashboard");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_username = trim($_POST["username"]);
            $new_display_name = trim($_POST["display_name"]);
            $new_password = trim($_POST["password"]); // Optional now
            $current_role = $this->user->role;
            $current_location_id = $this->user->location_id;

            if (empty($new_username)) {
                $username_err = "Please enter a username.";
            }
            if (empty($new_display_name)) {
                $display_name_err = "Please enter a display name.";
            }

            if (empty($username_err) && empty($display_name_err)) {
                try {
                    $image_data = null;
                    $image_type = null;
                    
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        list($image_data, $image_type) = $this->handleImageUpload($_FILES['profile_image']);
                    }

                    // Pass password only if it's not empty
                    $password_to_update = !empty($new_password) ? $new_password : null;

                    if ($this->user->update($user_id, $new_username, $new_display_name, $current_role, $current_location_id, $password_to_update, $image_data, $image_type)) {
                        $_SESSION['username'] = $new_username;
                        $_SESSION['display_name'] = $new_display_name;
                        $success_msg = "Profile updated successfully!";
                        $this->user->findById($user_id); // Refresh user data
                    } else {
                        $error_msg = "Error updating profile. Username might already exist.";
                    }
                } catch (Exception $e) {
                    $error_msg = $e->getMessage();
                }
            }
        }
        require_once BASE_PATH . '/views/user/profile_edit.php';
    }

    // Autocomplete endpoint for user search
    public function autocomplete() {
        $this->checkLoggedInAccess();
        header('Content-Type: application/json');

        $search_term = $_GET['q'] ?? '';
        $users = $this->user->getUsersForAutocomplete($search_term);
        
        echo json_encode($users);
        exit;
    }

    // Serve profile images from database
    public function serveProfileImage() {
        $this->checkLoggedInAccess();
        
        $user_id = $_GET['id'] ?? null;
        if (!$user_id) {
            http_response_code(404);
            exit;
        }

        $image_data = $this->user->getProfileImage($user_id);
        if (!$image_data) {
            http_response_code(404);
            exit;
        }

        // Set appropriate headers
        header('Content-Type: ' . $image_data['profile_image_type']);
        header('Content-Length: ' . strlen($image_data['profile_image']));
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
        
        // Output the image data
        echo $image_data['profile_image'];
        exit;
    }
}
?>