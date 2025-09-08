<?php 

// Database connection details
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "cinema";
$port = 4306;

// Create a database connection object using MySQLi
// $conn now represents the database, which we use to send queries and retrieve results
$conn = new mysqli($servername, $username, $password, $dbname, $port); 

// Check if the connection was successful
// If not, stop the script and show an error message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$responseMessage = ""; 

// Superglobals in PHP: $_GET, $_POST, $_SESSION, $_SERVER, $_COOKIE
// Here we use $_SERVER['REQUEST_METHOD'] to check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data and store in PHP variables
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password for security
    $email = $_POST['email_address'];  
    $age = intval($_POST['age']); // Ensure age is an integer
    $gender = $_POST['gender'];  
    $country = $_POST['country'];  

    // SQL query to insert new user into the database
    // Use the $conn object to execute the query
    $sql = "INSERT INTO users (username, password, email_address, age, gender, country) 
            VALUES ('$username', '$password', '$email', $age, '$gender', '$country')";
    
    // Execute the query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        $responseMessage = "User registered successfully!";
    } else {
        $responseMessage = "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Booking - Register</title>
    <link rel="stylesheet" href="css/reg.css">
</head>
<body>

    <div class="container">
        <h1>Register</h1>
        <h2>Join the Ultimate Cinema Experience</h2>
        <p>Enter your details to get started.</p>

        <!-- Display Response Message -->
        <?php if (!empty($responseMessage)) : ?>
            <p style="color: #FFD700;"><?php echo htmlspecialchars($responseMessage); ?></p>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="email" name="email_address" placeholder="Enter Email Address" required>
            <input type="number" name="age" placeholder="Enter Age" min="1" required>

            <select name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <input type="text" name="country" placeholder="Enter Country of Origin" required>

            <button type="submit">Register</button>
        </form>

        <div class="redirect-btn">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>

</body>
</html>
