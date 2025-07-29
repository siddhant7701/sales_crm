<?php 
$pageTitle = "Student Enquiry Management"; 
require_once BASE_PATH . '/views/layout/header.php'; 

// Get current filter parameters for CSV export
$filterParams = [];
$filterParams['branch'] = $_GET['branch'] ?? '';
$filterParams['mode'] = $_GET['mode'] ?? '';
$filterParams['stage'] = $_GET['stage'] ?? '';
$filterParams['status'] = $_GET['status'] ?? '';
$filterParams['date_filter'] = $_GET['date_filter'] ?? '';
$filterParams['date_from'] = $_GET['date_from'] ?? '';
$filterParams['date_to'] = $_GET['date_to'] ?? '';
$filterParams['search'] = $_GET['search'] ?? '';

// Build query string for CSV export
$csvParams = array_filter($filterParams);
$csvParams['action'] = 'export_students_csv';
$csvUrl = 'index.php?' . http_build_query($csvParams);
?>

<div class="student-management-wrapper">
    <div class="page-header">
        <h2>Student Enquiry Management</h2>
        <div class="header-actions">
            <a href="<?php echo $csvUrl; ?>" class="btn btn-success export-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export CSV
            </a>
            <a href="index.php?action=students_create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                Add New Enquiry
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])) { 
        echo '<div class="alert success">' . $_SESSION['success_message'] . '</div>'; 
        unset($_SESSION['success_message']); 
    } 
    if (isset($_SESSION['error_message'])) { 
        echo '<div class="alert error">' . $_SESSION['error_message'] . '</div>'; 
        unset($_SESSION['error_message']); 
    } ?>

    <!-- Enhanced Quick Filters Section -->
    <div class="filters-container">
        <!-- Date Filters Row -->
        <div class="filter-row date-filter-row">
            <div class="filter-group-header">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Filter by Date:
                </h4>
            </div>
            <div class="filter-buttons">
                <a href="index.php?action=students&date_filter=today<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'today') ? 'active' : ''; ?>">
                    <span class="badge date-today">Today</span>
                </a>
                <a href="index.php?action=students&date_filter=yesterday<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'yesterday') ? 'active' : ''; ?>">
                    <span class="badge date-yesterday">Yesterday</span>
                </a>
                <a href="index.php?action=students&date_filter=this_week<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'this_week') ? 'active' : ''; ?>">
                    <span class="badge date-week">This Week</span>
                </a>
                <a href="index.php?action=students&date_filter=this_month<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'this_month') ? 'active' : ''; ?>">
                    <span class="badge date-month">This Month</span>
                </a>
                <a href="index.php?action=students<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn clear-btn">
                    <span class="badge clear-filter">All Time</span>
                </a>
            </div>
        </div>

        <!-- Location Filters Row -->
        <div class="filter-row location-filter-row">
            <div class="filter-group-header">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Filter by Location:
                </h4>
            </div>
            <div class="filter-buttons">
                <a href="index.php?action=students&branch=nagpur<?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'nagpur') ? 'active' : ''; ?>">
                    <span class="badge branch-nagpur">Nagpur</span>
                </a>
                <a href="index.php?action=students&branch=pune<?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'pune') ? 'active' : ''; ?>">
                    <span class="badge branch-pune">Pune</span>
                </a>
                <a href="index.php?action=students&branch=indore<?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'indore') ? 'active' : ''; ?>">
                    <span class="badge branch-indore">Indore</span>
                </a>
                <a href="index.php?action=students&branch=mumbai<?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'mumbai') ? 'active' : ''; ?>">
                    <span class="badge branch-mumbai">Mumbai</span>
                </a>
                <a href="index.php?action=students&branch=delhi<?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?><?php echo isset($_GET['mode']) ? '&mode=' . $_GET['mode'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'delhi') ? 'active' : ''; ?>">
                    <span class="badge branch-delhi">Delhi</span>
                </a>
            </div>
        </div>

        <!-- Mode Filters Row -->
        <div class="filter-row mode-filter-row">
            <div class="filter-group-header">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M10 4v16"/>
                    </svg>
                    Filter by Mode:
                </h4>
            </div>
            <div class="filter-buttons">
                <a href="index.php?action=students&mode=online<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'online') ? 'active' : ''; ?>">
                    <span class="badge mode-online">Online</span>
                </a>
                <a href="index.php?action=students&mode=offline<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'offline') ? 'active' : ''; ?>">
                    <span class="badge mode-offline">Offline</span>
                </a>
                <a href="index.php?action=students&mode=hybrid<?php echo isset($_GET['branch']) ? '&branch=' . $_GET['branch'] : ''; ?><?php echo isset($_GET['date_filter']) ? '&date_filter=' . $_GET['date_filter'] : ''; ?>" 
                   class="filter-btn <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'hybrid') ? 'active' : ''; ?>">
                    <span class="badge mode-hybrid">Hybrid</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filters-section">
        <div class="filters-header">
            <h3>Advanced Filters</h3>
            <button type="button" class="toggle-filters-btn" onclick="toggleAdvancedFilters()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
                </svg>
                <span>Toggle Filters</span>
            </button>
        </div>
        <form method="GET" action="index.php" class="filters-form" id="advancedFilters">
            <input type="hidden" name="action" value="students">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="stage">Stage:</label>
                    <select name="stage" id="stage" class="form-control">
                        <option value="">All Stages</option>
                        <option value="hot" <?php echo (isset($_GET['stage']) && $_GET['stage'] == 'hot') ? 'selected' : ''; ?>>Hot</option>
                        <option value="warm" <?php echo (isset($_GET['stage']) && $_GET['stage'] == 'warm') ? 'selected' : ''; ?>>Warm</option>
                        <option value="cold" <?php echo (isset($_GET['stage']) && $_GET['stage'] == 'cold') ? 'selected' : ''; ?>>Cold</option>
                        <option value="irrelevant" <?php echo (isset($_GET['stage']) && $_GET['stage'] == 'irrelevant') ? 'selected' : ''; ?>>Irrelevant</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending_presales" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending_presales') ? 'selected' : ''; ?>>Pending Presales</option>
                        <option value="passed_presales" <?php echo (isset($_GET['status']) && $_GET['status'] == 'passed_presales') ? 'selected' : ''; ?>>Passed Presales</option>
                        <option value="passed_sales" <?php echo (isset($_GET['status']) && $_GET['status'] == 'passed_sales') ? 'selected' : ''; ?>>Passed Sales</option>
                        <option value="demo_scheduled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'demo_scheduled') ? 'selected' : ''; ?>>Demo Scheduled</option>
                        <option value="payment_received" <?php echo (isset($_GET['status']) && $_GET['status'] == 'payment_received') ? 'selected' : ''; ?>>Payment Received</option>
                        <option value="closed_finance" <?php echo (isset($_GET['status']) && $_GET['status'] == 'closed_finance') ? 'selected' : ''; ?>>Closed Finance</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="branch">Branch:</label>
                    <select name="branch" id="branch" class="form-control">
                        <option value="">All Branches</option>
                        <option value="nagpur" <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'nagpur') ? 'selected' : ''; ?>>Nagpur</option>
                        <option value="pune" <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'pune') ? 'selected' : ''; ?>>Pune</option>
                        <option value="indore" <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'indore') ? 'selected' : ''; ?>>Indore</option>
                        <option value="mumbai" <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'mumbai') ? 'selected' : ''; ?>>Mumbai</option>
                        <option value="delhi" <?php echo (isset($_GET['branch']) && $_GET['branch'] == 'delhi') ? 'selected' : ''; ?>>Delhi</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="mode">Mode:</label>
                    <select name="mode" id="mode" class="form-control">
                        <option value="">All Modes</option>
                        <option value="online" <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'online') ? 'selected' : ''; ?>>Online</option>
                        <option value="offline" <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'offline') ? 'selected' : ''; ?>>Offline</option>
                        <option value="hybrid" <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from">From Date:</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                </div>
                <div class="filter-group">
                    <label for="date_to">To Date:</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                </div>
                <div class="filter-group search-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Name, email, mobile..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="index.php?action=students" class="btn btn-secondary">Clear All</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Summary -->
    <div class="results-summary">
        <div class="summary-info">
            <p>Showing <strong><?php echo $students ? $students->num_rows : 0; ?></strong> enquiries</p>
            <?php if (!empty(array_filter($filterParams))): ?>
                <span class="active-filters">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
                    </svg>
                    Filters Active
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Students Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Mode</th>
                        <th>Source</th>
                        <th>Branch</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td class="student-name">
                                    <a href="index.php?action=students_view&id=<?php echo $student['id']; ?>" class="student-link">
                                        <?php echo htmlspecialchars($student['name']); ?>
                                    </a>
                                    <?php if (isset($student['creator_name']) && $student['creator_name']): ?>
                                        <small class="created-by">Created by: <?php echo htmlspecialchars($student['creator_name'] ?? 'Unknown'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="mobile-cell">
                                    <div class="mobile-actions">
                                        <span class="mobile-number"><?php echo htmlspecialchars($student['mobile']); ?></span>
                                        <div class="contact-actions">
                                            <a href="tel:<?php echo htmlspecialchars($student['mobile']); ?>" class="contact-btn call-btn" title="Call">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                            </a>
                                            <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D/', '', $student['mobile'])); ?>" target="_blank" class="contact-btn whatsapp-btn" title="WhatsApp">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="email-cell">
                                    <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="email-link">
                                        <?php echo htmlspecialchars($student['email']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge mode-<?php echo strtolower($student['mode'] ?? 'online'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['mode'] ?? 'Online')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge source-<?php echo strtolower($student['source'] ?? 'website'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['source'] ?? 'Website')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge branch-<?php echo strtolower($student['branch'] ?? 'nagpur'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['branch'] ?? 'Nagpur')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge stage-<?php echo strtolower($student['stage'] ?? 'warm'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['stage'] ?? 'Warm')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge status-<?php echo str_replace('_', '-', strtolower($student['status'] ?? 'pending-presales')); ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $student['status'] ?? 'Pending Presales'))); ?>
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <div class="datetime-info">
                                        <div class="date"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></div>
                                        <div class="time"><?php echo date('h:i A', strtotime($student['created_at'])); ?></div>
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="index.php?action=students_view&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        <?php if (($_SESSION['role'] === 'admin') || (isset($student['assigned_to']) && $student['assigned_to'] == $_SESSION['id'])): ?>
                                            <a href="index.php?action=students_delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="no-data">
                                <div class="no-data-message">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <h3>No students found</h3>
                                    <p>Try adjusting your filters or <a href="index.php?action=students_create">add a new enquiry</a></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Enhanced Student Management Styles */
