<?php
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

// Placeholder for fetching course IDs and names from the database
$sql = "SELECT course_id, course_name FROM courses";
$result = $conn->query($sql);

$courses = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[$row['course_id']] = $row['course_name'];
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $year = $_POST['year']; // Get selected year from dropdown
    $section = $_POST['section']; // Get selected section from dropdown
    $courseId = $_POST['course_id']; // Get course ID entered by the teacher
    $teacherId = $_POST['teacher_id']; // Get teacher ID entered by the teacher

    // Placeholder for database authentication
    // Replace this with your actual database query to authenticate teacher login and validate course ID
    // For simplicity, we'll just display the submitted data here
    // Perform authentication and course ID validation here...
    $authenticated = true; // Placeholder for authentication result
    $validCourseId = true; // Placeholder for course ID validation result

    if ($authenticated && $validCourseId) {
        // Authentication successful and course ID is valid, redirect to teachdisplay.php with parameters
        header("Location: teachdisplay.php?course_id=$courseId&year=$year&section=$section&teacher_id=$teacherId");
        exit();
    } else {
        // Authentication or course ID validation failed, display error message
        echo "<p>Authentication failed or invalid course ID. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Teacher Login</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="teacher_id" class="form-label">Teacher ID:</label>
                <input type="text" id="teacher_id" name="teacher_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Year:</label>
                <select id="year" name="year" class="form-select" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="section" class="form-label">Section:</label>
                <select id="section" name="section" class="form-select" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="course_id" class="form-label">Course:</label>
                <select id="course_id" name="course_id" class="form-select" required>
                    <?php foreach ($courses as $courseId => $courseName) : ?>
                        <option value="<?php echo $courseId; ?>"><?php echo $courseId . ' - ' . $courseName; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="mt-3">Don't have an account? <a href="teachsignin.php">Sign Up</a></p>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
