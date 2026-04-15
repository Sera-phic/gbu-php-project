<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Registration Portal' : 'Registration Portal'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    
    <link rel="icon" href="assets/images/banner2.jpg" type="image/jpg">
</head>
<body>
    <header class="main-header">
        <nav class="container nav-container">
            <div class="logo-section">
                <a href="index.php" class="logo-link">
                    <img src="assets/images/GBU logo.png" alt="GBU Logo" class="logo-img">
                    <span class="logo-text">GBU Registration</span>
                </a>
            </div>
            
            <div class="nav-menu" id="navMenu">
                <ul class="nav-list">
                    <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="index.php#instructions">Instructions</a></li>
                    <?php if (isset($_SESSION['student_logged_in'])): ?>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="logout.php" class="nav-btn logout-btn">Logout</a></li>
                    <?php else: ?>
                        <li><a href="register.php" class="nav-btn register-btn">Register Now</a></li>
                        <li><a href="login.php" class="nav-btn login-btn">Student Login</a></li>
                    <?php endif; ?>
                    <li><a href="admin/login.php" class="admin-link"><i class="fas fa-user-shield"></i></a></li>
                </ul>
            </div>
            
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>
    <main class="content-wrapper container">
        <?php display_flash_message(); ?>
