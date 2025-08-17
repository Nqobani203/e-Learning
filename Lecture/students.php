<?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to count the total number of students
    $studentCountQuery = "SELECT COUNT(*) as total FROM student";
    $result = $conn->query($studentCountQuery);
    $studentCount = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $studentCount = $row['total'];
    }

    // Fetch student information
    $sql = "SELECT student_id, email, username, pass FROM student";
    $result = $conn->query($sql);

    // Count only unread messages for this lecturer
    $countQuery = "SELECT COUNT(*) AS total_messages FROM messages WHERE lecturer_id = {$_SESSION['lecture_id']} AND viewed = 0";
    $countResult = $conn->query($countQuery);
    $totalMessages = 0;
    if ($countResult && $countRow = $countResult->fetch_assoc()) {
        $totalMessages = $countRow['total_messages'];
    }

    // Fetch unread messages and student information
    $messagesQuery = "
        SELECT student.email AS student_email, student.username, messages.message
        FROM messages
        JOIN student ON messages.student_id = student.student_id
        WHERE messages.lecturer_id = {$_SESSION['lecture_id']} AND messages.viewed = 0";
    $messagesResult = $conn->query($messagesQuery);

    // Update viewed status to mark messages as read after fetching them
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE lecturer_id = {$_SESSION['lecture_id']} AND viewed = 0";
    $conn->query($updateViewed);

    $conn->close();
?>


    <!--?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT student_id, email, username, pass FROM student";
    $result = $conn->query($sql);
        // Get the total number of messages
        $countQuery = "SELECT COUNT(*) AS total_messages FROM messages";
        $countResult = $conn->query($countQuery);
        $countRow = $countResult->fetch_assoc();
        $totalMessages = $countRow['total_messages'];
    
        // Fetch messages and student info
        $messagesQuery = "
            SELECT student.email AS student_email, student.username, messages.message
            FROM messages
            JOIN student ON messages.student_id = student.student_id";
        $messagesResult = $conn->query($messagesQuery);
?>-->

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student List</title>
        <link rel="stylesheet" href="lecture.css">
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    </head>

    <body>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 1em;
                min-width: 400px;
                background-color: #ffffff;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            }

            table thead tr {
                background-color: rgb(102, 67, 116);
                color: #ffffff;
                text-align: left;
                font-weight: bold;
            }

            table th,
            table td {
                padding: 12px 15px;
                border: 1px solid #dddddd;
            }

            table tbody tr {
                border-bottom: 1px solid #dddddd;
            }

            table tbody tr:nth-of-type(even) {
                background-color: #f3f3f3;
            }

            table tbody tr:last-of-type {
                border-bottom: 2px solid #4CAF50;
            }

            table tbody tr:hover {
                background-color: #f1f1f1;
            }
        </style>
        <div class="container">
            <div class="nav"> <!-- Navigation same as in lecture.php -->
                <ul>
                    <li>
                        <a href="#">

                            <span class="title" style="font-family: Lobster, sans-serif; margin-top:15px;"><img src="../logo.png" width="90"></span>

                        </a>
                    </li>


                    <li>
                        <a href="index.php">
                            <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                            <span class="title">Dashboard</span>
                        </a>
                    </li>



                    <li>
                        <a href="students.php">
                            <span class="icon"><ion-icon name="school-outline"></ion-icon></span>
                            <span class="title">Students</span>
                        </a>
                    </li>

                    <li>
                        <a href="modules.php">
                            <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                            <span class="title">Modules</span>
                        </a>
                    </li>


                    <li>
                        <a href="#">
                            <span class="icon"><ion-icon name="albums-outline"></ion-icon></span>
                            <span class="title">Timetables</span>
                        </a>
                    </li>



                    <li>
                        <a href="messages.php">
                            <span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span>
                            <span class="title">messages<span class="message-count">(<?php echo $totalMessages; ?>)</span></span>
                        </a>
                    </li>

                    <li>
                        <a href="../logout.php">
                            <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                            <span class="title">LogOut</span>
                        </a>
                    </li>


                </ul>
            </div>

            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Student List</h1>
                </div>

                <div class="cardBox">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["student_id"] . "</td>";
                                    echo "<td>" . $row["email"] . "</td>";
                                    echo "<td>" . $row["username"] . "</td>";
                                    echo "<td>" . $row["pass"] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No students found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                // menu toggle
                let toggle = document.querySelector(".toggle");
                let navigation = document.querySelector(".nav");
                let main = document.querySelector(".main");

                toggle.onclick = function() {
                    navigation.classList.toggle("active");
                    main.classList.toggle("active");
                }
            </script>
            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        </div>
    </body>
<?php
} else {
    header("Location:../login.php");
}
?>

    </html>