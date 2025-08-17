<?php
session_start();
if (isset($_SESSION['lecture_id']) && isset($_SESSION['role']) && isset($_GET['module'])) {
    $module_name = $_GET['module'];
    $module_id = $_GET['module_id'];

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    /*/ Get the total number of messages
        $countQuery = "SELECT COUNT(*) AS total_messages FROM messages";
        $countResult = $conn->query($countQuery);
        $countRow = $countResult->fetch_assoc();
        $totalMessages = $countRow['total_messages'];
    
        // Fetch messages and student info
        $messagesQuery = "
            SELECT student.email AS student_email, student.username, messages.message
            FROM messages
            JOIN student ON messages.student_id = student.student_id";
        $messagesResult = $conn->query($messagesQuery);*/
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

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prolearn - <?php echo $module_name; ?></title>
        <!--link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="lecture.css">
        <link rel="stylesheet" href="course.css">
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script>
            setInterval(function() {
                location.reload();
            }, 40000);
        </script>
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
                    <li><a href="students.php"><span class="icon"><ion-icon name="school-outline"></ion-icon></span><span class="title">Students</span></a></li>
                    <li><a href="modules.php"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">Modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Messages<span class="message-count">(<?php echo $totalMessages; ?>)</span></span></a></li>
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
                        <a href="modules.php">
                            <div class="iconBx">
                                <ion-icon name="book-outline"></ion-icon>
                            </div>
                        </a>
                    </div>

                    <div class="card">
                        <div>
                            <div class="cardName">Students</div>
                        </div>
                        <a href="students.php">
                            <div class="iconBx">
                                <ion-icon name="people-circle-outline"></ion-icon>
                            </div>
                        </a>
                    </div>

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
                            <div class="cardName">Media</div>
                        </div>
                        <div class="iconBx" id="fetchvideo">
                            <ion-icon name="videocam-outline"></ion-icon>
                        </div>
                    </div>

                    <div class="content-section">
                        <!-- Button to trigger the upload form popup -->
                        <!--button id="addContentBtn">Add Content</button>-->
                        <button class="button add-content-btn" id="addContentBtn">Add Content</button>

                        <!-- Hidden popup form for file upload -->
                        <div id="addContentForm" style="display: none;">
                            <form action="upload.php" method="post" enctype="multipart/form-data">
                                <label for="title">Content Title:</label>
                                <input type="text" name="title" required><br>

                                <label for="type">Type:</label>
                                <select name="type" required>
                                    <option value="pdf">PDF</option>
                                    <option value="docx">DOCX</option>
                                </select><br>

                                <label for="file">Upload File:</label>
                                <input type="file" name="file" required><br>

                                <input type="hidden" name="module_id" value="<?php echo $module_id; // Dynamically pass module ID 
                                                                                ?>">
                                <!--button type="submit">Upload</button>-->
                                <button type="submit" class="button upload-btn">Upload</button>
                            </form>
                        </div>



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
                                        echo "<a href='uploads/" . htmlspecialchars($row['file']) . "' target='_blank'>Open " . strtolower($row['title']) . "</a>";
                                    }
                                    echo "<form action='delete_content.php' method='post' style='display:inline;'>";
                                    echo "<input type='hidden' name='content_id' value='" . $row['id'] . "'>"; // Assuming there's an 'id' column in 'content'
                                    echo "<button type='submit' class='delete-btn'>Delete</button>";
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
                    <!--Window for video integration from YouTube-->
                    <div id="mediaModal" style="display: none;">
                        <div id="modalContent">
                            <h3>Videos for Module: <?php echo $module_name; ?></h3>
                            <form id="videoSelectionForm">
                                <div id="videoList">
                                    <!-- Videos fetched via YouTube Data API will populate here -->
                                </div>
                                <button type="button" id="uploadVideos" class="button">Upload</button>
                            </form>
                        </div>
                    </div>
                    <!--window section ends-->

                </div>
            </div>


            <div id="modal">
                <div id="modalContent">
                    <h3 id="modalMessage"></h3>
                    <button onclick="closeModal()">OK</button>
                </div>
            </div>

            <script>
                document.getElementById('addContentBtn').addEventListener('click', function() {
                    document.getElementById('addContentForm').style.display = 'block';
                });
            </script>
            <script>
                // Open modal and fetch videos when "Media" card is clicked
                document.getElementById('fetchvideo').addEventListener('click', function() {
                    const modal = document.getElementById('mediaModal');
                    if (modal) {
                        modal.style.display = 'block'; // Show modal
                        fetchYouTubeVideos('<?php echo $module_name; ?>'); // Correct function call
                    }
                });
            </script>

            <script>
                // Function to display modal
                function showModal(message) {
                    document.getElementById('modalMessage').textContent = message;
                    document.getElementById('modal').style.display = 'flex';
                }

                // Function to close modal
                function closeModal() {
                    document.getElementById('modal').style.display = 'none';
                    window.location.href = 'module_page.php'; // Refresh or remove query params
                }

                // Check for upload status
                <?php if (isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
                    showModal('File uploaded successfully.');
                <?php elseif (isset($_GET['upload']) && $_GET['upload'] == 'error'): ?>
                    showModal('Error uploading the file.');
                <?php endif; ?>


                //script for ftching the videos using YouTube DATA API
            </script>

            <!--scrit for fetching the videos uisng YouTube DATA API-->

            <script>
                // Function to fetch YouTube videos
                async function fetchYouTubeVideos(query) {
                    const API_KEY = 'AIzaSyD5RRz5catSqAsxFJPepIYX1teLWxP-uH0';
                    const url = `https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=5&q=${encodeURIComponent(query)}&type=video&key=${API_KEY}`;

                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`Network response was not ok: ${response.statusText}`);
                        }

                        const data = await response.json();
                        if (data.items && data.items.length > 0) {
                            displayVideos(data.items);
                        } else {
                            alert('No videos found for this query.');
                            document.getElementById('videoList').innerHTML = '<p>No videos found.</p>';
                        }
                    } catch (error) {
                        console.error('Error fetching YouTube data:', error);
                        alert('Error fetching YouTube videos. Please try again.');
                    }
                }
                // Function to display videos with radio buttons
                function displayVideos(videos) {
                    const videoListDiv = document.getElementById('videoList');
                    videoListDiv.innerHTML = ''; // Clear previous content

                    if (!videos || videos.length === 0) {
                        videoListDiv.innerHTML = '<p>No videos found for this query.</p>';
                        return;
                    }

                    videos.forEach(video => {
                        const videoElement = `
            <div>
                <input type="radio" name="video" value="${video.id.videoId}" id="${video.id.videoId}">
                <label for="${video.id.videoId}">${video.snippet.title}</label>
                <p>${video.snippet.description}</p>
                <img src="${video.snippet.thumbnails.default.url}" alt="${video.snippet.title}">
            </div>
        `;
                        videoListDiv.innerHTML += videoElement;
                    });
                }


                // Fetch YouTube videos when the page loads
                /*document.addEventListener('DOMContentLoaded', () => {
                    fetchYouTubeVideos(moduleName);
                });*/

                // Handle the "Upload" button click for videos selected in the media modal
                document.getElementById('uploadVideos').addEventListener('click', function() {
                    const selectedVideo = document.querySelector('input[name="video"]:checked');
                    if (!selectedVideo) {
                        alert('Please select a video to upload.');
                        return;
                    }

                    // Fetch video data
                    const videoId = selectedVideo.value;
                    const videoTitle = selectedVideo.nextElementSibling.textContent; // Assuming the label holds the title
                    const moduleId = '<?php echo $module_id; ?>'; // Dynamically pass module ID

                    // Prepare form data
                    const formData = new FormData();
                    formData.append('videoId', videoId);
                    formData.append('title', videoTitle);
                    formData.append('type', 'video'); // Optional: type can be used for distinguishing content types
                    formData.append('module_id', moduleId);

                    // Send data to upload.php
                    fetch('upload.php', {
                        method: 'POST',
                        body: formData
                    });
                    alert('Video uploaded successfully!');
                    // Append the video to the main section (similar to before)
                    const videoContainer = document.createElement('div');
                    videoContainer.classList.add('video-post');
                    videoContainer.innerHTML = `
                <h3>${videoTitle}</h3>
                <iframe width="400" height="225" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>
            `;
                    const contentSection = document.getElementById('uploadedContent');
                    contentSection.parentNode.insertBefore(videoContainer, contentSection.nextSibling);
                    document.getElementById('mediaModal').style.display = 'none';
                    //.then(response => response.text()) // Expecting a text response
                    /* .then(data => {
                         // Check if the response indicates success
                         if (data.success) {
                         } else {
                             alert('Error uploading video.');
                         }
                     })
                     .catch(error => {
                         console.error('Error uploading the video:', error);
                     });*/
                });
            </script>


            <!--fetching scripts ends-->


            <script>
                // Toggle course content when "Course" is clicked
                document.getElementById('courseBtn').onclick = function() {
                    document.getElementById('courseContent').style.display = 'block';
                };
            </script>

            <script>
                // Get modal elements
                var modal = document.getElementById("addContentModal");
                var addContentBtn = document.getElementById("addContentBtn");
                var span = document.getElementsByClassName("close")[0];

                // Open the modal when the button is clicked
                addContentBtn.onclick = function() {
                    modal.style.display = "block";
                }

                // Close the modal when the "x" is clicked
                span.onclick = function() {
                    modal.style.display = "none";
                }

                // Close the modal when the user clicks outside of it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
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