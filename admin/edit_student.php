<?php
$page_title = "Edit Registration";
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    set_flash_message("Invalid ID provided.", "danger");
    redirect('dashboard.php');
}

// Fetch student data
try {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        set_flash_message("Student record not found.", "danger");
        redirect('dashboard.php');
    }
} catch (PDOException $e) {
    set_flash_message("Error fetching record: " . $e->getMessage(), "danger");
    redirect('dashboard.php');
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE registrations SET 
            fullName = :fullName, 
            fathersName = :fathersName, 
            nameOfProgramme = :nameOfProgramme, 
            branchSpecialization = :branchSpecialization, 
            year = :year, 
            semester = :semester, 
            category = :category, 
            gender = :gender, 
            aadharCard = :aadharCard, 
            permanentAddress = :permanentAddress, 
            hostelAddress = :hostelAddress, 
            studentContact = :studentContact, 
            studentEmail = :studentEmail,
            oddSemesterAmount = :oddSemesterAmount,
            oddSemesterRemaining = :oddSemesterRemaining,
            oddSemesterTxnDetails = :oddSemesterTxnDetails,
            oddSemesterPlatform = :oddSemesterPlatform,
            oddSemesterDate = :oddSemesterDate,
            evenSemesterAmount = :evenSemesterAmount,
            evenSemesterRemaining = :evenSemesterRemaining,
            evenSemesterTxnDetails = :evenSemesterTxnDetails,
            evenSemesterPlatform = :evenSemesterPlatform,
            evenSemesterDate = :evenSemesterDate,
            hostelAmount = :hostelAmount,
            hostelRemaining = :hostelRemaining,
            hostelTxnDetails = :hostelTxnDetails,
            hostelPlatform = :hostelPlatform,
            hostelDate = :hostelDate,
            messAmount = :messAmount,
            messRemaining = :messRemaining,
            messTxnDetails = :messTxnDetails,
            messPlatform = :messPlatform,
            messDate = :messDate
        WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':fullName' => sanitize_input($_POST['fullName']),
            ':fathersName' => sanitize_input($_POST['fathersName']),
            ':nameOfProgramme' => sanitize_input($_POST['nameOfProgramme']),
            ':branchSpecialization' => sanitize_input($_POST['branchSpecialization']),
            ':year' => (int)$_POST['year'],
            ':semester' => (int)$_POST['semester'],
            ':category' => sanitize_input($_POST['category']),
            ':gender' => sanitize_input($_POST['gender']),
            ':aadharCard' => sanitize_input($_POST['aadharCard']),
            ':permanentAddress' => sanitize_input($_POST['permanentAddress']),
            ':hostelAddress' => sanitize_input($_POST['hostelAddress']),
            ':studentContact' => sanitize_input($_POST['studentContact']),
            ':studentEmail' => sanitize_input($_POST['studentEmail']),
            ':oddSemesterAmount' => $_POST['oddSemesterAmount'] ?: 0,
            ':oddSemesterRemaining' => $_POST['oddSemesterRemaining'] ?: 0,
            ':oddSemesterTxnDetails' => $_POST['oddSemesterTxnDetails'] ?? '',
            ':oddSemesterPlatform' => $_POST['oddSemesterPlatform'] ?? '',
            ':oddSemesterDate' => $_POST['oddSemesterDate'] ?: null,
            ':evenSemesterAmount' => $_POST['evenSemesterAmount'] ?: 0,
            ':evenSemesterRemaining' => $_POST['evenSemesterRemaining'] ?: 0,
            ':evenSemesterTxnDetails' => $_POST['evenSemesterTxnDetails'] ?? '',
            ':evenSemesterPlatform' => $_POST['evenSemesterPlatform'] ?? '',
            ':evenSemesterDate' => $_POST['evenSemesterDate'] ?: null,
            ':hostelAmount' => $_POST['hostelAmount'] ?: 0,
            ':hostelRemaining' => $_POST['hostelRemaining'] ?: 0,
            ':hostelTxnDetails' => $_POST['hostelTxnDetails'] ?? '',
            ':hostelPlatform' => $_POST['hostelPlatform'] ?? '',
            ':hostelDate' => $_POST['hostelDate'] ?: null,
            ':messAmount' => $_POST['messAmount'] ?: 0,
            ':messRemaining' => $_POST['messRemaining'] ?: 0,
            ':messTxnDetails' => $_POST['messTxnDetails'] ?? '',
            ':messPlatform' => $_POST['messPlatform'] ?? '',
            ':messDate' => $_POST['messDate'] ?: null
        ]);
        
        set_flash_message("Registration record updated successfully.", "success");
        redirect('dashboard.php');
    } catch (PDOException $e) {
        set_flash_message("Error updating record: " . $e->getMessage(), "danger");
    }
}
?>

