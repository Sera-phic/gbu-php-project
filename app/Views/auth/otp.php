<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification — Semester Online</title>
</head>
<body>
<h1>Enter OTP</h1>
<p>A 6-digit OTP has been sent to your registered mobile number. It is valid for 5 minutes.</p>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="POST" action="/otp">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <label>OTP*<br>
        <input type="text" name="otp" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
    </label><br><br>

    <button type="submit">Verify OTP</button>
</form>

<p><a href="/login">Back to Login</a></p>
</body>
</html>
