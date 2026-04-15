<?php
$page_title = "My Profile";
require_once 'includes/header.php';

// Check if logged in
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE id = :id");
    $stmt->execute([':id' => $student_id]);
    $student = $stmt->fetch();

    if (!$student) {
        set_flash_message("Profile not found.", "danger");
        redirect('logout.php');
    }
} catch (PDOException $e) {
    set_flash_message("Database Error: " . $e->getMessage(), "danger");
    redirect('index.php');
}
?>

<section class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Student Profile: <?php echo htmlspecialchars($student['fullName']); ?></h2>
        <a href="generate_registration_slip.php?rollNumber=<?php echo urlencode($student['rollNumber']); ?>" class="btn btn-success" target="_blank"><i class="fas fa-download"></i> Download Registration Slip</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="info-group">
                <h3>Personal Information</h3>
                <table class="table">
                    <tr><th>Roll Number</th><td><?php echo htmlspecialchars($student['rollNumber']); ?></td></tr>
                    <tr><th>Programme</th><td><?php echo htmlspecialchars($student['nameOfProgramme']); ?></td></tr>
                    <tr><th>Branch</th><td><?php echo htmlspecialchars($student['branchSpecialization']); ?></td></tr>
                    <tr><th>Year/Sem</th><td>Year <?php echo htmlspecialchars($student['year']); ?> / Sem <?php echo htmlspecialchars($student['semester']); ?></td></tr>
                    <tr><th>Category</th><td><?php echo htmlspecialchars($student['category']); ?></td></tr>
                    <tr><th>Gender</th><td><?php echo htmlspecialchars($student['gender']); ?></td></tr>
                    <tr><th>Aadhar</th><td><?php echo htmlspecialchars($student['aadharCard']); ?></td></tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-group">
                <h3>Contact & Address</h3>
                <table class="table">
                    <tr><th>Contact Number</th><td><?php echo htmlspecialchars($student['studentContact']); ?></td></tr>
                    <tr><th>Email Address</th><td><?php echo htmlspecialchars($student['studentEmail']); ?></td></tr>
                    <tr><th>Permanent Address</th><td><?php echo htmlspecialchars($student['permanentAddress']); ?></td></tr>
                    <tr><th>Hostel</th><td><?php echo htmlspecialchars($student['hostelAddress']); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</section>

<style>
.row { display: flex; flex-wrap: wrap; gap: 2rem; }
.col-md-6 { flex: 1; min-width: 400px; }
.info-group h3 { border-bottom: 2px solid var(--gray-300); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--primary-color); font-size: 1.2rem; }
.table th { width: 40%; color: var(--gray-600); font-weight: 500; }
@media (max-width: 768px) { .col-md-6 { min-width: 100%; } }
</style>

<?php require_once 'includes/footer.php'; ?>
