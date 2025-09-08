<?php
// ------------------- Database Connection -------------------
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create a connection object
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Stop execution if DB connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ------------------- Fetch Movie List -------------------
// Fetch all movies for management in delete panel
$query = "SELECT pid, title FROM poster";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>

    <!-- Fixed page heading -->
    <div class="fixed-heading">Admin Features</div>

    <!-- Back button to view_table.php -->
    <a href="view_table.php" class="back-arrow">&#9664; Back</a>

    <!-- ===================== Movie Setup Panel ===================== -->
    <div class="panel-wrapper">
        <div class="panel" id="addMoviePanel">
            <h1>Movie Setup</h1>
            <form action="movie_creation.php" method="POST" enctype="multipart/form-data">
                <label for="movie_name">Movie Name:</label>
                <input type="text" id="movie_name" name="movie_name" placeholder="Enter Movie Name" required>

                <label for="rows">Number of Rows:</label>
                <input type="number" id="rows" name="rows" min="1" placeholder="Enter Rows" required>

                <label for="columns">Number of Columns:</label>
                <input type="number" id="columns" name="columns" min="1" placeholder="Enter Columns" required>

                <label for="poster">Upload Movie Poster:</label>
                <input type="file" id="poster" name="poster" accept="image/*" required>

                <button type="submit">Set Movie & Seat Configuration</button>
            </form>

            <!-- Arrow to switch to Delete Movie Panel -->
            <div class="nav-arrow right-arrow" onclick="switchPanel('deleteMoviePanel')">▶</div>
        </div>

        <!-- ===================== Delete Movie Panel ===================== -->
        <div class="panel hidden" id="deleteMoviePanel">
            <h1>Manage Movies</h1>
            <div class="manage-movies">
                <?php
                // Loop through movies and create delete buttons
                while ($row = $result->fetch_assoc()) {
                    echo "<div>".$row['title']." 
                    <form action='delete_movie.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='pid' value='".$row['pid']."'>
                        <button type='submit'>Delete</button>
                    </form>
                    </div>";
                }
                ?>
            </div>

            <!-- Navigation arrows to switch between panels -->
            <div class="nav-arrow left-arrow" onclick="switchPanel('addMoviePanel')">◀</div>
            <div class="nav-arrow right-arrow" onclick="switchPanel('uploadVideoPanel')">▶</div>
        </div>

        <!-- ===================== Upload Video Panel ===================== -->
        <div class="panel hidden" id="uploadVideoPanel">
            <h1>Upload a YouTube Video</h1>
            <form action="upload_iframe.php" method="POST">
                <label>Video Title:</label>
                <input type="text" name="title" required>

                <label>Description:</label>
                <input type="text" name="description" required>

                <label>YouTube Embed Code:</label>
                <textarea name="link" placeholder='<iframe ...>' required></textarea>

                <button type="submit" name="submit">Upload</button>
            </form>

            <!-- Navigation arrow to switch to Delete Movie Panel -->
            <div class="nav-arrow left-arrow" onclick="switchPanel('deleteMoviePanel')">◀</div>
        </div>
    </div>

    <!-- ===================== Panel Switch Script ===================== -->
    <script>
        function switchPanel(panelId) {
            // Hide all panels first
            document.querySelectorAll('.panel').forEach(panel => panel.classList.add('hidden'));
            // Show the selected panel
            document.getElementById(panelId).classList.remove('hidden');
        }
    </script>

</body>
</html>

<?php
// Close DB connection
$conn->close();
?>
