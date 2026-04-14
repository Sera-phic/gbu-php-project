<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Semester Online</title>
</head>
<body>
<h1>Login</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($_GET['registered'])): ?>
    <p style="color:green;">Account created successfully. Please log in.</p>
<?php endif; ?>

<form method="POST" action="/login">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <label>Roll No / Registration No*<br>
        <input type="text" name="roll_no" required maxlength="20">
    </label><br><br>

    <label>Registered Mobile Number*<br>
        <input type="tel" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10">
    </label><br><br>

    <button type="submit">Send OTP</button>
</form>

<p>Don't have an account? <a href="/signup">Sign Up</a></p>
</body>
</html>
