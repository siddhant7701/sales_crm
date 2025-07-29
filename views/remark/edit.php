<?php
$pageTitle = "Edit Remark";
require_once BASE_PATH . '/views/layout/header.php'; // Ensure header is included
?>

<div class="content-card">
    <h2>Edit Remark</h2>
    
    <?php
    // Display messages
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <?php if (isset($remark) && $remark): ?>
        <div class="remark-info">
            <p><strong>Student:</strong> <?php echo htmlspecialchars($student_name ?? 'Unknown Student'); ?></p>
            <p><strong>Created by:</strong> <?php echo htmlspecialchars($remark['creator_display_name'] ?? $remark['creator_username'] ?? 'Unknown User'); ?></p>
            <p><strong>Created at:</strong> <?php echo date('Y-m-d H:i', strtotime($remark['created_at'])); ?></p>
            <?php if (isset($remark['updated_at']) && $remark['updated_at'] !== $remark['created_at']): ?>
                <p><strong>Last Updated:</strong> <?php echo date('Y-m-d H:i', strtotime($remark['updated_at'])); ?></p>
            <?php endif; ?>
        </div>

        <form action="index.php?action=remarks_update" method="post" class="remark-form">
            <input type="hidden" name="remark_id" value="<?php echo htmlspecialchars($remark['id'] ?? ''); ?>">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($remark['student_id'] ?? ''); ?>">
            
            <div class="form-group">
                <label for="remark_text">Remark Text:</label>
                <textarea name="remark_text" id="remark_text" class="form-control" rows="5" required><?php echo htmlspecialchars($remark['remark_text'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/></svg>
                    Update Remark
                </button>
                <a href="index.php?action=students_view&id=<?php echo htmlspecialchars($remark['student_id'] ?? ''); ?>" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="m15 18-6-6 6-6"/></svg>
                    Cancel
                </a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-error">Remark not found or you don't have permission to edit it.</div>
        <p><a href="index.php?action=students" class="btn btn-secondary">Back to Students</a></p>
    <?php endif; ?>
</div>

<style>
/* General layout for content cards */
.content-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin: var(--spacing-lg) auto;
    max-width: 800px;
    box-shadow: var(--shadow-md);
}

.content-card h2 {
    margin-top: 0;
    margin-bottom: var(--spacing-lg);
    color: var(--text-primary);
    font-size: 1.75rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: var(--spacing-md);
}

/* Alert Styles (re-using from student/view.php for consistency) */
.alert {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-lg);
    font-weight: 500;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid var(--success-color);
    color: var(--success-color);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid var(--danger-color);
    color: var(--danger-color);
}

.alert-icon {
    flex-shrink: 0;
}

/* Remark Info Section */
.remark-info {
    background: var(--bg-secondary);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-lg);
    border-left: 4px solid var(--primary-color);
    color: var(--text-primary);
}

.remark-info p {
    margin: var(--spacing-xs) 0;
    font-size: 0.95rem;
}

.remark-info strong {
    color: var(--text-primary);
}

/* Form Styles */
.remark-form {
    background: var(--bg-secondary);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
    border: 1px solid var(--border-color);
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-group label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all 0.3s ease;
    resize: vertical; /* Allow vertical resizing for textarea */
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:hover {
    border-color: var(--border-hover);
}

.form-actions {
    display: flex;
    gap: var(--spacing-sm);
    justify-content: flex-start;
}

/* Button Styles (re-using from student/view.php for consistency) */
.btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-lg);
    border: none;
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--bg-secondary);
    border-color: var(--border-hover);
    transform: translateY(-2px);
}

.icon {
    width: 18px;
    height: 18px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .content-card {
        padding: var(--spacing-md);
        margin: var(--spacing-md) auto;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
