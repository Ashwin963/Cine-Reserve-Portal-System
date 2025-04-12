<?php
session_start();

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

if ($password !== $confirmPassword) {
    echo '<script>alert("Passwords do not match. Please try again."); window.location.href = "signup.html";</script>';
    exit();
}


// Create connection
$conn = new mysqli('localhost', 'root', '', 'connect');

// Check connection
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Create 'sign' table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS sign (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
$conn->query($createTableQuery);

// Check if the email is already registered
$stmt = $conn->prepare("SELECT email FROM sign WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo '<script>alert("Email already registered. Please log in."); window.location.href = "login.html";</script>';
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Insert new user into the database
$stmt = $conn->prepare("INSERT INTO sign (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);
$stmt->execute();
$stmt->close();

// Set session name after successful signup
$_SESSION['name'] = $name;

$conn->close();
echo '<script>alert("Signup successful!"); window.location.href = "home.html";</script>';
?>
