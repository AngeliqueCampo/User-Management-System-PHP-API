<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

$conn = getDBConnection();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    case 'register':
        handleRegister($conn);
        break;
    case 'check_username':
        handleCheckUsername($conn);
        break;
    case 'get_users':
        handleGetUsers($conn);
        break;
    case 'add_user':
        handleAddUser($conn);
        break;
    case 'get_session':
        handleGetSession();
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();

// handle login
function handleLogin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = sanitizeInput($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT id, username, firstname, lastname, is_admin, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (verifyPassword($password, $user['password'])) {
            setUserSession($user);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'is_admin' => $user['is_admin']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
    
    $stmt->close();
}

// handle registration
function handleRegister($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = sanitizeInput($data['username'] ?? '');
    $firstname = sanitizeInput($data['firstname'] ?? '');
    $lastname = sanitizeInput($data['lastname'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    // validate username
    $usernameValidation = validateUsername($username);
    if (!$usernameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $usernameValidation['message']]);
        return;
    }
    
    // check if username exists
    if (usernameExists($conn, $username)) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        return;
    }
    
    // validate firstname
    $firstnameValidation = validateName($firstname, 'firstname');
    if (!$firstnameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $firstnameValidation['message']]);
        return;
    }
    
    // validate lastname
    $lastnameValidation = validateName($lastname, 'lastname');
    if (!$lastnameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $lastnameValidation['message']]);
        return;
    }
    
    // validate password
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $passwordValidation['message']]);
        return;
    }
    
    // check if passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }
    
    // hash password and insert user
    $hashedPassword = hashPassword($password);
    $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $firstname, $lastname, $hashedPassword);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
    
    $stmt->close();
}

// handle check username
function handleCheckUsername($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = sanitizeInput($data['username'] ?? '');
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        return;
    }
    
    $exists = usernameExists($conn, $username);
    echo json_encode(['success' => true, 'exists' => $exists]);
}

// handle get users (admin only)
function handleGetUsers($conn) {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        return;
    }
    
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT id, username, firstname, lastname, is_admin, date_added FROM users WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ? ORDER BY date_added DESC");
        $searchParam = "%{$search}%";
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    } else {
        $stmt = $conn->prepare("SELECT id, username, firstname, lastname, is_admin, date_added FROM users ORDER BY date_added DESC");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['success' => true, 'users' => $users]);
    $stmt->close();
}

// handle add user (admin only)
function handleAddUser($conn) {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = sanitizeInput($data['username'] ?? '');
    $firstname = sanitizeInput($data['firstname'] ?? '');
    $lastname = sanitizeInput($data['lastname'] ?? '');
    $password = $data['password'] ?? '';
    $isAdmin = isset($data['is_admin']) ? (int)$data['is_admin'] : 0;
    
    // validate username
    $usernameValidation = validateUsername($username);
    if (!$usernameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $usernameValidation['message']]);
        return;
    }
    
    // check if username exists
    if (usernameExists($conn, $username)) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        return;
    }
    
    // validate firstname
    $firstnameValidation = validateName($firstname, 'firstname');
    if (!$firstnameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $firstnameValidation['message']]);
        return;
    }
    
    // validate lastname
    $lastnameValidation = validateName($lastname, 'lastname');
    if (!$lastnameValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $lastnameValidation['message']]);
        return;
    }
    
    // validate password
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        echo json_encode(['success' => false, 'message' => $passwordValidation['message']]);
        return;
    }
    
    // hash password and insert user
    $hashedPassword = hashPassword($password);
    $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $username, $firstname, $lastname, $isAdmin, $hashedPassword);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user']);
    }
    
    $stmt->close();
}

// handle get session
function handleGetSession() {
    if (isLoggedIn()) {
        echo json_encode([
            'success' => true,
            'logged_in' => true,
            'user' => getCurrentUser()
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'logged_in' => false
        ]);
    }
}

// handle logout
function handleLogout() {
    destroyUserSession();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}
?>