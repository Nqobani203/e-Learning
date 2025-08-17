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
        <title>Admin|Prolearn - Add-Lectures</title>
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
                <a href="lecture.php" class="btn btn-primary">Back</a><br><br>

                <form class="shadow p-3 mt-5 form-w" method="post" action="req/lecture-add.php">
                    <div class="text-center">
                        <img src="../logo.png" width="180">
                    </div>
                    <hr>
                    <h3>Add Lecture</h3>
                    <hr>
                    <?php if(isset($_GET['error'])){?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_GET['error'] ?>
                    </div>
                        <?php }?>

                        <?php if(isset($_GET['success'])){?>
                    <div class="alert alert-success" role="alert">
                        <?= $_GET['success'] ?>
                    </div>
                        <?php }?>
                    <div class="mb-3">
                        <label class="form-label">First name</label>
                        <input type="text" class="form-control" name="fname">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last name</label>
                        <input type="text" class="form-control" name="lname">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="pass" id="passInput">
                            <button class="btn-secondary" id="gBtn">Randomize</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module</label>
                        <div class="row row-cols-5">
                            <input type="text" class="form-control" name="modules">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <input type="text" class="form-control" name="grade">
                    </div>

                    <button type="submit" class="btn btn-primary">Done</button>
                </form><br><br>


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

                function makePass(length) {
                    let result = '';
                    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    const charactersLength = characters.length;
                    let counter = 0;
                    while (counter < length) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                        counter += 1;
                    }
                    var passInput = document.getElementById('passInput');
                    passInput.value = result;
                }
                var gBtn = document.getElementById('gBtn');
                gBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    makePass(5);
                });
            </script>
    </body>

    </html>
<?php } else {
    header("Location:../login.php");
} ?>