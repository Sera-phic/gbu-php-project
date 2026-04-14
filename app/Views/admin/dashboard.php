<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container dashboard">
<h1>Admin Dashboard</h1>

<div class="card">
<h2>Pending Registrations</h2>

<?php if (!empty($_GET['action'])): ?>
    <div class="alert alert-success">Action: <?= htmlspecialchars($_GET['action'], ENT_QUOTES, 'UTF-8') ?> successfully.</div>
<?php endif; ?>

<?php if (empty($registrations)): ?>
    <p class="text-muted">No registrations pending approval.</p>
<?php else: ?>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>College ID</th>
                <th>Student Name</th>
                <th>Department</th>
                <th>Semester</th>
                <th>Academic Year</th>
                <th>Payment Method</th>
                <th>Amount (₹)</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $reg): ?>
            <tr>
                <td><?= (int) $reg['id'] ?></td>
                <td><?= htmlspecialchars($reg['college_id'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($reg['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($reg['department'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $reg['semester_id'] ?></td>
                <td><?= htmlspecialchars($reg['academic_year'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($reg['payment_method'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $reg['amount'] ? number_format((float) $reg['amount'], 2) : '—' ?></td>
                <td><?= htmlspecialchars($reg['submitted_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="/admin/detail?id=<?= (int) $reg['id'] ?>">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<p class="link-text"><a href="/logout">Logout</a></p>
</div>
</body>
</html>
