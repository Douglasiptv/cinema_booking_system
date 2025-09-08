<?php
/****************************************************
 * DATABASE CONNECTION
 ****************************************************/
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


/****************************************************
 * HANDLE FORM SUBMISSION
 ****************************************************/

if (isset($_POST["submit"])) {

    // Collect form data
    $title = $_POST["title"];           // Video title
    $description = $_POST["description"]; // Short description of the video
    $link = $_POST["link"];             // YouTube embed link (iframe or URL)

    /* 
    Beginner-friendly approach:
    Instead of uploading full video files, which requires
    storage space and complex handling, we only store
    YouTube video links. This keeps the project lightweight
    and easy to manage.
    */

    // Insert video details into the database
    $sql = "INSERT INTO youtube_videos (title, description, link) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $description, $link);

    if ($stmt->execute()) {
        echo "YouTube video link uploaded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>
