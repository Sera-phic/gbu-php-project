<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\NotificationService;
use PDO;

/**
 * AuthController — handles student sign-up, login (MFA), OTP verification,
 * and session management.
 */
class AuthController
{
    private PDO $db;
    private NotificationService $notification;

    public function __construct(PDO $db, NotificationService $notification)
    {
        $this->db           = $db;
        $this->notification = $notification;
    }

    // -------------------------------------------------------------------------
    // Sign-Up
    // -------------------------------------------------------------------------

    /**
     * Register a new student account.
     *
     * @param  array{
     *   college_id: string,
     *   full_name: string,
     *   mobile: string,
     *   email?: string,
     *   department?: string,
     *   program?: string,
     *   current_semester?: int,
     *   password: string,
     *   confirm_password: string
     * } $formData
     * @return array{success: bool, errors: list<string>}
     */
    public function signUp(array $formData): array
    {
        $errors = [];

        $collegeId       = trim((string) ($formData['college_id']       ?? ''));
        $fullName        = trim((string) ($formData['full_name']         ?? ''));
        $mobile          = trim((string) ($formData['mobile']            ?? ''));
        $email           = trim((string) ($formData['email']             ?? ''));
        $department      = trim((string) ($formData['department']        ?? ''));
        $program         = trim((string) ($formData['program']           ?? ''));
        $currentSemester = isset($formData['current_semester'])
            ? (int) $formData['current_semester']
            : null;
        $password        = (string) ($formData['password']         ?? '');
        $confirmPassword = (string) ($formData['confirm_password'] ?? '');

        // 1. Password match (Requirement 1.3)
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        // 2. Mobile validation — 10-digit Indian mobile (Requirement 1.4)
        if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
            $errors[] = 'Invalid mobile number';
        }

        // 3. College ID must exist in the enrolled_students master table (Requirement 1.1)
        if (!$this->collegeIdExistsInMasterList($collegeId)) {
            $errors[] = 'College ID not found. Only enrolled students may register.';
        }

        // 4. No duplicate account for this College ID (Requirement 1.2)
        if ($this->studentAccountExists($collegeId)) {
            $errors[] = 'An account already exists for this College ID.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // 5. Hash password with bcrypt cost 12 (Requirement 1.6)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // 6. Insert student record with is_active = 1 (Requirement 1.5)
        $stmt = $this->db->prepare(
            'INSERT INTO students
                (college_id, full_name, mobile, email, department, program, current_semester, password_hash, is_active)
             VALUES
                (:college_id, :full_name, :mobile, :email, :department, :program, :current_semester, :password_hash, 1)'
        );

        $stmt->execute([
            ':college_id'       => $collegeId,
            ':full_name'        => $fullName,
            ':mobile'           => $mobile,
            ':email'            => $email ?: null,
            ':department'       => $department ?: null,
            ':program'          => $program ?: null,
            ':current_semester' => $currentSemester,
            ':password_hash'    => $passwordHash,
        ]);

        // 7. Send welcome SMS (Requirement 1.7)
        $this->notification->sendWelcomeSms($mobile);

        return ['success' => true, 'errors' => []];
    }

    // -------------------------------------------------------------------------
    // Login / MFA
    // -------------------------------------------------------------------------

    /**
     * Initiate MFA login: validate credentials and dispatch OTP.
     *
     * Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 10.6, 10.8, 12.3
     *
     * @return array{success: bool, message: string}
     */
    public function login(string $rollNo, string $mobile): array
    {
        $rollNo = trim($rollNo);
        $mobile = trim($mobile);

        // Look up student by roll number (college_id)
        $stmt = $this->db->prepare(
            'SELECT id, mobile, is_active FROM students WHERE college_id = :roll_no LIMIT 1'
        );
        $stmt->execute([':roll_no' => $rollNo]);
        $student = $stmt->fetch();

        // Requirement 2.1 — unknown roll number
        if ($student === false) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Requirement 2.2 — mobile mismatch
        if ($student['mobile'] !== $mobile) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Requirement 2.3 — deactivated account
        if ((int) $student['is_active'] === 0) {
            return ['success' => false, 'message' => 'Account is deactivated'];
        }

        // Requirement 2.4 / 10.6 — OTP rate limit (max 3 per 10 minutes)
        $studentId = (int) $student['id'];
        if ($this->otpRequestCount($studentId, 10) >= 3) {
            return ['success' => false, 'message' => 'Too many OTP requests. Try after 10 minutes.'];
        }

        // Generate OTP, hash it, store in otp_tokens (Requirement 2.5, 10.8, 12.3)
        $otp     = $this->generateNumericOtp(6);
        $otpHash = password_hash($otp, PASSWORD_BCRYPT, ['cost' => 10]);

        $stmt = $this->db->prepare(
            'INSERT INTO otp_tokens (student_id, otp_hash, expires_at)
             VALUES (:student_id, :otp_hash, DATE_ADD(NOW(), INTERVAL 5 MINUTE))'
        );
        $stmt->execute([
            ':student_id' => $studentId,
            ':otp_hash'   => $otpHash,
        ]);

        // Deliver OTP via SMS
        $this->notification->sendOtp($mobile, $otp);

        return ['success' => true, 'message' => 'OTP sent to registered mobile'];
    }

