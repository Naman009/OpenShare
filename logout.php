<?php
session_start();
$dbLink = new mysqli('localhost', 'root', '', 'project_db');
$email=$_SESSION['EMAIL'];
$sql="UPDATE user_details set logged_in=0 where user_mail='$email'";
mysqli_query($dbLink, $sql);
echo mysqli_error($dbLink);
$dbLink->close();
$email='';
unset($_SESSION['IS_LOGIN']);
unset($_SESSION['EMAIL']);
unset($_COOKIE['redirect_url']);
header('location:home.php');
die();
