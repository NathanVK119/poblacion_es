<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';

// Get LRN from URL
$lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';

if (empty($lrn)) {
    header('Location: view.php');
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
    header('Location: view.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - <?php echo htmlspecialchars($student['name']); ?></title>
    <link rel="stylesheet" href="/student-registration/view-page/css/view.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background: #1e3a5f;
            color: white;
            padding: 15px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .con-up {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .con-down h1 {
            font-size: 22px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .con-down h1 i {
            color: #7faaff;
        }

        .btn-up {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .back-btn, .print-btn {
            background: #3b5998;
            color: white !important;
            border: none;
            padding: 8px 15px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-btn:hover, .print-btn:hover {
            background: #1e3a5f;
        }

        .student-details {
            max-width: 900px;
            margin: 85px auto 20px;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #b6c6e3;
        }

        .detail-item {
            display: flex;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            width: 180px;
            font-weight: 600;
            color: #1e3a5f;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .detail-label i {
            color: #3b5998;
            width: 16px;
            text-align: center;
        }

        .detail-value {
            flex: 1;
            color: #23408e;
            font-weight: 500;
            font-size: 15px;
            word-break: break-word;
            padding-left: 10px;
            min-width: 0;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .back-btn, .print-btn {
                display: none;
            }
            .student-details {
                margin: 0;
                padding: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border: 1px solid #b6c6e3;
                max-width: 100%;
                page-break-inside: avoid;
            }
            .detail-item {
                background: #f8f9fa;
                padding: 10px 12px;
                display: flex;
                align-items: center;
                page-break-inside: avoid;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                border: 1px solid #e9ecef;
            }
            .detail-item:last-child {
                margin-bottom: 0;
            }
            .detail-label {
                width: 180px;
                color: #1e3a5f;
                font-weight: 600;
                font-size: 15px;
                flex-shrink: 0;
            }
            .detail-label i {
                display: none;
            }
            .detail-value {
                color: #23408e;
                font-size: 15px;
                padding-left: 10px;
                min-width: 0;
            }
            @page {
                size: auto;
                margin: 1.5cm;
            }
        }

        @media screen and (max-width: 768px) {
            .con-up {
                flex-direction: column;
                gap: 8px;
            }
            .btn-up {
                justify-content: flex-start;
            }
            .detail-item {
                flex-direction: column;
                gap: 4px;
                align-items: flex-start;
                padding: 10px;
            }
            .detail-label {
                width: 100%;
                font-size: 15px;
            }
            .detail-value {
                padding-left: 0;
                width: 100%;
                font-size: 15px;
            }
            .student-details {
                margin: 75px 10px 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="con-up">
            <div class="con-down">
                <h1><i class="fas fa-user-graduate"></i> Poblacion Elementary School Student Details</h1>
            </div>
            <div class="btn-up">
                <a href="view.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
                <button onclick="window.print()" class="print-btn"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
    </header>
    <div class="student-details">
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-id-card"></i> LRN</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['lrn']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-user"></i> Name</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['name']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-venus-mars"></i> Sex</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['sex']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-calendar"></i> Birthday</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['birthday']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-birthday-cake"></i> Age</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['age']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-language"></i> Mother Tongue</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['mother_tongue']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-users"></i> IP</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['ip']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-pray"></i> Religion</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['religion']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-home"></i> House Number</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['house_number']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Barangay</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['barangay']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-city"></i> Municipality</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['municipality']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-map"></i> Province</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['province']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-globe-asia"></i> Region</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['region']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-male"></i> Father</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['father']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-female"></i> Mother</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['mother']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-user-shield"></i> Guardian</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['guardian_name']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-link"></i> Relationship</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['relationship']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-phone"></i> Contact</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['contact']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-laptop"></i> Learning Modality</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['learning_modality']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-calendar-alt"></i> School Year</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['sy']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-graduation-cap"></i> Grade</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['grade']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-chalkboard"></i> Section</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['section']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-chalkboard-teacher"></i> Adviser</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['adviser']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-info-circle"></i> Status</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['status']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label"><i class="fas fa-comment"></i> Remarks</span>
            <span class="detail-value"><?php echo htmlspecialchars($student['remarks']); ?></span>
        </div>
    </div>
</body>
</html> 