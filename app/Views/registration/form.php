<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester Registration — Semester Online</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
<h1>Semester <?= (int) ($semester_id ?? 0) ?> Registration</h1>
<p style="text-align:center; color:#666; margin-bottom:20px;"><?= htmlspecialchars($academic_year ?? '', ENT_QUOTES, 'UTF-8') ?></p>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($student)): ?>
<p style="text-align:center; color:#666;">Student: <?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($student['college_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</p>
<?php endif; ?>

<form method="POST" action="/register">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="semester_id" value="<?= (int) ($semester_id ?? 0) ?>">

    <div>
        <label>Select Subjects</label>
        <?php if (!empty($subjects)): ?>
            <?php foreach ($subjects as $subject): ?>
                <label style="font-weight:normal; display:block; margin:8px 0;">
                    <input type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject['code'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($subject['code'] . ' — ' . $subject['name'], ENT_QUOTES, 'UTF-8') ?>
                    (<?= (int) $subject['credits'] ?> credits)
                </label>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#999;">No subjects available for this semester.</p>
        <?php endif; ?>
    </div>

    <div>
        <label style="font-weight:normal;">
            <input type="checkbox" name="hostel_required" value="1">
            Hostel Required
        </label>
    </div>

    <div>
        <label>Transport Option</label>
        <select name="transport">
            <option value="">— None —</option>
            <option value="bus">University Bus</option>
            <option value="own">Own Arrangement</option>
        </select>
    </div>

    <div>
        <label>Remarks</label>
        <textarea name="remarks" rows="3" maxlength="500"></textarea>
    </div>

    <button type="submit">Submit Registration</button>
</form>

<p class="link-text"><a href="/portal">Back to Portal</a></p>
</div>
</body>
</html>
