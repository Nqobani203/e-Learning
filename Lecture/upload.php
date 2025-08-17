<?php
$targetDir = "uploads/";
$title = $_POST['title'];
$type = $_POST['type'];
$module_name = $_GET['module'];
$module_id = $_POST['module_id'];
$videoId = $_POST['videoId'];

$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// uploading videos from YouTube 
// Check if the request is for a video
if (isset($_POST['videoId'])) {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        // Return a JSON response with the error
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }
    // Insert video data into the content table
    $sql = "INSERT INTO content (title, type, file, module_id) VALUES ('$title', 'video', '$videoId', '$module_id')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $conn->close();
    exit;
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO content (title, type, file, module_id) VALUES ('$title', '$type', '$fileName', '$module_id')";
    if ($conn->query($sql) === TRUE) {
        // Redirect with success parameter
        header("Location:module_page.php?module={$module_name}&module_code={$module_code}&module_id={$module_id}upload=success");
        exit;
    } else {
        // Redirect with error parameter
        header("Location: modules.php?upload=error");
        exit;
    }
    $conn->close();
} else {
    header("Location: modules.php?upload=error");
    exit;
}
