-- Master list of enrolled students (imported from university records)
-- Only students in this table can create accounts

CREATE TABLE IF NOT EXISTS enrolled_students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    college_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    program VARCHAR(100),
    current_semester TINYINT UNSIGNED,
    enrollment_year YEAR,
    status ENUM('active', 'inactive', 'graduated', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_college_id (college_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample enrolled students for testing
INSERT INTO enrolled_students (college_id, full_name, department, program, current_semester, enrollment_year, status) VALUES
('235/ucd/048', 'Test Student One', 'Computer Science', 'B.Tech', 3, 2023, 'active'),
('235/ucd/049', 'Test Student Two', 'Electronics', 'B.Tech', 5, 2022, 'active'),
('235/ucd/050', 'Test Student Three', 'Mechanical', 'B.Tech', 7, 2021, 'active'),
('ADMIN001', 'Admin User', 'Administration', 'Staff', 1, 2020, 'active');
