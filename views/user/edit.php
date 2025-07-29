<?php
$pageTitle = "Edit User";
require_once BASE_PATH . '/views/layout/header.php';
$roles = ['super_admin', 'location_admin', 'admin', 'presales', 'sales', 'finance', 'teacher', 'counselor'];
$current_user_id = $this->user->id;
$current_username = $this->user->username;
$current_display_name = $this->user->display_name;
$current_profile_image = $this->user->profile_image;
$current_role = $this->user->role;
$current_location_id = $this->user->location_id;
?>
<div class="form-wrapper">
    <h2>Edit User: <?php echo htmlspecialchars($current_display_name ?: $current_username); ?></h2>
    <p>Update the user's details.</p>
    <?php
    if (!empty($success_msg)) {
        echo '<div class="alert success">' . $success_msg . '</div>';
    }
    if (!empty($error_msg)) {
        echo '<div class="alert error">' . $error_msg . '</div>';
    }
    ?>
    <?php if ($current_profile_image): ?>
        <div class="current-image">
            <p><strong>Current Profile Image:</strong></p>
            <img src="index.php?action=user_image&id=<?php echo htmlspecialchars($current_user_id); ?>" alt="Profile Image" style="max-width: 100px; max-height: 100px; border-radius: 50%;">
        </div>
    <?php endif; ?>
    <form action="index.php?action=users_edit&id=<?php echo htmlspecialchars($current_user_id); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username ?? $current_username); ?>" required>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" id="display_name" name="display_name" class="form-control <?php echo (!empty($display_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($display_name ?? $current_display_name); ?>" required>
            <span class="help-block"><?php echo $display_name_err; ?></span>
        </div>
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
            <small class="form-text">Leave blank to keep current image. Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB.</small>
        </div>
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control" value="">
            <small class="form-text"><strong>Leave blank to keep current password.</strong> Only enter a new password if you want to change it.</small>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo htmlspecialchars($r); ?>" <?php echo (isset($role) ? ($role === $r) : ($current_role === $r)) ? 'selected' : ''; ?>>
                        <?php echo ucwords(str_replace('_', ' ', $r)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="help-block"><?php echo $role_err; ?></span>
        </div>
        <div class="form-group">
            <label for="location_id">Location</label>
            <select id="location_id" name="location_id" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>">
                <option value="">Select Location</option>
                <?php if ($locations): ?>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?php echo $location['id']; ?>" <?php echo (isset($location_id) ? ($location_id == $location['id']) : ($current_location_id == $location['id'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($location['name'] . ' (' . $location['code'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <span class="help-block"><?php echo $location_err; ?></span>
            <small class="form-text">Select the location this user belongs to. Leave blank for online-only users.</small>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update User">
            <a href="index.php?action=users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Show/hide location field based on role
document.getElementById('role').addEventListener('change', function() {
    const locationGroup = document.getElementById('location_id').closest('.form-group');
    const locationSelect = document.getElementById('location_id');
    
    if (this.value === 'online') {
        locationGroup.style.display = 'none';
        locationSelect.value = '';
        locationSelect.required = false;
    } else {
        locationGroup.style.display = 'block';
        locationSelect.required = true;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value === 'online') {
        const locationGroup = document.getElementById('location_id').closest('.form-group');
        const locationSelect = document.getElementById('location_id');
        locationGroup.style.display = 'none';
        locationSelect.required = false;
    }
});
</script>