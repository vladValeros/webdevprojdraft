CREATE DATABASE random_events;

USE student_events;

-- Users table for storing admin and officer accounts
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer') NOT NULL
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL
);

-- Attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    student_number VARCHAR(20) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    time_in TIME NOT NULL,
    time_out TIME NOT NULL,
    attendance_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year_level INT NOT NULL
);

-- Students being linked to Attendance table
ALTER TABLE attendance ADD COLUMN student_id INT,
ADD CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id);

--Inserting the admin
INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES (NULL, 'admin', 'admin', 'admin');

--Inserting the students
INSERT INTO `students` (`id`, `student_number`, `name`, `course`, `year_level`) VALUES (NULL, '202300727', 'Ethan Wayne Cassion', 'Computer Science', '2');

INSERT INTO `students` (`id`, `student_number`, `name`, `course`, `year_level`) VALUES (NULL, '202301754', 'Eros Denz Etac', 'Computer Science', '2');

INSERT INTO `students` (`id`, `student_number`, `name`, `course`, `year_level`) VALUES (NULL, '202301224', 'Jasmin Majid', 'Computer Science', '2');

INSERT INTO `students` (`id`, `student_number`, `name`, `course`, `year_level`) VALUES (NULL, '202302172', 'Paul Seupon', 'Computer Science', '2');
