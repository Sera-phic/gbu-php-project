<?php
$page_title = "Student Login";
require_once 'includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    redirect('profile.php');
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rollNumber = $_POST['rollNumber'] ?? '';
    $studentContact = $_POST['studentContact'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT * FROM registrations WHERE rollNumber = :rollNumber AND studentContact = :studentContact");
        $stmt->execute([
            ':rollNumber' => sanitize_input($rollNumber),
            ':studentContact' => sanitize_input($studentContact)
        ]);
        $student = $stmt->fetch();

        if ($student) {
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['rollNumber'] = $student['rollNumber'];
            set_flash_message("Welcome, " . $student['fullName'] . "!", "success");
            redirect('profile.php');
        } else {
            $error = "Invalid Roll Number or Mobile Number.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>

<section class="section-card" style="max-width: 500px; margin: 0 auto;">
    <div class="login-header text-center mb-4">
        <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
        <h2 class="section-title">Student Login</h2>
        <p class="text-muted">Enter your details to access your profile and download registration slip.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="login.php" method="POST">
        <div class="form-group">
            <label class="form-label" for="rollNumber">Roll Number</label>
            <input type="text" id="rollNumber" name="rollNumber" class="form-control" placeholder="Enter Roll Number" required autofocus>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="studentContact">Mobile Number</label>
            <input type="text" id="studentContact" name="studentContact" class="form-control" placeholder="Enter Registered Mobile Number" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block w-100 mt-3">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>
    
    <div class="text-center mt-4">
        <p>Not registered yet? <a href="register.php">Register Now</a></p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
