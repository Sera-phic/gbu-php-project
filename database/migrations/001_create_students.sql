-- Migration 001: Create students table
CREATE TABLE IF NOT EXISTS students (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    college_id       VARCHAR(20)  NOT NULL UNIQUE,   -- Roll No / Registration No
    full_name        VARCHAR(100) NOT NULL,
    mobile           VARCHAR(15)  NOT NULL UNIQUE,
    email            VARCHAR(100),
    department       VARCHAR(100),
    program          VARCHAR(100),
    current_semester TINYINT UNSIGNED,
    password_hash    VARCHAR(255) NOT NULL,
    is_active        TINYINT(1)   DEFAULT 1,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- college_id is already covered by the UNIQUE index above.
-- Explicit named index for clarity and Accounts_DB cross-reference queries.
CREATE INDEX IF NOT EXISTS idx_students_college_id ON students (college_id);
