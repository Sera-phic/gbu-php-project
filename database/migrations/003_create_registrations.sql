-- Migration 003: Create registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id      INT UNSIGNED NOT NULL,
    semester_id     TINYINT UNSIGNED NOT NULL,
    academic_year   VARCHAR(9)   NOT NULL,           -- e.g. "2024-2025"
    subjects        JSON         NOT NULL,            -- array of subject codes enrolled
    hostel_required TINYINT(1)   DEFAULT 0,
    transport       VARCHAR(50),
    remarks         TEXT,
    status          ENUM(
                        'draft',
                        'pending_payment',
                        'payment_submitted',
                        'payment_verified',
                        'approved',
                        'rejected'
                    ) DEFAULT 'draft',
    submitted_at    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Index for per-student registration lookups
CREATE INDEX IF NOT EXISTS idx_registrations_student_id
    ON registrations (student_id);

-- Composite index for duplicate-registration eligibility check
CREATE INDEX IF NOT EXISTS idx_registrations_student_semester_year
    ON registrations (student_id, semester_id, academic_year);
