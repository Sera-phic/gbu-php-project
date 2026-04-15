<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if form data exists in session
if (!isset($_SESSION['formData'])) {
    set_flash_message("Session expired or invalid request.", "danger");
    redirect('register.php');
}

$data = $_SESSION['formData'];

try {
    // Prepare the SQL statement
    $sql = "INSERT INTO registrations (
        rollNumber, fullName, fathersName, nameOfProgramme, branchSpecialization, year, semester, 
        category, gender, aadharCard, permanentAddress, hostelAddress, studentContact, 
        studentEmail, fatherOccupation,
        oddSemesterAmount, oddSemesterRemaining, oddSemesterDetails, oddSemesterTxnDetails, oddSemesterPlatform, oddSemesterDate,
        evenSemesterAmount, evenSemesterRemaining, evenSemesterDetails, evenSemesterTxnDetails, evenSemesterPlatform, evenSemesterDate,
        hostelAmount, hostelRemaining, hostelPaymentMode, hostelTxnDetails, hostelPlatform, hostelDate,
        messAmount, messRemaining, messPaymentMode, messTxnDetails, messPlatform, messDate
    ) VALUES (
        :rollNumber, :fullName, :fathersName, :nameOfProgramme, :branchSpecialization, :year, :semester, 
        :category, :gender, :aadharCard, :permanentAddress, :hostelAddress, :studentContact, 
        :studentEmail, :fatherOccupation,
        :oddSemesterAmount, :oddSemesterRemaining, :oddSemesterDetails, :oddSemesterTxnDetails, :oddSemesterPlatform, :oddSemesterDate,
        :evenSemesterAmount, :evenSemesterRemaining, :evenSemesterDetails, :evenSemesterTxnDetails, :evenSemesterPlatform, :evenSemesterDate,
        :hostelAmount, :hostelRemaining, :hostelPaymentMode, :hostelTxnDetails, :hostelPlatform, :hostelDate,
        :messAmount, :messRemaining, :messPaymentMode, :messTxnDetails, :messPlatform, :messDate
    )";

    $stmt = $conn->prepare($sql);
    
    // Bind parameters and execute
    $stmt->execute([
        ':rollNumber' => sanitize_input($data['rollNumber']),
        ':fullName' => sanitize_input($data['fullName']),
        ':fathersName' => sanitize_input($data['fathersName']),
        ':nameOfProgramme' => sanitize_input($data['nameOfProgramme']),
        ':branchSpecialization' => sanitize_input($data['branchSpecialization']),
        ':year' => (int)$data['year'],
        ':semester' => (int)$data['semester'],
        ':category' => sanitize_input($data['category']),
        ':gender' => sanitize_input($data['gender']),
        ':aadharCard' => sanitize_input($data['aadharCard']),
        ':permanentAddress' => sanitize_input($data['permanentAddress']),
        ':hostelAddress' => sanitize_input($data['hostelAddress']),
        ':studentContact' => sanitize_input($data['studentContact']),
        ':studentEmail' => sanitize_input($data['studentEmail']),
        ':fatherOccupation' => sanitize_input($data['fatherOccupation'] ?? 'N/A'),
        ':oddSemesterAmount' => $data['oddSemesterAmount'] ?: 0,
        ':oddSemesterRemaining' => $data['oddSemesterRemaining'] ?: 0,
        ':oddSemesterDetails' => $data['oddSemesterDetails'] ?? '',
        ':oddSemesterTxnDetails' => $data['oddSemesterTxnDetails'] ?? '',
        ':oddSemesterPlatform' => $data['oddSemesterPlatform'] ?? '',
        ':oddSemesterDate' => $data['oddSemesterDate'] ?: null,
        ':evenSemesterAmount' => $data['evenSemesterAmount'] ?: 0,
        ':evenSemesterRemaining' => $data['evenSemesterRemaining'] ?: 0,
        ':evenSemesterDetails' => $data['evenSemesterDetails'] ?? '',
        ':evenSemesterTxnDetails' => $data['evenSemesterTxnDetails'] ?? '',
        ':evenSemesterPlatform' => $data['evenSemesterPlatform'] ?? '',
        ':evenSemesterDate' => $data['evenSemesterDate'] ?: null,
        ':hostelAmount' => $data['hostelAmount'] ?: 0,
        ':hostelRemaining' => $data['hostelRemaining'] ?: 0,
        ':hostelPaymentMode' => $data['hostelPaymentMode'] ?? '',
        ':hostelTxnDetails' => $data['hostelTxnDetails'] ?? '',
        ':hostelPlatform' => $data['hostelPlatform'] ?? '',
        ':hostelDate' => $data['hostelDate'] ?: null,
        ':messAmount' => $data['messAmount'] ?: 0,
        ':messRemaining' => $data['messRemaining'] ?: 0,
        ':messPaymentMode' => $data['messPaymentMode'] ?? '',
        ':messTxnDetails' => $data['messTxnDetails'] ?? '',
        ':messPlatform' => $data['messPlatform'] ?? '',
        ':messDate' => $data['messDate'] ?: null
    ]);

    // Registration successful
    unset($_SESSION['formData']);
    set_flash_message("Registration successful! You can now login with your Roll Number and Mobile Number.", "success");
    redirect('login.php');

} catch (PDOException $e) {
    // Check for duplicate roll number error (MySQL error code 1062)
    if ($e->getCode() == 23000) {
        set_flash_message("Error: This Roll Number is already registered.", "danger");
    } else {
        set_flash_message("Database Error: " . $e->getMessage(), "danger");
    }
    redirect('register.php');
}
?>
