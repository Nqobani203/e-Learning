<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prolearn-Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        setInterval(function () {
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
                    <a href="#">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>


                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Lectures</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="school-outline"></ion-icon></span>
                        <span class="title">Students</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                        <span class="title">Class</span>
                    </a>
                </li>


                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="albums-outline"></ion-icon></span>
                        <span class="title">Timetables</span>
                    </a>
                </li>


                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="help-circle-outline"></ion-icon></span>
                        <span class="title">FAQs</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="chatbubbles-outline"></ion-icon></span>
                        <span class="title">messages</span>
                    </a>
                </li>

                <li>
                    <a href="#">
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
                <h1>Administrator</h1>

            </div>

            <div class="cardBox">
                <div class="card" id="watBtn">
                    <div>
                        <div class="numbers"></div>
                        <div id="waterButton" class="cardName">Manage Users</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-circle-outline"></ion-icon>
                    </div>
                </div>

                <div class="card" id="eleBtn">
                    <div>
                        <div class="numbers"></div>
                        <div id="elecButton" class="cardName">Schedule Timetables</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="layers-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        
                        <div class="cardName">Send Notifications</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="paper-plane-outline"></ion-icon>
                    </div>
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
            // menu toggle
            let toggle = document.querySelector(".toggle");
            let navigation = document.querySelector(".nav");
            let main = document.querySelector(".main");

            toggle.onclick = function () {
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

        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>