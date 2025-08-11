<?php
session_start();

// Check if already logged in
if (isset($_SESSION['student_reg_logged_in']) && $_SESSION['student_reg_logged_in'] === true) {
    header('Location: home.php');
    exit;
}

$error = null;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Default credentials
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['user_role'] = 'admin';
        
        header('Location: home.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a5f;
            --success-color: #1cc88a;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, var(--success-color) 0%, #13855c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: var(--success-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .btn-success {
            background: var(--success-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .btn-success:hover {
            background: #13855c;
        }

        .back-link {
            position: absolute;
            top: 1rem;
            left: 1rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .back-link:hover {
            opacity: 1;
            color: white;
        }
    </style>
</head>
<body>
    <a href="../index.php" class="back-link">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Portal
    </a>

    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-user-graduate fa-3x mb-3"></i>
            <h3>Student Registration</h3>
            <p class="mb-0">Please login to continue</p>
        </div>
        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html> 