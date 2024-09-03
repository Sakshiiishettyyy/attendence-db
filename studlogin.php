<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    $servername = "localhost";
    $username = "root"; // Update with your actual username
    $password = ""; // Update with your actual password
    $dbname = "attendancedb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $usn = $_POST['usn'];
    $password = $_POST['password'];

    // Prepare SQL statement
    $sql = "SELECT * FROM students WHERE usn = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usn, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if login credentials are correct
    if ($result->num_rows > 0) {
        // Login successful
        // Set session variables
        $_SESSION['usn'] = $usn;
        
        // Redirect to student dashboard or another page
        header("Location: studdisplay.php");
        exit(); // Ensure script stops executing after redirection
    } else {
        // Login failed
        echo "Invalid USN or password";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        .form-control {
            margin-bottom: 10px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #007bff;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="usn" class="form-label">USN:</label>
                <input type="text" id="usn" name="usn" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <input type="submit" value="Login" class="btn btn-primary">
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="studsignin.php">Sign up</a></p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
