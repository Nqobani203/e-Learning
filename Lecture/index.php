<?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role'])) {
    // Connect to the database to get the total number of students
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
    // Update viewed status
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE lecturer_id = {$_SESSION['lecture_id']} AND viewed = 0";
    $conn->query($updateViewed);
    $conn->close();
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn-Lecture</title>
        <link rel="stylesheet" href="lecture.css">
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script>
            setInterval(function() {
                location.reload();
            }, 60000);
        </script>


    </head>

    <body>
        <div class="container">
            <div class="nav"> <!--Navigation starts-->
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
                        <a href="module-page.php">
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
            </div> <!--Navigation ends-->
            <!--Main section starts-->

            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Lecture</h1>

                </div>

                <div class="cardBox">
                    <div class="card" id="watBtn">
                        <div>
                            <div class="numbers"><?php echo $studentCount; ?></div>
                            <div id="waterButton" class="cardName">Number of Students</div>
                        </div>
                        <a href="students.php">
                            <div class="iconBx">
                                <ion-icon name="people-circle-outline"></ion-icon>
                            </div>
                        </a>
                    </div>

                    <div class="card" id="eleBtn">
                        <div>
                            <div class="numbers"></div>
                            <div id="elecButton" class="cardName">Launch Class</div>
                        </div>

                        <div class="iconBx">
                            <ion-icon name="layers-outline" id="launch"></ion-icon>
                        </div>
                    </div>
                    <!--For lecture to initialize a calss
                    <div id="launchClassBlock" style="width: 200px; height: 100px; background-color: #4CAF50; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <span>Launch Class</span>
                    </div>

                    <div id="emotionStatus">Current Emotion: Waiting for students...</div>
                    <div id="videoContainer"></div>
                    <--end-->

                    <div class="card">
                        <div>
                            <div class="cardName">Send Announcements</div>
                        </div>
                        <a href="lecture_announcement.php">
                            <div class="iconBx">
                                <ion-icon name="paper-plane-outline"></ion-icon>
                            </div>
                        </a>
                    </div>

                    <div class="card">
                        <div>

                            <div class="cardName">calendar</div>
                        </div>

                        <div class="iconBx">
                            <ion-icon name="calendar-clear-outline"></ion-icon>
                        </div>
                    </div>
                </div>


            </div>

            <script>
                //handling the "Launch Class" card once it is clicked to initial video on students dashboard, starts
                document.getElementById("launch").addEventListener("click", function() {
                    const socket = new WebSocket('ws://localhost:8080'); // 

                    socket.onopen = function() {
                        const lectureId = <?php echo $_SESSION['lecture_id']; ?>;
                        socket.send(JSON.stringify({
                            type: 'class_start',
                            lecture_id: lectureId
                        }));

                        // Update the database to signal class start
                        fetch("update_class_signal.php", {
                            method: "POST",
                            body: JSON.stringify({
                                lecture_id: lectureId
                            }),
                            headers: {
                                "Content-Type": "application/json"
                            }
                        });
                    };

                    socket.onclose = function() {
                        console.log('Connection closed');
                    };
                });

                //handling the "Launch Class" card once it is clicked to initial video on students dashboard, ends

                // menu toggle
                let toggle = document.querySelector(".toggle");
                let navigation = document.querySelector(".nav");
                let main = document.querySelector(".main");

                toggle.onclick = function() {
                    navigation.classList.toggle("active");
                    main.classList.toggle("active");
                }

                // add hovered class in seleted list item
                let list = document.querySelectorAll(".nav li");

                function activelink() {
                    list.forEach((item) =>
                        item.classList.remove("hovered"));
                    item.classList.add("hovered");
                }
                list.forEach((item) =>
                    item.addEventListener("mouseover", activelink));
            </script>



            <script>
                // Handle "Launch Class" block click
                document.getElementById("launchClassBlock").addEventListener("click", async function() {
                    // Notify the server to initiate class
                    const response = await fetch("start_class.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                    });

                    if (response.ok) {
                        // Start listening for student video streams
                        startVideoStream();
                    }
                });

                async function startVideoStream() {
                    <button id = "endSessionButton"> End Session </button> // button for ending the session
                    // Create a video element for the lecturer to view student feeds
                    const videoElement = document.createElement('video');
                    videoElement.autoplay = true;
                    document.getElementById("videoContainer").appendChild(videoElement);

                    // Periodically capture frames and send them to the emotion detection API
                    setInterval(async () => {
                        const frame = captureImage(videoElement);
                        const emotionData = await sendImageToAPI(frame);
                        document.getElementById("emotionStatus").innerText = `Current Emotion: ${emotionData.predicted_emotion}`;
                    }, 2000); // Every 2 seconds
                }

                // Capture image from video
                function captureImage(video) {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    return canvas.toDataURL('image/jpeg');
                }

                async function sendImageToAPI(frame) {
                    const response = await fetch("/predict-emotion", {
                        method: "POST",
                        body: JSON.stringify({
                            image: frame
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });
                    return await response.json();
                }
            </script>
            <script>
                document.getElementById('endSessionButton').addEventListener('click', function() {
                    if (confirm('Are you sure you want to end this session?')) {
                        fetch('end_class.php', {
                                method: 'POST',
                            })
                            .then(response => response.text())
                            .then(data => {
                                alert(data); // Display feedback (e.g., "Class session ended successfully.")
                                // Optionally, you can redirect or refresh the page after ending the session
                                window.location.href = 'dashboard.php'; // Redirect to dashboard or desired page
                            })
                            .catch(error => {
                                console.error('Error ending session:', error);
                                alert('Failed to end session.');
                            });
                    }
                });
            </script>


            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    </body><?php } else {
            header("Location:../login.php");
        } ?>

    </html>