<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>POBLACION ES - System Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a5f;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .system-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.3s ease;
            height: 100%;
            border: none;
        }

        .system-card:hover {
            transform: translateY(-5px);
        }

        .system-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .system-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .system-card.student-reg .system-icon {
            background: var(--success-color);
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b5998 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
        }

        .user-section {
            margin-left: auto;
        }

        .user-section .btn {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .user-section .btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        footer {
            margin-top: auto;
            background: white;
            padding: 1.5rem 0;
            box-shadow: 0 -0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .features-section {
            padding: 3rem 0;
            background: #f8f9fc;
        }

        .feature-card {
            padding: 1.5rem;
            border-radius: 1rem;
            background: white;
            height: 100%;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="shared-source/image/poblacion-logo.jpg" alt="Poblacion ES Logo" class="img-fluid rounded-circle" style="max-width: 120px; border: 3px solid white;">
                </div>
                <div class="col-md-6">
                    <h1>Welcome to POBLACION Elementary School System</h1>
                    <p class="lead mb-0">Empowering Education Through Technology</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-school text-white" style="font-size: 5rem; opacity: 0.8"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row g-4">
            <!-- Student Registration Card -->
            <div class="col-md-6">
                <a href="student-registration/login.php" class="text-decoration-none">
                    <div class="system-card student-reg p-4 text-center">
                        <div class="system-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="mb-3">Student Registration</h3>
                        <p class="text-muted mb-4">
                            Manage student informations, records, and registration processes.
                        </p>
                        <button class="btn btn-success">
                            <i class="fas fa-arrow-right me-2"></i> Access System
                        </button>
                    </div>
                </a>
            </div>

            <!-- Data Management Card -->
            <div class="col-md-6">
                <a href="data-management/login.php" class="text-decoration-none">
                    <div class="system-card p-4 text-center">
                        <div class="system-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3 class="mb-3">Data Management</h3>
                        <p class="text-muted mb-4">
                            Organize and manage school documents and files efficiently.
                        </p>
                        <button class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i> Access System
                        </button>
                    </div>
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section mt-5">
            <div class="container">
                <h2 class="text-center mb-4">System Features</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4>Secure Access</h4>
                            <p class="text-muted">Protected private access only for Poblacion Elementary School</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-sync"></i>
                            </div>
                            <h4>Real-time Updates</h4>
                            <p class="text-muted">Instant local updates to student records and school documents.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>Efficient Management</h4>
                            <p class="text-muted">Simplify processes for better school administration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> POBLACION ELEMENTARY SCHOOL. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 