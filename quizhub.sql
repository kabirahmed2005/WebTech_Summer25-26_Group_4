-- QuizHub Database
-- Simple Online Quiz System (University Project)

CREATE DATABASE IF NOT EXISTS quizhub;
USE quizhub;

-- ---------------------------------------------------
-- Table: users
-- Stores students, teachers and the admin account
-- ---------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','admin') NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------
-- Table: quizzes
-- ---------------------------------------------------
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- ---------------------------------------------------
-- Table: questions
-- ---------------------------------------------------
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text VARCHAR(500) NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL, -- A, B, C or D
    marks INT NOT NULL DEFAULT 1,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- ---------------------------------------------------
-- Table: results
-- ---------------------------------------------------
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_marks INT NOT NULL,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- ---------------------------------------------------
-- Seed data
-- Default password for every seeded account is: 123456
-- ---------------------------------------------------
INSERT INTO users (full_name, username, password, role, status) VALUES
('Admin', 'admin', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'admin', 'approved'),
('Prof. Sarah Ahmed', 'Prof. Sarah Ahmed', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'teacher', 'approved'),
('Mr. Usman Tariq', 'Mr. Usman Tariq', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'teacher', 'approved'),
('Ali Hassan', 'Ali Hassan', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'student', 'approved'),
('Ayesha Malik', 'Ayesha Malik', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'student', 'approved'),
('Sara Khan', 'Sara Khan', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'teacher', 'pending'),
('Ali Hassan', 'ali.hassan2', '$2y$10$CwaUXIN9eVbxbO8c4jxZz.8TO575wQaENIksuTJbj1O.JIC.bfB5C', 'student', 'pending');

INSERT INTO quizzes (title, teacher_id) VALUES
('Web Programming Quiz', 2),
('Database Fundamentals', 3),
('HTML & CSS Basics', 2);

INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES
(1, 'Which language is used to style a web page?', 'HTML', 'CSS', 'PHP', 'JSON', 'B', 1),
(1, 'Which tag is used to link a CSS file in HTML?', '<style>', '<script>', '<link>', '<css>', 'C', 1),
(1, 'What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Preprocessed Hypertext Page', 'B', 1),
(1, 'Which symbol is used for PHP variables?', '#', '@', '&', '$', 'D', 1),
(1, 'AJAX is mainly used to communicate with the server without ___?', 'A database', 'Reloading the page', 'JavaScript', 'A browser', 'B', 1);

INSERT INTO results (student_id, quiz_id, score, total_marks) VALUES
(4, 1, 18, 20),
(5, 1, 12, 20);
