// Hardcoded admin credentials
const adminUsername = "sunny rai";
const adminPassword = "12345678";

// Register function
function register() {
    const username = document.getElementById('usernameReg').value;
    const password = document.getElementById('passwordReg').value;
    const role = document.getElementById('role').value;

    if (username.toLowerCase() === adminUsername.toLowerCase()) {
        alert("Username is reserved for the admin.");
        return;
    }

    if (username && password && role) {
        localStorage.setItem('username', username);
        localStorage.setItem('password', password);
        localStorage.setItem('role', role);
        alert('Registration successful!');
        window.location.href = 'login.html';
    } else {
        alert('Please fill out all fields.');
    }
}

// Login function
function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Check if user is admin
    if (username.toLowerCase() === adminUsername.toLowerCase() && password === adminPassword) {
        alert('Admin login successful!');
        window.location.href = 'index.html'; // Redirect to the home page
        return;
    }

    // Check if user is registered
    const storedUsername = localStorage.getItem('username');
    const storedPassword = localStorage.getItem('password');

    if (username === storedUsername && password === storedPassword) {
        alert('Login successful!');
        window.location.href = 'index1.html'; // Redirect to the home page
    } else {
        alert('Invalid username or password.');
    }
}
