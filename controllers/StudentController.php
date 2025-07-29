<?php
require_once BASE_PATH . '/models/Student.php';
require_once BASE_PATH . '/models/Remark.php';

class StudentController {
    private $db;
    private $student;
    private $remark;

    public function __construct($db) {
        $this->db = $db;
        $this->student = new Student($db);
        $this->remark = new Remark($db);
    }

    private function checkAccess() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php?action=login");
            exit;
        }
    }

    public function index() {
        $this->checkAccess();
        
        // Fetch unread notification count for the sidebar
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
        
        // Build filters from GET parameters
        $filters = [];
        if (!empty($_GET['stage'])) $filters['stage'] = $_GET['stage'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        if (!empty($_GET['branch'])) $filters['branch'] = $_GET['branch'];
        if (!empty($_GET['mode'])) $filters['mode'] = $_GET['mode'];
        if (!empty($_GET['source'])) $filters['source'] = $_GET['source'];
        if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
        
        $students = $this->student->getAll($filters);
        
        require_once BASE_PATH . '/views/student/index.php';
    }

    public function create() {
        $this->checkAccess();
        
        // Fetch unread notification count for the sidebar
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
        
        $success_msg = $error_msg = "";

        // Handle Manual Form Submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['manual_submit'])) {
            $data = [
                'name' => trim($_POST['name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'course' => trim($_POST['course']),
                'location' => trim($_POST['location']),
                'mode' => trim($_POST['mode']),
                'source' => trim($_POST['source']),
                'branch' => trim($_POST['branch']),
                'stage' => trim($_POST['stage'])
            ];

            if (!empty($data['name']) && !empty($data['mobile']) && !empty($data['email'])) {
                // Check for duplicate email
                if ($this->student->emailExists($data['email'])) {
                    $error_msg = "Email address already exists in the system.";
                } elseif ($this->student->mobileExists($data['mobile'])) {
                    $error_msg = "Mobile number already exists in the system.";
                } else {
                    $student_id = $this->student->create($data, $_SESSION['id']);
                    if ($student_id) {
                        $success_msg = "Student enquiry created successfully! ID: " . $student_id;
                    } else {
                        $error_msg = "Error creating enquiry. Please try again.";
                    }
                }
            } else {
                $error_msg = "Name, Mobile Number, and Email are required.";
            }
        }

        // Handle CSV File Upload
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['student_file'])) {
            if ($_FILES['student_file']['error'] == UPLOAD_ERR_OK) {
                $file_tmp_path = $_FILES['student_file']['tmp_name'];
                $file_type = mime_content_type($file_tmp_path);

                if ($file_type == 'text/csv' || strpos($file_type, 'application/vnd.ms-excel') !== false) {
                    $handle = fopen($file_tmp_path, "r");
                    if ($handle !== FALSE) {
                        $created_count = 0;
                        $skipped_count = 0;
                        $error_details = [];

                        fgetcsv($handle); // Skip header row

                        $row_number = 1;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $row_number++;
                            
                            if (count($data) >= 3) { // At least name, mobile, email
                                $rowData = [
                                    trim($data[0]), // Name
                                    trim($data[1]), // Mobile
                                    trim($data[2]), // Email
                                    trim($data[3] ?? ''), // Course
                                    trim($data[4] ?? ''), // Location
                                    trim($data[5] ?? 'online'), // Mode
                                    trim($data[6] ?? 'website'), // Source
                                    trim($data[7] ?? 'nagpur'), // Branch
                                    trim($data[8] ?? 'warm'), // Stage
                                    $_SESSION['id']  // created_by
                                ];
                                
                                // Validate required fields
                                if (empty($rowData[0]) || empty($rowData[1]) || empty($rowData[2])) {
                                    $skipped_count++;
                                    $error_details[] = "Row $row_number: Missing required fields (Name, Mobile, Email)";
                                    continue;
                                }
                                
                                // Check for duplicates
                                if ($this->student->emailExists($rowData[2])) {
                                    $skipped_count++;
                                    $error_details[] = "Row $row_number: Email already exists - " . $rowData[2];
                                    continue;
                                }
                                
                                if ($this->student->mobileExists($rowData[1])) {
                                    $skipped_count++;
                                    $error_details[] = "Row $row_number: Mobile already exists - " . $rowData[1];
                                    continue;
                                }
                                
                                if ($this->student->createFromRow($rowData)) {
                                    $created_count++;
                                } else {
                                    $skipped_count++;
                                    $error_details[] = "Row $row_number: Database error for " . $rowData[0];
                                }
                            } else {
                                $skipped_count++;
                                $error_details[] = "Row $row_number: Insufficient columns (minimum 3 required)";
                            }
                        }
                        fclose($handle);
                        
                        $success_msg = "CSV file processed successfully!<br>";
                        $success_msg .= "Created: $created_count enquiries<br>";
                        $success_msg .= "Skipped: $skipped_count entries";
                        
                        if (!empty($error_details) && count($error_details) <= 10) {
                            $success_msg .= "<br><br>Details:<br>" . implode("<br>", $error_details);
                        } elseif (count($error_details) > 10) {
                            $success_msg .= "<br><br>First 10 errors:<br>" . implode("<br>", array_slice($error_details, 0, 10));
                            $success_msg .= "<br>... and " . (count($error_details) - 10) . " more errors.";
                        }
                    } else {
                        $error_msg = "Could not open the uploaded CSV file.";
                    }
                } else {
                    $error_msg = "Invalid file type. Please upload a CSV file.";
                }
            } else {
                $error_msg = "Error uploading file. Code: " . $_FILES['student_file']['error'];
            }
        }

        require_once BASE_PATH . '/views/student/create.php';
    }

    // View single student details
    public function view() {
        $this->checkAccess();
        
        // Fetch unread notification count for the sidebar
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
        
        $student_id = $_GET['id'] ?? null;
        $student = null;
        $remarks = null;
        $error_msg = "";

        if ($student_id) {
            $student = $this->student->findById($student_id);
            if ($student) {
                $remarks = $this->remark->getByStudentId($student_id);
            } else {
                $error_msg = "Student not found.";
            }
        } else {
            $error_msg = "Student ID not provided.";
        }

        require_once BASE_PATH . '/views/student/view.php';
    }

    // Update student status
    public function updateStatus() {
        $this->checkAccess();
        $student_id = $_POST['student_id'] ?? null;
        $new_status = $_POST['new_status'] ?? null;
        $current_user_role = $_SESSION['role'];

        if (!$student_id || !$new_status) {
            $_SESSION['error_message'] = "Invalid request for status update.";
            header("location: index.php?action=students");
            exit;
        }

        $student = $this->student->findById($student_id);
        if (!$student) {
            $_SESSION['error_message'] = "Student not found.";
            header("location: index.php?action=students");
            exit;
        }

        $current_status = $student['status'];
        $allowed_update = false;

        // Define allowed status transitions based on role
        switch ($current_user_role) {
            case 'admin':
                $allowed_update = true; // Admin can set any status
                break;
            case 'presales':
                if ($current_status === 'pending_presales' && $new_status === 'passed_presales') {
                    $allowed_update = true;
                }
                break;
            case 'sales':
                if ($current_status === 'passed_presales' && $new_status === 'passed_sales') {
                    $allowed_update = true;
                }
                break;
            case 'finance':
                if ($current_status === 'passed_sales' && $new_status === 'demo_scheduled') {
                    $allowed_update = true;
                } elseif ($current_status === 'demo_scheduled' && $new_status === 'payment_received') {
                    $allowed_update = true;
                } elseif ($current_status === 'payment_received' && $new_status === 'closed_finance') {
                    $allowed_update = true;
                }
                break;
        }

        if ($allowed_update) {
            if ($this->student->updateStatus($student_id, $new_status)) {
                $_SESSION['success_message'] = "Student status updated to " . ucfirst(str_replace('_', ' ', $new_status)) . ".";
            } else {
                $_SESSION['error_message'] = "Failed to update student status.";
            }
        } else {
            $_SESSION['error_message'] = "You do not have permission to make this status update or the transition is invalid.";
        }

        header("location: index.php?action=students_view&id=" . $student_id);
        exit;
    }

    // Delete a student (only creator or admin)
    public function delete() {
        $this->checkAccess();
        $student_id = $_GET['id'] ?? null;
        $current_user_id = $_SESSION['id'];
        $current_user_role = $_SESSION['role'];

        if (!$student_id) {
            $_SESSION['error_message'] = "Student ID not provided.";
            header("location: index.php?action=students");
            exit;
        }

        $student = $this->student->findById($student_id);
        if (!$student) {
            $_SESSION['error_message'] = "Student not found.";
            header("location: index.php?action=students");
            exit;
        }

        // Check permission: creator or admin
        if ($student['assigned_to'] == $current_user_id || $current_user_role === 'admin') {
            if ($this->student->delete($student_id)) {
                $_SESSION['success_message'] = "Student deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Error deleting student.";
            }
        } else {
            $_SESSION['error_message'] = "You do not have permission to delete this student.";
        }
        header("location: index.php?action=students");
        exit;
    }
}
?>
