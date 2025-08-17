<?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    $lecture_id = $_SESSION['lecture_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $reply_message = $_POST['reply_message'];
    $student_id = $_POST['student_id'];
    $lecture_id = $_SESSION['lecture_id'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sender_role, receiver_role) VALUES (?, ?, ?, 'lecture', 'student')");
    $stmt->bind_param("iis", $lecture_id, $student_id, $reply_message);

    if ($stmt->execute()) {
        echo "<script>alert('Reply sent successfully!'); window.location.href = 'messages.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
} else {
    header("Location: ../login.php");
}
?>
