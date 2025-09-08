<?php
session_start(); // Start the session to access user login info (user_id, username)

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get movie ID from URL parameter (pid) sent from movies_page.php
if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']); // Sanitize the input to prevent injection
} else {
    die("Error: No movie selected.");
}

// Fetch all columns of the selected movie using its pid
$movie_all_columns = $conn->query("SELECT * FROM poster WHERE pid = $pid")->fetch_assoc();
$movie_name = $movie_all_columns['title'];

// Use movie title as the table name to fetch seat data
$table_name = mysqli_real_escape_string($conn, $movie_name);

// Check if the movie-specific seat table exists
$table_check_query = "SHOW TABLES LIKE '$table_name'";
if ($conn->query($table_check_query)->num_rows == 0) {
    die("Error: Table for the movie does not exist.");
}

// Fetch all seats for the movie
$seat_query = "SELECT id, row, col, is_booked FROM `$table_name`";
$seat_result = $conn->query($seat_query);

// Store seats in an array for easier manipulation in HTML
$seats = [];
while ($loop = $seat_result->fetch_assoc()) { 
    $seats[] = $loop; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Seats for <?php echo htmlspecialchars($movie_name); ?></title>
    <link rel="stylesheet" href="css/seats_page.css">
</head>
<body>
    <div class="top-buttons">
        <!-- Button to go back to Movie Portal -->
        <button onclick="window.location.href='movies_page.php'">Movie Portal</button>
    </div>

    <h1>Book Seats for <?php echo htmlspecialchars($movie_name); ?></h1>

    <!-- Seat layout display -->
    <div class="seats-container" id="seatsContainer">
        <?php foreach ($seats as $seat): ?>
            <div class="seat <?php echo $seat['is_booked'] ? 'booked' : 'available'; ?>" 
                 data-seat-id="<?php echo $seat['id']; ?>">
                R<?php echo $seat['row']; ?>C<?php echo $seat['col']; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Book button -->
    <button id="bookButton" disabled>Book Selected Seat</button>

    <script>
        let selectedSeatId = null;

        // Seat selection logic: highlight selected seat
        document.querySelectorAll('.seat.available').forEach(seat => {
            seat.addEventListener('click', () => {
                document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
                seat.classList.add('selected');
                selectedSeatId = seat.getAttribute('data-seat-id');
                document.getElementById('bookButton').disabled = false;
            });
        });

        // Handle seat booking via POST request
        document.getElementById('bookButton').addEventListener('click', () => {
            if (!selectedSeatId) return alert("No seat selected!");

            fetch('bookseat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${selectedSeatId}&movie_name=<?php echo urlencode($movie_name); ?>`
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === "success") {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message);
                }
            });
        });
    </script>
</body>

<!-- 
This page displays the seat layout for the selected movie.
- Uses pid from movies_page.php to fetch movie title and seats.
- Seats are displayed as clickable divs with row & column info.
- Booking action is handled by bookseat.php via JavaScript POST request.
- This page is for display & selection only, not actual booking.
-->

</html>

<?php
$conn->close(); // Close DB connection
?>
