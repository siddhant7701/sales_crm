<?php
$pageTitle = "Manage Users";
require_once BASE_PATH . '/views/layout/header.php';
?>

<div class="user-management-wrapper">
    <h2>User Management</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert error">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <div class="action-bar">
        <a href="index.php?action=users_create" class="btn btn-primary">Create New User</a>
        <a href="index.php?action=cleanup_messages&manual=1" class="btn btn-secondary">Clean Old Messages</a>
    </div>

    <?php if ($users && $users->num_rows > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Display Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['has_image']): ?>
                                <img src="index.php?action=user_image&id=<?php echo $row['id']; ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?php echo strtoupper(substr($row['display_name'] ?: $row['username'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['display_name'] ?: 'Not set'); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <a href="index.php?action=users_edit&id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                            <a href="index.php?action=users_delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-small" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
