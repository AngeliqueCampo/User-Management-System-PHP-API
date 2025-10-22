<?php
require_once '../includes/session.php';

// redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
        <style>
        :root {
            --primary: #217B71;
            --accent: #8ACBA9;
            --light: #F0FFE3;
        }
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: var(--light);
        }
        .bg-primary { background-color: var(--primary) !important; }
        .bg-accent { background-color: var(--accent) !important; }
        .text-primary { color: var(--primary) !important; }
        .navbar-custom {
            background-color: var(--primary);
        }
        .nav-link-custom {
            color: #6b7280;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }
        .nav-link-custom:hover {
            color: var(--primary);
        }
        .nav-link-custom.active {
            color: var(--primary);
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: #1a635b;
            border-color: #1a635b;
        }
        .card {
            border: none;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 text-primary fw-bold">Login</h2>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            await login(username, password);
        });
    </script>
</body>
</html>