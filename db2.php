<?php
// db2.php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo json_encode(array('success' => false, 'error' => 'User not logged in.'));
    exit();
}

$name = $_SESSION['name']; // Get user's name from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieDate = $_POST['movieDate'];
    $movieName = $_POST['movieName'];
    $movieTime = $_POST['movieTime'];
    $movieTheatre = $_POST['movieTheatre'];
    $selectedSeats = json_decode($_POST['selectedSeats']); // Decode JSON array

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'connect');

    if ($conn->connect_error) {
        die(json_encode(array('success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error)));
    }

    // Create 'bookings' table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        movieDate DATE NOT NULL,
        movieName VARCHAR(255) NOT NULL,
        movieTime VARCHAR(10) NOT NULL,
        movieTheatre VARCHAR(255) NOT NULL,
        seat VARCHAR(10) NOT NULL,
        UNIQUE (movieDate, movieTime, movieTheatre, seat)  -- Prevent duplicate seat bookings
    )";

    if (!$conn->query($createTableQuery)) {
        die(json_encode(array('success' => false, 'error' => 'Table creation failed: ' . $conn->error)));
    }

    $success = true;
    $stmt = $conn->prepare("INSERT INTO bookings (name, movieDate, movieName, movieTime, movieTheatre, seat) VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode(array('success' => false, 'error' => $conn->error));
        exit();
    }

    foreach ($selectedSeats as $seat) {
        // Check if the seat is already booked
        $checkSeatQuery = "SELECT id FROM bookings WHERE movieDate = ? AND movieTime = ? AND movieTheatre = ? AND seat = ?";
        $seatStmt = $conn->prepare($checkSeatQuery);
        $seatStmt->bind_param("ssss", $movieDate, $movieTime, $movieTheatre, $seat);
        $seatStmt->execute();
        $seatStmt->store_result();

        if ($seatStmt->num_rows > 0) {
            $success = false;
            echo json_encode(array('success' => false, 'error' => "Seat $seat is already booked."));
            $seatStmt->close();
            exit();
        }
        $seatStmt->close();

        // Insert booking details into the database
        $stmt->bind_param("ssssss", $name, $movieDate, $movieName, $movieTime, $movieTheatre, $seat);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            $success = false;
            echo json_encode(array('success' => false, 'error' => $stmt->error));
            break;
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode(array('success' => $success));
} else {
    echo json_encode(array('success' => false, 'error' => 'Invalid request method.'));
}
?>
