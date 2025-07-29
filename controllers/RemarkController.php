<?php
require_once BASE_PATH . '/models/Remark.php';
require_once BASE_PATH . '/models/Student.php'; // Ensure Student model is available for student name
require_once BASE_PATH . '/models/User.php'; // Ensure User model is available for profile image check

class RemarkController {
    private $db;
    private $remarkModel;
    private $studentModel;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->remarkModel = new Remark($db);
        $this->studentModel = new Student($db);
        $this->userModel = new User($db);
    }

    private function checkAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }
    }

    public function add() {
        $this->checkAccess();
        
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $_SESSION['error_message'] = "Invalid request method for adding remark.";
            header("location: index.php?action=students");
            exit;
        }

        $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
        $remark_text = trim(filter_input(INPUT_POST, 'remark_text', FILTER_SANITIZE_STRING));
        $user_id = $_SESSION['id'] ?? null;

        if (!$student_id || empty($remark_text) || !$user_id) {
            $_SESSION['error_message'] = "Student ID, remark text, and user ID are required to add a remark.";
            header("location: index.php?action=students_view&id=" . ($student_id ?? ''));
            exit;
        }

        $result = $this->remarkModel->create($student_id, $user_id, $remark_text);
        if ($result) {
            $_SESSION['success_message'] = "Remark added successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to add remark. Database error or invalid data.";
        }
        
        header("location: index.php?action=students_view&id=" . $student_id);
        exit;
    }

    public function edit() {
        $this->checkAccess();
        
        // Fetch unread notification count for the sidebar (if header.php uses it)
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            require_once BASE_PATH . '/models/Notification.php'; // Ensure Notification model is loaded
            $notificationModel = new Notification($this->db);
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image (if header.php uses it)
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $user = $this->userModel->findById($_SESSION['id']);
            if ($user) {
                $has_profile_image = !empty($user['profile_image']);
            }
        }
        
        $remark_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $student_id_from_get = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT); // Keep this for fallback/initial context
        $current_user_id = $_SESSION['id'] ?? null;
        $current_user_role = $_SESSION['role'] ?? 'guest';

        $remark = null;
        $student_name = '';
        
        if (!$remark_id) {
            $_SESSION['error_message'] = "Remark ID is missing for editing.";
            header("location: index.php?action=students" . ($student_id_from_get ? "_view&id=" . $student_id_from_get : ""));
            exit;
        }

        $remark = $this->remarkModel->findById($remark_id);
        
        if (!$remark) {
            $_SESSION['error_message'] = "Remark not found.";
            header("location: index.php?action=students" . ($student_id_from_get ? "_view&id=" . $student_id_from_get : ""));
            exit;
        }
        
        // Use student_id from remark data, as it's more reliable
        $student_id = $remark['student_id']; 
        
        // Get student name
        $student = $this->studentModel->findById($student_id);
        if ($student) {
            $student_name = $student['name'];
        } else {
            $student_name = 'Unknown Student'; // Fallback if student not found
        }
        
        // Check permission: creator or admin
        if ($remark['user_id'] != $current_user_id && $current_user_role !== 'admin') {
            $_SESSION['error_message'] = "You do not have permission to edit this remark.";
            header("location: index.php?action=students_view&id=" . $student_id);
            exit;
        }

        require_once BASE_PATH . '/views/remark/edit.php';
    }

    public function update() {
        $this->checkAccess();
        
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $_SESSION['error_message'] = "Invalid request method for updating remark.";
            header("location: index.php?action=students");
            exit;
        }

        $remark_id = filter_input(INPUT_POST, 'remark_id', FILTER_VALIDATE_INT);
        $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
        $new_remark_text = trim(filter_input(INPUT_POST, 'remark_text', FILTER_SANITIZE_STRING));
        $current_user_id = $_SESSION['id'] ?? null;
        $current_user_role = $_SESSION['role'] ?? 'guest';

        if (!$remark_id || !$student_id || empty($new_remark_text) || !$current_user_id) {
            $_SESSION['error_message'] = "All fields (remark ID, student ID, remark text) and user ID are required to update a remark.";
            header("location: index.php?action=students_view&id=" . ($student_id ?? ''));
            exit;
        }

        $remark_data = $this->remarkModel->findById($remark_id);
        if (!$remark_data) {
            $_SESSION['error_message'] = "Remark not found.";
            header("location: index.php?action=students_view&id=" . $student_id);
            exit;
        }

        // Check permission: creator or admin
        if ($remark_data['user_id'] != $current_user_id && $current_user_role !== 'admin') {
            $_SESSION['error_message'] = "You do not have permission to edit this remark.";
            header("location: index.php?action=students_view&id=" . $student_id);
            exit;
        }

        if ($this->remarkModel->update($remark_id, $new_remark_text)) {
            $_SESSION['success_message'] = "Remark updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update remark. Database error or no changes made.";
        }
        
        header("location: index.php?action=students_view&id=" . $student_id);
        exit;
    }

    public function delete() {
        $this->checkAccess();
        
        $remark_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $student_id = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT);
        $current_user_id = $_SESSION['id'] ?? null;
        $current_user_role = $_SESSION['role'] ?? 'guest';

        if (!$remark_id || !$student_id || !$current_user_id) {
            $_SESSION['error_message'] = "Invalid request parameters for deleting remark.";
            header("location: index.php?action=students");
            exit;
        }

        $remark_data = $this->remarkModel->findById($remark_id);
        if (!$remark_data) {
            $_SESSION['error_message'] = "Remark not found.";
            header("location: index.php?action=students_view&id=" . $student_id);
            exit;
        }

        // Check permission: creator or admin
        if ($remark_data['user_id'] != $current_user_id && $current_user_role !== 'admin') {
            $_SESSION['error_message'] = "You do not have permission to delete this remark.";
        } else {
            if ($this->remarkModel->delete($remark_id)) {
                $_SESSION['success_message'] = "Remark deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to delete remark. Database error.";
            }
        }
        
        header("location: index.php?action=students_view&id=" . $student_id);
        exit;
    }
}
?>
