<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use Razorpay\Api\Api as RazorpayApi;

/**
 * PaymentVerificationService — cross-references submitted payment data against
 * the Accounts Department database.
 *
 * Requirements: 7.1–7.7, 11.1–11.3
 */
class PaymentVerificationService
{
    private PDO $appDb;
    private PDO $accountsDb;
    private RazorpayApi $razorpay;
    private NotificationService $notification;

    /**
     * Ordered registration status lifecycle.
     * Property 22: Status transitions are strictly forward.
     * Requirements: 11.1, 11.2
     */
    private const STATUS_ORDER = [
        'draft',
        'pending_payment',
        'payment_submitted',
        'payment_verified',
        'approved',
        'rejected',
    ];

    public function __construct(
        PDO $appDb,
        PDO $accountsDb,
        RazorpayApi $razorpay,
        NotificationService $notification
    ) {
        $this->appDb        = $appDb;
        $this->accountsDb   = $accountsDb;
        $this->razorpay     = $razorpay;
        $this->notification = $notification;
    }

    // -------------------------------------------------------------------------
    // Verify payment
    // -------------------------------------------------------------------------

    /**
     * Verify the payment for a registration.
     * Routes to gateway or bank-transfer verification based on payment method.
     *
     * Requirements: 7.1–7.5, 7.7
     *
     * @return array{verified: bool, message: string}
     */
    public function verify(int $registrationId): array
    {
        $payment = $this->fetchPayment($registrationId);

        if ($payment === null) {
            return ['verified' => false, 'message' => 'Payment record not found'];
        }

        if (in_array($payment['payment_method'], ['upi', 'razorpay'], true)) {
            return $this->verifyGatewayPayment($payment, $registrationId);
        }

        if ($payment['payment_method'] === 'bank_transfer') {
            return $this->verifyBankTransfer($payment, $registrationId);
        }

        return ['verified' => false, 'message' => 'Unknown payment method'];
    }

    /**
     * Poll verification for a single registration (used by cron retry).
     * Requirements: 7.6
     *
     * @return array{verified: bool, message: string}
     */
    public function pollVerification(int $registrationId): array
    {
        return $this->verify($registrationId);
    }

    // -------------------------------------------------------------------------
    // Status lifecycle enforcement
    // -------------------------------------------------------------------------

    /**
     * Update registration status with forward-only transition guard.
     *
     * Property 22: Registration status transitions are strictly forward.
     * Requirements: 11.1, 11.2, 11.3
     *
     * @return bool  true on success, false if transition is invalid
     */
    public function updateRegistrationStatus(int $registrationId, string $newStatus): bool
    {
        $registration = $this->fetchRegistration($registrationId);

        if ($registration === null) {
            return false;
        }

        $currentStatus = $registration['status'];

        // 'rejected' is always a valid terminal transition from payment_verified
        if ($newStatus === 'rejected') {
            return $this->applyStatusUpdate($registrationId, $newStatus);
        }

        $currentIndex = array_search($currentStatus, self::STATUS_ORDER, true);
        $newIndex     = array_search($newStatus, self::STATUS_ORDER, true);

        // Reject backward or same-level transitions (Requirement 11.2)
        if ($currentIndex === false || $newIndex === false || $newIndex <= $currentIndex) {
            return false;
        }

        return $this->applyStatusUpdate($registrationId, $newStatus);
    }

    // -------------------------------------------------------------------------
    // Private — gateway verification
    // -------------------------------------------------------------------------

