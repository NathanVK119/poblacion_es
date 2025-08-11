<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';//database connection path

// Get LRN from request
$lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';

if (empty($lrn)) {
    http_response_code(400);
    echo json_encode(['error' => 'LRN is required']);
    exit;
}

// Get student details
$sql = "SELECT * FROM poblacion WHERE lrn = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 's', $lrn);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    http_response_code(404);
    echo json_encode(['error' => 'Student not found']);
    exit;
}

// Always return all expected fields, even if missing in DB
$all_fields = [
    'lrn', 'name', 'sex', 'birthday', 'age', 'mother_tongue', 'ip', 'religion',
    'house_number', 'barangay', 'municipality', 'province', 'region',
    'father', 'mother', 'guardian_name', 'relationship', 'contact',
    'learning_modality', 'sy', 'grade', 'section', 'adviser', 'status', 'remarks'
];

$student_complete = [];
foreach ($all_fields as $field) {
    $student_complete[$field] = isset($student[$field]) ? $student[$field] : '';
}

header('Content-Type: application/json');
echo json_encode($student_complete);
?> 