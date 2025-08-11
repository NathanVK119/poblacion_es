<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create new database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS poblacion");
    
    // Get all tables from poblacion_es if it exists
    try {
        $pdo->exec("USE poblacion_es");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Copy data from each table to the new database
        foreach ($tables as $table) {
            // Create table in new database with data
            $pdo->exec("CREATE TABLE IF NOT EXISTS poblacion.$table LIKE poblacion_es.$table");
            $pdo->exec("INSERT IGNORE INTO poblacion.$table SELECT * FROM poblacion_es.$table");
            echo "Migrated table: $table\n";
        }
        
        // Drop the old database
        $pdo->exec("DROP DATABASE poblacion_es");
        echo "Dropped old database: poblacion_es\n";
    } catch (PDOException $e) {
        if ($e->getCode() != 1049) { // 1049 is "Unknown database"
            throw $e;
        }
        echo "Note: poblacion_es database not found (this is okay if you're starting fresh)\n";
    }

    // Switch to the new database
    $pdo->exec("USE poblacion");
    
    // Clean up and optimize tables
    // Keep only necessary tables and add any missing columns
    
    // 1. Users table - keep it simple
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Students table - essential fields only
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

    // 3. Folders table - for file organization
    $pdo->exec("CREATE TABLE IF NOT EXISTS folders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        parent_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES folders(id) ON DELETE CASCADE
    )");

    // 4. Files table - for document management
    $pdo->exec("CREATE TABLE IF NOT EXISTS files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        filepath VARCHAR(255) NOT NULL,
        folder_id INT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE
    )");

    // Drop any unnecessary tables that might exist from the old system
    $unnecessaryTables = [
        'admin',
        'login_attempts',
        'user_logs',
        'settings',
        'temp_files',
        'file_categories',
        'file_shares',
        'user_preferences'
    ];

    foreach ($unnecessaryTables as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $table");
            echo "Dropped unnecessary table if existed: $table\n";
        } catch (PDOException $e) {
            // Ignore errors for non-existent tables
        }
    }

    // Optimize all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("OPTIMIZE TABLE $table");
        echo "Optimized table: $table\n";
    }

    echo "\nDatabase cleanup and optimization completed successfully!\n";
    echo "All data is now consolidated in the 'poblacion' database.\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?> 