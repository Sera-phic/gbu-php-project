-- Subjects/Courses table for semester registration

CREATE TABLE IF NOT EXISTS subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    credits TINYINT UNSIGNED NOT NULL DEFAULT 3,
    semester TINYINT UNSIGNED NOT NULL,
    department VARCHAR(100),
    is_elective BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_semester (semester),
    INDEX idx_department (department),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample subjects for different semesters
INSERT INTO subjects (code, name, credits, semester, department, is_elective, is_active) VALUES
-- Semester 1
('CS101', 'Introduction to Programming', 4, 1, 'Computer Science', FALSE, TRUE),
('MA101', 'Engineering Mathematics I', 4, 1, 'Mathematics', FALSE, TRUE),
('PH101', 'Engineering Physics', 3, 1, 'Physics', FALSE, TRUE),
('CH101', 'Engineering Chemistry', 3, 1, 'Chemistry', FALSE, TRUE),
('EE101', 'Basic Electrical Engineering', 3, 1, 'Electrical', FALSE, TRUE),

-- Semester 2
('CS102', 'Data Structures', 4, 2, 'Computer Science', FALSE, TRUE),
('MA102', 'Engineering Mathematics II', 4, 2, 'Mathematics', FALSE, TRUE),
('CS103', 'Digital Logic Design', 3, 2, 'Computer Science', FALSE, TRUE),
('ME101', 'Engineering Mechanics', 3, 2, 'Mechanical', FALSE, TRUE),

-- Semester 3
('CS201', 'Object Oriented Programming', 4, 3, 'Computer Science', FALSE, TRUE),
('CS202', 'Database Management Systems', 4, 3, 'Computer Science', FALSE, TRUE),
('CS203', 'Computer Organization', 3, 3, 'Computer Science', FALSE, TRUE),
('MA201', 'Discrete Mathematics', 3, 3, 'Mathematics', FALSE, TRUE),
('CS204', 'Operating Systems', 4, 3, 'Computer Science', FALSE, TRUE),

-- Semester 4
('CS301', 'Design and Analysis of Algorithms', 4, 4, 'Computer Science', FALSE, TRUE),
('CS302', 'Computer Networks', 4, 4, 'Computer Science', FALSE, TRUE),
('CS303', 'Software Engineering', 3, 4, 'Computer Science', FALSE, TRUE),
('CS304', 'Theory of Computation', 3, 4, 'Computer Science', FALSE, TRUE),

-- Semester 5
('CS401', 'Artificial Intelligence', 4, 5, 'Computer Science', FALSE, TRUE),
('CS402', 'Web Technologies', 3, 5, 'Computer Science', FALSE, TRUE),
('CS403', 'Compiler Design', 4, 5, 'Computer Science', FALSE, TRUE),
('CS404', 'Machine Learning', 3, 5, 'Computer Science', TRUE, TRUE),

-- Semester 6
('CS501', 'Cloud Computing', 3, 6, 'Computer Science', TRUE, TRUE),
('CS502', 'Mobile Application Development', 3, 6, 'Computer Science', TRUE, TRUE),
('CS503', 'Cyber Security', 3, 6, 'Computer Science', TRUE, TRUE),
('CS504', 'Big Data Analytics', 3, 6, 'Computer Science', TRUE, TRUE),
('CS505', 'Internet of Things', 3, 6, 'Computer Science', TRUE, TRUE),

-- Semester 7
('CS601', 'Blockchain Technology', 3, 7, 'Computer Science', TRUE, TRUE),
('CS602', 'Natural Language Processing', 3, 7, 'Computer Science', TRUE, TRUE),
('CS603', 'Computer Vision', 3, 7, 'Computer Science', TRUE, TRUE),
('CS604', 'Project Work I', 4, 7, 'Computer Science', FALSE, TRUE),

-- Semester 8
('CS701', 'Distributed Systems', 3, 8, 'Computer Science', TRUE, TRUE),
('CS702', 'DevOps and Automation', 3, 8, 'Computer Science', TRUE, TRUE),
('CS703', 'Project Work II', 6, 8, 'Computer Science', FALSE, TRUE);
