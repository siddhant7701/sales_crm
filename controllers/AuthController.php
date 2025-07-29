<?php
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Student.php';
require_once BASE_PATH . '/models/GroupMessage.php';
require_once BASE_PATH . '/models/Notification.php';

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST["username"]);
            $password = trim($_POST["password"]);

            if (empty($username)) {
                $username_err = "Please enter username.";
            }

            if (empty($password)) {
                $password_err = "Please enter your password.";
            }

            if (empty($username_err) && empty($password_err)) {
                if ($this->user->findByUsername($username)) {
                    // Simple plain text password comparison
                    if ($this->user->verifyPassword($password)) {
                        session_start();

                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $this->user->id;
                        $_SESSION["username"] = $this->user->username;
                        $_SESSION["role"] = $this->user->role;
                        $_SESSION["display_name"] = $this->user->display_name;
                        $_SESSION["location_id"] = $this->user->location_id;
                        $_SESSION["is_super_admin"] = $this->user->is_super_admin;

                        header("location: index.php?action=dashboard");
                    } else {
                        $login_err = "Invalid username or password.";
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            }
        }

        require_once BASE_PATH . '/views/auth/login.php';
    }

    public function dashboard() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }

        // Get dashboard statistics from database
        $stats = $this->getDashboardStats();
        
        // Get recent activity
        $recent_activity = $this->getRecentActivity();
        
        // Check if current user has profile image
        $has_profile_image = false;
        if (isset($_SESSION['id'])) {
            $userModel = new User($this->db);
            if ($userModel->findById($_SESSION['id'])) {
                $has_profile_image = !empty($userModel->profile_image);
            }
        }
        
        // Get unread notifications count
        $notificationModel = new Notification($this->db);
        $unread_notifications_count = 0;
        if (isset($_SESSION['id'])) {
            $unread_notifications_count = $notificationModel->getUnreadCount($_SESSION['id']);
        }

        // Make the database connection available in the view
        $conn = $this->db;

        require_once BASE_PATH . '/views/dashboard/index.php';
    }

    private function getDashboardStats() {
        $stats = [
            'total_students' => 0,
            'pending_students' => 0,
            'active_students' => 0,
            'total_users' => 0,
            'pending_tasks' => 0
        ];

        $user_role = $_SESSION['role'] ?? '';
        $user_location_id = $_SESSION['location_id'] ?? null;
        $is_super_admin = $_SESSION['is_super_admin'] ?? false;

        // Base query conditions for location filtering
        $location_condition = "";
        $location_params = "";
        
        // Apply location filtering for non-super admins
        if (!$is_super_admin && $user_location_id) {
            $location_condition = " WHERE location_id = ?";
            $location_params = "i";
        }

        // Get total students (filtered by location if applicable)
        $query = "SELECT COUNT(*) as count FROM students" . $location_condition;
        if ($location_params) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($location_params, $user_location_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_students'] = $row['count'];
        }
        if (isset($stmt)) $stmt->close();

        // Get pending students (status = 'pending_presales')
        $where_clause = $location_condition ? " AND location_id = ?" : ($user_location_id && !$is_super_admin ? " WHERE location_id = ?" : "");
        $query = "SELECT COUNT(*) as count FROM students WHERE status = 'pending_presales'" . $where_clause;
        if ($user_location_id && !$is_super_admin) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_location_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        if ($result && $row = $result->fetch_assoc()) {
            $stats['pending_students'] = $row['count'];
        }
        if (isset($stmt)) $stmt->close();

        // Get active students (status = 'payment_received' or 'closed_finance')
        $where_clause = $location_condition ? " AND location_id = ?" : ($user_location_id && !$is_super_admin ? " WHERE location_id = ?" : "");
        $query = "SELECT COUNT(*) as count FROM students WHERE status IN ('payment_received', 'closed_finance')" . $where_clause;
        if ($user_location_id && !$is_super_admin) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_location_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        if ($result && $row = $result->fetch_assoc()) {
            $stats['active_students'] = $row['count'];
        }
        if (isset($stmt)) $stmt->close();

        // Get total users (all users for admins, location-specific for others)
        if ($is_super_admin || in_array($user_role, ['super_admin', 'location_admin'])) {
            $query = "SELECT COUNT(*) as count FROM users";
            $result = $this->db->query($query);
        } else {
            $query = "SELECT COUNT(*) as count FROM users WHERE location_id = ? OR location_id IS NULL";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_location_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_users'] = $row['count'];
        }
        if (isset($stmt)) $stmt->close();

        // Calculate pending tasks based on user role (with location filtering)
        switch ($user_role) {
            case 'super_admin':
            case 'location_admin':
            case 'admin':
                $stats['pending_tasks'] = $stats['pending_students'];
                break;
            case 'teacher':
                $where_clause = $user_location_id && !$is_super_admin ? " AND location_id = ?" : "";
                $query = "SELECT COUNT(*) as count FROM students WHERE status IN ('demo_scheduled', 'payment_received')" . $where_clause;
                if ($user_location_id && !$is_super_admin) {
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $user_location_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $this->db->query($query);
                }
                if ($result && $row = $result->fetch_assoc()) {
                    $stats['pending_tasks'] = $row['count'];
                }
                if (isset($stmt)) $stmt->close();
                break;
            case 'counselor':
                $where_clause = $user_location_id && !$is_super_admin ? " AND location_id = ?" : "";
                $query = "SELECT COUNT(*) as count FROM students WHERE stage IN ('hot', 'warm') AND status = 'pending_presales'" . $where_clause;
                if ($user_location_id && !$is_super_admin) {
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $user_location_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $this->db->query($query);
                }
                if ($result && $row = $result->fetch_assoc()) {
                    $stats['pending_tasks'] = $row['count'];
                }
                if (isset($stmt)) $stmt->close();
                break;
            case 'presales':
                $where_clause = $user_location_id && !$is_super_admin ? " AND location_id = ?" : "";
                $query = "SELECT COUNT(*) as count FROM students WHERE status = 'pending_presales'" . $where_clause;
                if ($user_location_id && !$is_super_admin) {
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $user_location_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $this->db->query($query);
                }
                if ($result && $row = $result->fetch_assoc()) {
                    $stats['pending_tasks'] = $row['count'];
                }
                if (isset($stmt)) $stmt->close();
                break;
            case 'sales':
                $where_clause = $user_location_id && !$is_super_admin ? " AND location_id = ?" : "";
                $query = "SELECT COUNT(*) as count FROM students WHERE status IN ('passed_presales', 'passed_sales')" . $where_clause;
                if ($user_location_id && !$is_super_admin) {
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $user_location_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $this->db->query($query);
                }
                if ($result && $row = $result->fetch_assoc()) {
                    $stats['pending_tasks'] = $row['count'];
                }
                if (isset($stmt)) $stmt->close();
                break;
            case 'finance':
                $where_clause = $user_location_id && !$is_super_admin ? " AND location_id = ?" : "";
                $query = "SELECT COUNT(*) as count FROM students WHERE status = 'payment_received'" . $where_clause;
                if ($user_location_id && !$is_super_admin) {
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $user_location_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $this->db->query($query);
                }
                if ($result && $row = $result->fetch_assoc()) {
                    $stats['pending_tasks'] = $row['count'];
                }
                if (isset($stmt)) $stmt->close();
                break;
            default:
                $stats['pending_tasks'] = 0;
        }

        return $stats;
    }

    private function getRecentActivity() {
        $activities = [];
        $user_location_id = $_SESSION['location_id'] ?? null;
        $is_super_admin = $_SESSION['is_super_admin'] ?? false;

        // Location filtering for non-super admins
        $location_condition = "";
        if (!$is_super_admin && $user_location_id) {
            $location_condition = " WHERE s.location_id = " . intval($user_location_id);
        }

        // Get recent student updates (last 5) - with location filtering
        $query = "SELECT s.name, s.status, s.updated_at, 'student_update' as type, l.name as location_name
                  FROM students s 
                  LEFT JOIN locations l ON s.location_id = l.id" . 
                  $location_condition . 
                  " ORDER BY s.updated_at DESC 
                  LIMIT 5";
        $result = $this->db->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $status_display = defined('STUDENT_STATUSES') && isset(STUDENT_STATUSES[$row['status']]) 
                    ? STUDENT_STATUSES[$row['status']] 
                    : ucwords(str_replace('_', ' ', $row['status']));
                
                $activities[] = [
                    'type' => 'student_update',
                    'description' => "Student {$row['name']} status changed to " . $status_display . 
                                   ($row['location_name'] ? " ({$row['location_name']})" : ""),
                    'time' => $row['updated_at']
                ];
            }
        }

        // Get recent group messages (last 5) - no location filtering needed for messages
        $query = "SELECT gm.message_text, u.display_name, u.username, gm.created_at, 'group_message' as type
                  FROM group_messages gm
                  JOIN users u ON gm.user_id = u.id
                  ORDER BY gm.created_at DESC
                  LIMIT 5";
        $result = $this->db->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $username = $row['display_name'] ?: $row['username'];
                $activities[] = [
                    'type' => 'group_message',
                    'description' => "{$username} sent a message in group chat",
                    'time' => $row['created_at']
                ];
            }
        }

        // Sort activities by time (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Return only the 8 most recent activities
        return array_slice($activities, 0, 8);
    }

    public function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        return floor($time/31536000) . ' years ago';
    }

    public function logout() {
        session_start();
        $_SESSION = array();
        session_destroy();
        header("location: index.php?action=login");
        exit;
    }
}
?>