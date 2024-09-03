<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Assuming root user
$password = ""; // Assuming blank password
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $id = $_POST['id'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $courseId = $_POST['course_id'];
    $courseName = $_POST['course_name']; // Get course name from user input
    $password = $_POST['password'];
    
    // Prepare SQL statement to insert data into teacher table
    $sqlTeacher = "INSERT INTO teachers (id, name, year, section, password) VALUES ('$id', '$name', '$year', '$section', '$password')";

    // Prepare SQL statement to insert data into course table
    $sqlCourse = "INSERT INTO courses (course_id, course_name, year, section, teacher_id) VALUES ('$courseId', '$courseName', '$year', '$section', '$id')";

    // Execute SQL statements
    if ($conn->query($sqlTeacher) === TRUE && $conn->query($sqlCourse) === TRUE) {
        echo "<h2>Teacher Signup Successful</h2>";
        echo "<p>Name: $name</p>";
        echo "<p>ID: $id</p>";
        echo "<p>Year: $year</p>";
        echo "<p>Section: $section</p>";
        echo "<p>Course Id: $courseId</p>";
        echo "<p>Course Name: $courseName</p>";
        
        // Redirect to a login page after successful signup
        header("Location: teachlogin.php");
        exit();
    } else {
        echo "Error: " . $sqlTeacher . "<br>" . $conn->error;
        echo "Error: " . $sqlCourse . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Signup</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Teacher Signup</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="id" class="form-label">ID:</label>
                <input type="text" id="id" name="id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Year:</label>
                <input type="number" id="year" name="year" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="section" class="form-label">Section:</label>
                <input type="text" id="section" name="section" class="form-control" required>
            </div>
            <div class="mb-3">
    <label for="course_id" class="form-label">Course Id:</label>
    <input type="text" id="course_id" name="course_id" class="form-control" pattern="[A-Za-z0-9]+" required>
</div>

            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name:</label>
                <input type="text" id="course_name" name="course_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <input type="submit" value="Signup" class="btn btn-primary">
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
