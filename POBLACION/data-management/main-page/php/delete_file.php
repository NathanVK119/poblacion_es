<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['file_id'])) {
    $file_id = (int)$_POST['file_id'];

    try {
        // Get file info
        $stmt = $pdo->prepare("SELECT folder_id, filepath FROM files WHERE id = ?");
        $stmt->execute([$file_id]);
        $file = $stmt->fetch();

        if ($file) {
            // Delete physical file
            $filepath = '../' . $file['filepath'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
            $stmt->execute([$file_id]);

            // Redirect back
            header('Location: ../index.php' . ($file['folder_id'] ? '?folder_id=' . $file['folder_id'] : ''));
            exit;
        }
    } catch (PDOException $e) {
        die('Error deleting file: ' . $e->getMessage());
    }
} else {
    die('Invalid request');
}
?> 