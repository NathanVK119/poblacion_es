<?php
require 'connect.php';

// Create users table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Check if admin user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute(['admin']);
$admin = $stmt->fetch();

// If admin doesn't exist, create it
if (!$admin) {
    $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hashedPassword, 'admin']);
    echo "Admin user created successfully!\n";
} else {
    echo "Admin user already exists!\n";
}

echo "Setup completed successfully!\n";
?> 