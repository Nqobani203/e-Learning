<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $student_id = $conn->real_escape_string($data['student_id']);
    $sql = "UPDATE student SET status = 'joined' WHERE student_id = '$student_id'";
    $conn->query($sql);
    $conn->close();
}
?>
