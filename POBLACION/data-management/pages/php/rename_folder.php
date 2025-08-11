<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_id']) && !empty($_POST['new_name'])) {
    $folder_id = (int)$_POST['folder_id'];
    $new_name = trim($_POST['new_name']);

    try {
        $stmt = $pdo->prepare("UPDATE folders SET name = ? WHERE id = ?");
        $stmt->execute([$new_name, $folder_id]);
        
        // Redirect back
        header('Location: ../index.php' . (isset($_POST['current_folder_id']) ? '?folder_id=' . $_POST['current_folder_id'] : ''));
        exit;
    } catch (PDOException $e) {
        die('Error renaming folder: ' . $e->getMessage());
    }
} else {
    die('Invalid request');
}
?> 