<?php
require '../../shared-source/database-connection/connect.php';

if (!isset($_GET['file_id'])) {
    die('File ID is required');
}

$file_id = (int)$_GET['file_id'];
$force_download = isset($_GET['download']) && $_GET['download'] == 1;

try {
    // Get file details
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch();

    if (!$file) {
        die('File not found');
    }

    $filepath = __DIR__ . '/../' . $file['filepath'];
    if (!file_exists($filepath)) {
        die('File does not exist on server');
    }

    // Get file extension
    $extension = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));

    // Set content type based on file extension
    $content_types = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain'
    ];

    // Clear any previous output
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Set headers
    header('Content-Type: ' . ($content_types[$extension] ?? 'application/octet-stream'));
    
    // If force download is requested or file type is not viewable, force download
    if ($force_download || !in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'])) {
        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
    } else {
        header('Content-Disposition: inline; filename="' . $file['filename'] . '"');
    }

    // Set file size
    header('Content-Length: ' . filesize($filepath));

    // Output file
    readfile($filepath);
    exit;
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?> 