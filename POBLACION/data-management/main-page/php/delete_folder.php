<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_id'])) {
    $folder_id = (int)$_POST['folder_id'];

    try {
        // Get folder info for redirect
        $stmt = $pdo->prepare("SELECT parent_id FROM folders WHERE id = ?");
        $stmt->execute([$folder_id]);
        $folder = $stmt->fetch();
        $parent_id = $folder ? $folder['parent_id'] : null;

        // Start transaction
        $pdo->beginTransaction();

        // Delete all files in this folder
        $stmt = $pdo->prepare("SELECT filepath FROM files WHERE folder_id = ?");
        $stmt->execute([$folder_id]);
        $files = $stmt->fetchAll();

        foreach ($files as $file) {
            $filepath = '../' . $file['filepath'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        // Delete files from database
        $stmt = $pdo->prepare("DELETE FROM files WHERE folder_id = ?");
        $stmt->execute([$folder_id]);

        // Delete the folder
        $stmt = $pdo->prepare("DELETE FROM folders WHERE id = ?");
        $stmt->execute([$folder_id]);

        // Commit transaction
        $pdo->commit();

        // Redirect back
        header('Location: ../index.php' . ($parent_id ? '?folder_id=' . $parent_id : ''));
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die('Error deleting folder: ' . $e->getMessage());
    }
} else {
    die('Invalid request');
}
?> 