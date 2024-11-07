<?php
session_start();

// Check if the user is logged in and the name is set in the session
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];  // Get the user's name from the session
} else {
    echo '<script>alert("User not logged in. Please log in to make a payment."); window.location.href = "login.php";</script>';
    exit;  // Stop further execution if the user is not logged in
}

// Retrieve payment details from the POST request
$bank = $_POST['bank'];
$cardNumber = $_POST['cardNumber'];
$expiryDate = $_POST['expiryDate'];
$cvv = $_POST['cvv'];

// Create a connection to the database
$conn = new mysqli('localhost', 'root', '', 'connect');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
} else {
    // Prepare the SQL statement for inserting into the 'pay' table
    $stmt = $conn->prepare("INSERT INTO pay (name, bank, cardNumber, expiryDate, cvv) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $bank, $cardNumber, $expiryDate, $cvv);  // Use "ssssi" for proper data types

    if ($stmt->execute()) {
        // Payment successful
        echo '<script>alert("Payment successful"); window.location.href = "ticket.php";</script>';
    } else {
        // Payment failed
        echo '<script>alert("Payment failed: ' . $stmt->error . '"); window.location.href = "pay.php";</script>';
    }

    $stmt->close();
    $conn->close();
}
?>
