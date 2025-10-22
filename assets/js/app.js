// API base URL
const API_URL = '../api/api.php';

// Utility function to make API calls
async function apiCall(endpoint, method = 'GET', data = null) {
    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(endpoint, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Network error occurred' };
    }
}

// Login function
async function login(username, password) {
    // Validate inputs
    if (!username || username.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Username is required'
        });
        return false;
    }

    if (!password || password.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Password is required'
        });
        return false;
    }

    const result = await apiCall(`${API_URL}?action=login`, 'POST', { username, password });

    if (result.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'index.php';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: result.message
        });
    }

    return result.success;
}

// Register function
async function register(username, firstname, lastname, password, confirmPassword) {
    // Validate username
    if (!username || username.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Username is required'
        });
        return false;
    }

    // Validate firstname
    if (!firstname || firstname.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'First name is required'
        });
        return false;
    }

    // Validate lastname
    if (!lastname || lastname.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Last name is required'
        });
        return false;
    }

    // Validate password
    if (!password || password.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Password is required'
        });
        return false;
    }

    if (password.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Password must be at least 8 characters long'
        });
        return false;
    }

    // Validate confirm password
    if (!confirmPassword || confirmPassword.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Confirm password is required'
        });
        return false;
    }

    if (password !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Passwords do not match'
        });
        return false;
    }

    // Check if username exists
    const checkResult = await apiCall(`${API_URL}?action=check_username`, 'POST', { username });
    if (checkResult.exists) {
        Swal.fire({
            icon: 'error',
            title: 'Username Taken',
            text: 'This username is already taken. Please choose another one.'
        });
        return false;
    }

    const result = await apiCall(`${API_URL}?action=register`, 'POST', {
        username,
        firstname,
        lastname,
        password,
        confirm_password: confirmPassword
    });

    if (result.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'login.php';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: result.message
        });
    }

    return result.success;
}

// Get all users (admin only)
async function getAllUsers(search = '') {
    const endpoint = search ? `${API_URL}?action=get_users&search=${encodeURIComponent(search)}` : `${API_URL}?action=get_users`;
    const result = await apiCall(endpoint, 'GET');

    if (result.success) {
        return result.users;
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message
        });
        return [];
    }
}

// Add user (admin only)
async function addUser(username, firstname, lastname, password, isAdmin) {
    // Validate username
    if (!username || username.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Username is required'
        });
        return false;
    }

    // Check if username exists
    const checkResult = await apiCall(`${API_URL}?action=check_username`, 'POST', { username });
    if (checkResult.exists) {
        Swal.fire({
            icon: 'error',
            title: 'Username Taken',
            text: 'This username is already taken. Please choose another one.'
        });
        return false;
    }

    // Validate firstname
    if (!firstname || firstname.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'First name is required'
        });
        return false;
    }

    // Validate lastname
    if (!lastname || lastname.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Last name is required'
        });
        return false;
    }

    // Validate password
    if (!password || password.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Password is required'
        });
        return false;
    }

    if (password.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Password must be at least 8 characters long'
        });
        return false;
    }

    const result = await apiCall(`${API_URL}?action=add_user`, 'POST', {
        username,
        firstname,
        lastname,
        password,
        is_admin: isAdmin ? 1 : 0
    });

    if (result.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message,
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: result.message
        });
    }

    return result.success;
}

// Logout function
async function logout() {
    const result = await apiCall(`${API_URL}?action=logout`, 'POST');

    if (result.success) {
        window.location.href = 'login.php';
    }
}

// Display users in table
function displayUsers(users) {
    const tableBody = document.getElementById('usersTableBody');
    tableBody.innerHTML = '';

    if (users.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
        return;
    }

    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.firstname}</td>
            <td>${user.lastname}</td>
            <td><span class="badge ${user.is_admin == 1 ? 'badge-success' : 'badge-secondary'}">${user.is_admin == 1 ? 'Admin' : 'User'}</span></td>
            <td>${new Date(user.date_added).toLocaleDateString()}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}