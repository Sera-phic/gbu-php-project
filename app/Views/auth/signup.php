<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up — Semester Online</title>
</head>
<body>
<h1>Create Your Account</h1>
<p>Only enrolled GBU students may register.</p>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="/signup">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <label>College ID (Roll No / Registration No)*<br>
        <input type="text" name="college_id" required maxlength="20">
    </label><br><br>

    <label>Full Name*<br>
        <input type="text" name="full_name" required maxlength="100">
    </label><br><br>

    <label>Mobile Number* (10-digit Indian mobile)<br>
        <input type="tel" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10">
    </label><br><br>

    <label>Email<br>
        <input type="email" name="email" maxlength="100">
    </label><br><br>

    <label>Department<br>
        <input type="text" name="department" maxlength="100">
    </label><br><br>

    <label>Program<br>
        <input type="text" name="program" maxlength="100">
    </label><br><br>

    <label>Current Semester<br>
        <input type="number" name="current_semester" min="1" max="12">
    </label><br><br>

    <label>Password*<br>
        <input type="password" name="password" required minlength="8">
    </label><br><br>

    <label>Confirm Password*<br>
        <input type="password" name="confirm_password" required minlength="8">
    </label><br><br>

    <button type="submit">Create Account</button>
</form>

<p>Already have an account? <a href="/login">Login</a></p>
</body>
</html>