.student-management-wrapper {
    padding: 20px;
    max-width: 100%;
    overflow-x: hidden;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h2 {
    margin: 0;
    color: var(--text-color);
    font-size: 1.8rem;
    font-weight: 600;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.export-btn {
    background: linear-gradient(135deg, #059669, #047857);
    border: none;
    color: white;
    transition: all 0.2s ease;
}

.export-btn:hover {
    background: linear-gradient(135deg, #047857, #065f46);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
}

/* Enhanced Filters Container */
.filters-container {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--border-color);
}

.filter-row {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.filter-row:last-child {
    margin-bottom: 0;
}

.date-filter-row {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(124, 58, 237, 0.04));
    border-left: 4px solid #8b5cf6;
}

.location-filter-row {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(37, 99, 235, 0.04));
    border-left: 4px solid #3b82f6;
}

.mode-filter-row {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(5, 150, 105, 0.04));
    border-left: 4px solid #10b981;
}

.filter-group-header {
    min-width: 180px;
    flex-shrink: 0;
}

.filter-group-header h4 {
    margin: 0;
    color: var(--text-color);
    font-size: 0.95rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-btn {
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
}

.filter-btn:hover {
    transform: translateY(-2px);
}

.filter-btn.active .badge {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    transform: scale(1.05);
}

.clear-filter {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

/* Advanced Filters */
.filters-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.02));
    border-bottom: 1px solid var(--border-color);
}

