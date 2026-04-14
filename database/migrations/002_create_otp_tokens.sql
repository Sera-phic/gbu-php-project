-- Migration 002: Create otp_tokens table
CREATE TABLE IF NOT EXISTS otp_tokens (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    otp_hash   VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP    NOT NULL,
    used       TINYINT(1)   DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Index for rate-limit queries (count recent tokens per student)
CREATE INDEX IF NOT EXISTS idx_otp_tokens_student_id ON otp_tokens (student_id);

-- Index for cron-based purge of expired tokens
CREATE INDEX IF NOT EXISTS idx_otp_tokens_expires_at ON otp_tokens (expires_at);
