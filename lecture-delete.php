<?php
session_start();
if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role']) &&
    isset($_GET['lecture_id'])
) {
    if ($_SESSION['role'] == 'Admin') {
        include "../database.php";
        include "data/lecture.php";

        $id = $_GET['lecture_id'];
        if(deletelecture($id, $conn)){
            $sm = "Lecture deleted";
            header("Location:lecture.php?success=$sm");
            exit;
        }else{
            $em = "Error occured!";
            header("Location:lecture.php?error=$em");
        }
    } else {
        header("Location:lecture.php");
        exit;
    }
} else {
    header("Location:lecture.php");
    exit;
}
