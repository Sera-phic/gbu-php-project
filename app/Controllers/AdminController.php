<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\NotificationService;
use App\Services\PaymentVerificationService;
use PDO;

/**
 * AdminController — admin dashboard for reviewing and approving registrations.
 *
 * Requirements: 8.1–8.7, 10.10, 11.4
 */
class AdminController
{
    private PDO $db;
    private PaymentVerificationService $verificationService;
    private NotificationService $notification;

    public function __construct(
        PDO $db,
        PaymentVerificationService $verificationService,
        NotificationService $notification
    ) {
        $this->db                  = $db;
        $this->verificationService = $verificationService;
        $this->notification        = $notification;
    }

    // -------------------------------------------------------------------------
    // Dashboard — pending registrations
    // -------------------------------------------------------------------------

    /**
     * Return all registrations with status = 'payment_verified'.
     *
     * Property 19: Admin dashboard shows only payment_verified registrations.
     * Requirement 8.1
     *
     * @return list<array>
     */
    public function getPendingRegistrations(): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.id,
                    r.student_id,
                    r.semester_id,
                    r.academic_year,
                    r.submitted_at,
                    s.full_name,
                    s.college_id,
                    s.department,
                    s.program,
                    p.payment_method,
                    p.amount,
                    p.verification_status AS payment_status
             FROM   registrations r
             JOIN   students s ON s.id = r.student_id
             LEFT JOIN payments p ON p.registration_id = r.id
             WHERE  r.status = 'payment_verified'
             ORDER  BY r.submitted_at ASC"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // -------------------------------------------------------------------------
    // Registration detail
    // -------------------------------------------------------------------------

    /**
     * Return full student profile and payment details for a registration.
     *
     * Requirement 8.2
     *
     * @return array{registration: array, student: array, payment: array|null}|array{error: string}
     */
    public function getRegistrationDetail(int $registrationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.id,
                    r.student_id,
                    r.semester_id,
                    r.academic_year,
                    r.subjects,
                    r.hostel_required,
                    r.transport,
                    r.remarks,
                    r.status,
                    r.submitted_at
             FROM   registrations r
             WHERE  r.id = :id
             LIMIT  1'
        );
        $stmt->execute([':id' => $registrationId]);
        $registration = $stmt->fetch();

        if ($registration === false) {
            return ['error' => 'Registration not found'];
        }

        // Fetch student profile
        $stmt = $this->db->prepare(
            'SELECT id, college_id, full_name, mobile, email, department, program, current_semester
             FROM   students
             WHERE  id = :id
             LIMIT  1'
        );
        $stmt->execute([':id' => $registration['student_id']]);
        $student = $stmt->fetch() ?: [];

        // Fetch payment details
        $stmt = $this->db->prepare(
            'SELECT payment_method, amount, transaction_ref, bank_name, account_holder,
                    transfer_date, transfer_amount, receipt_path, verification_status, verified_at
             FROM   payments
             WHERE  registration_id = :registration_id
             LIMIT  1'
        );
        $stmt->execute([':registration_id' => $registrationId]);
        $payment = $stmt->fetch() ?: null;

        return [
            'registration' => $registration,
            'student'      => $student,
            'payment'      => $payment,
        ];
    }

    // -------------------------------------------------------------------------
    // Approve registration
    // -------------------------------------------------------------------------

    /**
     * Approve a registration that is in 'payment_verified' status.
     *
     * Property 20: Admin approval only allowed from payment_verified status.
     * Property 21: Admin action is fully recorded.
     * Requirements: 8.3, 8.5, 8.7, 11.4
     *
     * @return array{success: bool, message: string}
     */
    public function approveRegistration(int $registrationId, int $adminId): array
    {
        $registration = $this->fetchRegistration($registrationId);

        if ($registration === null) {
            return ['success' => false, 'message' => 'Registration not found'];
        }

        // Guard: must be in payment_verified status (Requirement 8.5)
        if ($registration['status'] !== 'payment_verified') {
            return ['success' => false, 'message' => 'Registration is not in a verifiable state'];
        }

        // Forward-only status update via PaymentVerificationService (Requirement 11.1)
        $updated = $this->verificationService->updateRegistrationStatus($registrationId, 'approved');

        if (!$updated) {
            return ['success' => false, 'message' => 'Failed to update registration status'];
        }

        // Log admin action (Requirements 8.7, 11.4)
        $this->logAdminAction($registrationId, $adminId, 'approved', null);

        // Notify student (Requirement 8.3)
        $this->notification->sendStatusUpdate(
            (int) $registration['student_id'],
            'approved',
            'Your semester registration has been approved.'
        );

        return ['success' => true, 'message' => 'Registration approved successfully'];
    }

    // -------------------------------------------------------------------------
    // Reject registration
    // -------------------------------------------------------------------------

    /**
     * Reject a registration that is in 'payment_verified' status.
     *
     * Property 20: Admin rejection only allowed from payment_verified status.
     * Property 21: Admin action is fully recorded.
     * Requirements: 8.4, 8.5, 8.7, 11.4
     *
     * @return array{success: bool, message: string}
     */
    public function rejectRegistration(int $registrationId, int $adminId, string $reason): array
    {
        $registration = $this->fetchRegistration($registrationId);

        if ($registration === null) {
            return ['success' => false, 'message' => 'Registration not found'];
        }

        // Guard: must be in payment_verified status (Requirement 8.5)
        if ($registration['status'] !== 'payment_verified') {
            return ['success' => false, 'message' => 'Registration is not in a verifiable state'];
        }

        // Forward-only status update (Requirement 11.1)
        $updated = $this->verificationService->updateRegistrationStatus($registrationId, 'rejected');

        if (!$updated) {
            return ['success' => false, 'message' => 'Failed to update registration status'];
        }

        // Log admin action with reason (Requirements 8.7, 11.4)
        $this->logAdminAction($registrationId, $adminId, 'rejected', $reason);

        // Notify student (Requirement 8.4)
        $this->notification->sendStatusUpdate(
            (int) $registration['student_id'],
            'rejected',
            'Your semester registration has been rejected. Reason: ' . $reason
        );

        return ['success' => true, 'message' => 'Registration rejected'];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function fetchRegistration(int $registrationId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, student_id, status FROM registrations WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $registrationId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Insert a row into admin_actions for audit trail.
     * Requirements: 8.7, 11.4
     */
    private function logAdminAction(int $registrationId, int $adminId, string $action, ?string $notes): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO admin_actions (registration_id, admin_id, action, notes)
             VALUES (:registration_id, :admin_id, :action, :notes)'
        );
        $stmt->execute([
            ':registration_id' => $registrationId,
            ':admin_id'        => $adminId,
            ':action'          => $action,
            ':notes'           => $notes,
        ]);
    }
}
