<?php
session_start();
if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role'])
) {

    if ($_SESSION['role'] == 'Admin') {

        if (
            isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['username']) &&
            isset($_POST['lecture_id']) &&
            isset($_POST['modules']) &&
            isset($_POST['grade'])
        ) {

            include '../../database.php';
            include '../data/lecture.php';
            include '../data/grade.php';
            //$modules = getAllmodules($conn);
            //$grades = getAllGrades($conn);


            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $username = $_POST['username'];
            $grades = $_POST['grade'];
            $modules = $_POST['modules'];
            $lecture_id = $_POST['lecture_id'];

            /*$grades = "";
            foreach ($_POST['grade'] as $grade) {
                $grades .= $grade;
            }
            $modules = "";
            foreach ($_POST['modules'] as $module) {
                $modules .= $module;
            }

            $data = 'lecture_id='.$lecture_id;*/


            if (empty($fname)) {
                $em = "First is required";
                header("Location:../lecture-edit.php?error=$em");
                exit;
            } else if (empty($lname)) {
                $em = "Last name is required";
                header("Location:../lecture-edit.php?error=$em");
                exit;
            } else if (empty($username)) {
                $em = "Username is required";
                header("Location:../lecture-edit.php?error=$em");
                exit;
            } else if (empty(!unameIsUnique($username, $conn, $lecture_id))) {
                $em = "Username is taken!";
                header("Location:../lecture-edit.php?error=$em");
                exit;
            }  
            else {
                $sql = "UPDATE lecture SET username=?, fname=?, lname=?, modules=?, grades=? WHERE lecture_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$lecture_id, $username, $fname, $lname, $modules, $grades]);
                $sm = "successfully updated!";
                header("Location:../lecture-edit.php?success=$sm");
                exit;
                
            }
        } else {
            $em = "An error occured";
            header("Location:../lecture-edit.php?error=$em");
            exit;
        }
    } else {
        header("Location:../../logout.php");
        exit;
    }
} else {
    header("Location:../../logout.php");
    exit;
}
