<?php
session_start();

// Assuming you have stored the current session ID during class start
if (isset($_SESSION['current_session_id'])) {
    $sessionId = $_SESSION['current_session_id'];

    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $updateSession = "UPDATE class_sessions SET status = 'ended', end_time = NOW() WHERE session_id = ? AND status = 'active'";
    $stmt = $conn->prepare($updateSession);
    $stmt->bind_param("i", $sessionId);

    if ($stmt->execute()) {
        echo "Class session ended successfully.";
        unset($_SESSION['current_session_id']); // Optionally clear the session ID
    } else {
        echo "Error ending session: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No active session found to end.";
}
?>
