<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';//database connection path

$sql = "SELECT lrn, name, sex, birthday, age, mother_tongue, ip, religion, house_number, barangay, municipality, province, region, father, mother, guardian_name, relationship, contact, learning_modality, sy, grade, section, adviser, status, remarks, created_at FROM poblacion WHERE 1=1";

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';
$sy = isset($_GET['sy']) ? $_GET['sy'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';

// Add search conditions
if (!empty($search)) {
    $search = mysqli_real_escape_string($con, $search);
    $sql .= " AND (lrn LIKE '%$search%' OR name LIKE '%$search%' OR barangay LIKE '%$search%' OR municipality LIKE '%$search%')";
}
if (!empty($grade)) {
    $grade = mysqli_real_escape_string($con, $grade);
    $sql .= " AND grade = '$grade'";
}
if (!empty($sy)) {
    $sy = mysqli_real_escape_string($con, $sy);
    $sql .= " AND sy = '$sy'";
}
if (!empty($status)) {
    $status = mysqli_real_escape_string($con, $status);
    $sql .= " AND status = '$status'";
}
if (!empty($gender)) {
    $gender = mysqli_real_escape_string($con, $gender);
    $sql .= " AND sex = '$gender'";
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
    created_at DESC";

$result = mysqli_query($con, $sql);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

header('Content-Type: application/json');
echo json_encode($students); 