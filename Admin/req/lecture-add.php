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
            isset($_POST['pass']) &&
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
            $pass = $_POST['pass'];
            $modules = $_POST['modules'];
            $grades = $_POST['grade'];

            /*$grades ="";
            foreach ($_POST['grades'] as $grade){
                $grades .= $grade; 
            }
            $modules ="";
            foreach ($_POST['modules'] as $module){
                $modules .= $module; 
            }*/

            $data = '&fname=' .$fname.'&lname=' .$lname;
            if (empty($fname)) {
                $em = "First is required";
                header("Location:../lecture-add.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em = "Last name is required";
                header("Location:../lecture-add.php?error=$em&$data");
                exit;
            } else if (empty($username)) {
                $em = "Username is required";
                header("Location:../lecture-add.php?error=$em&$data");
                exit;
            } else if (empty(!unameIsUnique($username, $conn))) {
                $em = "Username is taken!";
                header("Location:../lecture-add.php?error=$em&$data");
                exit;
            } else if (empty($pass)) {
                $em = "Password is required!";
                header("Location:../lecture-add.php?error=$em&$data");
                exit;
            } else {
                /*$pass = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO lecture(username, fname, lname, modules, grades, pass) VALUES=(?, ?, ?, ?, ?, ?)";*/

                $stmt =$conn->prepare('INSERT INTO lecture (username,fname,lname,modules,grades,pass) VALUES (?,?,?,?,?,?);');
                $pass = password_hash($pass, PASSWORD_DEFAULT);

                if(!$stmt->execute(array($username, $fname, $lname, $modules,$grades, $pass))){
                    $stmt = NULL;
                    exit();
                }
                $stmt=NULL;

                /*$stmt = $conn->prepare($sql);
                $stmt->execute([$username, $fname, $lname, $modules, $grades, $pass]);*/

                $sm = "New Lecture added successfully";
                header("Location:../lecture-add.php?success=$sm");
                exit;
            }
        } else {
            $em = "An error occured";
            header("Location:../lecture-add.php?error=$em");
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
