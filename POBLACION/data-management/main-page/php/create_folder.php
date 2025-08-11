<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_name'])) {
    $folder_name = trim($_POST['folder_name']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO folders (name, parent_id) VALUES (?, ?)");
        $stmt->execute([$folder_name, $parent_id]);
        
        // Redirect back
        header('Location: ../index.php' . ($parent_id ? '?folder_id=' . $parent_id : ''));
        exit;
    } catch (PDOException $e) {
        die('Error creating folder: ' . $e->getMessage());
    }
} else {
    die('Invalid request');
}
?> 