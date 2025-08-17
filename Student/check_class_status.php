<?php
session_start();
header("Content-Type: application/json");

echo json_encode(["class_active" => $_SESSION['class_active'] ?? false]);
?>
