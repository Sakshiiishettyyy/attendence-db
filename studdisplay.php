<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['usn'])) {
    // If not logged in, redirect to the login page
    header("Location: studlogin.php");
    exit(); // Ensure script stops executing after redirection
}

// Retrieve student details from the session
$usn = $_SESSION['usn'];

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

// Retrieve student details from the database based on the USN
$sql = "SELECT * FROM students WHERE usn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usn);
$stmt->execute();
$result = $stmt->get_result();

// Check if the student exists
if ($result->num_rows > 0) {
    // Student details found, display them
    $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, header {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .card-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 18px;
        }
        .notifications {
            display: none; /* Initially hide the notifications */
            margin-top: 20px;
        }
        .notifications.active {
            display: block; /* Display the notifications when active */
        }
        .notification {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .notification.below-85 {
            background-color: #f8d7da; /* Red background for below 85% */
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .notification.above-85 {
            background-color: #d4edda; /* Green background for above 85% */
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.html">Attendance System</a>
        </div>
    </nav>
</header>
<div class="container">
    <div class="card">
        <div class="card-title">Student Details</div>
        <div class="card-text">
            <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
            <p><strong>USN:</strong> <?php echo $row['usn']; ?></p>
            <p><strong>Year:</strong> <?php echo $row['year']; ?></p>
            <p><strong>Section:</strong> <?php echo $row['section']; ?></p>
            <!-- Add more details as needed -->
        </div>
        <button class="btn btn-primary mt-3" id="showNotifications">Show Notifications</button>
        <div class="notifications" id="notifications">
            <?php
            // Fetch the latest notification for each subject
            $sql = "SELECT n.message, c.course_name
                    FROM notifications n
                    JOIN courses c ON n.course_id = c.course_id
                    WHERE n.usn = ?
                    ORDER BY n.notification_id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $usn);
            $stmt->execute();
            $result = $stmt->get_result();

            $notifications = array();

            while ($row = $result->fetch_assoc()) {
                $course_name = $row['course_name'];
                $message = $row['message'];
                // Store the latest notification for each subject
                if (!isset($notifications[$course_name])) {
                    $notifications[$course_name] = $message;
                }
            }

            if (!empty($notifications)) {
                echo "<h3 class='mt-3'>Latest Notifications:</h3>";
                foreach ($notifications as $course_name => $message) {
                    preg_match('/(\d+(?:\.\d+)?)%/', $message, $matches);
                    if ($matches) {
                        $attendancePercentage = (float)$matches[1];
                        // Check if the attendance percentage is below or above 85%
                        if ($attendancePercentage < 85) {
                            echo "<div class='notification below-85'>";
                            echo "<p><strong>{$course_name}:</strong> {$message}</p>";
                            echo "<p>Improve your attendance.</p>";
                            echo "</div>";
                        } else {
                            echo "<div class='notification above-85'>";
                            echo "<p><strong>{$course_name}:</strong> {$message}</p>";
                            echo "</div>";
                        }
                    }
                }
            } else {
                echo "<p>No notifications</p>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("showNotifications").addEventListener("click", function() {
        document.getElementById("notifications").classList.toggle("active");
    });
</script>
</body>
</html>
<?php
} else {
    // Student not found
    echo "Student details not found.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
