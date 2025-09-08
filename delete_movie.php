<?php
// ==========================
// DATABASE CONNECTION SETUP
// ==========================
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create connection object
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check if connection works
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}

// ==========================
// HANDLE DELETE REQUEST FROM ADMIN DASHBOARD
// ==========================
// This script is triggered from admin_dashboard.php
// We receive 'pid' (primary key from poster table) to identify the movie
if (isset($_POST['pid'])) {  // isset() ensures the variable exists and is not null
    $pid = intval($_POST['pid']); // Make sure it's a number

    // Step 1: Get the movie title from poster table using pid
    $query = "SELECT title FROM poster WHERE pid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();  // Fetch one full record
        $movie_name = $movie['title'];    // Extract the movie title

        // Step 2: Delete all bookings for this movie
        $delete_bookings_sql = "DELETE FROM bookings WHERE movie_name = ?";
        $delete_bookings_stmt = $conn->prepare($delete_bookings_sql);
        $delete_bookings_stmt->bind_param("s", $movie_name);
        $delete_bookings_stmt->execute();

        // Step 3: Drop the movie-specific table if it exists
        $drop_table_sql = "DROP TABLE IF EXISTS `$movie_name`";
        $conn->query($drop_table_sql);

        // Step 4: Remove the movie from the poster table
        $delete_poster_sql = "DELETE FROM poster WHERE pid = ?";
        $delete_stmt = $conn->prepare($delete_poster_sql);
        $delete_stmt->bind_param("i", $pid);

        // Step 5: Execute deletion and provide feedback
        if ($delete_stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Movie '$movie_name' and all related data were deleted successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete movie from poster table."
            ]);
        }
    } else {
        // Movie not found
        echo json_encode([
            "status" => "error",
            "message" => "Movie not found."
        ]);
    }
} else {
    // pid was not sent
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}

// ==========================
// SUMMARY
// ==========================
// 1. Receive pid from client side
// 2. Fetch movie title from poster table
// 3. Delete all related bookings
// 4. Drop movie-specific table
// 5. Delete movie record from poster table
// All steps together completely remove the movie from the database.

$conn->close();
?>
