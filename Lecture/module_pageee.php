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
        <link rel="stylesheet" href="lecture.css">
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
                width: 400px;
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
            <!-- Sidebar and Navigation Code -->
            <div class="main">
                <div class="topbar">
                    <h1><?php echo $module_name; ?></h1>
                </div>

                <div class="cardBox">
                    <!-- Other cards -->
                    <div class="card">
                        <div>
                            <div class="cardName">Media</div>
                        </div>
                        <div class="iconBx" id="fetchvideo">
                            <ion-icon name="videocam-outline"></ion-icon>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="content-section">
                        <!-- Existing "Add Content" Button and Form -->
                        <button class="button add-content-btn" id="addContentBtn">Add Content</button>
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
                                <input type="hidden" name="module_id" value="<?php echo $module_id; ?>">
                                <button type="submit" class="button upload-btn">Upload</button>
                            </form>
                        </div>

                        <!-- Display Course Content -->
                        <div id="uploadedContent">
                            <h2>Course Content</h2>
                            <?php
                            // Fetch and display uploaded content
                            $sql = "SELECT * FROM content WHERE module_id = '$module_id'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                echo "<div class='content-grid'>";
                                while ($row = $result->fetch_assoc()) {
                                    echo "<div class='content_card'>";
                                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                                    echo "<a href='uploads/" . htmlspecialchars($row['file']) . "' target='_blank'>Open " . strtolower($row['title']) . "</a>";
                                    echo "<form action='delete_content.php' method='post' style='display:inline;'>";
                                    echo "<input type='hidden' name='content_id' value='" . $row['id'] . "'>";
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

                        <!-- Video Integration Modal -->
                        <div id="mediaModal" style="display: none;">
                            <div id="modalContent">
                                <h3>Media Upload for Module: <?php echo $module_name; ?></h3>
                                <form id="videoSelectionForm">
                                    <div id="videoList">
                                        <!-- Videos fetched via YouTube Data API will populate here -->
                                    </div>
                                    <button type="button" id="uploadVideos" class="button">Upload</button>
                                </form>
                            </div>
                        </div>

                        <!-- Script for Modal and Fetching Videos -->
                        <script>
                            document.getElementById('addContentBtn').addEventListener('click', function() {
                                document.getElementById('addContentForm').style.display = 'block';
                            });

                            document.getElementById('fetchvideo').addEventListener('click', function() {
                                const modal = document.getElementById('mediaModal');
                                if (modal) {
                                    modal.style.display = 'block';
                                    fetchYouTubeVideos('<?php echo $module_name; ?>');
                                }
                            });

                            async function fetchYouTubeVideos(query) {
                                const API_KEY = 'AIzaSyCU8vFhAWV40CabpaJtHXcQkp5-uDrsG0g'; //  API key
                                const url = `https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=5&q=${encodeURIComponent(query)}&type=video&key=${API_KEY}`;

                                try {
                                    const response = await fetch(url);
                                    if (!response.ok) {
                                        throw new Error(`Network response was not ok: ${response.status}`);
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
                                    alert('Error fetching YouTube videos. Please check your API key or network connectivity.');
                                }
                            }

                            function displayVideos(videos) {
                                const videoListDiv = document.getElementById('videoList');
                                videoListDiv.innerHTML = '';

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

                            document.getElementById('uploadVideos').addEventListener('click', function() {
                                const selectedVideo = document.querySelector('input[name="video"]:checked');
                                if (!selectedVideo) {
                                    alert('Please select a video to upload.');
                                    return;
                                }

                                const formData = new FormData();
                                formData.append('videoId', selectedVideo.value);
                                formData.append('module_id', '<?php echo $module_id; ?>');

                                fetch('upload.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => {
                                        if (response.ok) {
                                            alert('Video uploaded successfully!');
                                            window.location.reload();
                                        } else {
                                            alert('Error uploading video.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error uploading video:', error);
                                    });
                            });
                        </script>
                    </div>
                </div>
            </div>
            <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" type="module"></script>
            <script src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js" nomodule></script>
    </body>

    </html>
<?php
} else {
    header("Location: ../login.php");
}
?>