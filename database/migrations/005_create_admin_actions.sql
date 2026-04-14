-- Migration 005: Create admin_actions table
CREATE TABLE IF NOT EXISTS admin_actions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id INT UNSIGNED NOT NULL,
    admin_id        INT UNSIGNED NOT NULL,
    action          ENUM('approved','rejected') NOT NULL,
    notes           TEXT,
    acted_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id)
);
