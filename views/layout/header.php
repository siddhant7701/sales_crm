<?php
// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php?action=login");
    exit;
}

// The unread notification count should be passed from the controller
// If not set, default to 0
$unread_notifications_count = $unread_notifications_count ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'CRM Admin'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">
    <?php require_once BASE_PATH . '/views/layout/sidebar.php'; ?>
    <div class="main-content-area">
        <div class="container">
