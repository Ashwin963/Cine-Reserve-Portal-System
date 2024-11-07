<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo json_encode(array('success' => false, 'error' => 'User not logged in.'));
    exit;
}

$name = $_SESSION['name'];

// Read the incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
    $movieDate = $data['movieDate'];
    $movieName = $data['movieName'];
    $movieTime = $data['movieTime'];
    $movieTheatre = $data['movieTheatre'];
    $selectedSeats = $data['selectedSeats'];

    // Establish database connection
    $conn = new mysqli('localhost', 'root', '', 'connect');

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    // Prepare the SQL statement for deletion
    $stmt = $conn->prepare("DELETE FROM bookings WHERE name = ? AND movieDate = ? AND movieName = ? AND movieTime = ? AND movieTheatre = ? AND seat IN (" . implode(',', array_fill(0, count($selectedSeats), '?')) . ")");
    
    if (!$stmt) {
        echo json_encode(array('success' => false, 'error' => $conn->error));
        exit;
    }

    // Bind parameters for the statement
    $params = array_merge([$name, $movieDate, $movieName, $movieTime, $movieTheatre], $selectedSeats);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'error' => $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array('success' => false, 'error' => 'Invalid request method.'));
}
?>
