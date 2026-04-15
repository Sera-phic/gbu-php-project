<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check for admin authentication
check_admin_auth();

$admin_role = $_SESSION['admin_role'] ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GBU Registration</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    
    <link rel="icon" href="../assets/images/banner2.jpg" type="image/jpg">
    
    <style>
        .admin-sidebar {
            background-color: var(--dark-color);
            color: var(--white);
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 2rem;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .admin-sidebar .sidebar-logo {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }
        
        .admin-sidebar .sidebar-logo h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-nav li a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav li a:hover, .sidebar-nav li a.active {
            color: var(--white);
            background-color: rgba(255,255,255,0.05);
            border-left-color: var(--primary-color);
        }
        
        .sidebar-nav li a i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
            background-color: var(--gray-100);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info .role-badge {
            background-color: var(--primary-color);
            color: var(--white);
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-logo">
            <h2>GBU ADMIN</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="registrations.php" class="<?php echo $current_page == 'registrations.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Registrations</a></li>
            <li><a href="reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-file-chart-line"></i> Reports</a></li>
            <li><a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
            <li class="mt-auto"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <button id="sidebarToggle" class="btn btn-light d-lg-none"><i class="fas fa-bars"></i></button>
                <h1 class="h4 mb-0">Admin Portal</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span class="role-badge"><?php echo htmlspecialchars($admin_role); ?></span>
                    <span><i class="fas fa-user-circle"></i> Administrator</span>
                </div>
            </div>
        </header>
        
        <?php display_flash_message(); ?>
