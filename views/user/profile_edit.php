<?php
$pageTitle = "Profile Settings";
require_once BASE_PATH . '/views/layout/header.php';
$current_user_id = $this->user->id;
$current_username = $this->user->username;
$current_display_name = $this->user->display_name;
$has_profile_image = !empty($this->user->profile_image);
$current_role = $this->user->role;
?>
<div class="content-card">
    <h2>Your Profile Settings</h2>
    <p>Update your username, display name, profile image, and password.</p>
    <?php
    if (!empty($success_msg)) {
        echo '<div class="alert success">' . $success_msg . '</div>';
    }
    if (!empty($error_msg)) {
        echo '<div class="alert error">' . $error_msg . '</div>';
    }
    ?>
    <?php if ($has_profile_image): ?>
        <div class="current-image" style="text-align: center; margin-bottom: 20px;">
            <p><strong>Current Profile Image:</strong></p>
            <img src="index.php?action=user_image&id=<?php echo $current_user_id; ?>" alt="Profile Image" style="max-width: 120px; max-height: 120px; border-radius: 50%; border: 3px solid var(--primary-color);">
        </div>
    <?php endif; ?>
    <form action="index.php?action=profile_edit" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username ?? $current_username); ?>" required>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" id="display_name" name="display_name" class="form-control <?php echo (!empty($display_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($display_name ?? $current_display_name); ?>" required>
            <span class="help-block"><?php echo $display_name_err; ?></span>
            <small class="form-text">This is the name that will be displayed in chats and throughout the system.</small>
        </div>
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
            <small class="form-text">Leave blank to keep current image. Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB. Images are stored securely in the database.</small>
        </div>
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control" value="">
            <small class="form-text"><strong>Leave blank to keep your current password.</strong> Only enter a new password if you want to change it.</small>
        </div>
        <div class="form-group">
            <label>Role</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $current_role))); ?>" disabled>
            <small class="form-text">Your role cannot be changed here. Contact an administrator.</small>
        </div>
        <div class="form-actions">
            <input type="submit" class="btn btn-primary" value="Update Profile">
            <a href="index.php?action=dashboard" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>