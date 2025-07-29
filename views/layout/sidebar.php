<?php
// Get current user info from session
$current_username = $_SESSION['username'] ?? 'Guest';
$current_role = $_SESSION['role'] ?? 'guest';
$current_user_id = $_SESSION['id'] ?? null;

// The unread notification count should be passed from the controller
// If not set, default to 0
$unread_notifications_count = $unread_notifications_count ?? 0;

// Check if user has profile image (this should be passed from controller)
$has_profile_image = $has_profile_image ?? false;
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <div class="logo-text">
                <div class="logo-title">CRM Admin</div>
                <div class="logo-subtitle">Management System</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php?action=dashboard" class="nav-link <?php echo ($action === 'dashboard') ? 'active' : ''; ?>">
                        <div class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=students" class="nav-link <?php echo (strpos($action, 'students') === 0) ? 'active' : ''; ?>">
                        <div class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <span class="nav-text">Students</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=group_chat" class="nav-link <?php echo ($action === 'group_chat') ? 'active' : ''; ?>">
                        <div class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <span class="nav-text">Group Chat</span>
                        <?php if ($unread_notifications_count > 0): ?>
                            <span class="nav-badge"><?php echo $unread_notifications_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>

        <?php if ($current_role === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php?action=users" class="nav-link <?php echo (strpos($action, 'users') === 0 && $action !== 'profile_edit') ? 'active' : ''; ?>">
                        <div class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <span class="nav-text">Manage Users</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <!-- User Profile Section -->
        <div class="user-profile-section">
            <a href="index.php?action=profile_edit" class="user-profile-link <?php echo ($action === 'profile_edit') ? 'active' : ''; ?>">
                <div class="user-avatar">
                    <?php if ($has_profile_image): ?>
                        <img src="index.php?action=user_image&id=<?php echo $current_user_id; ?>" alt="<?php echo htmlspecialchars($current_username); ?>" class="avatar-image">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($current_username, 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="user-status online"></div>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($current_username); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars(ucfirst($current_role)); ?></div>
                </div>
                <div class="profile-settings-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
            </a>
        </div>
        
        <!-- Theme Toggle -->
        <div class="theme-toggle-section">
            <button id="theme-toggle" class="theme-toggle-btn">
                <div class="theme-icon">
                    <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M4.93 19.07l1.41-1.41"/><path d="M17.66 6.34l1.41-1.41"/></svg>
                    <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </div>
                <span class="theme-text">Toggle Theme</span>
            </button>
        </div>

        <!-- Logout -->
        <div class="logout-section">
            <a href="index.php?action=logout" class="logout-btn">
                <div class="logout-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="10" y1="12" y2="12"/></svg>
                </div>
                <span class="logout-text">Logout</span>
            </a>
        </div>
    </div>
</aside>
