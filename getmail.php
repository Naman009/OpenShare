<?php
session_start();
if (isset($_SESSION['IS_LOGIN'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $dbLink = new mysqli('localhost', 'root', '', 'project_db');
        if (mysqli_connect_errno()) {
            die("MySQL connection failed: " . mysqli_connect_error());
        }
        $fetch = "SELECT user_access from access where fid='$id'";
        $mailfetch = $dbLink->query($fetch);
        $maillist='';
        while ($row = $mailfetch->fetch_assoc())
        {
            $maillist = $maillist. ' ' .$row['user_access']; 
        }
        echo $maillist;
    }
    else{
        header('location: dashboard.php');
    }
}
?>