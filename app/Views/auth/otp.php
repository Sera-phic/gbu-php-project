<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
<h1>Enter OTP</h1>
<p style="text-align:center; color:#666; margin-bottom:20px;">A 6-digit OTP has been sent to your registered mobile number. It is valid for 5 minutes.</p>

<?php if (defined('APP_ENV') && APP_ENV === 'development' && !empty($_SESSION['dev_otp'])): ?>
    <div class="alert alert-success">
        <strong>Development Mode:</strong> Your OTP is <strong><?= htmlspecialchars($_SESSION['dev_otp'], ENT_QUOTES, 'UTF-8') ?></strong>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="POST" action="/otp">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>

    <div>
        <label>OTP*</label>
        <input type="text" name="otp" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit OTP">
    </div>

    <button type="submit">Verify OTP</button>
</form>

<p class="link-text"><a href="/login">Back to Login</a></p>
</div>
</body>
</html>
