<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester Registration — Semester Online</title>
</head>
<body>
<h1>Semester <?= (int) ($semester_id ?? 0) ?> Registration — <?= htmlspecialchars($academic_year ?? '', ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($student)): ?>
<p>Student: <?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($student['college_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</p>
<?php endif; ?>

<form method="POST" action="/register">
    <?= (new \App\Middleware\CsrfMiddleware())->inputField() ?>
    <input type="hidden" name="semester_id" value="<?= (int) ($semester_id ?? 0) ?>">

    <h3>Select Subjects</h3>
    <?php if (!empty($subjects)): ?>
        <?php foreach ($subjects as $subject): ?>
            <label>
                <input type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject['code'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($subject['code'] . ' — ' . $subject['name'], ENT_QUOTES, 'UTF-8') ?>
                (<?= (int) $subject['credits'] ?> credits)
            </label><br>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No subjects available for this semester.</p>
    <?php endif; ?>

    <br>
    <label>
        <input type="checkbox" name="hostel_required" value="1">
        Hostel Required
    </label><br><br>

    <label>Transport Option<br>
        <select name="transport">
            <option value="">— None —</option>
            <option value="bus">University Bus</option>
            <option value="own">Own Arrangement</option>
        </select>
    </label><br><br>

    <label>Remarks<br>
        <textarea name="remarks" rows="3" cols="40" maxlength="500"></textarea>
    </label><br><br>

    <button type="submit">Submit Registration</button>
</form>

<p><a href="/portal">Back to Portal</a></p>
</body>
</html>
