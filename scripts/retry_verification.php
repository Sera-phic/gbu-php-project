<?php

declare(strict_types=1);

/**
 * Cron script: retry payment verification for all pending payments.
 * Run every 6 hours: add to crontab as  0 SLASH6 * * * php /path/to/scripts/retry_verification.php
 *
 * Requirement 7.6
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';

use App\Services\NotificationService;
use App\Services\PaymentVerificationService;
use Razorpay\Api\Api as RazorpayApi;

$appDb      = getAppDb();
$accountsDb = getAccountsDb();
$razorpay   = new RazorpayApi(
    $_ENV['RAZORPAY_KEY_ID']     ?? getenv('RAZORPAY_KEY_ID'),
    $_ENV['RAZORPAY_KEY_SECRET'] ?? getenv('RAZORPAY_KEY_SECRET')
);
$notification = new NotificationService();

$verificationService = new PaymentVerificationService($appDb, $accountsDb, $razorpay, $notification);

// Fetch all registrations with pending payment verification
$stmt = $appDb->prepare(
    "SELECT r.id AS registration_id
     FROM   registrations r
     JOIN   payments p ON p.registration_id = r.id
     WHERE  p.verification_status = 'pending'
       AND  r.status = 'payment_submitted'"
);
$stmt->execute();
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total    = count($pending);
$verified = 0;

foreach ($pending as $row) {
    $result = $verificationService->pollVerification((int) $row['registration_id']);
    if ($result['verified']) {
        $verified++;
    }
    echo sprintf(
        "[%s] Registration %d: %s\n",
        date('Y-m-d H:i:s'),
        $row['registration_id'],
        $result['message']
    );
}

echo sprintf("\nDone. %d/%d verified.\n", $verified, $total);
