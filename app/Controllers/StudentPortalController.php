<?php

declare(strict_types=1);

namespace App\Controllers;

use PDO;

/**
 * StudentPortalController — renders the student dashboard, semester history,
 * and current registration/payment status.
 *
 * Requirements: 3.1, 3.2, 3.3, 3.4, 13.4
 */
class StudentPortalController
{
    private PDO $db;

    /** Cache TTL in seconds (5 minutes). Requirement 3.4, 13.4 */
    private const CACHE_TTL = 300;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    /**
     * Return aggregated dashboard data for the given student.
     *
     * Caches the result in the PHP session for CACHE_TTL seconds to reduce
     * DB load during peak registration periods (Requirement 3.4, 13.4).
     *
     * @return array{student: array, semester_history: list<array>, registration_status: array}
     */
    public function dashboard(int $studentId): array
    {
        $cacheKey = "dashboard_{$studentId}";

        // Return cached data if still fresh
        if ($this->isCacheValid($cacheKey)) {
            return $_SESSION['cache'][$cacheKey]['data'];
        }

        $student         = $this->fetchStudent($studentId);
        $semesterHistory = $this->getSemesterHistory($studentId);
        $regStatus       = $this->getRegistrationStatus($studentId, (int) ($student['current_semester'] ?? 0));

        $data = [
            'student'             => $student,
            'semester_history'    => $semesterHistory,
            'registration_status' => $regStatus,
        ];

        $this->cacheSet($cacheKey, $data);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Semester history
    // -------------------------------------------------------------------------

    /**
     * Return all registration records for the student, ordered by semester.
     *
     * Requirements: 3.1, 3.2
     *
     * @return list<array>
     */
    public function getSemesterHistory(int $studentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.id,
                    r.semester_id,
                    r.academic_year,
                    r.status        AS registration_status,
                    r.submitted_at,
                    p.payment_method,
                    p.verification_status AS payment_status,
                    p.amount
             FROM   registrations r
             LEFT JOIN payments p ON p.registration_id = r.id
             WHERE  r.student_id = :student_id
             ORDER  BY r.semester_id ASC, r.created_at DESC'
        );
        $stmt->execute([':student_id' => $studentId]);

        return $stmt->fetchAll();
    }

    // -------------------------------------------------------------------------
    // Current registration status
    // -------------------------------------------------------------------------

    /**
     * Return the registration and payment status for the student's current semester.
     *
     * Requirement 3.2
     *
     * @return array{registration: array|null, payment: array|null}
     */
    public function getRegistrationStatus(int $studentId, int $semesterId): array
    {
        if ($semesterId === 0) {
            return ['registration' => null, 'payment' => null];
        }

        $academicYear = $this->currentAcademicYear();

        $stmt = $this->db->prepare(
            'SELECT r.id,
                    r.status,
                    r.submitted_at,
                    p.payment_method,
                    p.verification_status AS payment_status,
                    p.amount,
                    p.transaction_ref
             FROM   registrations r
             LEFT JOIN payments p ON p.registration_id = r.id
             WHERE  r.student_id   = :student_id
               AND  r.semester_id  = :semester_id
               AND  r.academic_year = :academic_year
             ORDER  BY r.created_at DESC
             LIMIT  1'
        );
        $stmt->execute([
            ':student_id'    => $studentId,
            ':semester_id'   => $semesterId,
            ':academic_year' => $academicYear,
        ]);

        $row = $stmt->fetch();

        return [
            'registration' => $row ?: null,
            'payment'      => $row ? [
                'method'              => $row['payment_method'],
                'verification_status' => $row['payment_status'],
                'amount'              => $row['amount'],
                'transaction_ref'     => $row['transaction_ref'],
            ] : null,
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function fetchStudent(int $studentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, college_id, full_name, mobile, email, department, program, current_semester
             FROM   students
             WHERE  id = :id
             LIMIT  1'
        );
        $stmt->execute([':id' => $studentId]);

        return $stmt->fetch() ?: [];
    }

    private function currentAcademicYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        // Academic year starts in July
        if ($month >= 7) {
            return $year . '-' . ($year + 1);
        }
        return ($year - 1) . '-' . $year;
    }

    private function isCacheValid(string $key): bool
    {
        if (empty($_SESSION['cache'][$key])) {
            return false;
        }
        return (time() - $_SESSION['cache'][$key]['ts']) < self::CACHE_TTL;
    }

    private function cacheSet(string $key, mixed $data): void
    {
        $_SESSION['cache'][$key] = ['ts' => time(), 'data' => $data];
    }
}
