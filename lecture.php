<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../database.php";
        include "data/lecture.php";
        include "data/module.php";
        include "data/grade.php";
        $lectures = getAlllectures($conn);
       
        /*echo "<pre>";
        print_r($modules);
        echo "</pre>";*/

    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Lectures</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="icon" href="logo.png" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script>
            setInterval(function() {
                location.reload();
            }, 60000);
        </script>
    </head>

    <body>
        <?php
        include "inc/navbar.php";
        if ($lectures != 0) {
        ?>
            <div class="container mt-5">
                <a href="lecture-add.php" class="btn btn-primary">Add new lecture</a>

                <?php if(isset($_GET['error'])){?>
                <div class="alert alert-danger mt-5" role="alert">
                    <?=$_GET['error']?>
                </div>
                <?php } ?>
                <?php if(isset($_GET['success'])){?>
                <div class="alert alert-success mt-5" role="alert">
                    <?=$_GET['success']?>
                </div>
                <?php } ?>

                <div class="table-responsive">

                    <table class="table table-bordered mt-3 n-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Username</th>
                                <th scope="col">Module</th>
                                <th scope="col">Grade</th>
                                <th scope="col">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lectures as $lecture){ ?>
                            <tr>
                                <th scope="row"><?=$lecture['lecture_id']?></th>
                                <td><?=$lecture['lecture_id']?></td>
                                <td><?=$lecture['fname']?></td>
                                <td><?=$lecture['lname']?></td>
                                <td><?=$lecture['username']?></td>
                                <td><?php
                                $s ='';
                                $modules = str_split(trim($lecture['modules']));
                                foreach($modules as $module){
                                    $s_temp = getmodulesById($module, $conn);
                                    if ($s_temp !=0)
                                        $s .=$s_temp['module_code'];
                                }
                                echo $s;
                                ?>
                                </td>
                                <td><?php
                                $g ='';
                                $grades = str_split(trim($lecture['grades']));
                                foreach($grades as $grade){
                                    $g_temp = getGradeById($grade, $conn);
                                    if ($g_temp !=0)
                                        $g .=$g_temp['grade_code']. '-'.$g_temp['grade'];
                                }
                                echo $g;
                                ?></td>
                                <td>
                                    <a href="lecture-edit.php?lecture_id=<?=$lecture['lecture_id']?>" class="btn btn-warning">Edit</a>
                                    <a href="lecture-delete.php?lecture_id=<?=$lecture['lecture_id']?>" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="alert alert-info .w-450 m-5" role="alert">
                    Empty!
                </div>

            <?php } ?>
            </div>


            <div class="text-center text-light">
                Copyright &copy; 2024 Prolearn. All rights reserved.
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    $("#navLinks li:nth-child(2) a").addClass('active');
                });
            </script>
    </body>

    </html>
<?php } else {
    header("Location:../login.php");
} ?>