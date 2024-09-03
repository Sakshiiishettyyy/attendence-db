<?php
session_start();

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

// Retrieve course, year, section, and course ID from URL parameters
$year = $_GET['year'] ?? '';
$section = $_GET['section'] ?? '';
$courseId = $_GET['course_id'] ?? '';

// Retrieve teacher ID from session (provided through login page)
$teacherId = $_GET['teacher_id'] ?? '';

// Fetch students based on selected year and section
$sql = "SELECT usn, name FROM students WHERE year = ? AND section = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $year, $section);
$stmt->execute();
$result = $stmt->get_result();

// Process attendance update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $selectedDate = $_POST['datepicker'] ?? ''; // Retrieve the selected date from the datepicker input field
    $attendances = $_POST['status'] ?? []; // Assuming you named the status select elements as status[] in the form

    // Prepare and execute SQL statement to insert or update attendance records
    $stmt = $conn->prepare("INSERT INTO attendance (teacher_id, course_id, usn, date, status) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
    
    // Define $selectedDate before using it in the foreach loop
    $selectedDate = $_POST['datepicker'] ?? '';

    // Bind parameters
    $stmt->bind_param("sssss", $teacherId, $courseId, $usn, $selectedDate, $status);

    // Iterate over the attendance data and insert or update records into the database
    foreach ($attendances as $usn => $status) {
        // Check if attendance record exists for the student, course, teacher, and date
        $checkStmt = $conn->prepare("SELECT * FROM attendance WHERE teacher_id = ? AND course_id = ? AND usn = ? AND date = ?");
        $checkStmt->bind_param("ssss", $teacherId, $courseId, $usn, $selectedDate);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // If attendance record exists, update the status
            $updateStmt = $conn->prepare("UPDATE attendance SET status = ? WHERE teacher_id = ? AND course_id = ? AND usn = ? AND date = ?");
            $updateStmt->bind_param("sssss", $status, $teacherId, $courseId, $usn, $selectedDate);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // If attendance record does not exist, insert a new record
            $stmt->execute();
        }

        $checkStmt->close();
    }

    // Close statement
    $stmt->close();

    // Redirect back to teachdisplay.php to prevent form resubmission
    header("Location: teachdisplay.php?year=$year&section=$section&course_id=$courseId&teacher_id=$teacherId");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
        }
        .status-btn-group .btn {
            margin-right: 10px;
            margin-bottom: 10px;
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
        <h2 class="mt-3 mb-4">Teacher Dashboard</h2>
        <!-- Display student list with options to mark present or absent -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?year=$year&section=$section&course_id=$courseId&teacher_id=$teacherId"; ?>" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>USN</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Check if attendance record exists for the student and date
                                $checkStmt = $conn->prepare("SELECT * FROM attendance WHERE teacher_id = ? AND course_id = ? AND usn = ? AND date = ?");
                                $checkStmt->bind_param("ssss", $teacherId, $courseId, $row['usn'], $selectedDate);
                                $checkStmt->execute();
                                $checkResult = $checkStmt->get_result();

                                $status = '';
                                if ($checkResult->num_rows > 0) {
                                    $attendanceRow = $checkResult->fetch_assoc();
                                    $status = $attendanceRow['status'];
                                }

                                echo "<tr>";
                                echo "<td>" . $row['usn'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td class='status-btn-group'>";
                                echo "<input type='radio' class='btn-check' name='status[" . $row['usn'] . "]' id='present-" . $row['usn'] . "' value='Present' autocomplete='off'" . ($status === 'Present' ? ' checked' : '') . ">";
                                echo "<label class='btn btn-outline-primary' for='present-" . $row['usn'] . "'>Present</label>";
                                echo "<input type='radio' class='btn-check' name='status[" . $row['usn'] . "]' id='absent-" . $row['usn'] . "' value='Absent' autocomplete='off'" . ($status === 'Absent' ? ' checked' : '') . ">";
                                echo "<label class='btn btn-outline-danger' for='absent-" . $row['usn'] . "'>Absent</label>";
                                echo "</td>";
                                echo "</tr>";

                                $checkStmt->close();
                            }
                        } else {
                            echo "<tr><td colspan='3'>No students found for the selected criteria.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="mb-3">
                <label for="datepicker" class="form-label">Select Date:</label>
                <input type="text" class="form-control" id="datepicker" name="datepicker" placeholder="<?php echo date('Y-m-d'); ?>" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery UI for datepicker -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $(function() {
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(selectedDate) {
                $("#datepicker").val(selectedDate); // Set the selected date to the datepicker input field
            }
        });
    });
    </script>
</body>
</html>
