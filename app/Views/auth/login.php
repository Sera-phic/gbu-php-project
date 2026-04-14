<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
<h1>Login</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($_GET['registered'])): ?>
    <div class="alert alert-success">Account created successfully. Please log in.</div>
<?php endif; ?>

<form method="POST" action="/login">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <div>
        <label>Roll No / Registration No*</label>
        <input type="text" name="roll_no" required maxlength="20">
    </div>

    <div>
        <label>Registered Mobile Number*</label>
        <input type="tel" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile number">
    </div>

    <button type="submit">Send OTP</button>
</form>

<p class="link-text">Don't have an account? <a href="/signup">Sign Up</a></p>
</div>
</body>
</html>
