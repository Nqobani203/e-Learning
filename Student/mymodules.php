<?php
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role'])) {

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "sms_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch modules from the database
    $sql = "SELECT module, module_code, module_id FROM modules";
    $result = $conn->query($sql);

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
        <title>Prolearn - My Modules</title>
        <link rel="stylesheet" href="student.css">
        <link rel="stylesheet" href="messages.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>
        <div class="container">
            <div class="nav">
                <ul>
                    <li>
                        <a href="#">
                            <span class="title" style="font-family: Lobster, sans-serif; margin-top:15px;">
                                <img src="../logo.png" width="90">
                            </span>
                        </a>
                    </li>
                    <li><a href="index.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                    <li><a href="mymodules.php"><span class="icon"><ion-icon name="cube-outline"></ion-icon></span><span class="title">My modules</span></a></li>
                    <li><a href="#"><span class="icon"><ion-icon name="albums-outline"></ion-icon></span><span class="title">Timetables</span></a></li>
                    <li><a href="student_messages.php"><span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span><span class="title">Responses<span class="message-count">(<?php echo $totalResponses; ?>)</span></span></a></li>
                    <li><a href="../logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Logout</span></a></li>
                </ul>
            </div>

            <div class="main">
                <div class="topbar">
                    <div class="toggle"><ion-icon name="grid-outline"></ion-icon></div>
                    <h1>Modules</h1>
                </div>

                <div class="cardBox">
                    <?php
                    if ($result->num_rows > 0) {
                        // Display each module as a card block
                        while ($row = $result->fetch_assoc()) {
                            $module_name = $row['module'];
                            $module_code = $row['module_code'];
                            $module_id = $row['module_id'];
                            echo "
                        <div class='card'>
                            <a href='mymodules_page.php?module={$module_name}&module_code={$module_code}&module_id={$module_id}'>
                                <div class='numbers'>$module_name</div>
                                <div class='cardName'>$module_code</div>
                            </a>
                            <div class='iconBx'>
                                <ion-icon name='layers-outline'></ion-icon>
                            </div>
                        </div>";
                        }
                    } else {
                        echo "<p>No modules found.</p>";
                    }
                    ?>
                </div>
            </div>

            <script>
                // Menu toggle
                let toggle = document.querySelector(".toggle");
                let navigation = document.querySelector(".nav");
                let main = document.querySelector(".main");

                toggle.onclick = function() {
                    navigation.classList.toggle("active");
                    main.classList.toggle("active");
                }

                // Add hovered class in selected list item
                let list = document.querySelectorAll(".nav li");

                function activelink() {
                    list.forEach((item) =>
                        item.classList.remove("hovered"));
                    this.classList.add("hovered");
                }
                list.forEach((item) =>
                    item.addEventListener("mouseover", activelink));
            </script>

            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>

    </html>

<?php
    $conn->close();
} else {
    header("Location:../login.php");
}
?>