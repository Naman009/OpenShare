<?php
session_start();

function send_mail($email, $otp){
    $to_email = $email;
    $subject = "Open Share OTP";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message = "<b>Dear ".$email.",</b><br><br><span> Your One-Time Password is </span><b>".$otp."</b>.<br><br><b>Happy Sharing!</b> <br><b>Team Open Share</b> <br><br><strong>This is an automatically generated email, please do not reply.</strong><br>";
    mail($to_email, $subject, $message, $headers);
}

function mail_validation($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 1;
    }
    else{
        return 0;
    }
}

$servername = "localhost";
$username = "root";
$pass = "";
$dbname = "project_db";
$conn = mysqli_connect($servername, $username, $pass, $dbname);
$email = $_POST['email'];
if (mail_validation($email) == 1){
    echo 'invalid email';
}
else{
    $query = "SELECT * from user_details where user_mail='$email';";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        $sql = "INSERT INTO user_details (user_mail,used_storage, logged_in) VALUES ('$email',0,0);";
        mysqli_query($conn, $sql);
    }
    echo mysqli_error($conn);
    $otp = rand(100000, 999999);
    $sql = "SELECT logged_in from user_details where user_mail='$email'";
    $result = mysqli_query($conn, $sql);
    $row = $result->fetch_assoc();
    if ($row['logged_in'] == 0)
    {
        $sql = "UPDATE user_details SET otp_value=$otp,created_at=CURRENT_TIMESTAMP WHERE user_mail='$email';";
        mysqli_query($conn, $sql);
        send_mail($email, $otp);
        $_SESSION['EMAIL'] = $email;
        echo 'yes';
    }
    else{
        echo 'multiple login';
    }
}