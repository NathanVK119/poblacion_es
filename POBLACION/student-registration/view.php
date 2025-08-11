<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';//database connection path

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';
$sy = isset($_GET['sy']) ? $_GET['sy'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';

// Build the WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(lrn LIKE ? OR name LIKE ? OR birthday LIKE ? OR age LIKE ? OR mother_tongue LIKE ? OR ip LIKE ? OR religion LIKE ? OR house_number LIKE ? OR barangay LIKE ? OR municipality LIKE ? OR province LIKE ? OR region LIKE ? OR father LIKE ? OR mother LIKE ? OR guardian_name LIKE ? OR relationship LIKE ? OR contact LIKE ? OR learning_modality LIKE ? OR adviser LIKE ? OR remarks LIKE ? OR section LIKE ?)";
    $search_param = "%$search%";
    for ($i = 0; $i < 21; $i++) {
        $params[] = $search_param;
    }
}

if (!empty($grade)) {
    $where_conditions[] = "grade = ?";
    $params[] = $grade;
}

if (!empty($sy)) {
    $where_conditions[] = "sy = ?";
    $params[] = $sy;
}

if (!empty($status)) {
    $where_conditions[] = "status = ?";
    $params[] = $status;
}

if (!empty($gender) && $gender !== 'total') {
    $where_conditions[] = "sex = ?";
    $params[] = $gender;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Pagination
$limit = 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Count total records with search
$count_query = "SELECT COUNT(*) as total FROM poblacion $where_clause";
$stmt = mysqli_prepare($con, $count_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
}
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get records with search and pagination
$sql = "SELECT * FROM poblacion WHERE 1=1";
$params = [];
$types = '';

// Add search conditions
if (!empty($search)) {
    $search = mysqli_real_escape_string($con, $search);
    $sql .= " AND (lrn LIKE ? OR name LIKE ? OR barangay LIKE ? OR municipality LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}
if (!empty($grade)) {
    $sql .= " AND grade = ?";
    $params[] = $grade;
    $types .= 's';
}
if (!empty($sy)) {
    $sql .= " AND sy = ?";
    $params[] = $sy;
    $types .= 's';
}
if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= 's';
}
if (!empty($gender)) {
    $sql .= " AND sex = ?";
    $params[] = $gender;
    $types .= 's';
}

// Order by status and created_at timestamp
$sql .= " ORDER BY 
    CASE 
        WHEN status = 'New Student' THEN 1
        WHEN status = 'Old Student' THEN 2
        WHEN status = 'Transfer In' THEN 3
        WHEN status = 'Transfer Out' THEN 4
        WHEN status = 'Returnee' THEN 5
        WHEN status = 'Dropped Out' THEN 6
        WHEN status = 'Graduated' THEN 7
        ELSE 8
    END,
    created_at DESC
    LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($con, $sql);
if (!empty($params)) {
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    mysqli_stmt_bind_param($stmt, $types, ...$params);
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poblacion Elementary School Form Records</title>
    <link rel="stylesheet" href="css/view.css">
    <style>
    .student-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #ddd;
    }

    .student-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #ddd;
    }

    .student-picture img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-picture {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }

    .no-picture i {
        font-size: 64px;
        color: #ccc;
    }

    .student-basic-info {
        flex: 1;
    }

    .student-basic-info h2 {
        margin: 0 0 0.5rem 0;
        color: #333;
    }

    .student-basic-info p {
        margin: 0.25rem 0;
        color: #666;
    }

    @media print {
        .student-picture {
            width: 100px;
            height: 100px;
        }
        
        .no-picture i {
            font-size: 48px;
        }
    }
    </style>
</head>
<body>