    /**
     * Verify the submitted OTP and create a session on success.
     *
     * Requirements: 2.6, 2.7, 2.8, 2.9, 2.10, 10.4, 10.5, 12.4, 12.5
     *
     * @return array{success: bool, message: string}
     */
    public function verifyOtp(string $rollNo, string $otp): array
    {
        $rollNo = trim($rollNo);

        $stmt = $this->db->prepare(
            'SELECT id FROM students WHERE college_id = :roll_no LIMIT 1'
        );
        $stmt->execute([':roll_no' => $rollNo]);
        $student = $stmt->fetch();

        if ($student === false) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $studentId = (int) $student['id'];

        // Fetch latest unused, unexpired token
        $stmt = $this->db->prepare(
            'SELECT id, otp_hash FROM otp_tokens
             WHERE student_id = :student_id
               AND used = 0
               AND expires_at > NOW()
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute([':student_id' => $studentId]);
        $token = $stmt->fetch();

        // Requirement 2.6 / 12.5 — expired or missing token
        if ($token === false) {
            return ['success' => false, 'message' => 'OTP expired or invalid'];
        }

        // Requirement 2.7 — wrong OTP
        if (!password_verify($otp, $token['otp_hash'])) {
            return ['success' => false, 'message' => 'OTP expired or invalid'];
        }

        // Requirement 2.8 / 12.4 — mark token used
        $stmt = $this->db->prepare('UPDATE otp_tokens SET used = 1 WHERE id = :id');
        $stmt->execute([':id' => $token['id']]);

        // Requirement 2.10 / 10.4 — regenerate session ID on privilege change
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);

        // Requirement 2.8 — create session with student_id and role
        $_SESSION['student_id']    = $studentId;
        $_SESSION['role']          = 'student';
        $_SESSION['last_activity'] = time();

        return ['success' => true, 'message' => 'Login successful'];
    }

    /**
     * Destroy the current session.
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }

    /**
     * Check whether the current request has an authenticated student session.
     */
    public function isAuthenticated(): bool
    {
        return !empty($_SESSION['student_id']) && !empty($_SESSION['role']);
    }

    /**
     * Check whether the current session belongs to an admin user.
     */
    public function isAdmin(): bool
    {
        return $this->isAuthenticated() && $_SESSION['role'] === 'admin';
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Return true if $collegeId exists in the enrolled_students master table.
     * Uses a PDO prepared statement to prevent SQL injection (Requirement 10.3).
     */
    private function collegeIdExistsInMasterList(string $collegeId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM enrolled_students WHERE college_id = :college_id LIMIT 1'
        );
        $stmt->execute([':college_id' => $collegeId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Return true if a student account already exists for $collegeId.
     */
    private function studentAccountExists(string $collegeId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM students WHERE college_id = :college_id LIMIT 1'
        );
        $stmt->execute([':college_id' => $collegeId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Generate a cryptographically secure numeric OTP of the given length.
     *
     * Postconditions:
     * - Returns a string of exactly $length decimal digits (0–9).
     * - Uses random_int() — a CSPRNG source.
     *
     * Requirements: 12.1, 12.2
     */
    private function generateNumericOtp(int $length = 6): string
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= (string) random_int(0, 9);
        }
        return $otp;
    }

    /**
     * Count OTP requests made by $studentId within the last $minutes minutes.
     * Used to enforce the rate limit (max 3 per 10 minutes).
     *
     * Requirements: 2.4, 10.6
     */
    private function otpRequestCount(int $studentId, int $minutes): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM otp_tokens
             WHERE student_id = :student_id
               AND created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)'
        );
        $stmt->execute([
            ':student_id' => $studentId,
            ':minutes'    => $minutes,
        ]);

        return (int) $stmt->fetchColumn();
    }
}
