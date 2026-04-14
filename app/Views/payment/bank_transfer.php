<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer — Semester Online</title>
</head>
<body>
<h1>Bank Transfer Details</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="POST" action="/payment/bank-transfer" enctype="multipart/form-data">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="registration_id" value="<?= (int) ($registration_id ?? 0) ?>">

    <label>Bank Name*<br>
        <input type="text" name="bank_name" required maxlength="100">
    </label><br><br>

    <label>Account Holder Name*<br>
        <input type="text" name="account_holder" required maxlength="100">
    </label><br><br>

    <label>Transfer Date*<br>
        <input type="date" name="transfer_date" required>
    </label><br><br>

    <label>Transfer Amount (₹)*<br>
        <input type="number" name="transfer_amount" required min="1" step="0.01">
    </label><br><br>

    <label>UTR / Transaction Reference*<br>
        <input type="text" name="transaction_ref" required maxlength="100">
    </label><br><br>

    <label>Upload Receipt (PDF / JPEG / PNG)*<br>
        <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png">
    </label><br><br>

    <button type="submit">Submit</button>
</form>

<a href="/portal">Back to Portal</a>
</body>
</html>
