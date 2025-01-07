<?php
session_start();
$servername = "localhost";
$username = "root";
$pass = "";
$dbname = "project_db";
$conn = mysqli_connect($servername, $username, $pass, $dbname);

$otp = $_POST['otp'];
$email = $_SESSION['EMAIL'];
$sql = "SELECT created_at from user_details where user_mail='$email'";
$result = mysqli_query($conn, $sql);
$row = $result->fetch_assoc();
date_default_timezone_set('Asia/Kolkata');
$t = time();
$currtime = date("Y-m-d H:i:s.u",$t);
$timediff = strtotime($currtime) - strtotime($row['created_at']);
if ($timediff > 300){
	echo 'otp expired';
}
else{
	$sql = "SELECT * from user_details where user_mail = '$email' and otp_value=$otp;";
	$res = mysqli_query($conn, $sql);
	echo mysqli_error($conn);
	
	if (mysqli_num_rows($res) == 1) {
		$sql = "UPDATE user_details SET otp_value=NULL where user_mail ='$email';";
		$result = mysqli_query($conn, $sql);
		$_SESSION['IS_LOGIN'] = $email;
		echo "yes";
		$sql = "UPDATE user_details SET logged_in=1 where user_mail='$email'";
		mysqli_query($conn, $sql);
	} else {
		echo "not_exist";
	}
}
