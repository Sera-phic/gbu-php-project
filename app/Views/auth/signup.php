<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
<h1>Create Your Account</h1>
<p style="text-align:center; color:#666; margin-bottom:20px;">Only enrolled GBU students may register.</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:20px;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="/signup">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <div>
        <label>College ID (Roll No / Registration No)*</label>
        <input type="text" name="college_id" required maxlength="20">
    </div>

    <div>
        <label>Full Name*</label>
        <input type="text" name="full_name" required maxlength="100">
    </div>

    <div>
        <label>Mobile Number* (10-digit Indian mobile)</label>
        <input type="tel" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile">
    </div>

    <div>
        <label>Email</label>
        <input type="email" name="email" maxlength="100">
    </div>

    <div>
        <label>Department</label>
        <input type="text" name="department" maxlength="100">
    </div>

    <div>
        <label>Program</label>
        <input type="text" name="program" maxlength="100">
    </div>

    <div>
        <label>Current Semester</label>
        <input type="number" name="current_semester" min="1" max="12">
    </div>

    <div>
        <label>Password*</label>
        <input type="password" name="password" required minlength="8">
    </div>

    <div>
        <label>Confirm Password*</label>
        <input type="password" name="confirm_password" required minlength="8">
    </div>

    <button type="submit">Create Account</button>
</form>

<p class="link-text">Already have an account? <a href="/login">Login</a></p>
</div>
</body>
</html>
