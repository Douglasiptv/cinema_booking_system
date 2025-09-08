<?php
session_start(); // Start a session to store user info across pages using $_SESSION

// Create a database connection
$conn = new mysqli("localhost", "root", "your_password", "cinema", 4306);

// Process form data when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL query to find user by username
    // $result links the query to the database via $conn
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data as an associative array
        $user = $result->fetch_assoc();

        // Verify the password against the hashed password in DB
        if (password_verify($password, $user['password'])) {
            // Store user ID and username in session for use across pages
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect logged-in user to homepage
            header("Location: homepage.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}

// Close the database connection
$conn->close();

// Summary for beginners:
// - session_start() lets us use $_SESSION to remember user info across pages.
// - If username exists and password matches, user_id and username are stored in $_SESSION.
// - This allows the user to stay "logged in" as they navigate the site.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Booking - Login</title>
    <link rel="stylesheet" href="css/login.css">

</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <!-- Error message -->
        <?php if (!empty($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <!-- Login Form -->
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="redirect">
            Don't have an account? <a href="reg.php">Register here</a>
        </div>
    </div>
</body>
</html>
