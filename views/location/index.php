<?php
$pageTitle = "Manage Locations";
require_once BASE_PATH . '/views/layout/header.php';
?>
<div class="content-header">
    <div class="header-section">
        <h1>Location Management</h1>
        <p>Manage all locations and branches in the system</p>
    </div>
    <div class="header-actions">
        <?php if (in_array($_SESSION['role'], ['super_admin', 'location_admin'])): ?>
            <a href="index.php?action=locations_create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14"/><path d="M5 12h14"/>
                </svg>
                Add New Location
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m9 12 2 2 4-4"/>
            <circle cx="12" cy="12" r="10"/>
        </svg>
        <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert error">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <?php echo $error_msg; ?>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Location Name</th>
                    <th>Code</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Students</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($locations && $locations->num_rows > 0): ?>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="location-name">
                                    <strong><?php echo htmlspecialchars($location['name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="location-code"><?php echo htmlspecialchars($location['code']); ?></span>
                            </td>
                            <td>
                                <div class="location-address">
                                    <?php if (!empty($location['address'])): ?>
                                        <?php echo htmlspecialchars($location['address']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <?php if (!empty($location['phone'])): ?>
                                        <div class="phone">📞 <?php echo htmlspecialchars($location['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($location['email'])): ?>
                                        <div class="email">✉️ <?php echo htmlspecialchars($location['email']); ?></div>
                                    <?php endif; ?>
                                    <?php if (empty($location['phone']) && empty($location['email'])): ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $location['status']; ?>">
                                    <?php echo ucfirst($location['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="count-badge">
                                    <?php echo $location['user_count']; ?> users
                                </span>
                            </td>
                            <td>
                                <span class="count-badge">
                                    <?php echo $location['student_count']; ?> students
                                </span>
                            </td>
                            <td>
                                <div class="date-info">
                                    <?php echo date('M j, Y', strtotime($location['created_at'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if (in_array($_SESSION['role'], ['super_admin', 'location_admin'])): ?>
                                        <a href="index.php?action=locations_edit&id=<?php echo $location['id']; ?>" 
                                           class="btn btn-sm btn-secondary" title="Edit Location">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                                <path d="m15 5 4 4"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($_SESSION['role'] === 'super_admin'): ?>
                                        <?php if ($location['user_count'] == 0 && $location['student_count'] == 0): ?>
                                            <a href="index.php?action=locations_delete&id=<?php echo $location['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this location? This action cannot be undone.')"
                                               title="Delete Location">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="btn btn-sm btn-disabled" title="Cannot delete - has associated users or students">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="m15 9-6 6"/>
                                                    <path d="m9 9 6 6"/>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <h3>No Locations Found</h3>
                                <p>Start by adding your first location to the system.</p>
                                <?php if (in_array($_SESSION['role'], ['super_admin', 'location_admin'])): ?>
                                    <a href="index.php?action=locations_create" class="btn btn-primary">Add First Location</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-color);
}

.header-section h1 {
    margin: 0;
    color: var(--text-primary);
    font-size: 28px;
    font-weight: 600;
}

.header-section p {
    margin: 4px 0 0 0;
    color: var(--text-secondary);
}

.location-code {
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    font-weight: bold;
}

.location-address {
    max-width: 200px;
    word-wrap: break-word;
}

.contact-info {
    font-size: 12px;
}

.contact-info .phone,
.contact-info .email {
    margin-bottom: 2px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.count-badge {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
}

.date-info {
    font-size: 12px;
    color: var(--text-secondary);
}

.btn-disabled {
    background: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
}

.empty-state svg {
    color: var(--text-secondary);
    margin-bottom: 16px;
}

.empty-state h3 {
    margin: 0 0 8px 0;
    color: var(--text-primary);
}

.empty-state p {
    margin: 0 0 20px 0;
    color: var(--text-secondary);
}

.text-muted {
    color: var(--text-secondary);
    font-style: italic;
}
</style>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>