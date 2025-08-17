<?php
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Student') {
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve lectures to populate the dropdown
    $lecture_query = "SELECT lecture_id, username FROM lecture";
    $lectures = $conn->query($lecture_query);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
        $message = $_POST['message'];
        $lecture_id = $_POST['lecture_id'];
        $student_id = $_SESSION['student_id'];


        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sender_role, receiver_role) VALUES (?, ?, ?, 'Student', 'Lecture')");
        $stmt->bind_param("iis", $student_id, $lecture_id, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Message sent successfully!'); window.location.href = 'index.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Get the total number of responses
    $countQuery = "SELECT COUNT(*) AS total_responses FROM messages WHERE receiver_id = {$_SESSION['student_id']} AND sender_role = 'lecture'";
    $countResult = $conn->query($countQuery);

    if ($countResult && $countRow = $countResult->fetch_assoc()) {
        $totalResponses = $countRow['total_responses'];
    } else {
        echo "Error with count query: " . $conn->error;
        $totalResponses = 0; // Set to zero if query fails
    }


?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Send Message</title>
        <link rel="stylesheet" href="student.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>
        <style>
            /* General form container styling */
            form {
                background-color: #f9f9f9;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                max-width: 500px;
                margin: 20px auto;
                font-family: Arial, sans-serif;
            }

            form h2 {
                text-align: center;
                color: #333;
                font-size: 24px;
                margin-bottom: 20px;
            }

            /* Input and textarea styling */
            form label {
                display: block;
                font-size: 16px;
                color: #555;
                margin-top: 10px;
                font-weight: bold;
            }

            form select,
            form textarea,
            form button {
                width: 100%;
                padding: 10px;
                margin-top: 8px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 16px;
                font-family: inherit;
                outline: none;
            }

            form textarea {
                resize: vertical;
                height: 120px;
            }

            form select:focus,
            form textarea:focus {
                border-color: #0066cc;
                box-shadow: 0 0 5px rgba(0, 102, 204, 0.5);
            }

            /* Submit button styling */
            form button {
                background-color: #4CAF50;
                color: white;
                border: none;
                cursor: pointer;
                font-weight: bold;
                transition: background-color 0.3s ease;
                margin-top: 15px;
            }

            form button:hover {
                background-color: #45a049;
            }

            form button:active {
                background-color: #3e8e41;
            }
        </style>
        <div class="container">
            <div class="nav">
                <!-- Navigation structure similar to index.php -->
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
                        <a href="mymodules.php">
                            <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                            <span class="title">My modules</span>
                        </a>
                    </li>


                    <li>
                        <a href="#">
                            <span class="icon"><ion-icon name="albums-outline"></ion-icon></span>
                            <span class="title">Timetables</span>
                        </a>
                    </li>




                    <li>
                        <a href="student_messages.php">
                            <span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span>
                            <span class="title">Responses<span class="message-count">(<?php echo $totalResponses; ?>)</span></span>
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
                    <h1>Send Message to Lecture</h1>
                </div>
                <form method="POST" action="">
                    <label for="lecture_id">Select Lecture:</label>
                    <select name="lecture_id" id="lecture_id" required>
                        <?php while ($lecture = $lectures->fetch_assoc()) { ?>
                            <option value="<?= $lecture['lecture_id'] ?>"><?= $lecture['username'] ?></option>
                        <?php } ?>
                    </select>

                    <label for="message">Message:</label>
                    <textarea name="message" id="message" rows="4" required></textarea>

                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </body>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    </html>

<?php
    $conn->close();
} else {
    header("Location: ../login.php");
}
?>