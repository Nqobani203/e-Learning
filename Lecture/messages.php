
<?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    $lecture_id = $_SESSION['lecture_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "sms_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Count total messages received by the lecture
    $countQuery = "SELECT COUNT(*) AS total_messages FROM messages WHERE receiver_id = ? AND receiver_role = 'lecture' AND viewed=0";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("i", $lecture_id);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalMessages = ($countRow = $countResult->fetch_assoc()) ? $countRow['total_messages'] : 0;

    // Fetch messages sent by students to this lecture
    $query = "SELECT m.message, m.sender_id, m.timestamp, s.username AS student_username 
              FROM messages m 
              JOIN student s ON m.sender_id = s.student_id 
              WHERE m.receiver_id = ? AND m.receiver_role = 'lecture' AND m.sender_role = 'student'
              ORDER BY m.timestamp DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lecture_id);
    $stmt->execute();
    $messages = $stmt->get_result();
    // Update viewed status
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE lecturer_id = {$_SESSION['lecture_id']} AND viewed = 0";
    $conn->query($updateViewed);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn - Messages</title>
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="lecture.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>
        <style>
            /* General Table Styling */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            }

            table th,
            table td {
                padding: 15px;
                text-align: left;
                border-bottom: 1px solid #e1e1e1;
            }

            /* Table Header */
            table th {
                background-color: #4CAF50;
                color: white;
                font-weight: bold;
                text-transform: uppercase;
            }

            /* Alternate Row Colors */
            table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            /* Hover Effect */
            table tr:hover {
                background-color: #e9f9e9;
            }

            /* Reply Form Styling */
            table td form {
                display: flex;
                flex-direction: column;
            }

            table td textarea {
                margin-top: 5px;
                padding: 8px;
                resize: none;
                font-size: 14px;
                font-family: Arial, sans-serif;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
                height: 60px;
                box-sizing: border-box;
            }

            /* Reply Button Styling */
            table td button {
                margin-top: 8px;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: bold;
                transition: background-color 0.3s;
            }

            table td button:hover {
                background-color: #45a049;
            }

            /* Responsive Adjustments */
            @media screen and (max-width: 768px) {

                table,
                table th,
                table td {
                    font-size: 14px;
                }

                table td textarea {
                    height: 50px;
                }
            }
        </style>
        <div class="container">
            <!-- Sidebar Navigation -->
            <div class="nav">
                <ul>
                    <li>
                        <a href="#">
                            <span class="title"><img src="../logo.png" width="90"></span>
                        </a>
                    </li>
                    <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                    <li><a href="students.php"><span class="icon"><ion-icon name="school-outline"></ion-icon></span><span class="title">Students</span></a></li>
                    <li><a href="modules.php"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">Modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Messages <span class="message-count">(<?php echo $totalMessages; ?>)</span></span></a></li>
                    <li><a href="../logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">LogOut</span></a></li>
                </ul>
            </div>

            <!-- Main Section -->
            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Messages</h1>
                </div>

                <div class="content-section">
                    <h2>Messages from Students</h2>
                    <table>
                        <tr>
                            <th>Student</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                            <th>Reply</th>
                        </tr>
                        <?php while ($row = $messages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_username']); ?></td>
                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                <td>
                                    <form action="reply_to_student.php" method="post">
                                        <input type="hidden" name="student_id" value="<?php echo $row['sender_id']; ?>">
                                        <textarea name="reply_message" required></textarea>
                                        <button type="submit">Send Reply</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>

        <script>
            // Toggle sidebar
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

    </html>
<?php
    $conn->close();
} else {
    header("Location: ../login.php");
}
?>