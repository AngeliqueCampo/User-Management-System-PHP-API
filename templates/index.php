<?php
require_once '../includes/session.php';

// Require login
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    <!-- header -->
    <header class="bg-primary text-white py-4 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 fw-bold mb-0">Admin Dashboard</h1>
            <div class="d-flex align-items-center gap-3">
                <span class="small">Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="#" onclick="logout()" class="btn btn-light text-primary fw-medium btn-sm">
                  🚪 Logout
                </a>
            </div>
        </div>
    </header>

    <!-- navigation -->
    <nav class="bg-white border-bottom shadow-sm">
        <div class="container py-2 d-flex justify-content-center gap-4">
            <a href="index.php" class="nav-link-custom active">🏠 Dashboard</a>
            <?php if (isAdmin()): ?>
            <a href="all_users.php" class="nav-link-custom">👥 All Users</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- main -->
    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <h1 class="display-5 mb-4 text-primary fw-bold">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                        <p class="lead text-muted mb-4">Hello there, <?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></p>
                        
                        <div class="mt-4">
                            <div class="card bg-light border-0 rounded-4">
                                <div class="card-body p-4">
                                    <h5 class="card-title text-primary mb-4 fw-semibold">Your Profile Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6 text-start">
                                            <p class="mb-2"><strong class="text-primary">Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                                            <p class="mb-0"><strong class="text-primary">First Name:</strong> <?php echo htmlspecialchars($user['firstname']); ?></p>
                                        </div>
                                        <div class="col-md-6 text-start">
                                            <p class="mb-2"><strong class="text-primary">Last Name:</strong> <?php echo htmlspecialchars($user['lastname']); ?></p>
                                            <p class="mb-0"><strong class="text-primary">Role:</strong> 
                                                <span class="badge <?php echo $user['is_admin'] ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                                                    <?php echo $user['is_admin'] ? 'Administrator' : 'Regular User'; ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isAdmin()): ?>
                        <div class="mt-4">
                            <a href="all_users.php" class="btn btn-primary btn-lg rounded-pill px-5">Manage Users</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
