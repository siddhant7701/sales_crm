<?php
$pageTitle = "Dashboard";
require_once BASE_PATH . '/views/layout/header.php';

// Create AuthController instance to access timeAgo method
$authController = new AuthController($conn);
?>

<div class="dashboard-content">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1 class="welcome-title">
                Welcome back, <?php echo htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username']); ?>!
            </h1>
            <p class="welcome-subtitle">
                Here's what's happening with your <?php echo ucfirst($_SESSION['role']); ?> dashboard today.
            </p>
        </div>
        <div class="welcome-actions">
            <a href="index.php?action=students_create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Student
            </a>
            <a href="index.php?action=group_chat" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Group Chat
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo $stats['total_students']; ?></h3>
                <p class="stat-label">Total Students</p>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo $stats['pending_students']; ?></h3>
                <p class="stat-label">Pending Students</p>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo $stats['active_students']; ?></h3>
                <p class="stat-label">Active Students</p>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo $stats['pending_tasks']; ?></h3>
                <p class="stat-label">Pending Tasks</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <h2 class="section-title">Quick Actions</h2>
        <div class="quick-actions-grid">
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'presales'): ?>
            <a href="index.php?action=students_create" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3>Add New Student</h3>
                <p>Register a new student in the system</p>
            </a>
            <?php endif; ?>

            <a href="index.php?action=students" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>
                </div>
                <h3>View All Students</h3>
                <p>Browse and manage student records</p>
            </a>

            <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="index.php?action=users" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/><path d="M16 18v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/></svg>
                </div>
                <h3>Manage Users</h3>
                <p>Add and manage system users</p>
            </a>
            <?php endif; ?>

            <a href="index.php?action=group_chat" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <h3>Group Chat</h3>
                <p>Communicate with team members</p>
            </a>
        </div>
    </div>

    <!-- Role Information -->
    <div class="role-info-section">
        <div class="role-info-card">
            <div class="role-info-header">
                <h2 class="section-title">Your Role: <?php echo ucfirst($_SESSION['role']); ?></h2>
                <div class="role-badge role-badge-<?php echo $_SESSION['role']; ?>">
                    <?php echo ucfirst($_SESSION['role']); ?>
                </div>
            </div>
            <div class="role-permissions">
                <h3>Your Permissions:</h3>
                <ul class="permissions-list">
                    <?php
                    $permissions = [];
                    switch ($_SESSION['role']) {
                        case 'admin':
                            $permissions = [
                                'Manage all students and users',
                                'Access all system features',
                                'View comprehensive reports',
                                'Configure system settings'
                            ];
                            break;
                        case 'sales':
                            $permissions = [
                                'View and update student records',
                                'Conduct sales calls',
                                'Add remarks and notes',
                                'Access group chat'
                            ];
                            break;
                        case 'presales':
                            $permissions = [
                                'Add new students',
                                'Update student status',
                                'Follow up with prospects',
                                'Access group chat'
                            ];
                            break;
                        case 'finance':
                            $permissions = [
                                'Manage payment records',
                                'Grant access to students',
                                'Handle financial transactions',
                                'Access group chat'
                            ];
                            break;
                        default:
                            $permissions = ['Basic system access'];
                    }
                    
                    foreach ($permissions as $permission): ?>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php echo $permission; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity-section">
        <h2 class="section-title">Recent Activity</h2>
        <div class="activity-feed">
            <?php if (!empty($recent_activity)): ?>
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon activity-icon-<?php echo $activity['type']; ?>">
                            <?php if ($activity['type'] === 'student_update'): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <?php elseif ($activity['type'] === 'group_message'): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <p class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></p>
                            <span class="activity-time"><?php echo $authController->timeAgo($activity['time']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    </div>
                    <div class="activity-content">
                        <p class="activity-description">No recent activity to display.</p>
                        <span class="activity-time">Start by adding students or sending messages!</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
