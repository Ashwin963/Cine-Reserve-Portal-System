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
    die('Connection Failed : ' . $conn->connect_error);
} else {
    // Prepare and execute SQL query to insert user data
    $stmt = $conn->prepare("INSERT INTO sign (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    $stmt->close();

    // Set session name after successful signup
    $_SESSION['name'] = $name;

    $conn->close();
    echo '<script>alert("Signup successful"); window.location.href = "home.html";</script>';
}
?>