<section class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Edit Registration: <?php echo htmlspecialchars($student['rollNumber']); ?></h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    
    <form action="edit_student.php?id=<?php echo $id; ?>" method="POST" class="registration-form">
        <!-- Reusing same form fields as register.php but with values -->
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Personal Details</h3>
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="fullName">Student's Full Name *</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" value="<?php echo htmlspecialchars($student['fullName']); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="fathersName">Father's / Husband's Name *</label>
                        <input type="text" id="fathersName" name="fathersName" class="form-control" value="<?php echo htmlspecialchars($student['fathersName']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="nameOfProgramme">Name of Programme *</label>
                        <input type="text" id="nameOfProgramme" name="nameOfProgramme" class="form-control" value="<?php echo htmlspecialchars($student['nameOfProgramme']); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="branchSpecialization">Branch / Specialization *</label>
                        <input type="text" id="branchSpecialization" name="branchSpecialization" class="form-control" value="<?php echo htmlspecialchars($student['branchSpecialization']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="year">Year *</label>
                        <select id="year" name="year" class="form-control" required>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $student['year'] == $i ? 'selected' : ''; ?>><?php echo $i; ?>st Year</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="semester">Semester *</label>
                        <select id="semester" name="semester" class="form-control" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $student['semester'] == $i ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="category">Category *</label>
                        <input type="text" id="category" name="category" class="form-control" value="<?php echo htmlspecialchars($student['category']); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="gender">Gender *</label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="Male" <?php echo $student['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $student['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $student['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section mt-4">
            <h3><i class="fas fa-address-book"></i> Contact Details</h3>
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="studentContact">Student Contact *</label>
                        <input type="text" id="studentContact" name="studentContact" class="form-control" value="<?php echo htmlspecialchars($student['studentContact']); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="studentEmail">Student Email *</label>
                        <input type="email" id="studentEmail" name="studentEmail" class="form-control" value="<?php echo htmlspecialchars($student['studentEmail']); ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="permanentAddress">Permanent Address *</label>
                <textarea id="permanentAddress" name="permanentAddress" class="form-control" rows="3" required><?php echo htmlspecialchars($student['permanentAddress']); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="hostelAddress">Hostel Selection *</label>
                <input type="text" id="hostelAddress" name="hostelAddress" class="form-control" value="<?php echo htmlspecialchars($student['hostelAddress']); ?>" required>
            </div>
        </div>

        <!-- Fee Details Section -->
        <div class="form-section mt-4">
            <h3><i class="fas fa-file-invoice-dollar"></i> Fee Details</h3>
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
                        <!-- Odd Semester -->
                        <tr>
                            <td>Odd Semester</td>
                            <td><input type="number" step="0.01" name="oddSemesterAmount" class="form-control" value="<?php echo htmlspecialchars($student['oddSemesterAmount']); ?>"></td>
                            <td><input type="number" step="0.01" name="oddSemesterRemaining" class="form-control" value="<?php echo htmlspecialchars($student['oddSemesterRemaining']); ?>"></td>
                            <td><input type="text" name="oddSemesterTxnDetails" class="form-control" value="<?php echo htmlspecialchars($student['oddSemesterTxnDetails']); ?>"></td>
                            <td>
                                <select name="oddSemesterPlatform" class="form-control">
                                    <option value="" <?php echo $student['oddSemesterPlatform'] == '' ? 'selected' : ''; ?>>Select</option>
                                    <option value="Online" <?php echo $student['oddSemesterPlatform'] == 'Online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="Challan" <?php echo $student['oddSemesterPlatform'] == 'Challan' ? 'selected' : ''; ?>>Challan</option>
                                    <option value="Loan" <?php echo $student['oddSemesterPlatform'] == 'Loan' ? 'selected' : ''; ?>>Loan</option>
                                </select>
                            </td>
                            <td><input type="date" name="oddSemesterDate" class="form-control" value="<?php echo htmlspecialchars($student['oddSemesterDate']); ?>"></td>
                        </tr>
                        <!-- Even Semester -->
                        <tr>
                            <td>Even Semester</td>
                            <td><input type="number" step="0.01" name="evenSemesterAmount" class="form-control" value="<?php echo htmlspecialchars($student['evenSemesterAmount']); ?>"></td>
                            <td><input type="number" step="0.01" name="evenSemesterRemaining" class="form-control" value="<?php echo htmlspecialchars($student['evenSemesterRemaining']); ?>"></td>
                            <td><input type="text" name="evenSemesterTxnDetails" class="form-control" value="<?php echo htmlspecialchars($student['evenSemesterTxnDetails']); ?>"></td>
                            <td>
                                <select name="evenSemesterPlatform" class="form-control">
                                    <option value="" <?php echo $student['evenSemesterPlatform'] == '' ? 'selected' : ''; ?>>Select</option>
                                    <option value="Online" <?php echo $student['evenSemesterPlatform'] == 'Online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="Challan" <?php echo $student['evenSemesterPlatform'] == 'Challan' ? 'selected' : ''; ?>>Challan</option>
                                    <option value="Loan" <?php echo $student['evenSemesterPlatform'] == 'Loan' ? 'selected' : ''; ?>>Loan</option>
                                </select>
                            </td>
                            <td><input type="date" name="evenSemesterDate" class="form-control" value="<?php echo htmlspecialchars($student['evenSemesterDate']); ?>"></td>
                        </tr>
                        <!-- Hostel Fee -->
                        <tr>
                            <td>Hostel Fee</td>
                            <td><input type="number" step="0.01" name="hostelAmount" class="form-control" value="<?php echo htmlspecialchars($student['hostelAmount']); ?>"></td>
                            <td><input type="number" step="0.01" name="hostelRemaining" class="form-control" value="<?php echo htmlspecialchars($student['hostelRemaining']); ?>"></td>
                            <td><input type="text" name="hostelTxnDetails" class="form-control" value="<?php echo htmlspecialchars($student['hostelTxnDetails']); ?>"></td>
                            <td>
                                <select name="hostelPlatform" class="form-control">
                                    <option value="" <?php echo $student['hostelPlatform'] == '' ? 'selected' : ''; ?>>Select</option>
                                    <option value="Online" <?php echo $student['hostelPlatform'] == 'Online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="Challan" <?php echo $student['hostelPlatform'] == 'Challan' ? 'selected' : ''; ?>>Challan</option>
                                </select>
                            </td>
                            <td><input type="date" name="hostelDate" class="form-control" value="<?php echo htmlspecialchars($student['hostelDate']); ?>"></td>
                        </tr>
                        <!-- Mess Fee -->
                        <tr>
                            <td>Mess Fee</td>
                            <td><input type="number" step="0.01" name="messAmount" class="form-control" value="<?php echo htmlspecialchars($student['messAmount']); ?>"></td>
                            <td><input type="number" step="0.01" name="messRemaining" class="form-control" value="<?php echo htmlspecialchars($student['messRemaining']); ?>"></td>
                            <td><input type="text" name="messTxnDetails" class="form-control" value="<?php echo htmlspecialchars($student['messTxnDetails']); ?>"></td>
                            <td>
                                <select name="messPlatform" class="form-control">
                                    <option value="" <?php echo $student['messPlatform'] == '' ? 'selected' : ''; ?>>Select</option>
                                    <option value="Online" <?php echo $student['messPlatform'] == 'Online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="Challan" <?php echo $student['messPlatform'] == 'Challan' ? 'selected' : ''; ?>>Challan</option>
                                </select>
                            </td>
                            <td><input type="date" name="messDate" class="form-control" value="<?php echo htmlspecialchars($student['messDate']); ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-actions mt-5 text-center">
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</section>

<style>
.px-5 { padding-left: 3rem !important; padding-right: 3rem !important; }
.form-section h3 { border-bottom: 2px solid var(--gray-300); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--primary-color); font-size: 1.25rem; }
.form-section h3 i { margin-right: 0.5rem; }
.mb-0 { margin-bottom: 0 !important; }
</style>

<?php require_once 'includes/footer.php'; ?>
