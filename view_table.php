<?php
/****************************************************
 * DATABASE CONNECTION
 ****************************************************/
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


/****************************************************
 * FETCH BOOKINGS WITH USERNAMES
 ****************************************************/
// Join 'bookings' and 'users' table to get username for each booking
// We order by movie_name and booking_time to keep records organized
$query = "SELECT users.username, bookings.movie_name, bookings.seat_number, bookings.booking_time 
          FROM bookings 
          JOIN users ON bookings.user_id = users.id
          ORDER BY bookings.movie_name, bookings.booking_time ASC";

$result = $conn->query($query);

// Form a two-dimensional array $bookings[movie_name][] for easy display
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[$row['movie_name']][] = $row;
}


/****************************************************
 * FETCH USER DATA (excluding sensitive info)
 ****************************************************/
$userQuery = "SELECT username, gender, country, age, email_address FROM users";
$userResult = $conn->query($userQuery);

$users = [];
while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movie Bookings</title>
    <link rel="stylesheet" href="css/view_table.css">
</head>
<body>

    <a href="admin_dashboard.php" class="back-arrow">Admin Features</a>

    <!-- MOVIE BOOKING LIST -->
    <div class="section movie-section active" id="movieSection">
        <h1>Booking Database  
            <button class="toggle-symbol" onclick="toggleSection()">🔄</button>
        </h1>

        <?php if (!empty($bookings)): ?>  
            <?php foreach ($bookings as $movie_name => $records): ?>
                <div class="movie-section">
                    <h2><?php echo htmlspecialchars($movie_name); ?></h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Movie Name</th>
                                <th>Seat No</th>
                                <th>Booking Time</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['username']); ?></td>
                                <td><?php echo htmlspecialchars($record['movie_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['seat_number']); ?></td>
                                <td><?php echo htmlspecialchars($record['booking_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-bookings">No bookings available.</p>
        <?php endif; ?>
    </div>

    <!-- USER LIST -->
    <div class="section user-section" id="userSection">
        <?php if (!empty($users)): ?>
            <h1>User Database  
                <button class="toggle-symbol" onclick="toggleSection()">🔄</button>
            </h1>
            <div class="user-section">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Country</th>
                            <th>Age</th>
                            <th>Email Address</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['gender']); ?></td>
                            <td><?php echo htmlspecialchars($user['country']); ?></td>
                            <td><?php echo htmlspecialchars($user['age']); ?></td>
                            <td><?php echo htmlspecialchars($user['email_address']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-users">No users available.</p>
        <?php endif; ?>
    </div>

    <script>
        // Toggle between movie and user sections
        function toggleSection() {
            const movieSection = document.getElementById('movieSection');
            const userSection = document.getElementById('userSection');
            movieSection.classList.toggle('active');
            userSection.classList.toggle('active');
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