<header>
    <div class="con-up">
        <div class="con-down">
            <h1>Poblacion Elementary School Form Records</h1>
        </div>
        <div class="btn-up">
            <button onclick="window.print()">Print</button>
            <button id="exportBtn">Export to Excel</button>
            <a href="home.php" class="back-btn">Home</a>
        </div>
    </div>
    <div class="search-container">
        <div class="con-input">
            <input type="text" id="searchInput" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">

            <select id="gradeFilter">
                <option value="">Grade Level</option>
                <option value="KINDER" <?php echo $grade === 'KINDER' ? 'selected' : ''; ?>>KINDER</option>
                <option value="GRADE 1" <?php echo $grade === 'GRADE 1' ? 'selected' : ''; ?>>GRADE 1</option>
                <option value="GRADE 2" <?php echo $grade === 'GRADE 2' ? 'selected' : ''; ?>>GRADE 2</option>
                <option value="GRADE 3" <?php echo $grade === 'GRADE 3' ? 'selected' : ''; ?>>GRADE 3</option>
                <option value="GRADE 4" <?php echo $grade === 'GRADE 4' ? 'selected' : ''; ?>>GRADE 4</option>
                <option value="GRADE 5" <?php echo $grade === 'GRADE 5' ? 'selected' : ''; ?>>GRADE 5</option>
                <option value="GRADE 6" <?php echo $grade === 'GRADE 6' ? 'selected' : ''; ?>>GRADE 6</option>
                <option value="GRADE 7" <?php echo $grade === 'GRADE 7' ? 'selected' : ''; ?>>GRADE 7</option>
                <option value="GRADE 8" <?php echo $grade === 'GRADE 8' ? 'selected' : ''; ?>>GRADE 8</option>
                <option value="GRADE 9" <?php echo $grade === 'GRADE 9' ? 'selected' : ''; ?>>GRADE 9</option>
                <option value="GRADE 10" <?php echo $grade === 'GRADE 10' ? 'selected' : ''; ?>>GRADE 10</option>
            </select>

            <select id="syFilter">
                <option value="">School Year</option>
            </select>

            <select id="statusFilter">
                <option value="">Status</option>
                <option value="New Student" <?php echo $status === 'New Student' ? 'selected' : ''; ?>>New Student</option>
                <option value="Old Student" <?php echo $status === 'Old Student' ? 'selected' : ''; ?>>Old Student</option>
                <option value="Transfer In" <?php echo $status === 'Transfer In' ? 'selected' : ''; ?>>Transfer In</option>
                <option value="Transfer Out" <?php echo $status === 'Transfer Out' ? 'selected' : ''; ?>>Transfer Out</option>
                <option value="Returnee" <?php echo $status === 'Returnee' ? 'selected' : ''; ?>>Returnee</option>
                <option value="Dropped Out" <?php echo $status === 'Dropped Out' ? 'selected' : ''; ?>>Dropped Out</option>
                <option value="Graduated" <?php echo $status === 'Graduated' ? 'selected' : ''; ?>>Graduated</option>
            </select>

            <select id="genderFilter">
                <option value="">Gender</option>
                <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Female</option>
                <option value="total" <?php echo $gender === 'total' ? 'selected' : ''; ?>>Total</option>
            </select>
        </div>

        <div class="btn-down">
            <button id="searchButton">Search</button>
            <a href="view.php"><button>Reset</button></a>
            <a href="#head"><button>↑</button></a>
            <a href="#foot"><button>↓</button></a>
        </div>
    </div>
