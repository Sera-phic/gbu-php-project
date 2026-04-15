<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check for admin authentication
check_admin_auth();

$id = $_GET['id'] ?? null;

if (!$id) {
    set_flash_message("Invalid ID provided.", "danger");
    redirect('dashboard.php');
}

try {
    $stmt = $conn->prepare("DELETE FROM registrations WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    set_flash_message("Registration record deleted successfully.", "success");
    redirect('dashboard.php');
} catch (PDOException $e) {
    set_flash_message("Error deleting record: " . $e->getMessage(), "danger");
    redirect('dashboard.php');
}
?>
