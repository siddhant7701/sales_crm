<?php
$pageTitle = "Login";
// Initialize variables to prevent undefined notices
$username_err = $password_err = "";
// We don't include the header here to have a custom page layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page-body <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

    <div class="login-container">
        <div class="login-branding">
            <h2>Welcome to Your CRM</h2>
            <p>Manage your students, sales, and finances all in one place.</p>
            <div class="theme-toggle-container">
                <button id="theme-toggle" class="theme-toggle-btn">Toggle Theme</button>
            </div>
        </div>
        <div class="login-form-wrapper">
            <h3>Sign In</h3>
            <p>Enter your credentials to access your account.</p>

            <?php
            if (!empty($login_err)) {
                echo '<div class="alert error">' . $login_err . '</div>';
            }
            ?>

            <form action="index.php?action=login" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
            </form>
        </div>
    </div>

    <script src="js/theme-toggle.js"></script>
</body>
</html>
