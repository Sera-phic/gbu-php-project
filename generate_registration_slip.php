<?php
/**
 * Generate Registration Slip
 * 
 * Fetches student and fee data to generate a printable registration slip.
 */
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch student data by roll number
$rollNumber = $_GET['rollNumber'] ?? null;

if (!$rollNumber) {
    die("Roll Number is required.");
}

try {
    // SQL query to get student data using PDO
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE rollNumber = :rollNumber");
    $stmt->execute([':rollNumber' => $rollNumber]);
    $studentData = $stmt->fetch();

    if ($studentData) {
        // Data is now in $studentData associative array
        $fullName = $studentData['fullName'];
        $fathersName = $studentData['fathersName'];
        $nameOfProgramme = $studentData['nameOfProgramme'];
        $branchSpecialization = $studentData['branchSpecialization'];
        $year = $studentData['year'];
        $semester = $studentData['semester'];
        $category = $studentData['category'];
        $gender = $studentData['gender'];
        $state = $studentData['stateDomicile'];
        $aadhar = $studentData['aadharCard'];
        $permanentAddress = $studentData['permanentAddress'];
        $hostelAddress = $studentData['hostelAddress'];
        $studentContact = $studentData['studentContact'];
        $studentEmail = $studentData['studentEmail'];
        $fatherContact = $studentData['fatherContact'];
        $fatherEmail = $studentData['fatherEmail'];
        $motherContact = $studentData['motherContact'];
        $motherEmail = $studentData['motherEmail'];
        
        // Semester Payment Information
        $oddSemesterAmount = $studentData['oddSemesterAmount'];
        $oddSemesterRemaining = $studentData['oddSemesterRemaining'];
        $oddSemesterDetails = $studentData['oddSemesterDetails'];
        $oddSemesterTxnDetails = $studentData['oddSemesterTxnDetails'];
        $oddSemesterPlatform = $studentData['oddSemesterPlatform'];
        $oddSemesterDate = $studentData['oddSemesterDate'];

        $evenSemesterAmount = $studentData['evenSemesterAmount'];
        $evenSemesterRemaining = $studentData['evenSemesterRemaining'];
        $evenSemesterDetails = $studentData['evenSemesterDetails'];
        $evenSemesterTxnDetails = $studentData['evenSemesterTxnDetails'];
        $evenSemesterPlatform = $studentData['evenSemesterPlatform'];
        $evenSemesterDate = $studentData['evenSemesterDate'];

        // Hostel Payment Information
        $hostelAmount = $studentData['hostelAmount'];
        $hostelRemaining = $studentData['hostelRemaining'];
        $hostelPaymentMode = $studentData['hostelPaymentMode'];
        $hostelTxnDetails = $studentData['hostelTxnDetails'];
        $hostelPlatform = $studentData['hostelPlatform'];
        $hostelDate = $studentData['hostelDate'];

        // Mess Payment Information
        $messAmount = $studentData['messAmount'];
        $messRemaining = $studentData['messRemaining'];
        $messPaymentMode = $studentData['messPaymentMode'];
        $messTxnDetails = $studentData['messTxnDetails'];
        $messPlatform = $studentData['messPlatform'];
        $messDate = $studentData['messDate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Slip - <?php echo htmlspecialchars($rollNumber); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: white; color: #333; padding: 0; margin: 0; }
        .slip-container { width: 100%; max-width: 100%; padding: 20px; padding-left: 2cm; }
        .slip-header, .details-section, .payment-section, .registration-summary { margin-bottom: 15px; }
        .slip-header { text-align: center; }
        .slip-header img { width: 80px; margin-bottom: 10px; }
        .slip-header h2 { font-size: 20px; color: #0056b3; }
        .slip-header h3 { font-size: 16px; color: #333; }
        .details-section h4, .payment-section h4, .registration-summary h4 { font-size: 16px; color: #0056b3; border-bottom: 1px solid #0056b3; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .row div { width: 48%; font-size: 14px; }
        .footer-section { display: flex; justify-content: flex-end; margin-top: 40px; font-size: 14px; padding-right: 2cm; }
        .signature-box { width: 35%; text-align: center; margin-left: auto; margin-top: 20px; }
        .download-button { text-align: center; margin-top: 20px; }
        .download-button button { padding: 10px 20px; font-size: 16px; background-color: #0056b3; color: white; border: none; cursor: pointer; border-radius: 5px; }
        @media print {
            body { margin: 0; padding: 0; font-size: 12px; }
            .slip-container { width: 100%; max-width: 100%; padding-left: 2cm; }
            .slip-header img { width: 70px; }
            .footer-section { margin-top: 15px; font-size: 12px; }
            .download-button { display: none; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="slip-header">
            <img src="assets/images/GBU logo.png" alt="University Logo">
            <h2>University School of Information and Communication Technology</h2>
            <h3>Gautam Buddha University, Greater Noida 2024-25</h3>
            <p><strong>Date:</strong> <?php echo date("d-m-Y"); ?></p>
        </div>

        <div class="details-section">
            <h4>Student Details</h4>
            <div class="row">
                <div><strong>Roll Number:</strong> <?php echo htmlspecialchars($rollNumber); ?></div>
                <div><strong>Student Name:</strong> <?php echo htmlspecialchars($fullName); ?></div>
            </div>
            <div class="row">
                <div><strong>Father's Name:</strong> <?php echo htmlspecialchars($fathersName); ?></div>
                <div><strong>Programme:</strong> <?php echo htmlspecialchars($nameOfProgramme); ?></div>
            </div>
            <div class="row">
                <div><strong>Branch/Specialization:</strong> <?php echo htmlspecialchars($branchSpecialization); ?></div>
                <div><strong>Year:</strong> <?php echo htmlspecialchars($year); ?></div>
            </div>
            <div class="row">
                <div><strong>Semester:</strong> <?php echo htmlspecialchars($semester); ?></div>
                <div><strong>Academic Session:</strong> 2024-2025</div>
            </div>
            <div class="row">
                <div><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></div>
                <div><strong>Category:</strong> <?php echo htmlspecialchars($category); ?></div>
            </div>
            <div class="row">
                <div><strong>Aadhar Card No:</strong> <?php echo htmlspecialchars($aadhar); ?></div>
                <div><strong>Contact:</strong> <?php echo htmlspecialchars($studentContact); ?></div>
            </div>
            <div class="row">
                <div><strong>Hostel Address:</strong> <?php echo htmlspecialchars($hostelAddress); ?></div>
                <div><strong>Permanent Address:</strong> <?php echo htmlspecialchars($permanentAddress); ?></div>
            </div>
        </div>

        <div class="payment-section">
            <h4>Fee Payment Details</h4>
            <div class="row">
                <div><strong>Odd Semester Amount:</strong> <?php echo htmlspecialchars($oddSemesterAmount); ?></div>
                <div><strong>Odd Semester Remaining:</strong> <?php echo htmlspecialchars($oddSemesterRemaining); ?></div>
            </div>
            <div class="row">
                <div><strong>Even Semester Amount:</strong> <?php echo htmlspecialchars($evenSemesterAmount); ?></div>
                <div><strong>Even Semester Remaining:</strong> <?php echo htmlspecialchars($evenSemesterRemaining); ?></div>
            </div>
            <div class="row">
                <div><strong>Hostel Amount:</strong> <?php echo htmlspecialchars($hostelAmount); ?></div>
                <div><strong>Hostel Remaining:</strong> <?php echo htmlspecialchars($hostelRemaining); ?></div>
            </div>
            <div class="row">
                <div><strong>Mess Amount:</strong> <?php echo htmlspecialchars($messAmount); ?></div>
                <div><strong>Mess Remaining:</strong> <?php echo htmlspecialchars($messRemaining); ?></div>
            </div>
        </div>

        <div class="footer-section">
            <div class="signature-box">
                <br><br>
                <hr>
                <p>(Student Signature)</p>
            </div>
            <div class="signature-box">
                <br><br>
                <hr>
                <p>(Coordinator Signature)</p>
            </div>
        </div>

        <div class="download-button">
            <button onclick="window.print();">Print Registration Slip</button>
            <a href="profile.php" style="margin-left: 10px; text-decoration: none; color: #666;">Back to Profile</a>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo "<p>No data found for the provided Roll Number.</p>";
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
