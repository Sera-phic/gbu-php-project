<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('dashboard.php');
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple hardcoded credentials for now as per original
    $admins = [
        'super_admin' => ['username' => 'superadmin', 'password' => 'ICT@admin25'],
        'office_admin' => ['username' => 'admin', 'password' => 'ICT@202525']
    ];

    $logged_in = false;
    foreach ($admins as $role => $credentials) {
        if ($username === $credentials['username'] && $password === $credentials['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_role'] = $role;
            set_flash_message("Welcome back, Administrator!", "success");
            redirect('dashboard.php');
            $logged_in = true;
            break;
        }
    }

    if (!$logged_in) {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GBU Registration</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    
    <link rel="icon" href="../assets/images/banner2.jpg" type="image/jpg">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--dark-color) 0%, #1a1a1a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        
        .admin-login-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header img {
            height: 80px;
            margin-bottom: 1rem;
        }
        
        .login-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <div class="login-header">
            <img src="../assets/images/GBU logo.png" alt="GBU Logo">
            <h2>Admin Login</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="width:100%; margin-top:1.5rem;">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="text-secondary small"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</body>
</html>
