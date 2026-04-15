<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container dashboard">
<h1>Welcome, <?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>

<div class="card">
    <h2>Student Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <strong>College ID</strong>
            <span><?= htmlspecialchars($student['college_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="info-item">
            <strong>Department</strong>
            <span><?= htmlspecialchars($student['department'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="info-item">
            <strong>Program</strong>
            <span><?= htmlspecialchars($student['program'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="info-item">
            <strong>Current Semester</strong>
            <span><?= (int) ($student['current_semester'] ?? 0) ?></span>
        </div>
    </div>
</div>

<div class="card">
    <h2>Current Semester Status</h2>
    <?php $reg = $registration_status['registration'] ?? null; ?>
    <?php if ($reg): ?>
        <div class="info-grid">
            <div class="info-item">
                <strong>Registration Status</strong>
                <span class="badge badge-<?= $reg['status'] === 'approved' ? 'success' : ($reg['status'] === 'pending' ? 'warning' : 'danger') ?>">
                    <?= htmlspecialchars($reg['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Payment Status</strong>
                <span class="badge badge-<?= ($reg['payment_status'] ?? '') === 'verified' ? 'success' : 'warning' ?>">
                    <?= htmlspecialchars($reg['payment_status'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted mb-3">No registration found for the current semester.</p>
        <a href="/register?semester=<?= (int) ($student['current_semester'] ?? 0) ?>" class="btn">
            Register for Semester <?= (int) ($student['current_semester'] ?? 0) ?>
        </a>
    <?php endif; ?>
</div>

<div class="card">
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
    <p class="text-muted">No previous registrations found.</p>
<?php endif; ?>
</div>

<p class="link-text"><a href="/logout">Logout</a></p>
</div>
</body>
</html>
