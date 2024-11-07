<?php
session_start();

$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];

// Create a connection
$conn = new mysqli('localhost', 'root', '', 'connect');

// Check the connection
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
} else {
    // Insert data into the 'login' table
    $stmt = $conn->prepare("INSERT INTO login (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    $stmt->close();

    // Check if the user exists in the 'sign' table
    $stmt = $conn->prepare("SELECT * FROM sign WHERE name = ? AND email = ? AND password = ?");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, set session and redirect to home
        $_SESSION['name'] = $name;
        echo '<script>alert("Login successful"); window.location.href = "home.html";</script>';
    } else {
        // No match found, redirect to login page
        echo '<script>alert("Incorrect name, email, or password. Please try again or sign up."); window.location.href = "log.html";</script>';
    }

    $stmt->close();
    $conn->close();
}
?>
