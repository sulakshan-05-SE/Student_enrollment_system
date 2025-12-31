<?php
include 'db.php';

// 1. Add phone column to students table if it doesn't exist
$check_col = $conn->query("SHOW COLUMNS FROM students LIKE 'phone'");
if ($check_col->num_rows == 0) {
    if ($conn->query("ALTER TABLE students ADD COLUMN phone VARCHAR(20) AFTER email")) {
        echo "Added 'phone' column to 'students' table.<br>";
    } else {
        echo "Error adding 'phone' column: " . $conn->error . "<br>";
    }
} else {
    echo "'phone' column already exists in 'students' table.<br>";
}

// 2. Update Stored Procedure sp_add_student
$sql_drop = "DROP PROCEDURE IF EXISTS sp_add_student";
$conn->query($sql_drop);

$sql_create = "CREATE PROCEDURE sp_add_student(
    IN p_first_name VARCHAR(50), 
    IN p_last_name VARCHAR(50), 
    IN p_email VARCHAR(100), 
    IN p_phone VARCHAR(20),
    IN p_dob DATE, 
    IN p_age INT, 
    IN p_address TEXT
)
BEGIN
    INSERT INTO students (first_name, last_name, email, phone, dob, age, address) 
    VALUES (p_first_name, p_last_name, p_email, p_phone, p_dob, p_age, p_address);
END";

if ($conn->query($sql_create) === TRUE) {
    echo "Stored Procedure sp_add_student updated successfully.<br>";
} else {
    echo "Error updating stored procedure: " . $conn->error . "<br>";
}

echo "Schema update completed.";
