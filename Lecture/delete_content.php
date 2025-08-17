<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content_id'])) {
    $content_id = intval($_POST['content_id']); // Sanitizing content_id

    $conn = new mysqli("localhost", "root", "", "sms_db"); // Adjust credentials

    // Fetch the file path to delete from the server
    $sql = "SELECT file FROM content WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $content_id);
    $stmt->execute();
    $stmt->bind_result($file);
    $stmt->fetch();
    $stmt->close();

    // Delete file from the server and database
    if ($file && unlink("uploads/" . $file)) {
        $delete_sql = "DELETE FROM content WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $content_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }else{
        $delete_sql = "DELETE FROM content WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $content_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    $conn->close();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
