<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie Ticket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="ticket.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');
  </style>
</head>
<body>
  <div class="ticket-container">
    <div class="ticket">
      <div class="ticket-content ash1">
        <div class="ticket-info">
          <div class="ticket-header d-flex flex-row">
            <img src="images/tic.png" height="90" width="90" class="tic">
            <div class="mt-4">
              <h1>CINE RESERVE TICKET</h1>
              <p>Admit One: <strong id="user-name"><?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest'; ?></strong></p>
            </div>
          </div>

          <div class="ticket-details">
            <p><strong>Movie:</strong> <span id="movie-name"></span></p>
            <p><strong>Date:</strong> <span id="date"></span></p>
            <p><strong>Time:</strong> <span id="time"></span></p>
            <p><strong>Theatre:</strong> <span id="theatre"></span></p>
            <p><strong>Seat:</strong> <span id="seat"></span></p>
            <p><strong>Price:</strong> <span id="price"></span></p>
          </div>

          <div class="ticket-footer">
            <p>Enjoy the movie!</p>
          </div>
          <div class="text-center ">
            <button id="cancel-ticket" class="btn btn-warning m-2 mr-3">Cancel Ticket</button>
            <a href="home.html">Return To Home</a>
            </div>
        </div>
        <div class="d-flex flex-column">
          <div class="ticket-image">
            <img alt="Movie Poster" id="movie-poster">
          </div>
          <div id="qr-code" class="m-4"></div>
        </div>
      </div>
    </div> 
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    document.getElementById("cancel-ticket").addEventListener("click", function() {
        const bookingData = JSON.parse(localStorage.getItem('bookingData'));
        
        if (bookingData) {
            const confirmation = confirm("Are you sure you want to cancel your ticket?");
            if (confirmation) {
                const requestData = {
                    movieDate: bookingData.movieDate,
                    movieName: bookingData.movieName,
                    movieTime: bookingData.movieTime,
                    movieTheatre: bookingData.movieTheatre,
                    selectedSeats: bookingData.selectedSeats
                };
                
                fetch('db4.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Ticket cancelled successfully.");
                        localStorage.removeItem('bookingData');  // Clear the booking data
                        window.location.href = 'home.html';  // Redirect to home
                    } else {
                        alert("Error cancelling ticket: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while cancelling the ticket.");
                });
            }
        } else {
            alert("No booking data found.");
        }
    });
    document.addEventListener("DOMContentLoaded", function() {
      // Retrieve booking data from localStorage
      const bookingData = JSON.parse(localStorage.getItem('bookingData'));

      if (bookingData) {
        // Update ticket details with booking data
        document.getElementById("movie-name").textContent = bookingData.movieName;
        document.getElementById("date").textContent = bookingData.movieDate;
        document.getElementById("time").textContent = bookingData.movieTime;
        document.getElementById("theatre").textContent = bookingData.movieTheatre;
        document.getElementById("seat").textContent = bookingData.selectedSeats.join(', ');
        document.getElementById("price").textContent = `Rs. ${bookingData.totalCost}`;
        document.getElementById("movie-poster").src = bookingData.posterImage;

        // Generate QR code based on the booking data
        const qrCodeData = `Movie: ${bookingData.movieName}, Seat: ${bookingData.selectedSeats.join(', ')}, Date: ${bookingData.movieDate}, Time: ${bookingData.movieTime}`;
        new QRCode(document.getElementById("qr-code"), {
          text: qrCodeData,
          width: 128,  // width of the QR code
          height: 128  // height of the QR code
        });
      } else {
        alert("No booking data found.");
      }
    });
  </script>
</body>
</html>