.filters-header h3 {
    margin: 0;
    color: var(--text-color);
    font-size: 1.1rem;
    font-weight: 600;
}

.toggle-filters-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.toggle-filters-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.filters-form {
    padding: 25px;
    display: none;
}

.filters-form.show {
    display: block;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 500;
    margin-bottom: 8px;
    color: var(--text-color);
    font-size: 0.9rem;
}

.search-group {
    grid-column: span 2;
}

.filter-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    grid-column: span 2;
    justify-content: center;
}

/* Results Summary */
.results-summary {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: var(--card-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.summary-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.summary-info p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.active-filters {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--primary-color);
    font-size: 0.85rem;
    font-weight: 500;
    background: rgba(59, 130, 246, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}

/* Table Container */
.table-container {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.table-wrapper {
    overflow-x: auto;
    max-width: 100%;
}

.students-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

.students-table th {
    background: var(--header-bg);
    color: var(--text-color);
    font-weight: 600;
    padding: 15px 12px;
    text-align: left;
    border-bottom: 2px solid var(--border-color);
    font-size: 0.9rem;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.students-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    font-size: 0.9rem;
}

.students-table tbody tr {
    transition: background-color 0.2s ease;
}

.students-table tbody tr:hover {
    background: var(--hover-bg);
}

/* Cell Specific Styles */
.student-name {
    min-width: 180px;
}

.student-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.student-link:hover {
    text-decoration: underline;
}

.created-by {
    color: var(--text-secondary);
    font-size: 0.8rem;
    display: block;
    font-style: italic;
}

.mobile-cell {
    min-width: 140px;
}

.mobile-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.mobile-number {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.contact-actions {
    display: flex;
    gap: 6px;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.call-btn {
    background: #10b981;
    color: white;
}

.call-btn:hover {
    background: #059669;
    transform: scale(1.1);
}

.whatsapp-btn {
    background: #25d366;
    color: white;
}

.whatsapp-btn:hover {
    background: #128c7e;
    transform: scale(1.1);
}

.email-cell {
    min-width: 200px;
}

.email-link {
    color: var(--text-color);
    text-decoration: none;
    word-break: break-all;
}

.email-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

.date-cell {
    min-width: 120px;
}

.datetime-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.datetime-info .date {
    font-size: 0.85rem;
    color: var(--text-color);
    font-weight: 500;
}

.datetime-info .time {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-family: 'Courier New', monospace;
}

.actions-cell {
    min-width: 100px;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

/* Enhanced Badge Styles */
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    transition: all 0.2s ease;
    cursor: pointer;
}

.badge:hover {
    transform: scale(1.05);
}

/* Date Badges */
.date-today {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.date-yesterday {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.date-week {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.date-month {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

/* Stage Badges */
.stage-hot, .badge.hot {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.stage-warm, .badge.warm {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.stage-cold, .badge.cold {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.stage-irrelevant {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

/* Status Badges */
.status-pending-presales {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #92400e;
}

.status-passed-presales {
    background: linear-gradient(135deg, #34d399, #10b981);
    color: #065f46;
}

.status-passed-sales {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color: #1e40af;
}

.status-demo-scheduled {
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    color: #5b21b6;
}

.status-payment-received {
    background: linear-gradient(135deg, #fb7185, #f43f5e);
    color: #be185d;
}

.status-closed-finance {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: #15803d;
}

/* Mode Badges */
.mode-online {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
}

.mode-offline {
    background: linear-gradient(135deg, #84cc16, #65a30d);
    color: white;
}

.mode-hybrid {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
}

/* Source Badges */
.source-website {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.source-referral {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.source-whatsapp {
    background: linear-gradient(135deg, #25d366, #128c7e);
    color: white;
}

.source-ivr {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.source-walkin {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.source-others {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

/* Branch Badges */
.branch-nagpur {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.branch-pune {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.branch-indore {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.branch-mumbai {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.branch-delhi {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

/* No Data Message */
.no-data {
    text-align: center;
    padding: 60px 20px;
}

.no-data-message {
    color: var(--text-secondary);
}

.no-data-message svg {
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-data-message h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
}

.no-data-message p {
    margin: 0;
}

.no-data-message a {
    color: var(--primary-color);
    text-decoration: none;
}

.no-data-message a:hover {
    text-decoration: underline;
}

/* Custom Date Input Styling */
input[type="date"] {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--card-bg);
    color: var(--text-color);
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

input[type="date"]:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .student-management-wrapper {
        padding: 15px;
    }

    .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }

    .page-header h2 {
        font-size: 1.5rem;
    }

    .header-actions {
        justify-content: center;
    }

    .filter-row {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
        padding: 15px;
    }

    .filter-group-header {
        min-width: auto;
        text-align: center;
    }

    .filter-buttons {
        justify-content: center;
    }

    .filters-grid {
        grid-template-columns: 1fr;
    }

    .search-group {
        grid-column: span 1;
    }

    .filter-actions {
        grid-column: span 1;
        justify-content: stretch;
    }

    .filter-actions .btn {
        flex: 1;
    }

    .results-summary {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }

    .students-table th,
    .students-table td {
        padding: 8px 6px;
        font-size: 0.8rem;
    }

    .mobile-actions {
        align-items: flex-start;
    }

    .contact-actions {
        margin-top: 4px;
    }

    .datetime-info .date {
        font-size: 0.8rem;
    }

    .datetime-info .time {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .students-table {
        min-width: 800px;
    }

    .badge {
        font-size: 0.7rem;
        padding: 3px 6px;
    }

    .filters-container {
        padding: 15px;
    }

    .filter-row {
        padding: 12px;
    }

    .filter-group-header h4 {
        font-size: 0.85rem;
    }
}

/* Animation for filter buttons */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.filter-btn.active .badge {
    animation: pulse 2s infinite;
}

/* Smooth transitions */
* {
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

/* Enhanced hover effects */
.students-table tbody tr:hover .badge {
    transform: scale(1.05);
}

.students-table tbody tr:hover .contact-btn {
    transform: scale(1.1);
}
</style>

<script>
function toggleAdvancedFilters() {
    const filtersForm = document.getElementById('advancedFilters');
    const toggleBtn = document.querySelector('.toggle-filters-btn');
    
    if (filtersForm.classList.contains('show')) {
        filtersForm.classList.remove('show');
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
            </svg>
            <span>Show Filters</span>
        `;
    } else {
        filtersForm.classList.add('show');
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
            </svg>
            <span>Hide Filters</span>
        `;
    }
}

// Show advanced filters if any advanced filter is applied
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasAdvancedFilters = urlParams.has('stage') || urlParams.has('status') || 
                              urlParams.has('date_from') || urlParams.has('date_to') || 
                              urlParams.has('search');
    
    if (hasAdvancedFilters) {
        document.getElementById('advancedFilters').classList.add('show');
        document.querySelector('.toggle-filters-btn').innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
            </svg>
            <span>Hide Filters</span>
        `;
    }
});
</script>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>