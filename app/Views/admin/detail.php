<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Detail — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container dashboard">
<h1>Registration Detail</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <p class="link-text"><a href="/admin">Back to Dashboard</a></p>
    <?php return; ?>
<?php endif; ?>

<h2>Student Information</h2>
<table border="1" cellpadding="6">
    <tr><th>College ID</th><td><?= htmlspecialchars($student['college_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Full Name</th><td><?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Mobile</th><td><?= htmlspecialchars($student['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($student['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Department</th><td><?= htmlspecialchars($student['department'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Program</th><td><?= htmlspecialchars($student['program'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Current Semester</th><td><?= (int) ($student['current_semester'] ?? 0) ?></td></tr>
</table>

<h2>Registration Details</h2>
<table border="1" cellpadding="6">
    <tr><th>Registration ID</th><td><?= (int) ($registration['id'] ?? 0) ?></td></tr>
    <tr><th>Semester</th><td><?= (int) ($registration['semester_id'] ?? 0) ?></td></tr>
    <tr><th>Academic Year</th><td><?= htmlspecialchars($registration['academic_year'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Status</th><td><?= htmlspecialchars($registration['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Hostel Required</th><td><?= ($registration['hostel_required'] ?? 0) ? 'Yes' : 'No' ?></td></tr>
    <tr><th>Transport</th><td><?= htmlspecialchars($registration['transport'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Remarks</th><td><?= htmlspecialchars($registration['remarks'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Submitted At</th><td><?= htmlspecialchars($registration['submitted_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
</table>

<?php if (!empty($payment)): ?>
<h2>Payment Details</h2>
<table border="1" cellpadding="6">
    <tr><th>Method</th><td><?= htmlspecialchars($payment['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Amount (₹)</th><td><?= $payment['amount'] ? number_format((float) $payment['amount'], 2) : '—' ?></td></tr>
    <tr><th>Transaction Ref</th><td><?= htmlspecialchars($payment['transaction_ref'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Bank Name</th><td><?= htmlspecialchars($payment['bank_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Account Holder</th><td><?= htmlspecialchars($payment['account_holder'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Transfer Date</th><td><?= htmlspecialchars($payment['transfer_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Transfer Amount (₹)</th><td><?= $payment['transfer_amount'] ? number_format((float) $payment['transfer_amount'], 2) : '—' ?></td></tr>
    <tr><th>Verification Status</th><td><?= htmlspecialchars($payment['verification_status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>Verified At</th><td><?= htmlspecialchars($payment['verified_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td></tr>
</table>
<?php endif; ?>

<h2>Actions</h2>

<div class="action-buttons">
<form method="POST" action="/admin/approve" style="display:inline;">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="registration_id" value="<?= (int) ($registration['id'] ?? 0) ?>">
    <button type="submit" class="btn btn-approve" onclick="return confirm('Approve this registration?')">✅ Approve Registration</button>
</form>

<form method="POST" action="/admin/reject" style="display:inline;">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="registration_id" value="<?= (int) ($registration['id'] ?? 0) ?>">
    <input type="text" name="reason" required maxlength="255" placeholder="Rejection reason" style="margin-right:8px;">
    <button type="submit" class="btn btn-reject" onclick="return confirm('Reject this registration?')">❌ Reject Registration</button>
</form>
</div>

<br><br>
<p class="link-text"><a href="/admin">Back to Dashboard</a></p>
</div>
</body>
</html>
