<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo '<script>alert("User not logged in. Please log in to make a payment."); window.location.href = "login.php";</script>';
    exit();
}

$name = $_SESSION['name']; // Get the user's name from session

// Retrieve payment details from the POST request
$bank = $_POST['bank'];
$cardNumber = $_POST['cardNumber'];
$expiryDate = $_POST['expiryDate'];
$cvv = $_POST['cvv'];

// Validate card number (simple length check)
if (!preg_match('/^\d{16}$/', $cardNumber)) {
    echo '<script>alert("Invalid card number. Please enter a valid 16-digit card number."); window.location.href = "pay.php";</script>';
    exit();
}

// Hash the CVV before storing it (for security)
$hashedCvv = password_hash($cvv, PASSWORD_DEFAULT);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'connect');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Create 'pay' table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS pay (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bank VARCHAR(255) NOT NULL,
    cardNumber VARCHAR(16) NOT NULL,
    expiryDate VARCHAR(7) NOT NULL,
    cvv VARCHAR(255) NOT NULL
)";

if (!$conn->query($createTableQuery)) {
    die('<script>alert("Table creation failed: ' . $conn->error . '"); window.location.href = "pay.php";</script>');
}

// Insert payment details
$stmt = $conn->prepare("INSERT INTO pay (name, bank, cardNumber, expiryDate, cvv) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $bank, $cardNumber, $expiryDate, $hashedCvv);

if ($stmt->execute()) {
    // Payment successful
    echo '<script>alert("Payment successful!"); window.location.href = "ticket.php";</script>';
} else {
    // Payment failed
    echo '<script>alert("Payment failed: ' . $stmt->error . '"); window.location.href = "pay.php";</script>';
}

$stmt->close();
$conn->close();
?>