</header>
<div id="head"></div>
<table id="dataTable">
    <thead>
        <tr>
            <th>LRN</th>
            <th>Name</th>
            <th>Grade</th>
            <th>Section</th>
            <th>School Year</th>
            <th>Status</th>
            <th>Gender</th>
            <th class="no-print">ACTIONS</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr data-lrn='{$row['lrn']}'>
                    <td>{$row['lrn']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['grade']}</td>
                    <td>{$row['section']}</td>
                    <td>{$row['sy']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['sex']}</td>
                    <td class='no-print'>
                        <div class='action-container'>
                            <a href='view-student.php?lrn={$row['lrn']}' class='view-btn'>View</a>
                            <a href='edit.php?id={$row['lrn']}' class='edit-btn'>Edit</a>
                            <a href='delete.php?id={$row['lrn']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>
                        </div>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No records found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Student Details Modal -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Student Details</h2>
            <span class="close-modal" onclick="closeModal()">&times;</span>
        </div>
        <div class="student-details">
            <div class="student-info">
                <div class="student-basic-info">
                    <h2><?= $row['name'] ?></h2>
                    <p><strong>LRN:</strong> <?= $row['lrn'] ?></p>
                    <p><strong>Status:</strong> <?= $row['status'] ?></p>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Basic Information</h3>
                <div class="detail-item">
                    <span class="detail-label">LRN:</span>
                    <span class="detail-value" id="modal-lrn"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value" id="modal-name"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Sex:</span>
                    <span class="detail-value" id="modal-sex"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Birthday:</span>
                    <span class="detail-value" id="modal-birthday"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Age:</span>
                    <span class="detail-value" id="modal-age"></span>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Cultural Information</h3>
                <div class="detail-item">
                    <span class="detail-label">Mother Tongue:</span>
                    <span class="detail-value" id="modal-mother-tongue"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">IP:</span>
                    <span class="detail-value" id="modal-ip"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Religion:</span>
                    <span class="detail-value" id="modal-religion"></span>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Address Information</h3>
                <div class="detail-item">
                    <span class="detail-label">House Number:</span>
                    <span class="detail-value" id="modal-house-number"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Barangay:</span>
                    <span class="detail-value" id="modal-barangay"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Municipality:</span>
                    <span class="detail-value" id="modal-municipality"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Province:</span>
                    <span class="detail-value" id="modal-province"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Region:</span>
                    <span class="detail-value" id="modal-region"></span>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Family Information</h3>
                <div class="detail-item">
                    <span class="detail-label">Father:</span>
                    <span class="detail-value" id="modal-father"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Mother:</span>
                    <span class="detail-value" id="modal-mother"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Guardian:</span>
                    <span class="detail-value" id="modal-guardian"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Relationship:</span>
                    <span class="detail-value" id="modal-relationship"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Contact:</span>
                    <span class="detail-value" id="modal-contact"></span>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Academic Information</h3>
                <div class="detail-item">
                    <span class="detail-label">Learning Modality:</span>
                    <span class="detail-value" id="modal-learning-modality"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">School Year:</span>
                    <span class="detail-value" id="modal-sy"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Grade:</span>
                    <span class="detail-value" id="modal-grade"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Section:</span>
                    <span class="detail-value" id="modal-section"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Adviser:</span>
                    <span class="detail-value" id="modal-adviser"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" id="modal-status"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Remarks:</span>
                    <span class="detail-value" id="modal-remarks"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gender-count" id="foot">
    <?php
    // Count gender statistics
    $male_count = 0;
    $female_count = 0;
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['sex'] === 'MALE') {
            $male_count++;
        } else if ($row['sex'] === 'FEMALE') {
            $female_count++;
        }
    }
    ?>
    <span id="maleCount">Male: <?php echo $male_count; ?></span>
    <span id="femaleCount">Female: <?php echo $female_count; ?></span>
    <span id="totalCount">Total: <?php echo $male_count + $female_count; ?></span>
</div>
<footer>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&grade=<?php echo urlencode($grade); ?>&sy=<?php echo urlencode($sy); ?>&status=<?php echo urlencode($status); ?>&gender=<?php echo urlencode($gender); ?>">« Previous</a>
        <?php endif; ?>

        <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&grade=<?php echo urlencode($grade); ?>&sy=<?php echo urlencode($sy); ?>&status=<?php echo urlencode($status); ?>&gender=<?php echo urlencode($gender); ?>">Next »</a>
        <?php endif; ?>
    </div>
</footer>
<!-- XLSX Library -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="export.js"></script>
<script src="search.js"></script>
<script src="sy.js"></script>
<script src="main.js"></script>
<script>
function printAllDetails() {
    window.print();
}
</script>
</body>
</html>
