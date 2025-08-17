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
    // Update viewed status
    $updateViewed = "UPDATE messages SET viewed = 1 WHERE receiver_id = {$_SESSION['student_id']} AND sender_role = 'lecture' AND viewed = 0";
    $conn->query($updateViewed);


?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn-student</title>
        <link rel="stylesheet" href="student.css">
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
                        <a href="mymodules.php">
                            <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                            <span class="title">My modules</span>
                        </a>
                    </li>

                    <!--li>
                        <a href="#">
                            <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                            <span class="title">My profile</span>
                        </a>
                    </li>-->


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
            </div> <!--Navigation ends-->
            <!--Main section starts-->

            <div class="main">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="grid-outline"></ion-icon>
                    </div>
                    <h1>Student</h1>

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



                    <div class="card">
                        <div>

                            <div class="cardName">Send Notifications</div>
                        </div>
                        <a href="student_notification.php">
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
                    <!-- for Student to receive the launching the class -->
                    <div id="joinClassModal" style="display:none;">
                        <div style="width: 200px; height: 100px; background-color: #FFA500; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <span id="joinClassButton">Join Class</span>
                        </div>
                    </div>
                    <div id="videoContainer"></div>
                </div>


            </div>
            <script>
                //handling the join pop window sent from lecture dash to initialize the class through webcams, starts
                const socket = new WebSocket('ws://localhost:8080');

                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    if (data.type === 'class_start') {
                        document.getElementById("joinClassModal").style.display = "block";
                    }
                };

                document.getElementById("joinClassButton").addEventListener("click", function() {
                    document.getElementById("joinClassModal").style.display = "none";
                    openStudentVideoStream();

                    // Notify the server that the student has accepted the invitation
                    fetch("student_accept_signal.php", {
                        method: "POST",
                        body: JSON.stringify({
                            student_id: <?php echo $_SESSION['student_id']; ?>
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });
                });

                async function openStudentVideoStream() {
                    const videoElement = document.createElement('video');
                    videoElement.autoplay = true;
                    document.getElementById("videoContainer").appendChild(videoElement);

                    // Simulate video frame streaming
                    setInterval(async () => {
                        const frame = captureImage(videoElement);
                        await sendImageToLecturerAPI(frame); // Example function to send video frame
                    }, 2000);
                }

                function captureImage(video) {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    return canvas.toDataURL('image/jpeg');
                }

                async function sendImageToLecturerAPI(frame) {
                    await fetch("../stream_video.php", { //where the images frames from the video to be sent the emotion detection API
                        method: "POST",
                        body: JSON.stringify({
                            image: frame
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });
                }
                //handling the join pop window sent from lecture dash to initialize the class through webcams, ends

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
                const socket = new WebSocket('ws://localhost:8080');

                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    if (data.type === 'class_start') {
                        document.getElementById("joinClassModal").style.display = "block";
                    }
                };
            </script>


            <script>
                // Poll server for class start signal
                setInterval(async function() {
                    const response = await fetch("/check_class_status.php");
                    const data = await response.json();

                    if (data.class_active && document.getElementById("joinClassModal").style.display === "none") {
                        // Show modal to join class
                        document.getElementById("joinClassModal").style.display = "block";
                    }
                }, 2000); // Check every 2 seconds

                // Handle "Join Class" block click
                document.getElementById("joinClassButton").addEventListener("click", function() {
                    document.getElementById("joinClassModal").style.display = "none";
                    openStudentVideoStream();
                });

                async function openStudentVideoStream() {
                    const videoElement = document.createElement('video');
                    videoElement.autoplay = true;
                    document.getElementById("videoContainer").appendChild(videoElement);

                    // Start video streaming to backend
                    setInterval(async () => {
                        const frame = captureImage(videoElement);
                        await sendImageToLecturerAPI(frame); // Stream frame to backend
                    }, 2000); // Every 2 seconds
                }

                function captureImage(video) {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    return canvas.toDataURL('image/jpeg');
                }

                async function sendImageToLecturerAPI(frame) {
                    await fetch("/stream_video.php", {
                        method: "POST",
                        body: JSON.stringify({
                            image: frame
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });
                }
            </script>

            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    </body><?php } else {
            header("Location:../login.php");
        } ?>

    </html>