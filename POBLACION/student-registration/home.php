<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';

// Get total students count
$total_query = "SELECT COUNT(*) as total FROM poblacion";
$total_result = mysqli_query($con, $total_query);
$total_students = mysqli_fetch_assoc($total_result)['total'];

// Get gender distribution
$gender_query = "SELECT sex, COUNT(*) as count FROM poblacion GROUP BY sex";
$gender_result = mysqli_query($con, $gender_query);
$male_count = 0;
$female_count = 0;
while ($row = mysqli_fetch_assoc($gender_result)) {
    if ($row['sex'] === 'MALE') {
        $male_count = $row['count'];
    } else if ($row['sex'] === 'FEMALE') {
        $female_count = $row['count'];
    }
}

// Get grade level distribution
$grade_query = "SELECT grade, COUNT(*) as count FROM poblacion GROUP BY grade ORDER BY FIELD(grade, 'KINDER', 'GRADE 1', 'GRADE 2', 'GRADE 3', 'GRADE 4', 'GRADE 5', 'GRADE 6', 'GRADE 7', 'GRADE 8', 'GRADE 9', 'GRADE 10')";
$grade_result = mysqli_query($con, $grade_query);
$grade_distribution = [];
while ($row = mysqli_fetch_assoc($grade_result)) {
    $grade_distribution[$row['grade']] = $row['count'];
}

// Get status distribution
$status_query = "SELECT status, COUNT(*) as count FROM poblacion GROUP BY status";
$status_result = mysqli_query($con, $status_query);
$status_distribution = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_distribution[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poblacion School - Student Registration System</title>
    <link rel="stylesheet" href="css/home.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Poblacion Elementary School</h1>
        <h2>Student Registration System</h2>
    </header>

    <main>
        <section class="dashboard">
            <h2>Dashboard</h2>
            <div class="stats-container">
                <div class="stat-card total">
                    <i class="material-icons">people</i>
                    <div class="stat-info">
                        <h3>Total Students</h3>
                        <p><?php echo $total_students; ?></p>
                    </div>
                </div>
                <div class="stat-card male">
                    <i class="material-icons">male</i>
                    <div class="stat-info">
                        <h3>Male Students</h3>
                        <p><?php echo $male_count; ?></p>
                    </div>
                </div>
                <div class="stat-card female">
                    <i class="material-icons">female</i>
                    <div class="stat-info">
                        <h3>Female Students</h3>
                        <p><?php echo $female_count; ?></p>
                    </div>
                </div>
            </div>

            <div class="distribution-container">
                <div class="distribution-card">
                    <h3>Grade Level Distribution</h3>
                    <div class="distribution-list">
                        <?php foreach ($grade_distribution as $grade => $count): ?>
                        <div class="distribution-item">
                            <span class="grade"><?php echo $grade; ?></span>
                            <div class="progress-bar">
                                <div class="progress" style="width: <?php echo ($count / $total_students * 100); ?>%"></div>
                            </div>
                            <span class="count"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="distribution-card">
                    <h3>Status Distribution</h3>
                    <div class="distribution-list">
                        <?php foreach ($status_distribution as $status => $count): ?>
                        <div class="distribution-item">
                            <span class="status"><?php echo $status; ?></span>
                            <div class="progress-bar">
                                <div class="progress" style="width: <?php echo ($count / $total_students * 100); ?>%"></div>
                            </div>
                            <span class="count"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="navigation">
            <h2>Quick Actions</h2>
            <div class="nav-cards">
                <a href="input.php" class="nav-card">
                    <i class="material-icons">person_add</i>
                    <h3>Register Student</h3>
                    <p>Add new student records</p>
                </a>
                <a href="view.php" class="nav-card">
                    <i class="material-icons">search</i>
                    <h3>Search Records</h3>
                    <p>View and search student records</p>
                </a>
                <a href="../index.php" class="nav-card">
                    <i class="material-icons">logout</i>
                    <h3>Back to Portal</h3>
                    <p>Return to main portal</p>
                </a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Poblacion Elementary School. All rights reserved.</p>
    </footer>
</body>
</html> 