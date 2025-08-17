<?php
// End any active session for the current lecturer
$endPreviousSession = "UPDATE class_sessions SET status = 'ended', end_time = NOW() WHERE lecture_id = ? AND status = 'active'";
$stmtEnd = $conn->prepare($endPreviousSession);
$stmtEnd->bind_param("i", $lectureId);
$stmtEnd->execute();
$stmtEnd->close();

session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update a table or signal the start of the class (e.g., mark the start time or update a 'class status' column)
    $lectureId = $_SESSION['lecture_id'];
    $startClassQuery = "INSERT INTO class_sessions (lecture_id, start_time, status) VALUES (?, NOW(), 'active')";
    
    $stmt = $conn->prepare($startClassQuery);
    if ($stmt) {
        $stmt->bind_param("i", $lectureId);
        if ($stmt->execute()) {
            // Respond with success
            echo json_encode(["status" => "success", "message" => "Class session started."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to start class session."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }

    $conn->close();
} else {
    // If not logged in or no proper session, deny access
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
}
?>
