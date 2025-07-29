<?php
$pageTitle = "Create User";
require_once BASE_PATH . '/views/layout/header.php';
$roles = ['super_admin', 'location_admin', 'admin', 'presales', 'sales', 'finance', 'teacher', 'counselor'];
?>
<div class="form-wrapper">
    <h2>Create New User</h2>
    <p>Add a new user to the system with their role and location.</p>
    <?php
    if (!empty($success_msg)) {
        echo '<div class="alert success">' . $success_msg . '</div>';
    }
    if (!empty($error_msg)) {
        echo '<div class="alert error">' . $error_msg . '</div>';
    }
    ?>
    <form action="index.php?action=users_create" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username <span class="required">*</span></label>
            <input type="text" id="username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label for="display_name">Display Name <span class="required">*</span></label>
            <input type="text" id="display_name" name="display_name" class="form-control <?php echo (!empty($display_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($display_name ?? ''); ?>" required>
            <span class="help-block"><?php echo $display_name_err; ?></span>
            <small class="form-text">This name will be displayed throughout the system.</small>
        </div>
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
            <small class="form-text">Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB. Optional.</small>
        </div>
        <div class="form-group">
            <label for="password">Password <span class="required">*</span></label>
            <input type="password" id="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($password ?? ''); ?>" required>
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <label for="role">Role <span class="required">*</span></label>
            <select id="role" name="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo htmlspecialchars($r); ?>" <?php echo (isset($role) && $role === $r) ? 'selected' : ''; ?>>
                        <?php echo ucwords(str_replace('_', ' ', $r)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="help-block"><?php echo $role_err; ?></span>
        </div>
        <div class="form-group" id="location-group">
            <label for="location_id">Location</label>
            <select id="location_id" name="location_id" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>">
                <option value="">Select Location</option>
                <?php if ($locations): ?>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?php echo $location['id']; ?>" <?php echo (isset($location_id) && $location_id == $location['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($location['name'] . ' (' . $location['code'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <span class="help-block"><?php echo $location_err; ?></span>
            <small class="form-text">Select the location this user belongs to. Required for most roles.</small>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Create User">
            <a href="index.php?action=users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.required {
    color: #e74c3c;
    font-weight: bold;
}

.form-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    outline: none;
}

.form-control.is-invalid {
    border-color: #e74c3c;
}

.help-block {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.form-text {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #333;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
// Show/hide location field based on role
document.getElementById('role').addEventListener('change', function() {
    const locationGroup = document.getElementById('location-group');
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
        const locationGroup = document.getElementById('location-group');
        const locationSelect = document.getElementById('location_id');
        locationGroup.style.display = 'none';
        locationSelect.required = false;
    }
});
</script>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>