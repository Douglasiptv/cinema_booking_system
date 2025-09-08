<?php
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create database connection object
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all movie posters from 'poster' table
// $result will hold the query result
$query = "SELECT pid, title, poster_image FROM poster";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Posters</title>
    <link rel="stylesheet" href="css/movies_page.css">
</head>
<body>
    <a href="homepage.php" class="back-arrow">&#9664;</a>
    <h1>Movie Posters</h1>

    <div class="poster-container">
        <?php
        if ($result && $result->num_rows > 0) {
            // Loop through each poster record
            // fetch_assoc() lets us access columns by name
            while ($row = $result->fetch_assoc()) {
                $title = htmlspecialchars($row['title']);
                $poster_image = htmlspecialchars($row['poster_image']);
                $pid = $row['pid'];

                // Display each poster as a clickable card
                // Clicking sends $pid to seats_page.php to select seats
                echo "
                <div class='poster-card'>
                    <a href='seats_page.php?pid=$pid'> 
                        <img src='$poster_image' alt='$title Poster'>
                        <div class='poster-title'>$title</div>
                    </a>
                </div>";
            }
        } else {
            // Fallback if no posters exist in the database
            echo "<p class='no-posters'>No posters available.</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
