-- StudySwap Database Schema
-- This file contains the complete database structure for the StudySwap platform

-- Create database
CREATE DATABASE IF NOT EXISTS studyswap;
USE studyswap;

-- Level table
CREATE TABLE IF NOT EXISTS level (
    level_id INT PRIMARY KEY AUTO_INCREMENT,
    level_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sector table
CREATE TABLE IF NOT EXISTS sector (
    Sector_id INT PRIMARY KEY AUTO_INCREMENT,
    sector_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subject table
CREATE TABLE IF NOT EXISTS subject (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Level-Sector relationship table
CREATE TABLE IF NOT EXISTS level_sector (
    level_id INT,
    Sector_id INT,
    PRIMARY KEY (level_id, Sector_id),
    FOREIGN KEY (level_id) REFERENCES level(level_id) ON DELETE CASCADE,
    FOREIGN KEY (Sector_id) REFERENCES sector(Sector_id) ON DELETE CASCADE
);

-- Program table (combination of level, sector, and subject)
CREATE TABLE IF NOT EXISTS program (
    program_id INT PRIMARY KEY AUTO_INCREMENT,
    level_id INT NOT NULL,
    Sector_id INT NOT NULL,
    subject_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (level_id) REFERENCES level(level_id) ON DELETE CASCADE,
    FOREIGN KEY (Sector_id) REFERENCES sector(Sector_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subject(subject_id) ON DELETE CASCADE,
    UNIQUE KEY unique_program (level_id, Sector_id, subject_id)
);

-- Course table
CREATE TABLE IF NOT EXISTS course (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    program_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES program(program_id) ON DELETE CASCADE,
    INDEX idx_program (program_id),
    INDEX idx_created (created_at)
);

-- Document table
CREATE TABLE IF NOT EXISTS document (
    document_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    type ENUM('course', 'exercise', 'exam', 'notes', 'summary') NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE,
    INDEX idx_course (course_id),
    INDEX idx_type (type)
);

-- Comment table
CREATE TABLE IF NOT EXISTS comment (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    content TEXT NOT NULL,
    author_name VARCHAR(100) NOT NULL DEFAULT 'Anonymous',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE,
    INDEX idx_course (course_id),
    INDEX idx_created (created_at)
);

-- Insert sample data
INSERT IGNORE INTO level (level_name) VALUES 
('Primary'),
('Middle School'),
('High School');

INSERT IGNORE INTO sector (sector_name) VALUES 
('Science'),
('Literature'),
('Economics'),
('Technology'),
('Arts');

INSERT IGNORE INTO subject (subject_name) VALUES 
('Mathematics'),
('Physics and Chemistry'),
('Life and Earth Science'),
('Frensh'),
('English'),
('Arab'),
('Islamic Education'),
('Philosophy'),
('History and Geography'),
('Computer Science');

-- Create level-sector relationships
INSERT IGNORE INTO level_sector (level_id, Sector_id) VALUES 
-- Primary level
(1, 1), (1, 2), (1, 5),
-- Middle School
(2, 1), (2, 2), (2, 4), (2, 5),
-- High School  
(3, 1), (3, 2), (3, 3), (3, 4);

-- Create sample programs
INSERT IGNORE INTO program (level_id, Sector_id, subject_id) VALUES 
-- High School Science
(3, 1, 1), -- Mathematics
(3, 1, 2), -- Physics and Chemistry
(3, 1, 3), -- Life and Earth Science
(3, 1, 4), -- French
(3, 1, 5), -- English
(3, 1, 7), -- Islamic Education
(3, 1, 8), -- Philosophy

-- High School Literature
(3, 2, 4), -- French
(3, 2, 5), -- English
(3, 2, 6), -- Arab
(3, 2, 7), -- Islamic Education
(3, 2, 8), -- Philosophy
(3, 2, 9), -- History and Geography

-- Middle School Science
(2, 1, 1), -- Mathematics
(2, 1, 2), -- Physics and Chemistry
(2, 1, 3), -- Life and Earth Science
(2, 1, 4), -- French
(2, 1, 5), -- English
(2, 1, 6), -- Arab
(2, 1, 7); -- Islamic Education