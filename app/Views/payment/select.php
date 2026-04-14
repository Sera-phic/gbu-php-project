<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — Semester Online</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<h1>Pay Semester Fees</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($_GET['payment'])): ?>
    <p style="color:green;">Payment <?= htmlspecialchars($_GET['payment'], ENT_QUOTES, 'UTF-8') ?> successfully.</p>
<?php endif; ?>

<p>Registration ID: <?= (int) ($registration_id ?? 0) ?></p>

<h3>Choose Payment Method</h3>

<!-- Option A: Razorpay -->
<form id="razorpay-form" method="POST" action="/payment/initiate">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="registration_id" value="<?= (int) ($registration_id ?? 0) ?>">
    <input type="hidden" name="method" value="razorpay">
    <button type="submit">Pay via UPI / Card (Razorpay)</button>
</form>

<br>

<!-- Option B: Bank Transfer -->
<details>
    <summary><strong>Pay via Bank Transfer</strong></summary>
    <br>
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

        <button type="submit">Submit Bank Transfer Details</button>
    </form>
</details>

<br>
<a href="/portal">Back to Portal</a>
</body>
</html>
