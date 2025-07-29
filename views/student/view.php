<?php
$pageTitle = "Student Details";
require_once BASE_PATH . '/views/layout/header.php';
?>

<div class="student-view-wrapper">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="alert-icon"><polyline points="20 6 9 17 4 12"/></svg>
            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="alert-icon"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <?php if ($student): ?>
        <!-- Student Header -->
        <div class="student-header-card">
            <div class="student-info">
                <div class="student-avatar">
                    <div class="avatar-circle">
                        <?php echo strtoupper(substr($student['name'], 0, 2)); ?>
                    </div>
                </div>
                <div class="student-details">
                    <h1 class="student-name"><?php echo htmlspecialchars($student['name']); ?></h1>
                    <p class="student-email"><?php echo htmlspecialchars($student['email']); ?></p>
                    <div class="student-badges">
                        <span class="badge status-<?php echo htmlspecialchars($student['status']); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($student['status']))); ?>
                        </span>
                        <span class="badge stage-<?php echo htmlspecialchars($student['stage']); ?>">
                            <?php echo ucfirst(htmlspecialchars($student['stage'])); ?> Lead
                        </span>
                    </div>
                </div>
            </div>
            <div class="student-actions">
                <?php if ($student['assigned_to'] == ($_SESSION['id'] ?? null) || ($_SESSION['role'] ?? 'guest') === 'admin'): ?>
                    <a href="index.php?action=students_edit&id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit Student
                    </a>
                <?php endif; ?>
                <a href="index.php?action=students" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="m15 18-6-6 6-6"/></svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Contact Actions -->
        <div class="contact-actions-card">
            <h3>Quick Actions</h3>
            <div class="contact-buttons">
                <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="contact-btn email-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    Send Email
                </a>
                <?php if (!empty($student['mobile'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($student['mobile']); ?>" class="contact-btn phone-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Call Now
                    </a>
                    <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D/', '', $student['mobile'])); ?>" target="_blank" class="contact-btn whatsapp-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Student Information Grid -->
        <div class="info-grid">
            <!-- Basic Information -->
            <div class="info-card">
                <div class="card-header">
                    <h3>Basic Information</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-icon"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="card-content">
                    <div class="info-item">
                        <span class="label">Full Name</span>
                        <span class="value"><?php echo htmlspecialchars($student['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email</span>
                        <span class="value"><?php echo htmlspecialchars($student['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Mobile</span>
                        <span class="value"><?php echo htmlspecialchars($student['mobile'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Course Interest</span>
                        <span class="value"><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Location</span>
                        <span class="value"><?php echo htmlspecialchars($student['location'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="info-card">
                <div class="card-header">
                    <h3>Enrollment Details</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-icon"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div class="card-content">
                    <div class="info-item">
                        <span class="label">Learning Mode</span>
                        <span class="value"><?php echo ucfirst(htmlspecialchars($student['mode'] ?? 'N/A')); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Lead Source</span>
                        <span class="value"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($student['source'] ?? 'N/A'))); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Branch</span>
                        <span class="value"><?php echo ucfirst(htmlspecialchars($student['branch'] ?? 'N/A')); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Lead Stage</span>
                        <span class="value">
                            <span class="badge stage-<?php echo htmlspecialchars($student['stage']); ?>">
                                <?php echo ucfirst(htmlspecialchars($student['stage'] ?? 'N/A')); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Current Status</span>
                        <span class="value">
                            <span class="badge status-<?php echo htmlspecialchars($student['status']); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($student['status'] ?? 'N/A'))); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="info-card">
                <div class="card-header">
                    <h3>System Information</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-icon"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="card-content">
                    <div class="info-item">
                        <span class="label">Created By</span>
                        <span class="value"><?php echo htmlspecialchars($student['creator_display_name'] ?? $student['creator_name'] ?? 'Unknown'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Created Date</span>
                        <span class="value"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Created Time</span>
                        <span class="value"><?php echo date('h:i A', strtotime($student['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Last Updated</span>
                        <span class="value"><?php echo date('M d, Y h:i A', strtotime($student['updated_at'] ?? $student['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Demo & Payment Details -->
            <?php if (!empty($student['demo_date']) || !empty($student['payment_amount'])): ?>
            <div class="info-card">
                <div class="card-header">
                    <h3>Demo & Payment</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-icon"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="card-content">
                    <?php if (!empty($student['demo_date'])): ?>
                        <div class="info-item">
                            <span class="label">Demo Date</span>
                            <span class="value"><?php echo date('M d, Y', strtotime($student['demo_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Demo Time</span>
                            <span class="value"><?php echo htmlspecialchars($student['demo_time'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Demo Pass</span>
                            <span class="value"><?php echo htmlspecialchars($student['demo_pass'] ?? 'N/A'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($student['payment_amount'])): ?>
                        <div class="info-item">
                            <span class="label">Payment Amount</span>
                            <span class="value payment-amount">₹<?php echo number_format($student['payment_amount'], 2); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Payment Date</span>
                            <span class="value"><?php echo date('M d, Y', strtotime($student['payment_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Payment Method</span>
                            <span class="value"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($student['payment_method'] ?? 'N/A'))); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Status Update Section -->
        <div class="status-update-section">
            <h3>Status Management</h3>
            <div class="status-actions">
                <?php
                $current_status = $student['status'];
                $user_role = $_SESSION['role'] ?? 'guest';
                ?>

                <?php if (($user_role === 'presales' && $current_status === 'pending_presales') || $user_role === 'admin'): ?>
                    <form action="index.php?action=students_update_status" method="post" class="status-form">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                        <input type="hidden" name="new_status" value="passed_presales">
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><polyline points="20 6 9 17 4 12"/></svg>
                            Mark as Passed Presales
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($user_role === 'sales' && $current_status === 'passed_presales') || $user_role === 'admin'): ?>
                    <form action="index.php?action=students_update_status" method="post" class="status-form">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                        <input type="hidden" name="new_status" value="passed_sales">
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><polyline points="20 6 9 17 4 12"/></svg>
                            Mark as Passed Sales
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($user_role === 'finance' && $current_status === 'passed_sales') || $user_role === 'admin'): ?>
                    <div class="demo-schedule-form">
                        <h4>Schedule Demo</h4>
                        <form action="index.php?action=students_add_demo_pass" method="post">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                            <div class="form-row">
                                <input type="date" name="demo_date" class="form-control" required>
                                <input type="time" name="demo_time" class="form-control" required>
                                <input type="text" name="demo_pass" class="form-control" placeholder="Demo Password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Schedule Demo</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (($user_role === 'finance' && $current_status === 'demo_scheduled') || $user_role === 'admin'): ?>
                    <div class="payment-record-form">
                        <h4>Record Payment</h4>
                        <form action="index.php?action=students_record_payment" method="post">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                            <div class="form-row">
                                <input type="number" name="payment_amount" class="form-control" step="0.01" placeholder="Amount (₹)" required>
                                <input type="date" name="payment_date" class="form-control" required>
                                <select name="payment_method" class="form-control" required>
                                    <option value="">Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Record Payment</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (($user_role === 'finance' && $current_status === 'payment_received') || $user_role === 'admin'): ?>
                    <form action="index.php?action=students_grant_access" method="post" class="status-form">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><key/></svg>
                            Grant Full Access
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Remarks Section -->
        <div class="remarks-section">
            <div class="remarks-header">
                <h3>Remarks & Notes</h3>
                <button onclick="toggleRemarkForm()" class="btn btn-primary" id="addRemarkBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Remark
                </button>
            </div>

            <!-- Add Remark Form -->
            <div id="remarkForm" class="remark-form-container" style="display: none;">
                <form action="index.php?action=remarks_add" method="post" class="remark-form">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                    <div class="form-group">
                        <textarea name="remark_text" class="form-control" rows="4" placeholder="Enter your remark or note here..." required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><check/></svg>
                            Add Remark
                        </button>
                        <button type="button" onclick="toggleRemarkForm()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Remarks List -->
            <div class="remarks-list">
                <?php if ($remarks && $remarks->num_rows > 0): ?>
                    <?php while ($remark = $remarks->fetch_assoc()): ?>
                        <div class="remark-item">
                            <div class="remark-header">
                                <div class="remark-author">
                                    <div class="author-avatar">
                                        <?php echo strtoupper(substr($remark['creator_username'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <div class="author-info">
                                        <strong><?php echo htmlspecialchars($remark['creator_display_name'] ?? $remark['creator_username'] ?? 'Unknown'); ?></strong>
                                        <span class="remark-date"><?php echo date('M d, Y \a\t h:i A', strtotime($remark['created_at'])); ?></span>
                                    </div>
                                </div>
                                <?php 
                                // Check if current user is the creator of the remark OR is an admin
                                $can_edit_delete = (($_SESSION['id'] ?? null) == $remark['user_id'] || ($_SESSION['role'] ?? 'guest') === 'admin');
                                if ($can_edit_delete): 
                                ?>
                                    <div class="remark-actions">
                                        <a href="index.php?action=remarks_edit&id=<?php echo htmlspecialchars($remark['id']); ?>&student_id=<?php echo htmlspecialchars($student['id']); ?>" class="btn-icon edit-btn" title="Edit Remark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="index.php?action=remarks_delete&id=<?php echo htmlspecialchars($remark['id']); ?>&student_id=<?php echo htmlspecialchars($student['id']); ?>" class="btn-icon delete-btn" title="Delete Remark" onclick="return confirm('Are you sure you want to delete this remark?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="remark-content">
                                <?php echo nl2br(htmlspecialchars($remark['remark_text'] ?? $remark['remark'] ?? '')); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-remarks">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="empty-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>
                        <h4>No remarks yet</h4>
                        <p>Be the first to add a remark or note about this student.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="alert-icon"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?php echo htmlspecialchars($error_msg ?: "Student not found."); ?>
        </div>
        <div class="text-center">
            <a href="index.php?action=students" class="btn btn-secondary">Back to Students</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleRemarkForm() {
    const form = document.getElementById('remarkForm');
    const btn = document.getElementById('addRemarkBtn');
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Cancel';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Remark';
    }
}
</script>

<style>
/* Variables (assuming these are defined in a global CSS or similar) */
:root {
    --primary-color: #3b82f6; /* Blue-500 */
    --primary-hover: #2563eb; /* Blue-600 */
    --secondary-color: #e2e8f0; /* Slate-200 */
    --success-color: #10b981; /* Green-500 */
    --success-hover: #059669; /* Green-600 */
    --danger-color: #ef4444; /* Red-500 */
    --danger-hover: #dc2626; /* Red-600 */
    --info-color: #3b82f6; /* Blue-500 (re-using primary for info) */
    --info-hover: #2563eb; /* Blue-600 */

    --text-primary: #1f2937; /* Gray-900 */
    --text-secondary: #6b7280; /* Gray-500 */
    --bg-primary: #ffffff; /* White */
    --bg-secondary: #f9fafb; /* Gray-50 */
    --card-bg: #ffffff; /* White */
    --border-color: #e5e7eb; /* Gray-200 */
    --border-hover: #d1d5db; /* Gray-300 */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

    --spacing-xs: 0.25rem; /* 4px */
    --spacing-sm: 0.5rem;  /* 8px */
    --spacing-md: 1rem;    /* 16px */
    --spacing-lg: 1.5rem;  /* 24px */
    --spacing-xl: 2rem;    /* 32px */
    --spacing-2xl: 3rem;   /* 48px */

    --radius-sm: 0.25rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
}

/* Base styles for the wrapper */
.student-view-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: var(--spacing-lg);
    background: var(--bg-secondary);
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    color: var(--text-primary);
}

/* Alert Styles */
.alert {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    border-radius: var(--radius-lg);
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

/* Student Header Card */
.student-header-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
    color: white;
    border-radius: var(--radius-xl);
    padding: var(--spacing-2xl);
    margin-bottom: var(--spacing-xl);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.student-header-card::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.student-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    z-index: 1;
}

.student-avatar {
    position: relative;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.student-details {
    flex: 1;
}

.student-name {
    margin: 0 0 var(--spacing-xs) 0;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
}

.student-email {
    margin: 0 0 var(--spacing-md) 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.student-badges {
    display: flex;
    gap: var(--spacing-sm);
}

.student-actions {
    display: flex;
    gap: var(--spacing-sm);
    z-index: 1;
}

/* Contact Actions Card */
.contact-actions-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
    box-shadow: var(--shadow-md);
}

.contact-actions-card h3 {
    margin: 0 0 var(--spacing-md) 0;
    color: var(--text-primary);
    font-size: 1.25rem;
}

.contact-buttons {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.contact-btn {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-lg);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.email-btn {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.email-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.phone-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.phone-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.whatsapp-btn {
    background: linear-gradient(135deg, #25d366, #128c7e);
    color: white;
}

.whatsapp-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
}

/* Information Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

.info-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    background: var(--bg-secondary);
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-header h3 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 600;
}

.card-icon {
    color: var(--primary-color);
    opacity: 0.7;
}

.card-content {
    padding: var(--spacing-lg);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    font-weight: 600;
    color: var(--text-secondary);
    min-width: 120px;
    font-size: 0.9rem;
}

.info-item .value {
    color: var(--text-primary);
    text-align: right;
    flex: 1;
    font-size: 0.9rem;
}

.payment-amount {
    font-weight: 700;
    color: var(--success-color);
    font-size: 1.1rem;
}

/* Badge Styles */
.badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending_presales { background: #fef3c7; color: #92400e; }
.status-passed_presales { background: #dbeafe; color: #1e40af; }
.status-passed_sales { background: #dcfce7; color: #166534; }
.status-demo_scheduled { background: #e0e7ff; color: #3730a3; }
.status-payment_received { background: #fce7f3; color: #be185d; }
.status-closed_finance { background: #d1fae5; color: #065f46; }

.stage-hot { background: #fee2e2; color: #dc2626; }
.stage-warm { background: #fef3c7; color: #d97706; }
.stage-cold { background: #dbeafe; color: #2563eb; }

/* Status Update Section */
.status-update-section {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin-bottom: var(--spacing-xl);
    box-shadow: var(--shadow-md);
}

.status-update-section h3 {
    margin: 0 0 var(--spacing-lg) 0;
    color: var(--text-primary);
    font-size: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: var(--spacing-sm);
}

.status-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
    align-items: flex-start;
}

.status-form {
    display: inline-block;
}

.demo-schedule-form,
.payment-record-form {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    flex: 1;
    min-width: 300px;
}

.demo-schedule-form h4,
.payment-record-form h4 {
    margin: 0 0 var(--spacing-md) 0;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.form-row {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.form-row .form-control {
    flex: 1;
}

/* Remarks Section */
.remarks-section {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
}

.remarks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
    padding-bottom: var(--spacing-md);
}

.remarks-header h3 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.remark-form-container {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.remark-form {
    margin: 0;
}

.remarks-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.remark-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    transition: all 0.3s ease;
}

.remark-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.remark-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-md);
}

.remark-author {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.author-info {
    display: flex;
    flex-direction: column;
}

.author-info strong {
    color: var(--text-primary);
    font-size: 0.9rem;
}

.remark-date {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-top: 2px;
}

.remark-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.remark-content {
    color: var(--text-primary);
    line-height: 1.6;
    font-size: 0.95rem;
}

.no-remarks {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--text-secondary);
}

.empty-icon {
    margin-bottom: var(--spacing-md);
    opacity: 0.5;
}

.no-remarks h4 {
    margin: 0 0 var(--spacing-sm) 0;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.no-remarks p {
    margin: 0;
    font-size: 0.9rem;
}

/* Button Styles */
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

.btn-success {
    background: var(--success-color);
    color: white;
}

.btn-success:hover {
    background: var(--success-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.edit-btn {
    background: var(--info-color);
    color: white;
}

.edit-btn:hover {
    background: var(--info-hover);
    transform: translateY(-2px);
}

.delete-btn {
    background: var(--danger-color);
    color: white;
}

.delete-btn:hover {
    background: var(--danger-hover);
    transform: translateY(-2px);
}

/* Form Styles */
.form-group {
    margin-bottom: var(--spacing-md);
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
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: var(--spacing-sm);
    justify-content: flex-start;
}

.icon {
    width: 18px;
    height: 18px;
}

.text-center {
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .student-view-wrapper {
        padding: var(--spacing-md);
    }
    
    .student-header-card {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-lg);
        text-align: left;
    }
    
    .student-name {
        font-size: 2rem;
    }
    
    .student-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .contact-buttons {
        flex-direction: column;
    }
    
    .contact-btn {
        justify-content: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .status-actions {
        flex-direction: column;
    }
    
    .form-row {
        flex-direction: column;
    }
    
    .remarks-header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-sm);
    }
    
    .remark-header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-xs);
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .student-badges {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .avatar-circle {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .student-name {
        font-size: 1.75rem;
    }
    
    .student-email {
        font-size: 1rem;
    }
}
</style>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
