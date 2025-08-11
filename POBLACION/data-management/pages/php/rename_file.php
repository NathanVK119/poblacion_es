<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['file_id']) && !empty($_POST['new_name'])) {
    $file_id = (int)$_POST['file_id'];
    $new_name = trim($_POST['new_name']);
    $folder_id = !empty($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

    try {
        // Get current file info
        $stmt = $pdo->prepare("SELECT filename, filepath, folder_id FROM files WHERE id = ?");
        $stmt->execute([$file_id]);
        $file = $stmt->fetch();

        if (!$file) {
            die('File not found');
        }

        // Get file extension
        $extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
        $new_name = $new_name . '.' . $extension;

        // Function to generate unique filename
        function generateUniqueFileName($pdo, $base_name, $folder_id) {
            $counter = 1;
            $new_name = $base_name;
            $name_without_ext = pathinfo($base_name, PATHINFO_FILENAME);
            $extension = pathinfo($base_name, PATHINFO_EXTENSION);
            
            while (true) {
                // Check if filename exists in the same folder
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE filename = ? AND folder_id " . ($folder_id === null ? "IS NULL" : "= ?"));
                $params = [$new_name];
                if ($folder_id !== null) {
                    $params[] = $folder_id;
                }
                $stmt->execute($params);
                $exists = $stmt->fetchColumn();

                if (!$exists) {
                    break;
                }

                // If name exists, try with (1), (2), etc.
                $new_name = $name_without_ext . " (" . $counter . ")." . $extension;
                $counter++;
            }

            return $new_name;
        }

        // Generate unique filename
        $new_name = generateUniqueFileName($pdo, $new_name, $folder_id);

        // Get old and new file paths
        $old_path = __DIR__ . '/../../' . $file['filepath'];
        $new_path = __DIR__ . '/../../uploads/' . $new_name;

        // Rename the physical file
        if (file_exists($old_path)) {
            rename($old_path, $new_path);
        }

        // Update database record
        $stmt = $pdo->prepare("UPDATE files SET filename = ?, filepath = ? WHERE id = ?");
        $stmt->execute([$new_name, 'uploads/' . $new_name, $file_id]);

        // Redirect back
        header('Location: ../index.php' . ($folder_id ? '?folder_id=' . $folder_id : ''));
        exit;
    } catch (PDOException $e) {
        die('Error renaming file: ' . $e->getMessage());
    }
} else {
    die('Invalid request');
}
?> 