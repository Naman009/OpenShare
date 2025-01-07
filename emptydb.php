<?php
session_start();
if (isset($_SESSION['IS_LOGIN'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id = substr($id,0,10);
        $dbLink = new mysqli('localhost', 'root', '', 'project_db');
        if (mysqli_connect_errno()) {
            die("MySQL connection failed: " . mysqli_connect_error());
        }
        $sql = "DELETE FROM access WHERE fid='$id';";
        mysqli_query($dbLink, $sql);
        echo 'yes';
    }
    else{
        header('location: list_files.php');
    }
}
