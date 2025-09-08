<?php
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all YouTube videos from newest to oldest
// $result holds the query result, ready to fetch each row
$sql = "SELECT * FROM youtube_videos ORDER BY uploaded_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch YouTube Videos</title>
    <link rel="stylesheet" href="css/homepage.css">
</head>
<body>

    <div class="sidebar">
        <h2>Menu</h2>
        <div class="tabs">
            <a href="#" style="color: #FFD700;">Home</a>
            <a href="movies_page.php">Movies</a>
            <a href="profile.php">Profile</a>
        </div>
    </div>

    <div class="content">
        <h2>Available YouTube Videos</h2>
        <div class="video-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {    
                    // Loop through each video record one by one
                    // fetch_assoc() allows accessing each column of the row directly
                    echo "<div class='video-card'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo $row['link']; // Embed video link
                    echo "</div>";
                }
            } else {
                echo "<p>No videos uploaded yet.</p>"; // Fallback if no videos exist
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
