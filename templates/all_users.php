<?php
require_once '../includes/session.php';

// require login
requireLogin();

// require admin access
requireAdmin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - User Management System</title>
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
    <header class="bg-primary text-white py-4 shadow">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 fw-bold">User Management System</h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="small">Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="#" onclick="logout()" class="btn btn-light btn-sm text-primary fw-semibold">
                        🚪 Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- navigation -->
    <nav class="bg-white border-bottom shadow-sm">
        <div class="container py-2 d-flex justify-content-center gap-4">
            <a href="index.php" class="nav-link-custom">🏠 Dashboard</a>
            <a href="all_users.php" class="nav-link-custom active">👥 All Users</a>
        </div>
    </nav>


    <!-- main -->
    <main class="container py-5 flex-grow-1">
        <div class="card shadow-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Users</h4>
                <button class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    ➕ Add User
                </button>
            </div>
            <div class="card-body p-4">
                <!-- search bar -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput" placeholder="🔍 Search by username, first name, or last name...">
                    </div>
                </div>

                <!-- users table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Role</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- add user modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="addUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="addUsername" placeholder="Enter username" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="addFirstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="addFirstname" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addLastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="addLastname" placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="addPassword" placeholder="At least 8 characters" required>
                            <small class="form-text text-muted">Password must be at least 8 characters long</small>
                        </div>
                        <div class="mb-3">
                            <label for="addConfirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="addConfirmPassword" placeholder="Re-enter password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="addIsAdmin">
                            <label class="form-check-label fw-semibold" for="addIsAdmin">Administrator</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="handleAddUser()">Add User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0 small">User Management System © <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        let addUserModal;

        // load users on page load
        document.addEventListener('DOMContentLoaded', async function() {
            addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            await loadUsers();
        });

        // load users function
        async function loadUsers(search = '') {
            const users = await getAllUsers(search);
            displayUsers(users);
        }

        // search functionality with debounce
        const debouncedSearch = debounce(async function() {
            const searchTerm = document.getElementById('searchInput').value;
            await loadUsers(searchTerm);
        }, 500);

        document.getElementById('searchInput').addEventListener('input', debouncedSearch);

        // handle add user
        async function handleAddUser() {
            const username = document.getElementById('addUsername').value;
            const firstname = document.getElementById('addFirstname').value;
            const lastname = document.getElementById('addLastname').value;
            const password = document.getElementById('addPassword').value;
            const confirmPassword = document.getElementById('addConfirmPassword').value;
            const isAdmin = document.getElementById('addIsAdmin').checked;

            // validate confirm password
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Passwords do not match'
                });
                return;
            }

            const success = await addUser(username, firstname, lastname, password, isAdmin);

            if (success) {
                // close modal
                addUserModal.hide();
                
                // reset form
                document.getElementById('addUserForm').reset();
                
                // reload users
                await loadUsers();
            }
        }
    </script>
</body>
</html>