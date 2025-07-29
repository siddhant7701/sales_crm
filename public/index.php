<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/UserController.php';
require_once BASE_PATH . '/controllers/StudentController.php';
require_once BASE_PATH . '/controllers/RemarkController.php';
require_once BASE_PATH . '/controllers/GroupMessageController.php';
require_once BASE_PATH . '/controllers/NotificationController.php';

$authController = new AuthController($conn);
$userController = new UserController($conn);
$studentController = new StudentController($conn);
$remarkController = new RemarkController($conn);
$groupMessageController = new GroupMessageController($conn);
$notificationController = new NotificationController($conn);

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'dashboard':
        $authController->dashboard();
        break;
    case 'logout':
        $authController->logout();
        break;
    
    // User Management Actions
    case 'users':
        $userController->index();
        break;
    case 'users_create':
        $userController->create();
        break;
    case 'users_edit':
        $userController->edit();
        break;
    case 'users_delete':
        $userController->delete();
        break;
    case 'profile_edit':
        $userController->profileEdit();
        break;
    case 'users_autocomplete':
        $userController->autocomplete();
        break;
    case 'user_image': // Route for serving profile images
        $userController->serveProfileImage();
        break;
    
    // Student Management Actions
    case 'students':
        $studentController->index();
        break;
    case 'students_create':
        $studentController->create();
        break;
    case 'students_view':
        $studentController->view();
        break;
    case 'students_update_status':
        $studentController->updateStatus();
        break;
    case 'students_add_demo_pass':
        $studentController->addDemoPassDetails();
        break;
    case 'students_record_payment':
        $studentController->recordPaymentDetails();
        break;
    case 'students_grant_access':
        $studentController->grantFullAccess();
        break;
    case 'students_delete':
        $studentController->delete();
        break;
    
    // Remark Actions
    case 'remarks_add':
        $remarkController->add();
        break;
    case 'remarks_edit':
        $remarkController->edit();
        break;
    case 'remarks_delete':
        $remarkController->delete();
        break;
    
    // Group Message Actions
    case 'group_chat':
        $groupMessageController->index();
        break;
    case 'group_chat_send':
        $groupMessageController->send();
        break;
    case 'group_chat_fetch_new':
        $groupMessageController->fetchNewMessages();
        break;
    case 'cleanup_messages': // Fixed route for message cleanup
        $groupMessageController->cleanupExpiredMessages();
        break;
    
    // Notification Actions
    case 'notifications':
        $notificationController->index();
        break;
    case 'notifications_mark_read':
        $notificationController->markAsRead();
        break;
    case 'notifications_delete':
        $notificationController->delete();
        break;
    
    // API-style routes for AJAX calls
    case 'send_message':
        // Handle AJAX message sending
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $message_text = trim($input['message'] ?? '');

            if (empty($message_text)) {
                http_response_code(400);
                echo json_encode(['error' => 'Message cannot be empty']);
                exit;
            }

            require_once BASE_PATH . '/models/GroupMessage.php';
            require_once BASE_PATH . '/models/User.php';
            
            $groupMessage = new GroupMessage($conn);
            $user = new User($conn);
            
            $message_id = $groupMessage->create($_SESSION['id'], $message_text);

            if ($message_id) {
                // Get the newly created message with user info
                $query = "SELECT gm.id, gm.message_text, gm.created_at, gm.user_id,
                                 u.username as sender_username, u.display_name as sender_display_name,
                                 CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as sender_has_image
                          FROM group_messages gm
                          JOIN users u ON gm.user_id = u.id
                          WHERE gm.id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $message_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $message_data = $result->fetch_assoc();
                $stmt->close();

                echo json_encode([
                    'success' => true,
                    'message' => [
                        'id' => $message_data['id'],
                        'message_text' => $message_data['message_text'],
                        'username' => $message_data['sender_display_name'] ?? $message_data['sender_username'],
                        'created_at' => $message_data['created_at'],
                        'user_id' => $message_data['user_id'],
                        'has_profile_image' => (bool)$message_data['sender_has_image']
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send message']);
            }
        }
        break;
    
    case 'get_messages':
        // Handle AJAX message fetching
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        $lastMessageId = $_GET['last_id'] ?? 0;
        
        $query = "SELECT gm.id, gm.message_text, gm.created_at, gm.user_id,
                         u.username as sender_username, u.display_name as sender_display_name,
                         CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as sender_has_image
                  FROM group_messages gm
                  JOIN users u ON gm.user_id = u.id
                  WHERE gm.id > ? AND gm.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                  ORDER BY gm.created_at ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $lastMessageId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message_text' => $row['message_text'],
                'username' => $row['sender_display_name'] ?? $row['sender_username'],
                'created_at' => $row['created_at'],
                'user_id' => $row['user_id'],
                'has_profile_image' => (bool)$row['sender_has_image']
            ];
        }
        $stmt->close();

        echo json_encode(['messages' => $messages]);
        break;

    // Location Management (for super admins and location admins)
    case 'locations':
        // Check admin access
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            !in_array($_SESSION["role"], ['super_admin', 'location_admin', 'admin'])) {
            header("location: index.php?action=login");
            exit;
        }
        
        require_once BASE_PATH . '/controllers/LocationController.php';
        $locationController = new LocationController($conn);
        $locationController->index();
        break;

    case 'locations_create':
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            !in_array($_SESSION["role"], ['super_admin', 'location_admin'])) {
            header("location: index.php?action=login");
            exit;
        }
        
        require_once BASE_PATH . '/controllers/LocationController.php';
        $locationController = new LocationController($conn);
        $locationController->create();
        break;

    case 'locations_edit':
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            !in_array($_SESSION["role"], ['super_admin', 'location_admin'])) {
            header("location: index.php?action=login");
            exit;
        }
        
        require_once BASE_PATH . '/controllers/LocationController.php';
        $locationController = new LocationController($conn);
        $locationController->edit();
        break;

    case 'locations_delete':
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
            $_SESSION["role"] !== 'super_admin') {
            header("location: index.php?action=login");
            exit;
        }
        
        require_once BASE_PATH . '/controllers/LocationController.php';
        $locationController = new LocationController($conn);
        $locationController->delete();
        break;
    
    default:
        $authController->login();
        break;
}

$conn->close();
?>