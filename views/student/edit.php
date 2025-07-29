<?php
$pageTitle = "Edit Student";
require_once BASE_PATH . '/views/layout/header.php';
?>

<div class="edit-student-container">
    <div class="edit-header">
        <h2>Edit Student Details</h2>
        <div class="header-actions">
            <a href="index.php?action=students_view&id=<?php echo $student['id']; ?>" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="m15 18-6-6 6-6"/></svg>
                Back to View
            </a>
        </div>
    </div>

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

    <?php if ($student): ?>
        <form action="index.php?action=students_update" method="post" class="edit-form">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
            
            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3>Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile">Mobile Number <span class="required">*</span></label>
                        <input type="tel" id="mobile" name="mobile" class="form-control" value="<?php echo htmlspecialchars($student['mobile']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="course">Course/Subject</label>
                        <input type="text" id="course" name="course" class="form-control" value="<?php echo htmlspecialchars($student['course'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Contact & Location -->
                <div class="form-section">
                    <h3>Contact & Location</h3>
                    
                    <div class="form-group">
                        <label for="location">Location/Address</label>
                        <textarea id="location" name="location" class="form-control" rows="3"><?php echo htmlspecialchars($student['location'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="mode">Learning Mode</label>
                        <select id="mode" name="mode" class="form-control">
                            <option value="online" <?php echo ($student['mode'] ?? '') === 'online' ? 'selected' : ''; ?>>Online</option>
                            <option value="offline" <?php echo ($student['mode'] ?? '') === 'offline' ? 'selected' : ''; ?>>Offline</option>
                            <option value="hybrid" <?php echo ($student['mode'] ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="source">Lead Source</label>
                        <select id="source" name="source" class="form-control">
                            <option value="website" <?php echo ($student['source'] ?? '') === 'website' ? 'selected' : ''; ?>>Website</option>
                            <option value="social_media" <?php echo ($student['source'] ?? '') === 'social_media' ? 'selected' : ''; ?>>Social Media</option>
                            <option value="referral" <?php echo ($student['source'] ?? '') === 'referral' ? 'selected' : ''; ?>>Referral</option>
                            <option value="advertisement" <?php echo ($student['source'] ?? '') === 'advertisement' ? 'selected' : ''; ?>>Advertisement</option>
                            <option value="direct" <?php echo ($student['source'] ?? '') === 'direct' ? 'selected' : ''; ?>>Direct</option>
                            <option value="other" <?php echo ($student['source'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="branch">Branch</label>
                        <select id="branch" name="branch" class="form-control">
                            <option value="nagpur" <?php echo ($student['branch'] ?? '') === 'nagpur' ? 'selected' : ''; ?>>Nagpur</option>
                            <option value="mumbai" <?php echo ($student['branch'] ?? '') === 'mumbai' ? 'selected' : ''; ?>>Mumbai</option>
                            <option value="pune" <?php echo ($student['branch'] ?? '') === 'pune' ? 'selected' : ''; ?>>Pune</option>
                            <option value="delhi" <?php echo ($student['branch'] ?? '') === 'delhi' ? 'selected' : ''; ?>>Delhi</option>
                        </select>
                    </div>
                </div>

                <!-- Status & Stage -->
                <div class="form-section">
                    <h3>Status & Stage</h3>
                    
                    <div class="form-group">
                        <label for="stage">Lead Stage</label>
                        <select id="stage" name="stage" class="form-control">
                            <option value="hot" <?php echo ($student['stage'] ?? '') === 'hot' ? 'selected' : ''; ?>>Hot</option>
                            <option value="warm" <?php echo ($student['stage'] ?? '') === 'warm' ? 'selected' : ''; ?>>Warm</option>
                            <option value="cold" <?php echo ($student['stage'] ?? '') === 'cold' ? 'selected' : ''; ?>>Cold</option>
                        </select>
                    </div>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="form-group">
                            <label for="status">Status (Admin Only)</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending_presales" <?php echo ($student['status'] ?? '') === 'pending_presales' ? 'selected' : ''; ?>>Pending Presales</option>
                                <option value="passed_presales" <?php echo ($student['status'] ?? '') === 'passed_presales' ? 'selected' : ''; ?>>Passed Presales</option>
                                <option value="passed_sales" <?php echo ($student['status'] ?? '') === 'passed_sales' ? 'selected' : ''; ?>>Passed Sales</option>
                                <option value="demo_scheduled" <?php echo ($student['status'] ?? '') === 'demo_scheduled' ? 'selected' : ''; ?>>Demo Scheduled</option>
                                <option value="payment_received" <?php echo ($student['status'] ?? '') === 'payment_received' ? 'selected' : ''; ?>>Payment Received</option>
                                <option value="closed_finance" <?php echo ($student['status'] ?? '') === 'closed_finance' ? 'selected' : ''; ?>>Closed Finance</option>
                            </select>
                            <small class="form-text">Only admins can change the status directly</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/></svg>
                    Update Student
                </button>
                <a href="index.php?action=students_view&id=<?php echo $student['id']; ?>" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M11 17l-5-5 5-5m6 10l-5-5 5-5"/></svg>
                    Cancel
                </a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert error">
            <?php echo $error_msg ?: "Student not found or you don't have permission to edit."; ?>
        </div>
        <p><a href="index.php?action=students" class="btn btn-secondary">Back to Students</a></p>
    <?php endif; ?>
</div>

<style>
.edit-student-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--spacing-lg);
}

.edit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
    padding-bottom: var(--spacing-lg);
    border-bottom: 2px solid var(--border-color);
}

.edit-header h2 {
    margin: 0;
    color: var(--text-primary);
    font-size: 2rem;
}

.header-actions {
    display: flex;
    gap: var(--spacing-sm);
}

.edit-form {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-xl);
}

.form-section {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
}

.form-section h3 {
    margin: 0 0 var(--spacing-lg) 0;
    color: var(--text-primary);
    font-size: 1.25rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: var(--spacing-sm);
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.required {
    color: var(--danger-color);
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
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:hover {
    border-color: var(--border-hover);
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.form-text {
    display: block;
    margin-top: var(--spacing-xs);
    font-size: 0.8rem;
    color: var(--text-muted);
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    justify-content: flex-start;
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border-color);
}

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
    min-width: 140px;
    justify-content: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
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

.alert {
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-lg);
    border: 1px solid;
    font-size: 0.9rem;
}

.alert.success {
    background: rgba(16, 185, 129, 0.1);
    border-color: var(--success-color);
    color: var(--success-color);
}

.alert.error {
    background: rgba(239, 68, 68, 0.1);
    border-color: var(--danger-color);
    color: var(--danger-color);
}

@media (max-width: 768px) {
    .edit-header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }
    
    .header-actions {
        width: 100%;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .edit-student-container {
        padding: var(--spacing-md);
    }
    
    .edit-form {
        padding: var(--spacing-lg);
    }
    
    .form-section {
        padding: var(--spacing-md);
    }
}
</style>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
