<?php
require '../../../shared-source/database-connection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_name'])) {
    $folder_name = trim($_POST['folder_name']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    try {
        // Function to generate unique folder name
        function generateUniqueFolderName($pdo, $base_name, $parent_id) {
            $counter = 1;
            $new_name = $base_name;
            
            while (true) {
                // Check if folder name exists in the same parent
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM folders WHERE name = ? AND parent_id " . ($parent_id === null ? "IS NULL" : "= ?"));
                $params = [$new_name];
                if ($parent_id !== null) {
                    $params[] = $parent_id;
    }
                $stmt->execute($params);
                $exists = $stmt->fetchColumn();

                if (!$exists) {
                    break;
                }

                // If name exists, try with (1), (2), etc.
                $new_name = $base_name . " (" . $counter . ")";
                $counter++;
            }

            return $new_name;
        }

        // Generate unique folder name
        $folder_name = generateUniqueFolderName($pdo, $folder_name, $parent_id);

        // Insert the new folder
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