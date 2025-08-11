<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $folder_id = !empty($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('File upload failed with error code: ' . $file['error']);
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . '/../../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $filename = $file['name'];
    $unique_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $filepath = $upload_dir . $unique_filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO files (folder_id, filename, filepath) VALUES (?, ?, ?)");
            $stmt->execute([$folder_id, $filename, 'uploads/' . $unique_filename]);
            
            // Redirect back with success
            header('Location: ../index.php' . ($folder_id ? '?folder_id=' . $folder_id : ''));
            exit;
        } catch (PDOException $e) {
            // If database insert fails, delete the uploaded file
            unlink($filepath);
            die('Error saving file information: ' . $e->getMessage());
        }
    } else {
        die('Error moving uploaded file');
    }
} else {
    die('Invalid request');
}
?>
