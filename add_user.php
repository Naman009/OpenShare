<?php
session_start();
if (isset($_SESSION['IS_LOGIN'])) {
    if (isset($_POST['email'])) {
        $id = $_POST['id'];
        $dbLink = new mysqli('localhost', 'root', '', 'project_db');
        if (mysqli_connect_errno()) {
            die("MySQL connection failed: " . mysqli_connect_error());
        }
        $user_array = explode(",", $_POST['email']);
        foreach ($user_array as $usermail) {
            $usermail = trim($usermail);
            $sql = "INSERT INTO access (fid, user_access) VALUES ('$id', '$usermail');";
            mysqli_query($dbLink, $sql);
        }
        echo 'yes';
    } else {
        echo 'no users provided';
    }
} else {
    header('location: home.php');
    die();
}