    /**
     * Verify a UPI/Razorpay payment via the gateway API.
     *
     * Property 16: Gateway payment verification outcome is deterministic.
     * Requirements: 7.1, 7.2
     *
     * @return array{verified: bool, message: string}
     */
    private function verifyGatewayPayment(array $payment, int $registrationId): array
    {
        try {
            $gatewayPayment = $this->razorpay->payment->fetch($payment['transaction_ref']);

            if ($gatewayPayment->status === 'captured') {
                $this->markPaymentVerified($payment['id']);
                $this->updateRegistrationStatus($registrationId, 'payment_verified');
                return ['verified' => true, 'message' => 'Payment verified via gateway'];
            }

            return ['verified' => false, 'message' => 'Gateway payment not confirmed'];
        } catch (\Exception $e) {
            return ['verified' => false, 'message' => 'Gateway verification error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------------------------------
    // Private — bank transfer verification
    // -------------------------------------------------------------------------

    /**
     * Verify a bank transfer against the Accounts Department database.
     *
     * Property 17: Bank transfer verification matches on correct criteria.
     * Property 18: Accounts_DB is never mutated by verification.
     * Requirements: 7.3, 7.4, 7.5, 7.7
     *
     * @return array{verified: bool, message: string}
     */
    private function verifyBankTransfer(array $payment, int $registrationId): array
    {
        $student = $this->fetchStudent((int) $payment['student_id']);

        if ($student === null) {
            return ['verified' => false, 'message' => 'Student not found'];
        }

        try {
            // Query Accounts_DB read-only — never mutates (Requirement 7.7)
            $stmt = $this->accountsDb->prepare(
                'SELECT id
                 FROM   payments
                 WHERE  college_id    = :college_id
                   AND  amount        = :amount
                   AND  payment_date >= DATE_SUB(:transfer_date, INTERVAL 2 DAY)
                   AND  payment_date <= DATE_ADD(:transfer_date, INTERVAL 2 DAY)
                 LIMIT  1'
            );
            $stmt->execute([
                ':college_id'    => $student['college_id'],
                ':amount'        => $payment['transfer_amount'],
                ':transfer_date' => $payment['transfer_date'],
            ]);

            $accRecord = $stmt->fetch();

            if ($accRecord !== false) {
                // Match found — mark verified (Requirements 7.3)
                $this->markPaymentVerified($payment['id']);
                $this->updateRegistrationStatus($registrationId, 'payment_verified');
                $this->notification->sendStatusUpdate(
                    (int) $payment['student_id'],
                    'payment_verified',
                    'Payment verified. Awaiting admin approval.'
                );
                return ['verified' => true, 'message' => 'Bank transfer matched in Accounts DB'];
            }

            // No match — leave as pending for cron retry (Requirement 7.4)
            $this->setPendingStatus($payment['id']);
            return ['verified' => false, 'message' => 'No matching record in Accounts DB. Will retry.'];

        } catch (\PDOException $e) {
            // Accounts DB unavailable (Requirement 7.5)
            error_log('Accounts DB unavailable: ' . $e->getMessage());
            $this->setPendingStatus($payment['id']);
            return ['verified' => false, 'message' => 'Verification is in progress. You will be notified.'];
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function fetchPayment(int $registrationId): ?array
    {
        $stmt = $this->appDb->prepare(
            'SELECT id, student_id, payment_method, transaction_ref,
                    transfer_amount, transfer_date, verification_status
             FROM   payments
             WHERE  registration_id = :registration_id
             LIMIT  1'
        );
        $stmt->execute([':registration_id' => $registrationId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function fetchRegistration(int $registrationId): ?array
    {
        $stmt = $this->appDb->prepare(
            'SELECT id, status FROM registrations WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $registrationId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function fetchStudent(int $studentId): ?array
    {
        $stmt = $this->appDb->prepare(
            'SELECT id, college_id FROM students WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $studentId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function markPaymentVerified(int $paymentId): void
    {
        $stmt = $this->appDb->prepare(
            'UPDATE payments SET verification_status = \'verified\', verified_at = NOW() WHERE id = :id'
        );
        $stmt->execute([':id' => $paymentId]);
    }

    private function setPendingStatus(int $paymentId): void
    {
        $stmt = $this->appDb->prepare(
            'UPDATE payments SET verification_status = \'pending\' WHERE id = :id'
        );
        $stmt->execute([':id' => $paymentId]);
    }

    private function applyStatusUpdate(int $registrationId, string $status): bool
    {
        $stmt = $this->appDb->prepare(
            'UPDATE registrations SET status = :status WHERE id = :id'
        );
        $stmt->execute([':status' => $status, ':id' => $registrationId]);

        return $stmt->rowCount() > 0;
    }
}
