<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $lecture_id = $conn->real_escape_string($data['lecture_id']);
    $sql = "UPDATE lecture SET class_active = 1 WHERE lecture_id = '$lecture_id'";
    $conn->query($sql);
    $conn->close();
}
?>
