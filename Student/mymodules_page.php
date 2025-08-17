<?php
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role']) && isset($_GET['module'])) {
    $module_name = $_GET['module'];
    $module_id = $_GET['module_id'];

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
    // Update viewed status
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE receiver_id = {$_SESSION['student_id']} AND sender_role = 'lecture' AND viewed = 0";
    $conn->query($updateViewed);
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn - <?php echo $module_name; ?></title>
        <!--link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="student.css">
        <link rel="stylesheet" href="course.css">
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    </head>

    <body>
        <style>
            /* Style for the Add Content form */
            #addContentForm {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                padding: 15px;
                margin-top: 15px;
            }

            .file-block {
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
            }

            .side-panel {
                float: right;
                width: 20%;
                background-color: #f1f1f1;
                padding: 10px;
            }

            .side-panel ul {
                list-style-type: none;
            }

            .side-panel ul li {
                margin-bottom: 10px;
            }

            .side-panel ul li a {
                text-decoration: none;
                color: #333;
                font-weight: bold;
            }

            .content-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .content_card {
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 8px;
                width: 600px;
                text-align: center;
                position: relative;
            }

            .content_card h3 {
                font-size: 1.1em;
                margin: 10px 0;
            }

            .delete-btn {
                background-color: #ff4d4d;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
                position: absolute;
                bottom: 10px;
                right: 10px;
            }

            .delete-btn:hover {
                background-color: #ff3333;
            }

            /* General Button Styling */
            .button {
                background-color: #4CAF50;
                /* Primary color */
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s ease;
            }

            /* Hover Effect */
            .button:hover {
                background-color: #45a049;
                /* Slightly darker green */
            }

            /* Additional Styling for "Add Content" Button */
            .add-content-btn {
                margin: 10px 0;
                background-color: #2196F3;
                /* Blue color for differentiation */
                padding: 12px 24px;
                font-size: 18px;
            }

            /* Hover Effect for "Add Content" Button */
            .add-content-btn:hover {
                background-color: #0b7dda;
            }

            /* Additional Styling for "Upload" Button */
            .upload-btn {
                background-color: #ff9800;
                /* Orange color for differentiation */
                padding: 8px 16px;
                font-size: 14px;
                margin-top: 10px;
            }

            /* Hover Effect for "Upload" Button */
            .upload-btn:hover {
                background-color: #e68900;
            }

            /* Modal styles */
            #modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                display: none;
                /* Hidden by default */
                z-index: 1000;
            }

            #modalContent {
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                text-align: center;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            }

            #modalContent h3 {
                margin: 0 0 10px;
            }

            #modalContent button {
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
        </style>
        <div class="container">
            <div class="nav">
                <ul>
                    <li>
                        <a href="#">
                            <span class="title" style="font-family: Lobster, sans-serif; margin-top:15px;"><img src="../logo.png" width="90"></span>
                        </a>
                    </li>
                    <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                    <li><a href="mymodules.php"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">Modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="student_messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Responses<span class="message-count">(<?php echo $totalResponses; ?>)</span></span></a></li>
                    <li><a href="../logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Logout</span></a></li>
                </ul>
            </div>

            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1><?php echo $module_name; ?></h1>
                </div>

                <div class="cardBox">
                    <div class="card" id="courseBtn">
                        <div>
                            <div class="cardName">Course</div>
                        </div>
                        <a href="mymodules.php">
                            <div class="iconBx">
                                <ion-icon name="book-outline"></ion-icon>
                            </div>
                        </a>
                    </div>

                    <!--div class="card">
                        <div>
                            <div class="cardName">Students</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="people-circle-outline"></ion-icon>
                        </div>
                    </div>-->

                    <div class="card">
                        <div>
                            <div class="cardName">Grades</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="stats-chart-outline"></ion-icon>
                        </div>
                    </div>

                    <div class="card">
                        <div>
                            <div class="cardName">Take Exam</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="pencil-outline"></ion-icon>
                        </div>
                    </div><br>

                    <div id="uploadedContent">
                        <h2>Course Content</h2>
                        <?php
                        // Fetch and display uploaded content from the database
                        $conn = new mysqli("localhost", "root", "", "sms_db"); // Adjust credentials
                        $sql = "SELECT * FROM content WHERE module_id = '$module_id'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            echo "<div class='content-grid'>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='content_card'>";
                                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                                if ($row['type'] === 'video') {
                                    // Display video using iframe
                                    echo "<iframe  width='400' height='225' src='https://www.youtube.com/embed/" . htmlspecialchars($row['file']) . "' frameborder='0' allowfullscreen></iframe>";
                                } else {
                                    // Display other content types
                                    echo "<a href='../Lecture/uploads/" . htmlspecialchars($row['file']) . "' target='_blank'>Open " . strtolower($row['title']) . "</a>";
                                }
                                //echo "<form action='delete_content.php' method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='content_id' value='" . $row['id'] . "'>"; // Assuming there's an 'id' column in 'content'
                                //echo "<button type='submit' class='delete-btn'>Delete</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "No content uploaded yet.";
                        }
                        ?>
                    </div>


                </div>

                <script>
                    document.getElementById('addContentBtn').addEventListener('click', function() {
                        document.getElementById('addContentForm').style.display = 'block';
                    });
                </script>





            </div>
        </div>

        <script>
            // Toggle course content when "Course" is clicked
            document.getElementById('courseBtn').onclick = function() {
                document.getElementById('courseContent').style.display = 'block';
            };
        </script>
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
    </body>
<?php
} else {
    header("Location:../login.php");
}
?>

    </html>