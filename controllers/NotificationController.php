<?php
require_once BASE_PATH . '/models/Notification.php';

class NotificationController {
    private $db;
    private $notification;

    public function __construct($db) {
        $this->db = $db;
        $this->notification = new Notification($db);
    }

    private function checkAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }
    }

    public function index() {
        $this->checkAccess();
        
        $user_id = $_SESSION['id'];
        $notifications = $this->notification->getByUserId($user_id, 50);
        
        require_once BASE_PATH . '/views/notification/index.php';
    }

    public function markAsRead() {
        $this->checkAccess();
        
        $notification_id = $_GET['id'] ?? null;
        if ($notification_id) {
            $this->notification->markAsRead($notification_id);
        }
        
        header("location: index.php?action=notifications");
        exit;
    }

    public function delete() {
        $this->checkAccess();
        
        $notification_id = $_GET['id'] ?? null;
        if ($notification_id) {
            $this->notification->delete($notification_id);
        }
        
        header("location: index.php?action=notifications");
        exit;
    }
}
?>
