<?php

declare(strict_types=1);

/**
 * Cron script: purge expired OTP tokens to prevent table bloat.
 * Run hourly: add to crontab as  0 * * * * php /path/to/scripts/purge_otp_tokens.php
 *
 * Requirement 13.3
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = getAppDb();

$stmt = $db->prepare('DELETE FROM otp_tokens WHERE expires_at < NOW()');
$stmt->execute();

$deleted = $stmt->rowCount();

echo sprintf("[%s] Purged %d expired OTP token(s).\n", date('Y-m-d H:i:s'), $deleted);
