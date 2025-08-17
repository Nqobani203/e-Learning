<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home - Prolearn</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="icon" href="logo.png" type="image/x-icon">
    </head>

    <body class="body-home">
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="shadow w-450 p-3 text-center">
                <small>
                    Role:
                    <b>
                        <?php
                        if ($_SESSION['role'] == 'Admin') {
                            echo "Admin";
                        } else if ($_SESSION['Lecture']) {
                            echo "Lecture";
                        } else {
                            echo "Student";
                        }
                        ?>
                    </b><br>
                    <h3><?= $_SESSION['username'] ?></h3>
                    <a href="logout.php" class="btn btn-warning">
                        logout
                    </a>
                </small>
            </div>
        </div>
        <div class="text-center text-light">
            Copyright &copy; 2024 Prolearn. All rights reserved.
        </div>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php } else {
    header("Location:../login.php");
} ?>