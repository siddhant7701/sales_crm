<?php
require_once BASE_PATH . '/models/Location.php';

class LocationController {
    private $db;
    private $location;

    public function __construct($db) {
        $this->db = $db;
        $this->location = new Location($db);
    }

    private function checkSuperAdminAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            !in_array($_SESSION["role"], ['super_admin', 'location_admin', 'admin'])) {
            header("location: index.php?action=login");
            exit;
        }
    }

    private function checkCreateEditAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            !in_array($_SESSION["role"], ['super_admin', 'location_admin'])) {
            header("location: index.php?action=login");
            exit;
        }
    }

    private function checkDeleteAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            $_SESSION["role"] !== 'super_admin') {
            header("location: index.php?action=login");
            exit;
        }
    }

    public function index() {
        $this->checkSuperAdminAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/User.php';
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        // Get success/error messages from session
        $success_msg = $_SESSION['success_message'] ?? '';
        $error_msg = $_SESSION['error_message'] ?? '';
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $locations = $this->location->getAll();
        require_once BASE_PATH . '/views/location/index.php';
    }

    public function create() {
        $this->checkCreateEditAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/User.php';
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $name_err = $code_err = $success_msg = $error_msg = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = trim($_POST["name"]);
            $code = trim($_POST["code"]);
            $address = trim($_POST["address"]);
            $phone = trim($_POST["phone"]);
            $email = trim($_POST["email"]);
            $status = trim($_POST["status"]);

            // Validation
            if (empty($name)) {
                $name_err = "Please enter a location name.";
            }
            if (empty($code)) {
                $code_err = "Please enter a location code.";
            } elseif (strlen($code) > 10) {
                $code_err = "Location code cannot be longer than 10 characters.";
            }

            // Check if code already exists
            if (empty($code_err) && $this->location->codeExists($code)) {
                $code_err = "This location code already exists.";
            }

            if (empty($name_err) && empty($code_err)) {
                if ($this->location->create($name, $code, $address, $phone, $email, $status)) {
                    $success_msg = "Location created successfully!";
                    $name = $code = $address = $phone = $email = "";
                } else {
                    $error_msg = "Error creating location. Location name might already exist.";
                }
            }
        }
        require_once BASE_PATH . '/views/location/create.php';
    }

    public function edit() {
        $this->checkCreateEditAccess();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php';
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/User.php';
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        $name_err = $code_err = $success_msg = $error_msg = "";
        $location_id = $_GET['id'] ?? null;

        if (!$location_id || !$this->location->findById($location_id)) {
            header("location: index.php?action=locations");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = trim($_POST["name"]);
            $code = trim($_POST["code"]);
            $address = trim($_POST["address"]);
            $phone = trim($_POST["phone"]);
            $email = trim($_POST["email"]);
            $status = trim($_POST["status"]);

            // Validation
            if (empty($name)) {
                $name_err = "Please enter a location name.";
            }
            if (empty($code)) {
                $code_err = "Please enter a location code.";
            } elseif (strlen($code) > 10) {
                $code_err = "Location code cannot be longer than 10 characters.";
            }

            // Check if code already exists (excluding current location)
            if (empty($code_err) && $this->location->codeExists($code, $location_id)) {
                $code_err = "This location code already exists.";
            }

            if (empty($name_err) && empty($code_err)) {
                if ($this->location->update($location_id, $name, $code, $address, $phone, $email, $status)) {
                    $success_msg = "Location updated successfully!";
                    $this->location->findById($location_id); // Refresh location data
                } else {
                    $error_msg = "Error updating location. Location name might already exist.";
                }
            }
        }
        require_once BASE_PATH . '/views/location/edit.php';
    }

    public function delete() {
        $this->checkDeleteAccess();
        $location_id = $_GET['id'] ?? null;

        if ($location_id && $this->location->findById($location_id)) {
            // Check if location has associated users or students
            if ($this->location->hasAssociatedRecords($location_id)) {
                $_SESSION['error_message'] = "Cannot delete location. It has associated users or students.";
            } else {
                if ($this->location->delete($location_id)) {
                    $_SESSION['success_message'] = "Location deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting location.";
                }
            }
        } else {
            $_SESSION['error_message'] = "Location not found.";
        }
        header("location: index.php?action=locations");
        exit;
    }
}
?>