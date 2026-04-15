<?php
$page_title = "Preview Registration";
require_once 'includes/header.php';

// Check if data is coming from POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// Store POST data in session for later processing
$_SESSION['formData'] = $_POST;

// Extract data for display
$data = $_SESSION['formData'];
?>

<section class="section-card">
    <div class="preview-header">
        <h2 class="section-title">Registration Preview</h2>
        <div class="preview-actions">
            <a href="register.php" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Edit Details
            </a>
            <a href="process_register.php" class="btn btn-success">
                <i class="fas fa-check"></i> Confirm & Submit
            </a>
        </div>
    </div>
    
    <p class="alert alert-info mt-3">Please review your information carefully before final submission.</p>
    
    <div class="preview-grid mt-4">
        <div class="preview-section">
            <h3>Personal Information</h3>
            <table class="table">
                <tr><th>Roll Number</th><td><?php echo sanitize_input($data['rollNumber']); ?></td></tr>
                <tr><th>Full Name</th><td><?php echo sanitize_input($data['fullName']); ?></td></tr>
                <tr><th>Father's Name</th><td><?php echo sanitize_input($data['fathersName']); ?></td></tr>
                <tr><th>Programme</th><td><?php echo sanitize_input($data['nameOfProgramme']); ?></td></tr>
                <tr><th>Branch</th><td><?php echo sanitize_input($data['branchSpecialization']); ?></td></tr>
                <tr><th>Year/Semester</th><td>Year <?php echo sanitize_input($data['year']); ?> / Sem <?php echo sanitize_input($data['semester']); ?></td></tr>
                <tr><th>Category</th><td><?php echo sanitize_input($data['category']); ?></td></tr>
                <tr><th>Gender</th><td><?php echo sanitize_input($data['gender']); ?></td></tr>
                <tr><th>Aadhar</th><td><?php echo sanitize_input($data['aadharCard']); ?></td></tr>
            </table>
        </div>
        
        <div class="preview-section">
            <h3>Contact & Address</h3>
            <table class="table">
                <tr><th>Student Contact</th><td><?php echo sanitize_input($data['studentContact']); ?></td></tr>
                <tr><th>Student Email</th><td><?php echo sanitize_input($data['studentEmail']); ?></td></tr>
                <tr><th>Permanent Address</th><td><?php echo sanitize_input($data['permanentAddress']); ?></td></tr>
                <tr><th>Hostel</th><td><?php echo sanitize_input($data['hostelAddress']); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="preview-section mt-4">
        <h3>Fee Payment Information</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th>Amount Paid</th>
                        <th>Remaining</th>
                        <th>Txn Details</th>
                        <th>Platform</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Odd Semester</td>
                        <td><?php echo sanitize_input($data['oddSemesterAmount'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['oddSemesterRemaining'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['oddSemesterTxnDetails'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['oddSemesterPlatform'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['oddSemesterDate'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Even Semester</td>
                        <td><?php echo sanitize_input($data['evenSemesterAmount'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['evenSemesterRemaining'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['evenSemesterTxnDetails'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['evenSemesterPlatform'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['evenSemesterDate'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Hostel Fee</td>
                        <td><?php echo sanitize_input($data['hostelAmount'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['hostelRemaining'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['hostelTxnDetails'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['hostelPlatform'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['hostelDate'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Mess Fee</td>
                        <td><?php echo sanitize_input($data['messAmount'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['messRemaining'] ?: '0.00'); ?></td>
                        <td><?php echo sanitize_input($data['messTxnDetails'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['messPlatform'] ?: '-'); ?></td>
                        <td><?php echo sanitize_input($data['messDate'] ?: '-'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="form-actions mt-5 text-center">
        <p class="mb-3">By clicking 'Confirm & Submit', you agree that all information provided is correct.</p>
        <div class="btn-group">
            <a href="register.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
            <a href="process_register.php" class="btn btn-success btn-lg">
                <i class="fas fa-paper-plane"></i> Confirm & Submit Registration
            </a>
        </div>
    </div>
</section>

<style>
.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 2rem;
}

.preview-section h3 {
    font-size: 1.1rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--gray-300);
    padding-bottom: 0.5rem;
}

.table th {
    width: 35%;
    font-size: 0.9rem;
    color: var(--gray-700);
}

.btn-group {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

@media (max-width: 600px) {
    .preview-grid {
        grid-template-columns: 1fr;
    }
    .btn-group {
        flex-direction: column;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
