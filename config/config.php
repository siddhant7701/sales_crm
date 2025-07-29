<?php
// Database configuration for CloudBlitz CRM
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Your database username
define('DB_PASSWORD', '');     // Your database password
define('DB_NAME', 'cloudblitz_crm'); // Updated database name

// Define the base path for the project
define('BASE_PATH', dirname(__DIR__));

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for better Unicode support
$conn->set_charset("utf8mb4");

// Application constants
define('APP_NAME', 'CloudBlitz CRM');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Customer Relationship Management System for Educational Institute');

// Default pagination settings
define('DEFAULT_PAGE_SIZE', 25);
define('MAX_PAGE_SIZE', 100);

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['csv', 'xlsx', 'xls']);

// Session timeout (in seconds) - 2 hours
define('SESSION_TIMEOUT', 7200);

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'login') === false) {
        header("Location: index.php?action=login&timeout=1");
        exit;
    }
}
$_SESSION['last_activity'] = time();

// Helper functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php?action=login');
    }
}

function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_role = $_SESSION['role'] ?? '';
    
    // Admin has access to everything
    if ($user_role === 'admin') {
        return true;
    }
    
    // Check specific role
    if (is_array($required_role)) {
        return in_array($user_role, $required_role);
    }
    
    return $user_role === $required_role;
}

function requireRole($required_role) {
    if (!hasRole($required_role)) {
        die("Access denied. Insufficient permissions.");
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? 'guest';
}

function getUserId() {
    return $_SESSION['id'] ?? null;
}

// Constants for student status workflow
define('STUDENT_STATUSES', [
    'pending_presales' => 'Pending Presales',
    'passed_presales' => 'Passed Presales',
    'passed_sales' => 'Passed Sales',
    'demo_scheduled' => 'Demo Scheduled',
    'payment_received' => 'Payment Received',
    'closed_finance' => 'Closed Finance'
]);

define('STUDENT_STAGES', [
    'hot' => 'Hot',
    'warm' => 'Warm',
    'cold' => 'Cold',
    'irrelevant' => 'Irrelevant'
]);

define('STUDENT_MODES', [
    'online' => 'Online',
    'offline' => 'Offline',
    'hybrid' => 'Hybrid'
]);

define('STUDENT_SOURCES', [
    'website' => 'Website',
    'ivr' => 'IVR',
    'whatsapp' => 'WhatsApp',
    'referral' => 'Referral',
    'walkin' => 'Walk-in',
    'others' => 'Others'
]);

define('BRANCHES', [
    'nagpur' => 'Nagpur',
    'pune' => 'Pune',
    'indore' => 'Indore',
    'mumbai' => 'Mumbai',
    'delhi' => 'Delhi'
]);

define('USER_ROLES', [
    'admin' => 'Administrator',
    'presales' => 'Presales',
    'sales' => 'Sales',
    'finance' => 'Finance',
    'teacher' => 'Teacher',
    'counselor' => 'Counselor'
]);

// File upload configuration
define('UPLOAD_PATH', BASE_PATH . '/uploads/');

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
?>
