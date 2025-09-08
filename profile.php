<?php
// Start session to track logged-in user info across pages
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user's data
$user_username = $_SESSION['username']; 
$sql_user = "SELECT * FROM users WHERE username = '$user_username'";
$result_user = $conn->query($sql_user);
$user_data = $result_user->fetch_assoc();

// Fetch this user's bookings
$sql_bookings = "SELECT * FROM bookings WHERE user_id = ".$user_data['id'];
$result_bookings = $conn->query($sql_bookings);

// Fetch this user's profile picture
$sql_pic = "SELECT profile_picture FROM profile_pic WHERE user_id = ".$user_data['id'];
$result_pic = $conn->query($sql_pic);
$profile_pic = $result_pic->fetch_assoc();

// Handle profile picture update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_picture'])) {
    if ($_FILES['profile_picture']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            if ($profile_pic) {
                // Update existing picture
                $sql_pic_update = "UPDATE profile_pic SET profile_picture = '$target_file' WHERE user_id = ".$user_data['id'];
                $conn->query($sql_pic_update);
            } else {
                // Insert new picture
                $sql_pic_insert = "INSERT INTO profile_pic (user_id, profile_picture) VALUES (".$user_data['id'].", '$target_file')";
                $conn->query($sql_pic_insert);
            }
        }
    }

    // Refresh page to reflect changes
    header('Location: profile.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>

<!-- Back Button -->
<a href="homepage.php" class="back-button">&larr; Back</a>

<div class="profile-container">

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-pic-container">
            <?php if ($profile_pic && $profile_pic['profile_picture']): ?>
                <img src="<?php echo $profile_pic['profile_picture']; ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="uploads/default_profile_pic.png" alt="Profile Picture">
            <?php endif; ?>
            <button onclick="openModal()">View Image</button>
            <button onclick="showUploadForm()">Change Picture</button>
        </div>
        <h2><?php echo htmlspecialchars($user_data['username']); ?></h2>
    </div>

    <!-- Personal Information -->
    <div class="personal-info">
        <h3>Personal Information</h3>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email_address']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user_data['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user_data['gender']); ?></p>
        <p><strong>Country:</strong> <?php echo htmlspecialchars($user_data['country']); ?></p>
    </div>

    <!-- Booking Information -->
    <div class="booking-info">
        <h3>Booking Information</h3>
        <?php if ($result_bookings->num_rows > 0): ?> 
            <table>
                <tr>
                    <th>Movie Name</th>
                    <th>Seat Number</th>
                    <th>Booking Time</th>
                </tr>
                <?php while ($booking = $result_bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['movie_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['seat_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no bookings yet.</p>
        <?php endif; ?>
    </div>

    <!-- Update Profile Picture Form -->
    <div class="update-profile" id="uploadForm" style="display: none;">
        <h3>Update Profile Picture</h3>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <label for="profile_picture">Upload New Profile Picture (Optional):</label>
            <input type="file" name="profile_picture">
            <button type="submit" name="update_picture">Update Profile Picture</button>
        </form>
    </div>
</div>

<!-- View Image Modal -->
<div class="view-image-modal" id="viewImageModal">
    <button class="close-btn" onclick="closeModal()">Close</button>
    <img src="<?php echo $profile_pic ? $profile_pic['profile_picture'] : 'uploads/default_profile_pic.png'; ?>" alt="Profile Picture">
</div>

<script>
    // Show the picture upload form
    function showUploadForm() {
        document.getElementById('uploadForm').style.display = 'block';
    }

    // Open modal to view profile picture
    function openModal() {
        document.getElementById('viewImageModal').style.display = 'flex';
    }

    // Close modal
    function closeModal() {
        document.getElementById('viewImageModal').style.display = 'none';
    }
</script>
</body>
</html>
