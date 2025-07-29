<?php
require_once BASE_PATH . '/models/GroupMessage.php';
require_once BASE_PATH . '/models/Notification.php';
require_once BASE_PATH . '/models/User.php';

class GroupMessageController {
    private $db;
    private $groupMessage;
    private $notification;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->groupMessage = new GroupMessage($db);
        $this->notification = new Notification($db);
        $this->user = new User($db);
    }

    private function checkAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }
    }

    public function index() {
        $this->checkAccess();
        
        // Clean up expired messages every time someone visits the chat
        $this->groupMessage->cleanupExpiredMessages();
        
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            $unread_notifications_count = $this->notification->getUnreadCount($_SESSION['id']);
        }
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        // Get ALL messages (not just newer ones) for initial load
        $messages = $this->groupMessage->getAll();
        $messages_data = [];
        if ($messages) {
            while ($row = $messages->fetch_assoc()) {
                $message_id = $row['id'];
                $row['tags'] = $this->groupMessage->getTagsByMessageId($message_id);
                $messages_data[] = $row;
            }
        }
        
        $all_users = $this->user->getAll();
        $users_map = [];
        if ($all_users) {
            while($user_row = $all_users->fetch_assoc()) {
                $users_map[$user_row['username']] = $user_row['id'];
            }
        }

        // Mark notifications as read when user visits chat
        $this->notification->markAsRead($_SESSION['id']);

        require_once BASE_PATH . '/views/group_chat/index.php';
    }

    public function send() {
        $this->checkAccess();
        $user_id = $_SESSION['id'];
        $message_text = trim($_POST['message_text'] ?? '');

        if (empty($message_text)) {
            $_SESSION['error_message'] = "Message cannot be empty.";
            header("location: index.php?action=group_chat");
            exit;
        }

        $message_id = $this->groupMessage->create($user_id, $message_text);

        if ($message_id) {
            $_SESSION['success_message'] = "Message sent!";

            // Handle @mentions
            preg_match_all('/@([a-zA-Z0-9_]+)/', $message_text, $matches);
            $mentioned_usernames = array_unique($matches[1]);

            if (!empty($mentioned_usernames)) {
                $all_users_result = $this->user->getAll();
                $users_lookup = [];
                if ($all_users_result) {
                    while ($row = $all_users_result->fetch_assoc()) {
                        $users_lookup[$row['username']] = $row['id'];
                    }
                }

                foreach ($mentioned_usernames as $mentioned_username) {
                    if (isset($users_lookup[$mentioned_username])) {
                        $tagged_user_id = $users_lookup[$mentioned_username];
                        $this->groupMessage->addTag($message_id, $tagged_user_id);
                        $notification_content = $_SESSION['username'] . " mentioned you in the group chat: \"" . substr($message_text, 0, 100) . "...\"";
                        $this->notification->create($tagged_user_id, 'mention', $notification_content, $message_id);
                    }
                }
            }
        } else {
            $_SESSION['error_message'] = "Failed to send message.";
        }

        header("location: index.php?action=group_chat");
        exit;
    }

    public function fetchNewMessages() {
        $this->checkAccess();
        header('Content-Type: application/json');

        $last_timestamp = $_GET['last_timestamp'] ?? '1970-01-01 00:00:00';

        $messages_result = $this->groupMessage->getNewerThan($last_timestamp);
        $messages_data = [];
        if ($messages_result) {
            while ($row = $messages_result->fetch_assoc()) {
                $message_id = $row['id'];
                $row['tags'] = $this->groupMessage->getTagsByMessageId($message_id);
                $messages_data[] = $row;
            }
        }

        echo json_encode($messages_data);
        exit;
    }

    // Clean up expired messages (can be called via cron or manually)
    public function cleanupExpiredMessages() {
        $this->checkAccess();
        
        $deleted_count = $this->groupMessage->cleanupExpiredMessages();
        
        if (isset($_GET['manual']) && $_GET['manual'] === '1') {
            $_SESSION['success_message'] = "Cleaned up $deleted_count expired messages.";
            header("location: index.php?action=users");
            exit;
        } else {
            // For cron job or API call
            header('Content-Type: application/json');
            echo json_encode(['deleted_count' => $deleted_count]);
            exit;
        }
    }
}
?>
