<?php
// db2.php
session_start();

// Check if the user is logged in and the name is set in the session
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];  // Get the user's name from the session
} else {
    echo json_encode(array('success' => false, 'error' => 'User not logged in.'));
    exit;  // Stop further execution if the user is not logged in
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieDate = $_POST['movieDate'];
    $movieName = $_POST['movieName'];
    $movieTime = $_POST['movieTime'];
    $movieTheatre = $_POST['movieTheatre'];
    $selectedSeats = json_decode($_POST['selectedSeats']);  // Decode the JSON array of selected seats

    // Perform database insertion
    $conn = new mysqli('localhost', 'root', '', 'connect');

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        $success = true;

        // Prepare the SQL statement for inserting into the 'bookings' table
        $stmt = $conn->prepare("INSERT INTO bookings (name, movieDate, movieName, movieTime, movieTheatre, seat) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            $success = false;
            echo json_encode(array('success' => $success, 'error' => $conn->error));
        } else {
            // Loop through each selected seat and execute the prepared statement
            foreach ($selectedSeats as $seat) {
                $stmt->bind_param("ssssss", $name, $movieDate, $movieName, $movieTime, $movieTheatre, $seat);
                $stmt->execute();

                if ($stmt->affected_rows <= 0) {
                    $success = false;
                    echo json_encode(array('success' => $success, 'error' => $stmt->error));
                    break;
                }
            }

            $stmt->close();
        }

        $conn->close();

        // Respond to the client with a success or failure message
        echo json_encode(array('success' => $success));
    }
} else {
    // Handle invalid request methods
    echo json_encode(array('success' => false, 'error' => 'Invalid request method.'));
}
?>
