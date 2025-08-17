<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require Composer's autoloader
require '../PHPMailer-master/src/PHPMailer.php';  // Adjust path based on your setup
require '../PHPMailer-master/src/Exception.php';  // Don't forget Exception file for error handling
require '../PHPMailer-master/src/SMTP.php';       // SMTP for email sending

include "../database.php";  // Your database connection
session_start();

if (isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['role'])) {
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    if (empty($uname)) {
        $em = "Username is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Password is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "An Error Occured!";
        header("Location:../login.php?error=$em");
        exit;
    } else {
        // Determine role-based table to fetch user details
        if ($role == '1') {
            $sql = "SELECT * FROM admin WHERE username=?";
            $log_role = "Admin";
        } else if ($role == '2') {
            $sql = "SELECT * FROM lecture WHERE username=?";
            $log_role = "Lecture";
        } else if ($role == '3') {
            $sql = "SELECT * FROM student WHERE username=?";
            $log_role = "Student";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $username = $user['username'];
            $password = $user['pass'];
            $email = $user['email']; // Retrieve the email from the database

            if (password_verify($pass, $password)) {
                $_SESSION['role'] = $log_role;

                // Capture login logs
                $device = $_SERVER['HTTP_USER_AGENT'];  // Get the device info
                $location = $_SERVER['REMOTE_ADDR'];    // Get the IP (basic location tracking)
                $login_time = date('Y-m-d H:i:s');      // Current time

                // Determine which ID to use based on role
                if ($log_role == 'Admin') {
                    $id = $user['admin_id'];
                    $_SESSION['admin_id'] = $id;
                } else if ($log_role == 'Student') {
                    $id = $user['student_id'];
                    $_SESSION['student_id'] = $id;
                } else if ($log_role == 'Lecture') {
                    $id = $user['lecture_id'];
                    $_SESSION['lecture_id'] = $id;
                }

                // Insert login logs into the login_logs table
                $log_sql = "INSERT INTO login_logs (student_id, role, login_time, device, location) VALUES (?, ?, ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->execute([$id, $log_role, $login_time, $device, $location]);

                // Send email notification using PHPMailer
                $mail = new PHPMailer(true); // Create a new PHPMailer instance
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nqobanit23@gmail.com';   // Your Gmail address
                    $mail->Password = 'ofyn ybtn oqtf rcgt';   // Gmail App Password (not your actual password)
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('nqobanit23@gmail.com', 'ProLearn Notifications');
                    $mail->addAddress($email);  // Send to the student's email address

                    // Email content
                    $mail->isHTML(true);                                // Set email format to HTML
                    $mail->Subject = 'New Login Detected';
                    $mail->Body    = "Hello, <br><br> 
                        A new login to your account was detected. Here are the details: <br>
                        Time: $login_time <br>
                        Device: $device <br>
                        Location: $location <br><br>
                        If this wasn't you, please contact support immediately.";

                    $mail->send();  // Send the email
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                // Redirect after successful login based on role
                if ($log_role == 'Admin') {
                    header("Location:../Admin/index.php");
                    exit;
                } else if ($log_role == 'Student') {
                    header("Location:../Student/index.php");
                    exit;
                } else if ($log_role == 'Lecture') {
                    header("Location:../Lecture/index.php");
                    exit;
                }
            } else {
                $em = "Incorrect username or Password";
                header("Location:../login.php?error=$em");
                exit;
            }
        } else {
            $em = "Incorrect username or Password";
            header("Location:../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location:../login.php");
    exit;
}



/*
<?php
include "../database.php";
session_start();
if (
    isset($_POST['uname']) &&
    isset($_POST['pass']) &&
    isset($_POST['role'])
) {
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    if (empty($uname)) {
        $em = "Username is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Password is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "An Error Occured!";
        header("Location:../login.php?error=$em");
        exit;
    } else {
        if ($role == '1') {
            $sql = "SELECT * FROM admin WHERE username=?";
            $role = "Admin";
        } else  if ($role == '2') {
            $sql = "SELECT * FROM lecture WHERE username=?";
            $role = "Lecture";
        } else if ($role == '3') {
            $sql = "SELECT * FROM student WHERE username=?";
            $role = "Student";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $username = $user['username'];
            $password = $user['pass'];
            $id = $user['admin_id'];

            if ($username == $uname) {
                if (password_verify($pass, $password)) {
                    $_SESSION['role'] = $role;
                    if ($role == 'Admin') {
                        $id = $user['admin_id'];
                        $_SESSION['admin_id'] = $id;
                        header("Location:../Admin/index.php");
                        exit;
                    } else if ($role == 'Student') {
                        $id = $user['student_id'];
                        $_SESSION['student_id'] = $id;
                        header("Location:../Student/index.php");
                        exit;
                    } else if ($role == 'Lecture') {
                        $id = $user['lecture_id'];
                        $_SESSION['lecture_id'] = $id;
                        header("Location:../Lecture/index.php");
                        exit;
                    }
                } else {
                    $em = "Incorrect username or Password";
                    header("Location:../login.php?error=$em");
                    exit;
                }
            } else {
                $em = "Incorrect username or Password";
                header("Location:../login.php?error=$em");
                exit;
            }
        }
    }
} else {
    header("Location:../login.php");
    exit;
}





/*<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require Composer's autoloader
require '../PHPMailer-master/src/PHPMailer.php';  // Adjust path based on your setup
require '../PHPMailer-master/src/Exception.php';  // Don't forget Exception file for error handling
require '../PHPMailer-master/src/SMTP.php';       // SMTP for email sending

include "../database.php";  // Your database connection
session_start();

if (isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['role'])) {
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    if (empty($uname)) {
        $em = "Username is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Password is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "An Error Occured!";
        header("Location:../login.php?error=$em");
        exit;
    } else {
        // Determine role-based table to fetch user details
        if ($role == '1') {
            $sql = "SELECT * FROM admin WHERE username=?";
            $log_role = "Admin";
        } else if ($role == '2') {
            $sql = "SELECT * FROM lecture WHERE username=?";
            $log_role = "Lecture";
        } else if ($role == '3') {
            $sql = "SELECT * FROM student WHERE username=?";
            $log_role = "Student";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $username = $user['username'];
            $password = $user['pass'];
            $email = $user['email']; // Retrieve the email from the database

            if (password_verify($pass, $password)) {
                $_SESSION['role'] = $log_role;

                // Capture login logs
                $device = $_SERVER['HTTP_USER_AGENT'];  // Get the device info
                $location = $_SERVER['REMOTE_ADDR'];    // Get the IP (basic location tracking)
                $login_time = date('Y-m-d H:i:s');      // Current time

                // Determine which ID to use based on role
                if ($log_role == 'Admin') {
                    $id = $user['admin_id'];
                    $_SESSION['admin_id'] = $id;
                } else if ($log_role == 'Student') {
                    $id = $user['student_id'];
                    $_SESSION['student_id'] = $id;
                } else if ($log_role == 'Lecture') {
                    $id = $user['lecture_id']; // Now includes lecture_id for lecturers
                    $_SESSION['lecture_id'] = $id;
                }

                // Insert login logs into the login_logs table, including both student_id and lecture_id
                $log_sql = "INSERT INTO login_logs (student_id, lecture_id, role, login_time, device, location) VALUES (?, ?, ?, ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                
                // Handle the case where we only have either student_id or lecture_id
                $student_id = ($log_role == 'Student') ? $id : NULL;
                $lecture_id = ($log_role == 'Lecture') ? $id : NULL;

                $log_stmt->execute([$student_id, $lecture_id, $log_role, $login_time, $device, $location]);

                // Send email notification using PHPMailer
                $mail = new PHPMailer(true); // Create a new PHPMailer instance
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nqobanit23@gmail.com';   // Your Gmail address
                    $mail->Password = 'your-gmail-app-password';   // Gmail App Password (not your actual password)
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('nqobanit23@gmail.com', 'ProLearn Notifications');
                    $mail->addAddress($email);  // Send to the student's or lecture's email address

                    // Email content
                    $mail->isHTML(true);                                // Set email format to HTML
                    $mail->Subject = 'New Login Detected';
                    $mail->Body    = "Hello, <br><br> 
                        A new login to your account was detected. Here are the details: <br>
                        Time: $login_time <br>
                        Device: $device <br>
                        Location: $location <br><br>
                        If this wasn't you, please contact support immediately.";

                    $mail->send();  // Send the email
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                // Redirect after successful login based on role
                if ($log_role == 'Admin') {
                    header("Location:../Admin/index.php");
                    exit;
                } else if ($log_role == 'Student') {
                    header("Location:../Student/index.php");
                    exit;
                } else if ($log_role == 'Lecture') {
                    header("Location:../Lecture/index.php");
                    exit;
                }
            } else {
                $em = "Incorrect username or Password";
                header("Location:../login.php?error=$em");
                exit;
            }
        } else {
            $em = "Incorrect username or Password";
            header("Location:../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location:../login.php");
    exit;
}









/
*/
/*
<?php
include "../database.php";
session_start();

// Include Twilio PHP Library if using for SMS notifications
require_once '../twilio-php-main/src/Twilio/autoload.php';
use Twilio\Rest\Client;

if (
    isset($_POST['uname']) &&
    isset($_POST['pass']) &&
    isset($_POST['role'])
) {
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    if (empty($uname)) {
        $em = "Username is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Password is required";
        header("Location:../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "An Error Occurred!";
        header("Location:../login.php?error=$em");
        exit;
    } else {
        if ($role == '1') {
            $sql = "SELECT * FROM admin WHERE username=?";
            $role = "Admin";
        } else if ($role == '2') {
            $sql = "SELECT * FROM lecture WHERE username=?";
            $role = "Lecture";
        } else if ($role == '3') {
            $sql = "SELECT * FROM student WHERE username=?";
            $role = "Student";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $username = $user['username'];
            $password = $user['pass'];

            if ($role == 'Admin') {
                $id = $user['admin_id'];
                $_SESSION['admin_id'] = $id;
                header("Location:../Admin/index.php");
                exit;
            } else if ($role == 'Student') {
                $id = $user['student_id'];
                $_SESSION['student_id'] = $id;

                // Retrieve the student's phone number
                $student_phone_number = $user['stu_phone'];

                // If password is correct
                if (password_verify($pass, $password)) {
                    $_SESSION['role'] = $role;

                    // Fetch device information (you can refine this logic)
                    $device = $_SERVER['HTTP_USER_AGENT'];  // Browser and OS details
                    $location = 'Unknown location';  // You can replace this with a geolocation API to get the location

                    // Insert the login log into the database
                    $log_insert_query = "INSERT INTO login_logs (student_id, device, location) VALUES (?, ?, ?)";
                    $log_stmt = $conn->prepare($log_insert_query);
                    $log_stmt->execute([$id, $device, $location]);

                    // Now send the SMS with the login details (Twilio integration example)
                    $message = "Login alert: Device: $device, Time: " . date('Y-m-d H:i:s') . ", Location: $location";

                    // Twilio API credentials
                    $sid = 'AC38d83172fc8978f4a2497c21619eeba2';
                    $token = 'c20292f7535de330bb3c08a6fd514b58';
                    $twilio_number = '+15312141453';
                    $client = new Client($sid, $token);

                    // Send SMS to the student
                    $client->messages->create(
                        $student_phone_number,  // Send to the student's phone number
                        array(
                            'from' => $twilio_number,
                            'body' => $message
                        )
                    );

                    // Redirect student to their dashboard after logging and sending SMS
                    header("Location:../Student/index.php");
                    exit;
                } else {
                    $em = "Incorrect username or Password";
                    header("Location:../login.php?error=$em");
                    exit;
                }
            } else if ($role == 'Lecture') {
                $id = $user['lecture_id'];
                $_SESSION['lecture_id'] = $id;
                header("Location:../Lecture/index.php");
                exit;
            }
        } else {
            $em = "Incorrect username or Password";
            header("Location:../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location:../login.php");
    exit;
}
?>
*/
