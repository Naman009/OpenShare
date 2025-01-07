<?php
    session_start();

    if (!isset($_SESSION['IS_LOGIN'])){
        header('location:home.php');
        die();
    }
    else{
        $email = $_SESSION['EMAIL'];
    }

    function send_mail($email, $fid){
        $to_email = $email;
        $subject = "Open Share File access by ". $_SESSION['EMAIL'];
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message = "<b>Dear ".$email.",</b><br><br><span>A file was shared by <b>".$_SESSION['EMAIL']."</b></span>.<br><span>Access the shared file at : <b>http://localhost/WPminiproject/get_file.php?id=".$fid."</b></span><br><br><b>Happy Sharing!</b><br><b>Team Open Share</b> <br><br><strong>This is an automatically generated email, please do not reply.</strong><br>";
        mail($to_email, $subject, $message, $headers);
    }

    $msg='';
    $errmsg='';
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $dbLink = new mysqli('localhost', 'root', '', 'project_db');
        if(mysqli_connect_errno()) {
            die("MySQL connection failed: ". mysqli_connect_error());
        }
        if(!empty($_FILES['uploaded_file'])) {
            if($_FILES['uploaded_file']['error'] == 0) {
                $name = $dbLink->real_escape_string($_FILES['uploaded_file']['name']);
                $mime = $dbLink->real_escape_string($_FILES['uploaded_file']['type']);
                $data = $dbLink->real_escape_string(file_get_contents($_FILES ['uploaded_file']['tmp_name']));
                $size = $_FILES['uploaded_file']['size'];
                $sql = "SELECT used_storage FROM user_details WHERE user_mail='$email'";
                $result = mysqli_query($dbLink, $sql);
                $row=$result->fetch_assoc();
                $used_storage = (float)$row['used_storage'];
                if (($used_storage + $size) > 5368709120){
                    $errmsg='Not enough space! Delete some files to create space.';
                }
                else{
                    newid:
                        $bytes = random_bytes(5);
                        $fid = bin2hex($bytes);
                    $exists = "SELECT * FROM fileinfo where fid='$fid'";
                    $result = mysqli_query($dbLink, $exists);
                    if (mysqli_num_rows($result) != 0){
                        goto newid;
                    }
                    $query = "INSERT INTO fileinfo (email, fname, fid, file_size, file_type, file_data) VALUES ('$email','$name', '$fid', '$size', '$mime', '$data')";
                    $result = $dbLink->query($query);

                    if($result) {
                        $msg='Your file was successfully added!';
                    }
                    else {
                        $errmsg='Failed to insert the file';
                    }
                }
            }
            else {
                $errmsg = 'An error occured while the file was being uploaded.';
            }
        }
        else {
            $errmsg='No file was selected.';
        }

        if (isset($_POST['tags'])){
            $user_string = str_replace(array('[', ']', '{', '}', '"', ':', 'value'), '', $_POST['tags']);
            $user_array = explode(",", $user_string);
            foreach ($user_array as $usermail){
                if ($usermail != $email)
                {
		   if($usermail!=''){ 
                    $sql = "INSERT INTO access (fid, user_access) VALUES ('$fid', '$usermail')";
                    mysqli_query($dbLink, $sql);
                    if (isset($_POST['notify'])){
                        send_mail($usermail, $fid);
                    }
}
                }
            }
        }
        $dbLink->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <noscript>
		<meta HTTP-EQUIV="refresh" content=0;url="javascriptNotEnabled.html">
    </noscript>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://unpkg.com/@yaireo/tagify"></script>
    <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Oregano&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Overlock&display=swap');

    .email-input {
        min-width: 250px;
        border-radius: 5px;
        padding: 2px;
    }
</style>

<body>
    <script>
        function user_access(){    
            var input = document.getElementsByClassName('tagify__tag-text');
            var email_string = '';
            for (let i = 0; i < input.length; i++) {
                email_string = input[i].innerHTML + ',' + email_string;
            }
            console.log(email_string);
            document.getElementById('access').setAttribute('value', email_string);
        }
    </script>
    <img src="icons\bg-openshare3.jpg" width='100%' height="100%" class="position-absolute" style="z-index: -1;" alt="">
    <nav class="navbar navbar-light bg-light">
        <a style="font-family: 'Major Mono Display', monospace;font-size: 25px;" class="navbar-brand px-3" href="dashboard.php">
            OPEN SHARE !
        </a>
        <div class="d-flex me-4">
            <span style="font-size:18px;"
                class="text-muted me-3 mt-1 d-none d-lg-block d-xl-block"><?php echo $email ?></span>
            <a href='logout.php'><button style="border-radius: 20px;" class="btn btn-primary px-4">Log Out</button></a>
        </div>
    </nav>
    
    <div class="row pt-4 mt-lg-4 mx-2">
        <div class="col-lg-1"></div>
        <div class="col-lg-4 pt-lg-4 mt-lg-4">
            <div class="pt-lg-4">
                <form style="padding: 30px;border-radius: 15px;" class="bg-white mt-lg-4" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                    <div class="">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="inputGroupFile02" name='uploaded_file' onchange="enable()">
                        </div>
                        <div>
                            <label class="pb-2" for="floatingInputValue">Add User(s) to Access File</label>
                            <input class='form-control input-text-group bg-white border border-primary
                        email-input' name='tags' value='' autofocus>
                            <input name='access' id='access' style='display:none'>
                        </div>
                        <div>
                            <div class="form-check pt-3" style="font-size: 18px;">
                                <input class="form-check-input" type="checkbox" value="true" name='notify' id="flexCheckChecked" checked>
                                <label class="form-check-label" for="flexCheckChecked">
                                    Notify Users Via Email
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-success py-2 mt-2 mb-1" role="alert" id='msg' style='border-radius:10px; display:none;'>
                            <?php 
                                if($msg != '') 
                                {
                                    echo "<script>document.getElementById('msg').style.display='block';</script>"; 
                                    echo $msg; 
                                    $msg='';
                                } 
                            ?>
                        </div>
                        <div class="alert alert-danger py-2 mt-2 mb-1" role="alert" id='errmsg' style='border-radius:10px; display:none;'>
                            <?php 
                                if($errmsg != '') 
                                {
                                    echo "<script>document.getElementById('errmsg').style.display='block';</script>"; 
                                    echo $errmsg; 
                                    $errmsg='';
                                } 
                            ?>
                        </div>
                        <button type="submit" style="border-radius: 25px;" class="btn btn-primary mt-2 px-4" id='upload' disabled>Upload File</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-1"></div>

        <div class="col-lg-5 pt-lg-4 ">
            <center>
            </center>
            <div class="d-none d-lg-block">
                <div class="pt-lg-4 mt-lg-4">
                    <h1 style="font-family:'Oregano', cursive;font-size: 80px; color: white;">Any File.<br>Any Format.</h1>
                    <h2 style="font-family: 'Overlock', cursive;font-size: 55px;color: white;">Send Anything upto 5GB with 
                        <a href='dashboard.php' style='text-decoration:none'><span style="font-family: 'Major Mono Display', monospace;font-size: 50px;color: white;">OPEN SHARE</span></a>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <script>
        var input = document.querySelector('input[name=tags]');
        new Tagify(input, {
            pattern: /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            delimiters: ' '
        })

        function enable(){
            document.getElementById('upload').disabled=false;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</body>
</html>