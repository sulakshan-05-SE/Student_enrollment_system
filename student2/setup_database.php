<?php
include 'db.php';

// Drop existing tables to reset schema (Clean slate for development)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS enrollments");
$conn->query("DROP TABLE IF EXISTS courses");
$conn->query("DROP TABLE IF EXISTS students");
$conn->query("DROP TABLE IF EXISTS teachers");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$tables = [
    "CREATE TABLE teachers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20)
    )",
    "CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        dob DATE,
        age INT,
        address TEXT
    )",
    "CREATE TABLE courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        fees DECIMAL(10, 2) DEFAULT 0.00,
        duration VARCHAR(50),
        teacher_id INT,
        FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
    )",
    "CREATE TABLE enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        UNIQUE(student_id, course_id)
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Seed Data
$conn->query("INSERT INTO teachers (first_name, last_name, email, phone) VALUES 
    ('John', 'Doe', 'john@example.com', '1234567890'), 
    ('Jane', 'Smith', 'jane@example.com', '0987654321')");

// Note: Inserting age manually for seed data, though typically calculated from DOB
$conn->query("INSERT INTO students (first_name, last_name, email, dob, age, address) VALUES 
    ('Alice', 'Johnson', 'alice@example.com', '2000-01-01', 25, '123 Maple St, Springfield'), 
    ('Bob', 'Brown', 'bob@example.com', '1999-05-15', 26, '456 Oak Ave, Metropolis')");

// Get IDs to be safe
$t_res = $conn->query("SELECT id FROM teachers LIMIT 2");
$t1 = $t_res->fetch_assoc()['id'];
$t2 = $t_res->fetch_assoc()['id'];

$conn->query("INSERT INTO courses (name, description, fees, duration, teacher_id) VALUES 
    ('Mathematics', 'Algebra and Calculus', 500.00, '3 Months', $t1), 
    ('Physics', 'Mechanics and Waves', 450.00, '4 Months', $t2)");

$s_res = $conn->query("SELECT id FROM students LIMIT 2");
$s1 = $s_res->fetch_assoc()['id'];
$s2 = $s_res->fetch_assoc()['id'];

$c_res = $conn->query("SELECT id FROM courses LIMIT 2");
$c1 = $c_res->fetch_assoc()['id'];
$c2 = $c_res->fetch_assoc()['id'];


// Create Stored Procedures
$procedures = [
    "DROP PROCEDURE IF EXISTS sp_add_student",
    "CREATE PROCEDURE sp_add_student(
        IN p_first_name VARCHAR(50), 
        IN p_last_name VARCHAR(50), 
        IN p_email VARCHAR(100), 
        IN p_dob DATE, 
        IN p_age INT, 
        IN p_address TEXT
    )
    BEGIN
        INSERT INTO students (first_name, last_name, email, dob, age, address) 
        VALUES (p_first_name, p_last_name, p_email, p_dob, p_age, p_address);
    END",

    "DROP PROCEDURE IF EXISTS sp_enroll_student",
    "CREATE PROCEDURE sp_enroll_student(
        IN p_student_id INT,
        IN p_course_id INT
    )
    BEGIN
        INSERT INTO enrollments (student_id, course_id) VALUES (p_student_id, p_course_id);
    END",

    "DROP PROCEDURE IF EXISTS sp_get_enrollment_report",
    "CREATE PROCEDURE sp_get_enrollment_report()
    BEGIN
        SELECT e.id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
               c.name as course_name, 
               CONCAT(t.first_name, ' ', t.last_name) as teacher_name, 
               e.enrollment_date 
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.id
        INNER JOIN courses c ON e.course_id = c.id
        INNER JOIN teachers t ON c.teacher_id = t.id
        ORDER BY e.enrollment_date DESC;
    END"
];

foreach ($procedures as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Procedure created successfully.<br>";
    } else {
        echo "Error creating procedure: " . $conn->error . "<br>";
    }
}

$conn->query("INSERT INTO enrollments (student_id, course_id) VALUES ($s1, $c1), ($s1, $c2), ($s2, $c1)");

echo "Database updated with Stored Procedures and Inner Join logic. <a href='index.php'>Go to Home</a>";

$conn->close();
?>
