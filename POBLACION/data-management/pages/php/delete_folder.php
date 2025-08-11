<?php
require '../../../shared-source/database-connection/connect.php';

// Set proper content type for JSON responses
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_id'])) {
    $folder_id = (int)$_POST['folder_id'];

    try {
        // Get folder info for redirect
        $stmt = $pdo->prepare("SELECT parent_id FROM folders WHERE id = ?");
        $stmt->execute([$folder_id]);
        $folder = $stmt->fetch();
        
        if (!$folder) {
            echo json_encode(['error' => 'Folder not found']);
            exit;
        }
        
        $parent_id = $folder['parent_id'];

        // Check if this is a confirmation request
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
        // Start transaction
        $pdo->beginTransaction();

            // Function to recursively delete folder and its contents
            function deleteFolder($pdo, $folder_id) {
                // Get all subfolders
                $stmt = $pdo->prepare("SELECT id FROM folders WHERE parent_id = ?");
                $stmt->execute([$folder_id]);
                $subfolders = $stmt->fetchAll();

                // Recursively delete subfolders
                foreach ($subfolders as $subfolder) {
                    deleteFolder($pdo, $subfolder['id']);
                }

        // Delete all files in this folder
        $stmt = $pdo->prepare("SELECT filepath FROM files WHERE folder_id = ?");
        $stmt->execute([$folder_id]);
        $files = $stmt->fetchAll();

        foreach ($files as $file) {
            $filepath = __DIR__ . '/../../' . $file['filepath'];
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
            }

            // Delete the folder and all its contents
            deleteFolder($pdo, $folder_id);

        // Commit transaction
        $pdo->commit();

        // Redirect back
        header('Location: ../index.php' . ($parent_id ? '?folder_id=' . $parent_id : ''));
        exit;
        } else {
            // Check if folder has contents
            $stmt = $pdo->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM folders WHERE parent_id = ?) as subfolder_count,
                    (SELECT COUNT(*) FROM files WHERE folder_id = ?) as file_count,
                    (SELECT name FROM folders WHERE id = ?) as folder_name
            ");
            $stmt->execute([$folder_id, $folder_id, $folder_id]);
            $counts = $stmt->fetch();

            // Always return folder contents info, even for empty folders
            echo json_encode([
                'has_contents' => true,
                'subfolder_count' => (int)$counts['subfolder_count'],
                'file_count' => (int)$counts['file_count'],
                'folder_name' => $counts['folder_name']
            ]);
            exit;
        }
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        }
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}
?> 