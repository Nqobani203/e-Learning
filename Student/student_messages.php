<?php
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role'])) {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the total number of responses
    $countQuery = "SELECT COUNT(*) AS total_responses FROM messages WHERE receiver_id = {$_SESSION['student_id']} AND sender_role = 'lecture' AND viewed=0";
    $countResult = $conn->query($countQuery);

    if ($countResult && $countRow = $countResult->fetch_assoc()) {
        $totalResponses = $countRow['total_responses'];
    } else {
        echo "Error with count query: " . $conn->error;
        $totalResponses = 0; // Set to zero if query fails
    }

    // Fetch responses from lecturers
    $responsesQuery = "SELECT lecture.username AS lecturer_username, lecture.lname AS lecturer_name, messages.message, messages.timestamp 
                       FROM messages
                       JOIN lecture ON messages.sender_id = lecture.lecture_id
                       WHERE messages.receiver_id = {$_SESSION['student_id']} AND messages.sender_role = 'lecture'
                       ORDER BY messages.timestamp DESC";
    $responsesResult = $conn->query($responsesQuery);

    if (!$responsesResult) {
        // Display error if the query failed
        echo "Error with responses query: " . $conn->error;
    }
    // Update viewed status
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE receiver_id = {$_SESSION['student_id']} AND sender_role = 'lecture' AND viewed = 0";
    $conn->query($updateViewed);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn - Student Responses</title>
        <link rel="stylesheet" href="student.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>
        <style>
            .content-section {
                padding: 20px;
            }

            .messages-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .messages-table th,
            .messages-table td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: left;
            }

            .messages-table th {
                background-color: #f4f4f4;
                font-weight: bold;
                color: #333;
            }

            .messages-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .messages-table tr:hover {
                background-color: #eaeaea;
            }
        </style>
        <div class="container">
            <!-- Sidebar Navigation (same as student.php) -->
            <div class="nav">
                <ul>
                    <li><a href="#"><span class="title" style="font-family: Lobster, sans-serif; margin-top:15px;"><img src="../logo.png" width="90"></span></a></li>
                    <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                    <li><a href="mymodules.php"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">My modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="student_messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Responses<span class="message-count">(<?php echo $totalResponses; ?>)</span></span></a></li>
                    <li><a href="../logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">LogOut</span></a></li>
                </ul>
            </div>

            <!-- Main Section -->
            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Responses from Lecturers</h1>
                </div>

                <!-- Responses Table Section -->
                <div class="content-section">
                    <h2>Responses</h2>
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>Lecturer Username</th>
                                <th>Lecturer Last Name</th>
                                <th>Response</th>
                                <th>Date Sent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($responsesResult && $responsesResult->num_rows > 0) {
                                while ($row = $responsesResult->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['lecturer_username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['lecturer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No responses from lecturers yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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
        <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" type="module"></script>
        <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js" nomodule></script>
    </body>
<?php
    $conn->close();
} else {
    header("Location: ../login.php");
}
?>

    </html>




    <!--?php
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role'])) {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

        // Get the total number of messages
        $countQuery = "SELECT COUNT(*) AS total_messages FROM messages";
        $countResult = $conn->query($countQuery);
        $countRow = $countResult->fetch_assoc();
        $totalMessages = $countRow['total_messages'];

    // Fetch messages from the lecturer
    $messagesQuery = "SELECT lecturer.email AS lecturer_email, lecturer.username AS lecturer_name, messages.message 
                      FROM messages
                      JOIN lecturer ON messages.lecturer_id = lecturer.lecture_id
                      WHERE messages.student_id = {$_SESSION['student_id']}";
    $messagesResult = $conn->query($messagesQuery);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn - Student Messages</title>
        <link rel="stylesheet" href="student.css">
        <link rel="stylesheet" href="../Lecture/messa">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>
        <style>
            .content-section {
                padding: 20px;
            }

            .messages-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .messages-table th,
            .messages-table td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: left;
            }

            .messages-table th {
                background-color: #f4f4f4;
                font-weight: bold;
                color: #333;
            }

            .messages-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .messages-table tr:hover {
                background-color: #eaeaea;
            }
        </style>
        <div class="container">
            <! Sidebar Navigation (same as student.php) 
            <div class="nav">
                <ul>
                    <li><a href="#"><span class="title" style="font-family: Lobster, sans-serif; margin-top:15px;"><img src="../logo.png" width="90"></span></a></li>
                    <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">My modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">My profile</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="student_messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Messages<span class="message-count">(<?php echo $totalMessages; ?>)</span></span></a></li>
                    <li><a href="../logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">LogOut</span></a></li>
                </ul>
            </div>

            <!-- Main Section -
            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Messages from Lecturer</h1>
                </div>

                <!-- Messages Table Section --
                <div class="content-section">
                    <h2>Messages</h2>
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>Lecturer Email</th>
                                <th>Lecturer Name</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!?php
                            if ($messagesResult->num_rows > 0) {
                                while ($row = $messagesResult->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['lecturer_email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['lecturer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No messages from the lecturer.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" type="module"></script>
        <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js" nomodule></script>
    </body>
<!?php
    $conn->close();
} else {
    header("Location: ../login.php");
}
?>

    </html>