<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../shared-source/database-connection/connect.php';

// Get search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get current folder ID from URL (?folder_id=)
$folder_id = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;

// Get current folder details (if viewing inside a folder)
$current_folder = null;
if ($folder_id) {
    $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
    $stmt->execute([$folder_id]);
    $current_folder = $stmt->fetch();
}

// Get folders inside current folder with search
    if ($search) {
    // Search across all folders regardless of location
    $stmt = $pdo->prepare("SELECT * FROM folders WHERE name LIKE ? ORDER BY name");
    $stmt->execute(["%$search%"]);
    } else {
    if ($folder_id) {
        $stmt = $pdo->prepare("SELECT * FROM folders WHERE parent_id = ? ORDER BY name");
        $stmt->execute([$folder_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM folders WHERE parent_id IS NULL ORDER BY name");
        $stmt->execute();
    }
}
$folders = $stmt->fetchAll();

// Get files (show all files regardless of folder)
    if ($search) {
    // Show all files matching search
    $stmt = $pdo->prepare("SELECT * FROM files WHERE filename LIKE ? ORDER BY uploaded_at DESC");
    $stmt->execute(["%$search%"]);
    } else {
    // Only show files in the current folder
    if ($folder_id) {
        $stmt = $pdo->prepare("SELECT * FROM files WHERE folder_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$folder_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM files WHERE folder_id IS NULL ORDER BY uploaded_at DESC");
        $stmt->execute();
    }
}
$files = $stmt->fetchAll();

// Get total items for current folder
$total_items = count($folders) + count($files);
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../shared-source/css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', Arial, sans-serif;
            background: #f8f9fa;
        }
        .sidebar {
            width: 260px;
            background: #23395d;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar .sidebar-brand {
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar .nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar .nav-content {
            flex: 1;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 24px;
            transition: all 0.3s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .sidebar .badge.bg-info {
            background: #4062bb !important;
        }
        .sidebar .badge.bg-warning {
            background: #ffe066 !important;
            color: #333 !important;
        }
        .sidebar .back-to-portal {
            padding: 16px;
            background: rgba(0,0,0,0.1);
            margin-top: auto;
            position: sticky;
            bottom: 0;
            width: 100%;
        }
        .sidebar .btn-light {
            background: #fff;
            color: #23395d;
            border: none;
            font-weight: 600;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar .btn-light:hover {
            background: #e9ecef;
        }
        .sidebar .quick-stats {
            padding: 12px 16px;
            overflow: hidden;
        }
        .sidebar .quick-stats .d-flex {
            overflow: hidden;
        }
        .sidebar .quick-stats span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .main-content {
            margin-left: 260px;
            padding: 32px 32px 32px 32px;
            min-height: 100vh;
        }
        .top-bar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
            padding: 18px 24px;
            margin-bottom: 24px;
        }
        .search-bar {
            max-width: 300px;
            margin-left: auto;
        }
        .navigation-buttons .btn-group .btn {
            border-radius: 8px;
        }
        .stats-card {
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
            border: none;
            margin-bottom: 16px;
        }
        .stats-card .h5 {
            font-weight: 700;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
            border: none;
            margin-bottom: 24px;
        }
        .card-header {
            background: #f8f9fa;
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid #e9ecef;
        }
        .folder-item, .file-item {
            transition: background-color 0.2s;
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            background: #fff;
            box-shadow: 0 1px 3px rgba(44,62,80,0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .file-item {
            padding-right: 45px;
        }
        .folder-item:hover, .file-item:hover {
            background-color: #f1f3f6;
        }
        .folder-item a {
            color: inherit;
            text-decoration: none;
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
        }
        .folder-item a:hover {
            color: #4062bb;
        }
        .item-details {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .actions {
            display: flex;
            gap: 5px;
            flex-shrink: 0;
        }
        .actions .btn {
            border-radius: 8px;
            padding: 0.25rem 0.5rem;
        }
        .upload-area {
            border: 2px dashed #b0b8c1;
            border-radius: 12px;
            background: #f8f9fa;
            padding: 48px 0;
            color: #b0b8c1;
        }
        .quick-actions {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }
        .quick-actions .btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        @media (max-width: 991px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: 80px;
                flex-direction: row;
            }
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand p-4">
            <h4 class="m-0">
                <i class="fas fa-school me-2"></i>
                File Management
            </h4>
        </div>
        <div class="nav">
            <div class="nav-content">
                <a href="index.php" class="nav-link <?= !$folder_id ? 'active' : '' ?>">
                    <i class="fas fa-home me-2"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i>
                    <span>Upload File</span>
                </a>
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fas fa-folder-plus me-2"></i>
                    <span>New Folder</span>
                </a>
                <hr class="my-2 bg-white opacity-25">
                <div class="quick-stats">
                    <small class="d-block mb-2 opacity-75">Quick Stats</small>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Items:</span>
                        <span class="badge bg-info"><?= $total_items ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Location:</span>
                        <span class="badge bg-warning text-dark">
                            <?= $current_folder ? htmlspecialchars($current_folder['name']) : 'Home' ?>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Back to Portal Button -->
            <div class="back-to-portal">
                <a href="../../index.php" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Portal
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar rounded d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <?php
                    if ($folder_id && $current_folder) {
                        $path = [];
                        $current = $current_folder;
                        while ($current) {
                            array_unshift($path, $current);
                            if ($current['parent_id']) {
                                $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
                                $stmt->execute([$current['parent_id']]);
                                $current = $stmt->fetch();
                            } else {
                                break;
                            }
                        }
                        foreach ($path as $folder) {
                            if ($folder['id'] == $folder_id) {
                                echo '<li class="breadcrumb-item active">' . htmlspecialchars($folder['name']) . '</li>';
                            } else {
                                echo '<li class="breadcrumb-item"><a href="index.php?folder_id=' . $folder['id'] . '" class="text-decoration-none">' 
                                    . htmlspecialchars($folder['name']) . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ol>
            </nav>
            <form class="search-bar" method="GET" style="display: flex; gap: 8px;">
                <?php if ($folder_id): ?>
                    <input type="hidden" name="folder_id" value="<?= $folder_id ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search files & folders..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <?php if ($search): ?>
                    <a href="index.php<?= $folder_id ? '?folder_id=' . $folder_id : '' ?>" class="btn btn-outline-secondary" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons mb-4">
            <div class="btn-group">
                <?php if ($folder_id): ?>
                    <?php
                    // Get parent folder path
                    $path = [];
                    $current = $current_folder;
                    while ($current) {
                        array_unshift($path, $current);
                        if ($current['parent_id']) {
                            $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
                            $stmt->execute([$current['parent_id']]);
                            $current = $stmt->fetch();
                        } else {
                            break;
                        }
                    }
                    ?>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <?php if (count($path) > 1): ?>
                        <a href="index.php?folder_id=<?= $path[count($path)-2]['id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <button class="btn btn-outline-primary" onclick="window.history.back()">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <button class="btn btn-outline-primary" onclick="window.history.forward()">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stats-card folders">
                    <div class="row p-3">
                        <div class="col">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($folders) ?> Folders</div>
                            <?php if ($search): ?>
                                <small class="text-muted">Found in search</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card files">
                    <div class="row p-3">
                        <div class="col">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($files) ?> Files</div>
                            <?php if ($search): ?>
                                <small class="text-muted">Found in search</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Folders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-folder-open me-2"></i> Folders
                </h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fas fa-plus me-1"></i> New Folder
                </button>
            </div>
            <div class="card-body">
                <?php if ($folders): ?>
                    <?php foreach ($folders as $folder): ?>
                        <div class="folder-item d-flex justify-content-between align-items-center">
                            <a href="index.php?folder_id=<?= $folder['id'] ?>" class="text-decoration-none flex-grow-1 d-flex align-items-center">
                                <i class="fas fa-folder folder-icon me-2 text-warning"></i>
                                <div>
                                    <div><?= htmlspecialchars($folder['name']) ?></div>
                                    <?php if ($search): ?>
                                        <div class="item-details">
                                            <?php
                                            // Show folder path for search results
                                            $path = [];
                                            $current = $folder;
                                            while ($current && $current['parent_id']) {
                                                $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
                                                $stmt->execute([$current['parent_id']]);
                                                $current = $stmt->fetch();
                                                if ($current) array_unshift($path, $current['name']);
                                            }
                                            if ($path) echo 'Location: ' . implode(' / ', $path);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                            </div>
                            </a>
                            <div class="actions">
                                <button class="btn btn-primary btn-sm me-1" title="Rename Folder" 
                                    onclick="openRenameFolderModal(<?= $folder['id'] ?>, '<?= htmlspecialchars($folder['name']) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="php/delete_folder.php" method="POST" class="d-inline delete-folder-form" 
                                    data-folder-id="<?= $folder['id'] ?>">
                                    <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Folder">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No folders found</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Files -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i> Files
                </h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1"></i> Upload File
                </button>
            </div>
            <div class="card-body">
                <?php if ($files): ?>
                    <?php foreach ($files as $file): ?>
                        <div class="file-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file file-icon me-2 text-primary"></i>
                                <div>
                                <a href="<?= htmlspecialchars($file['filepath']) ?>" class="text-decoration-none" download>
                                    <?= htmlspecialchars($file['filename']) ?>
                                </a>
                                    <span class="item-details">
                                    <i class="fas fa-clock me-1"></i>
                                    <?= date('M j, Y g:i A', strtotime($file['uploaded_at'])) ?>
                                        <?php if ($search): ?>
                                            <br>
                                            <?php
                                            // Show file path for search results
                                            $path = [];
                                            if ($file['folder_id']) {
                                                $fid = $file['folder_id'];
                                                while ($fid) {
                                                    $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
                                                    $stmt->execute([$fid]);
                                                    $f = $stmt->fetch();
                                                    if ($f) {
                                                        array_unshift($path, $f['name']);
                                                        $fid = $f['parent_id'];
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            }
                                            if ($path) echo 'Location: ' . implode(' / ', $path);
                                            ?>
                                        <?php endif; ?>
                                </span>
                                </div>
                            </div>
                            <div class="actions">
                                <button class="btn btn-primary btn-sm me-1" title="Rename File" 
                                    onclick="openRenameFileModal(<?= $file['id'] ?>, '<?= htmlspecialchars(pathinfo($file['filename'], PATHINFO_FILENAME)) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="view_file.php?file_id=<?= $file['id'] ?>" class="btn btn-info btn-sm me-1" title="View File" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="view_file.php?file_id=<?= $file['id'] ?>&download=1" class="btn btn-primary btn-sm me-1" title="Download File">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="php/delete_file.php" method="POST" class="d-inline delete-file-form"
                                    onsubmit="return confirmDeleteFile();">
                                    <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete File">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="upload-area text-center py-5">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                        <p class="text-muted mb-0">
                            No files found. Drop files here or click to upload.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="quick-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal" title="Quick Upload">
                <i class="fas fa-upload"></i>
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createFolderModal" title="Quick Create Folder">
                <i class="fas fa-folder-plus"></i>
            </button>
            <?php if ($folder_id): ?>
                <a href="index.php" class="btn btn-info" title="Back to Home">
                    <i class="fas fa-home"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/create_folder.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Folder Name</label>
                            <input type="text" name="folder_name" class="form-control" required>
                            <input type="hidden" name="parent_id" value="<?= $folder_id ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/upload.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Choose File</label>
                            <input type="file" name="file" class="form-control" required>
                            <input type="hidden" name="folder_id" value="<?= $folder_id ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rename Folder Modal -->
    <div class="modal fade" id="renameFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/rename_folder.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="folder_id" id="rename_folder_id">
                        <input type="hidden" name="current_folder_id" value="<?= $folder_id ?>">
                        <div class="mb-3">
                            <label class="form-label">New Folder Name</label>
                            <input type="text" class="form-control" name="new_name" id="new_folder_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Rename</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Folder Confirmation Modal -->
    <div class="modal fade" id="deleteFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Folder Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the folder "<span id="folderName"></span>"?</p>
                    <p>This folder contains:</p>
                    <ul>
                        <li><span id="subfolderCount">0</span> subfolder(s)</li>
                        <li><span id="fileCount">0</span> file(s)</li>
                    </ul>
                    <p class="text-danger">Warning: Deleting this folder will permanently remove all contents!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="confirmDeleteForm" method="POST" action="php/delete_folder.php">
                        <input type="hidden" name="folder_id" id="confirmFolderId">
                        <input type="hidden" name="confirm" value="true">
                        <button type="submit" class="btn btn-danger">Delete Folder and All Contents</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename File Modal -->
    <div class="modal fade" id="renameFileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/rename_file.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="file_id" id="rename_file_id">
                        <input type="hidden" name="folder_id" value="<?= $folder_id ?>">
                        <div class="mb-3">
                            <label class="form-label">New File Name</label>
                            <input type="text" class="form-control" name="new_name" id="new_file_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Rename</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../shared-source/js/main.js"></script>
    <script>
    function openRenameFolderModal(folderId, currentName) {
        document.getElementById('rename_folder_id').value = folderId;
        document.getElementById('new_folder_name').value = currentName;
        new bootstrap.Modal(document.getElementById('renameFolderModal')).show();
    }

    // Handle folder deletion
    document.querySelectorAll('.delete-folder-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const folderId = this.dataset.folderId;
            
            // Check folder contents
            fetch('php/delete_folder.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `folder_id=${folderId}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Always show confirmation modal
                document.getElementById('subfolderCount').textContent = data.subfolder_count;
                document.getElementById('fileCount').textContent = data.file_count;
                document.getElementById('folderName').textContent = data.folder_name;
                document.getElementById('confirmFolderId').value = folderId;
                new bootstrap.Modal(document.getElementById('deleteFolderModal')).show();
            })
            .catch(error => {
                console.error('Error details:', error);
                alert('Error: ' + error.message);
            });
        });
    });

    function openRenameFileModal(fileId, currentName) {
        document.getElementById('rename_file_id').value = fileId;
        document.getElementById('new_file_name').value = currentName;
        new bootstrap.Modal(document.getElementById('renameFileModal')).show();
    }

    function confirmDeleteFile() {
        return confirm('Are you sure you want to delete this file? This action cannot be undone.');
    }
    </script>
</body>
</html>
