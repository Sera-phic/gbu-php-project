<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PaymentVerificationService;
use PDO;
use Razorpay\Api\Api as RazorpayApi;

/**
 * PaymentController — handles payment method selection, Razorpay gateway
 * integration, and bank transfer form submission.
 *
 * Requirements: 6.1–6.6, 10.9
 */
class PaymentController
{
    private PDO $db;
    private PaymentVerificationService $verificationService;
    private RazorpayApi $razorpay;

    /** Allowed MIME types for bank transfer receipt uploads. Requirement 6.6 */
    private const ALLOWED_MIME_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];

    /** Storage path outside the web root. Requirement 10.9 */
    private const UPLOAD_DIR = __DIR__ . '/../../storage/uploads/';

    public function __construct(
        PDO $db,
        PaymentVerificationService $verificationService,
        RazorpayApi $razorpay
    ) {
        $this->db                  = $db;
        $this->verificationService = $verificationService;
        $this->razorpay            = $razorpay;
    }

    // -------------------------------------------------------------------------
    // Initiate payment (UPI / Razorpay)
    // -------------------------------------------------------------------------

    /**
     * Create a Razorpay order and return the order details for the checkout JS.
     *
     * Property 15: Payment submission only allowed from pending_payment status.
     * Requirements: 6.1, 6.5
     *
     * @return array{success: bool, order?: array, error?: string}
     */
    public function initiatePayment(int $registrationId, string $method): array
    {
        $registration = $this->fetchRegistration($registrationId);

        if ($registration === null) {
            return ['success' => false, 'error' => 'Registration not found'];
        }

        // Guard: only allowed from pending_payment status (Requirement 6.5)
        if ($registration['status'] !== 'pending_payment') {
            return ['success' => false, 'error' => 'Payment cannot be initiated for this registration'];
        }

        if (!in_array($method, ['upi', 'razorpay'], true)) {
            return ['success' => false, 'error' => 'Invalid payment method'];
        }

        // Fetch fee amount for this semester
        $amount = $this->getSemesterFee($registration['semester_id'], $registration['student_id']);

        // Create Razorpay order (amount in paise)
        $order = $this->razorpay->order->create([
            'amount'          => (int) round($amount * 100),
            'currency'        => 'INR',
            'receipt'         => 'reg_' . $registrationId,
            'payment_capture' => 1,
        ]);

        // Persist payment record (verification_status = pending)
        $stmt = $this->db->prepare(
            'INSERT INTO payments (registration_id, student_id, amount, payment_method, transaction_ref)
             VALUES (:registration_id, :student_id, :amount, :method, :order_id)'
        );
        $stmt->execute([
            ':registration_id' => $registrationId,
            ':student_id'      => $registration['student_id'],
            ':amount'          => $amount,
            ':method'          => $method,
            ':order_id'        => $order->id,
        ]);

        return [
            'success' => true,
            'order'   => [
                'id'       => $order->id,
                'amount'   => $order->amount,
                'currency' => $order->currency,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Gateway callback
    // -------------------------------------------------------------------------

    /**
     * Handle the Razorpay payment callback after checkout.
     *
     * Requirements: 6.2, 6.4
     *
     * @param  array{razorpay_order_id: string, razorpay_payment_id: string, razorpay_signature: string} $callbackData
     * @return array{success: bool, error?: string}
     */
    public function handleGatewayCallback(array $callbackData): array
    {
        $orderId   = $callbackData['razorpay_order_id']   ?? '';
        $paymentId = $callbackData['razorpay_payment_id'] ?? '';
        $signature = $callbackData['razorpay_signature']  ?? '';

        // Validate Razorpay signature
        try {
            $this->razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Invalid payment signature'];
        }

        // Update transaction_ref with the actual payment ID
        $stmt = $this->db->prepare(
            'UPDATE payments SET transaction_ref = :payment_id WHERE transaction_ref = :order_id'
        );
        $stmt->execute([
            ':payment_id' => $paymentId,
            ':order_id'   => $orderId,
        ]);

        // Fetch registration_id for this payment
        $stmt = $this->db->prepare(
            'SELECT registration_id FROM payments WHERE transaction_ref = :payment_id LIMIT 1'
        );
        $stmt->execute([':payment_id' => $paymentId]);
        $payment = $stmt->fetch();

        if ($payment === false) {
            return ['success' => false, 'error' => 'Payment record not found'];
        }

        $registrationId = (int) $payment['registration_id'];

        // Update registration status to payment_submitted
        $this->updateRegistrationStatus($registrationId, 'payment_submitted');

        // Trigger verification (Requirement 6.4)
        $this->verificationService->verify($registrationId);

        return ['success' => true];
    }

    // -------------------------------------------------------------------------
    // Bank transfer submission
    // -------------------------------------------------------------------------

    /**
     * Submit bank transfer details and receipt for a registration.
     *
     * Requirements: 6.3, 6.4, 6.5, 6.6, 10.9
     *
     * @param  array{
     *   bank_name: string,
     *   account_holder: string,
     *   transfer_date: string,
     *   transfer_amount: float,
     *   transaction_ref: string
     * } $transferDetails
     * @param  array|null $uploadedFile  $_FILES['receipt'] entry
     * @return array{success: bool, error?: string}
     */
    public function submitBankTransfer(int $registrationId, array $transferDetails, ?array $uploadedFile = null): array
    {
        $registration = $this->fetchRegistration($registrationId);

        if ($registration === null) {
            return ['success' => false, 'error' => 'Registration not found'];
        }

        // Guard: only allowed from pending_payment status (Requirement 6.5)
        if ($registration['status'] !== 'pending_payment') {
            return ['success' => false, 'error' => 'Payment cannot be submitted for this registration'];
        }

        // Validate and store receipt file (Requirements 6.6, 10.9)
        $receiptPath = null;
        if ($uploadedFile !== null && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $mimeType = mime_content_type($uploadedFile['tmp_name']);
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                return ['success' => false, 'error' => 'Invalid file type. Only PDF, JPEG, and PNG are accepted.'];
            }

            $ext         = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            $filename    = 'receipt_' . $registrationId . '_' . time() . '.' . $ext;
            $destination = self::UPLOAD_DIR . $filename;

            if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
                return ['success' => false, 'error' => 'Failed to store receipt file'];
            }

            $receiptPath = $filename;
        }

        $amount = $this->getSemesterFee($registration['semester_id'], $registration['student_id']);

        // Persist payment record
        $stmt = $this->db->prepare(
            'INSERT INTO payments
                (registration_id, student_id, amount, payment_method, transaction_ref,
                 bank_name, account_holder, transfer_date, transfer_amount, receipt_path)
             VALUES
                (:registration_id, :student_id, :amount, \'bank_transfer\', :transaction_ref,
                 :bank_name, :account_holder, :transfer_date, :transfer_amount, :receipt_path)'
        );
        $stmt->execute([
            ':registration_id' => $registrationId,
            ':student_id'      => $registration['student_id'],
            ':amount'          => $amount,
            ':transaction_ref' => trim($transferDetails['transaction_ref'] ?? ''),
            ':bank_name'       => trim($transferDetails['bank_name']       ?? ''),
            ':account_holder'  => trim($transferDetails['account_holder']  ?? ''),
            ':transfer_date'   => $transferDetails['transfer_date']        ?? null,
            ':transfer_amount' => $transferDetails['transfer_amount']      ?? $amount,
            ':receipt_path'    => $receiptPath,
        ]);

        // Update registration status to payment_submitted
        $this->updateRegistrationStatus($registrationId, 'payment_submitted');

        // Trigger verification (Requirement 6.4)
        $this->verificationService->verify($registrationId);

        return ['success' => true];
    }

    // -------------------------------------------------------------------------
    // Payment status
    // -------------------------------------------------------------------------

    /**
     * Return the current payment status for a registration.
     *
     * @return array{payment_method?: string, verification_status?: string, amount?: float}|array{error: string}
     */
    public function getPaymentStatus(int $registrationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT payment_method, verification_status, amount
             FROM   payments
             WHERE  registration_id = :registration_id
             LIMIT  1'
        );
        $stmt->execute([':registration_id' => $registrationId]);
        $row = $stmt->fetch();

        if ($row === false) {
            return ['error' => 'No payment record found'];
        }

        return $row;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function fetchRegistration(int $registrationId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, student_id, semester_id, status
             FROM   registrations
             WHERE  id = :id
             LIMIT  1'
        );
        $stmt->execute([':id' => $registrationId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function updateRegistrationStatus(int $registrationId, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE registrations SET status = :status WHERE id = :id'
        );
        $stmt->execute([':status' => $status, ':id' => $registrationId]);
    }

    /**
     * Fetch the semester fee for the student's program.
     * Falls back to a default fee if no specific record is found.
     */
    private function getSemesterFee(int $semesterId, int $studentId): float
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT sf.amount
                 FROM   semester_fees sf
                 JOIN   students s ON s.program = sf.program
                 WHERE  sf.semester_id = :semester_id
                   AND  s.id           = :student_id
                 LIMIT  1'
            );
            $stmt->execute([':semester_id' => $semesterId, ':student_id' => $studentId]);
            $amount = $stmt->fetchColumn();
            return $amount !== false ? (float) $amount : 0.0;
        } catch (\PDOException) {
            return 0.0;
        }
    }
}
