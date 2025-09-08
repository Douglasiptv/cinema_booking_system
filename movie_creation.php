<?php
/****************************************************
 * SECTION 1: DATABASE CONNECTION
 ****************************************************/
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/****************************************************
 * SECTION 2: COLLECT FORM DATA (MOVIE INFO)
 ****************************************************/
$movie_name = $_POST['movie_name']; 
$rows = intval($_POST['rows']);     
$columns = intval($_POST['columns']); 
$movie_name = mysqli_real_escape_string($conn, $movie_name);  // Sanitize the movie name to avoid SQL injection

/****************************************************
 * SECTION 3: HANDLE POSTER IMAGE UPLOAD
 ****************************************************/
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) 
{
    $upload_directory = 'uploads/';
    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    $poster = $_FILES['poster'];
    $poster_name = $poster['name'];
    $poster_tmp_name = $poster['tmp_name'];
    $file_ext = strtolower(pathinfo($poster_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    // Check file type
    if (!in_array($file_ext, $allowed_exts)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    // Rename file to unique name
    $new_file_name = uniqid('', true) . '.' . $file_ext;
    $target_path = $upload_directory . $new_file_name;

    // Move file into uploads folder
    if (move_uploaded_file($poster_tmp_name, $target_path)) {
        // Insert movie name + poster into poster table
        $query = "INSERT INTO poster (title, poster_image) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $movie_name, $target_path);

        if ($stmt->execute()) {
            echo "Poster uploaded successfully!<br>";

            // Save the poster ID for linking with movie table
            $poster_id = $conn->insert_id;

        } else {
            die("Error inserting poster into database.");
        }
    } else {
        die("Failed to upload the image. Please try again.");
    }
}

/****************************************************
 * SECTION 4: CREATE MOVIE-SPECIFIC SEAT TABLE
 ****************************************************/
$table_name = $movie_name;
$table_check_query = "SHOW TABLES LIKE '$table_name'";   // Check if the table already exists

if ($conn->query($table_check_query)->num_rows == 0) 
{
    $create_table_query = "
        CREATE TABLE `$table_name` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pid INT NOT NULL,
            row INT NOT NULL,
            col INT NOT NULL,
            is_booked BOOLEAN NOT NULL DEFAULT FALSE,
            FOREIGN KEY (pid) REFERENCES poster(pid) 
        )";
    if ($conn->query($create_table_query) === TRUE) {
        echo "Table for '$movie_name' created successfully.<br>";
    } else {
        die("Movie already exits!!" . $conn->error);
    }
}

/****************************************************
 * SECTION 5: POPULATE SEATS FOR THE MOVIE
 ****************************************************/
for ($row = 1; $row <= $rows; $row++) 
{
    for ($col = 1; $col <= $columns; $col++) {
        $sql = "INSERT INTO `$table_name` (pid, row, col, is_booked) VALUES (?, ?, ?, FALSE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $poster_id, $row, $col);
        
        if (!$stmt->execute()) {
            die("Error inserting seat: " . $conn->error);
        }
    }
}
echo "Seat configuration for '$movie_name' updated successfully!<br>";

// Section 4: query() and Section 5: prepare(), they both are connection object's methods.
// While query() can use used for all SQL commands, generally and widely,
// prepare() is only used for inserting data, but place-holders " ? " are used, so, prepare() doesn't input the data at once.
// It leaves the placeholder and later, can manipulate the values with any kind of reference we want, so, much more flexible way of inserting data compared to query() which inputs data at once.

$conn->close();
?>
