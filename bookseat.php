<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Stop if connection fails
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error", 
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}

// Get seat info from POST and user_id from session
$seat_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$movie_name = isset($_POST['movie_name']) ? trim(mysqli_real_escape_string($conn, $_POST['movie_name'])) : '';
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Require login before booking
if ($user_id <= 0) {
    die(json_encode([
        "status" => "error",
        "message" => "You must login first.",
        "redirect" => "login.php" // Front-end can handle this
    ]));
}

// Mark the seat as booked in the movie table
$book_seat_query = "UPDATE `$movie_name` SET is_booked = 1 WHERE id = $seat_id";
$conn->query($book_seat_query);

// Add booking record in the bookings table
$insert_booking_query = "INSERT INTO bookings (user_id, movie_name, seat_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insert_booking_query);
$stmt->bind_param("isi", $user_id, $movie_name, $seat_id);

// Check if booking succeeded
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Seat booked successfully!"]);
} else {
    die(json_encode([
        "status" => "error", 
        "message" => "Error inserting booking record: " . $stmt->error
    ]));
}

// Close connections
$stmt->close();
$conn->close();
?>
