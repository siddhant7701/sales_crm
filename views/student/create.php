<?php
$pageTitle = "Add Student Enquiry";
require_once BASE_PATH . '/views/layout/header.php';
?>

<div class="user-management-wrapper">
    <h2>Add New Student Enquiry</h2>

    <?php
    if (!empty($success_msg)) {
        echo '<div class="alert success">' . $success_msg . '</div>';
    }
    if (!empty($error_msg)) {
        echo '<div class="alert error">' . $error_msg . '</div>';
    }
    ?>

    <!-- Manual Entry Form -->
    <div class="form-wrapper" style="margin-top: 20px;">
        <h3>Enquiry Form</h3>
        <form action="index.php?action=students_create" method="post">
            <input type="hidden" name="manual_submit" value="1">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile Number *</label>
                    <input type="text" id="mobile" name="mobile" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="course">Course Interested</label>
                    <input type="text" id="course" name="course" class="form-control" placeholder="e.g., Java, Python, Web Development">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" placeholder="City, State">
                </div>
                <div class="form-group">
                    <label for="mode">Mode</label>
                    <select id="mode" name="mode" class="form-control">
                        <option value="">Select Mode</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="source">Source</label>
                    <select id="source" name="source" class="form-control">
                        <option value="">Select Source</option>
                        <option value="website">Website</option>
                        <option value="ivr">IVR</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="referral">Referral</option>
                        <option value="walkin">Walk-in</option>
                        <option value="others">Others</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <select id="branch" name="branch" class="form-control">
                        <option value="">Select Branch</option>
                        <option value="nagpur">Nagpur</option>
                        <option value="pune">Pune</option>
                        <option value="indore">Indore</option>
                        <option value="mumbai">Mumbai</option>
                        <option value="delhi">Delhi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="stage">Stage</label>
                <select id="stage" name="stage" class="form-control">
                    <option value="hot">Hot</option>
                    <option value="warm" selected>Warm</option>
                    <option value="cold">Cold</option>
                    <option value="irrelevant">Irrelevant</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add Enquiry">
            </div>
        </form>
    </div>

    <!-- File Upload Form -->
    <div class="form-wrapper" style="margin-top: 40px;">
        <h3>Upload from CSV File</h3>
        <p>Upload a CSV file with columns in this exact order: <strong>Name, Mobile, Email, Course, Location, Mode, Source, Branch, Stage</strong>. The first row will be skipped as a header.</p>
        <div class="csv-sample" style="background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px;">
            <strong>Sample CSV format:</strong><br>
            <code>Name,Mobile,Email,Course,Location,Mode,Source,Branch,Stage<br>
            John Doe,9876543210,john@email.com,Java Full Stack,Mumbai Maharashtra,online,website,mumbai,hot</code>
        </div>
        <form action="index.php?action=students_create" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="student_file">Select CSV File</label>
                <input type="file" id="student_file" name="student_file" class="form-control" accept=".csv" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-secondary" value="Upload and Process CSV">
            </div>
        </form>
    </div>
</div>

<style>
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.form-row .form-group {
    flex: 1;
    margin-bottom: 0;
}

.csv-sample {
    font-family: monospace;
    font-size: 14px;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .form-row .form-group {
        margin-bottom: 15px;
    }
}
</style>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
