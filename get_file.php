<?php
session_start();
if (isset($_SESSION['IS_LOGIN'])){
    echo $_GET['id'];
    $email = $_SESSION['EMAIL'];
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $dbLink = new mysqli('localhost', 'root', '', 'project_db');
        if(mysqli_connect_errno()) {
            die("MySQL connection failed: ". mysqli_connect_error());
        }

        $query1 = "SELECT * FROM access where fid='$id' and user_access='$email'";
        $result1 = mysqli_query($dbLink, $query1);

        $query2 = "SELECT * FROM fileinfo where fid='$id' and email='$email'";
        $result2 = mysqli_query($dbLink, $query2);

        if (mysqli_num_rows($result1) > 0 || mysqli_num_rows($result2) > 0){
            $query = "SELECT fname, file_type, file_size, file_data FROM fileinfo WHERE fid = '{$id}'";
            $result = $dbLink->query($query);
            if($result) {
                if($result->num_rows == 1) {
                    $row = mysqli_fetch_assoc($result);
                    header("Content-Type: ". $row['file_type']);
                    header("Content-Length: ". $row['file_size']);
                    header("Content-Disposition: attachment; filename=". $row['fname']);
                    echo $row['file_data'];
                }
                else {
                    echo 'Error! No file exists with that ID.';
                }
                @mysqli_free_result($result);
            }
            else {
                echo "Error! Query failed: <pre>{$dbLink->error}</pre>";
            }
            @mysqli_close($dbLink);
        }
        else{
            header('location:restricted_access.php');
        }
    }
    else {
        header('location:list_files.php');
    }
}
else{
    if(isset($_GET['id'])){
        $cookie_name = "redirect_url";
        $cookie_value = $_GET['id'];
        setcookie($cookie_name, $cookie_value, time() + (600), "/");
    }
    header('location:home.php');
	die();
}