<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — Semester Online</title>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
<p>College ID: <?= htmlspecialchars($student['college_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
<p>Department: <?= htmlspecialchars($student['department'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
<p>Program: <?= htmlspecialchars($student['program'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
<p>Current Semester: <?= (int) ($student['current_semester'] ?? 0) ?></p>

<hr>

<h2>Current Semester Status</h2>
<?php $reg = $registration_status['registration'] ?? null; ?>
<?php if ($reg): ?>
    <p>Registration Status: <strong><?= htmlspecialchars($reg['status'], ENT_QUOTES, 'UTF-8') ?></strong></p>
    <p>Payment Status: <strong><?= htmlspecialchars($reg['payment_status'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong></p>
<?php else: ?>
    <p>No registration found for the current semester.</p>
    <a href="/register?semester=<?= (int) ($student['current_semester'] ?? 0) ?>">
        <button>Register for Semester <?= (int) ($student['current_semester'] ?? 0) ?></button>
    </a>
<?php endif; ?>

<hr>

<h2>Semester History</h2>
<?php if (!empty($semester_history)): ?>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Semester</th>
                <th>Academic Year</th>
                <th>Registration Status</th>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Amount (₹)</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($semester_history as $row): ?>
            <tr>
                <td><?= (int) $row['semester_id'] ?></td>
                <td><?= htmlspecialchars($row['academic_year'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['registration_status'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['payment_method'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['payment_status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $row['amount'] ? number_format((float) $row['amount'], 2) : '—' ?></td>
                <td><?= htmlspecialchars($row['submitted_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No previous registrations found.</p>
<?php endif; ?>

<hr>
<a href="/logout">Logout</a>
</body>
</html>
