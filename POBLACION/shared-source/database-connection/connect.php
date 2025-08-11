<?php
$host = 'localhost';
$dbname = 'poblacion';
$username = 'root';
$password = '';

// PDO connection (for data-management)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist, create it
    if ($e->getCode() == 1049) {
        try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create the new database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            $pdo->exec("USE $dbname");

            // Create tables if they don't exist
            // Users table
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Poblacion table (Student Registration)
            $pdo->exec("CREATE TABLE IF NOT EXISTS poblacion (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lrn VARCHAR(20) UNIQUE,
                name VARCHAR(255) NOT NULL,
                sex VARCHAR(10),
                birthday DATE,
                age INT,
                mother_tongue VARCHAR(50),
                ip VARCHAR(50),
                religion VARCHAR(50),
                house_number VARCHAR(100),
                barangay VARCHAR(100),
                municipality VARCHAR(100),
                province VARCHAR(100),
                region VARCHAR(100),
                father VARCHAR(255),
                mother VARCHAR(255),
                guardian_name VARCHAR(255),
                relationship VARCHAR(50),
                contact VARCHAR(20),
                learning_modality VARCHAR(50),
                sy VARCHAR(20),
                grade VARCHAR(20),
                section VARCHAR(50),
                adviser VARCHAR(255),
                status VARCHAR(50),
                remarks TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Students table
            $pdo->exec("CREATE TABLE IF NOT EXISTS students (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id VARCHAR(20) UNIQUE,
                first_name VARCHAR(50) NOT NULL,
                middle_name VARCHAR(50),
                last_name VARCHAR(50) NOT NULL,
                birth_date DATE,
                gender VARCHAR(10),
                address TEXT,
                contact_number VARCHAR(20),
                email VARCHAR(100),
                parent_guardian VARCHAR(100),
                parent_contact VARCHAR(20),
                enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status VARCHAR(20) DEFAULT 'Active'
            )");

            // Files table
            $pdo->exec("CREATE TABLE IF NOT EXISTS files (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL,
                filepath VARCHAR(255) NOT NULL,
                folder_id INT,
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE
            )");

            // Folders table
            $pdo->exec("CREATE TABLE IF NOT EXISTS folders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                parent_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES folders(id) ON DELETE CASCADE
            )");

        } catch(PDOException $e2) {
            die("Database creation failed: " . $e2->getMessage());
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

// MySQLi connection (for student-registration)
$con = new mysqli($host, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?> 