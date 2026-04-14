-- Migration 004: Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id     INT UNSIGNED NOT NULL UNIQUE,
    student_id          INT UNSIGNED NOT NULL,
    amount              DECIMAL(10,2) NOT NULL,
    payment_method      ENUM('upi','razorpay','bank_transfer') NOT NULL,
    transaction_ref     VARCHAR(100),              -- gateway txn ID or bank UTR
    bank_name           VARCHAR(100),
    account_holder      VARCHAR(100),
    transfer_date       DATE,
    transfer_amount     DECIMAL(10,2),
    receipt_path        VARCHAR(255),              -- uploaded receipt file path
    verification_status ENUM('pending','verified','failed') DEFAULT 'pending',
    verified_at         TIMESTAMP NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id),
    FOREIGN KEY (student_id)      REFERENCES students(id)
);

-- Index for Accounts_DB cross-reference queries on transfer date
CREATE INDEX IF NOT EXISTS idx_payments_transfer_date ON payments (transfer_date);
