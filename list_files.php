<?php
session_start();
if (isset($_POST['id'])){
    echo $_POST['id'];
}
$dbLink = new mysqli('localhost', 'root', '', 'project_db');
if (mysqli_connect_errno()) {
    die("MySQL connection failed: " . mysqli_connect_error());
}

function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

if (isset($_SESSION['IS_LOGIN'])) 
{
    $email = $_SESSION['EMAIL'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $selected = '';
        if (sizeof($_POST) > 1 and isset($_POST['apply'])) {
            $pdf = isset($_POST['pdf']) ? $_POST['pdf'] : '';
            $doc = isset($_POST['word']) ? $_POST['word'] : '';
            $ppt = isset($_POST['ppt']) ? $_POST['ppt'] : '';
            $excel = isset($_POST['excel']) ? $_POST['excel'] : '';
            $vid = isset($_POST['video']) ? $_POST['video'] . '%' : '';
            $img = isset($_POST['image']) ? $_POST['image'] . '%' : '';
            $audio = isset($_POST['audio']) ? $_POST['audio'] . '%' : '';
            $sql = "SELECT fname, fid, file_size, file_type FROM fileinfo where email='$email' and (file_type in ('$pdf', '$doc', '$ppt', '$excel') or (file_type like '$vid' or file_type like '$img' or file_type like '$audio'))";
            $selected = $_POST;
            unset($_POST);
        } 
        else {
            $sql = "SELECT fname, fid, file_size, file_type FROM fileinfo where email='$email'";
        }
    } 
    else {
        $sql = "SELECT fname, fid, file_size, file_type FROM fileinfo where email='$email'";
    }
?>

<!doctype html>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    </head>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap');

        .icons{
            transition: transform .2s;
        }

        #add-user-popup {
            display: none;
            position: absolute;
            z-index: 9;
        }

        .email-input {
            min-width: 250px;
            border-radius: 5px;
            padding: 2px;
        }

        .icons:hover {
            cursor: pointer;
            transform: scale(1.2);
        }
    </style>

    <body>
        <div class="position-fixed bottom-0 end-0 m-4">
            <div class="toast align-items-center " id='liveToast' role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        File Deleted Succesfully
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="position-fixed bottom-0 end-0 m-4">
            <div class="toast align-items-center " id='liveToast2' role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Download Link Copied
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div class="position-fixed bottom-0 end-0 m-4">
            <div class="toast align-items-center " id='liveToast3' role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Users Added Succesfully
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>


        <script>
            function delete_files(id) {
                jQuery.ajax({
                    url: 'delete_file.php',
                    type: 'post',
                    data: 'id=' + id,
                    success: function(result) {
                        if (result == 'yes') {
                            window.location.href = 'http://localhost/WPminiproject/list_files.php';
                        } else {
                            alert('failed');
                        }
                    }
                });
            }

            function addUser(id) {
                jQuery.ajax({
                    url: 'emptydb.php',
                    type: 'post',
                    data: 'id='+id,
                    success: function(result) {
                    }
                });
                var input = document.getElementsByClassName('tagify__tag-text')
                var email_string ='';
                for(let i=0;i<input.length;i++){
                    email_string = input[i].innerHTML+','+email_string;
                }
                email_string=email_string.substring(0, email_string.length-1);
                console.log(email_string);

                jQuery.ajax({
                    url: 'add_user.php',
                    type: 'post',
                    data: 'email=' + email_string + '&id=' + id,
                    success: function(result) {
                        if (result == 'yes') {
                            var toastLiveExample = document.getElementById('liveToast3');
                            var toast = new bootstrap.Toast(toastLiveExample);
                            toast.show();
                        } else {
                            alert(result);
                        }
                    }
                });
                window.location.href='http://localhost/WPminiproject/list_files.php';
            }

            function addUserScreen(fid) {
                var toastLiveExample1 = document.getElementById('liveToast1')
                var toast1 = new bootstrap.Toast(toastLiveExample1);
                toast1.show();
                console.log(fid);
                document.getElementById("email").removeAttribute("value");
                jQuery.ajax({
                    url: 'getmail.php',
                    type: 'post',
                    data: 'id=' + fid,
                    success: function(result) {
                        email_list=result;
                        console.log(email_list);
                        document.getElementById("email").setAttribute("value", email_list);
                        var input = document.querySelector('input[name=tags]');
                        var tagify = new Tagify(input, {
                            pattern: /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                            delimiters: ' '
                        })
                        document.getElementById("add").setAttribute("onclick", 'addUser("' + fid + ', ' + tagify + '")');
                        tagify.removeAllTags.bind(tagify);
                        email_list='';
                    }
                });
            }


            function copyLink(fid) {
                var filelink = 'http://localhost/WPminiproject/get_file.php?id=' + fid;
                navigator.clipboard.writeText(filelink);
                var toastLiveExample2 = document.getElementById('liveToast2');
                var toast2 = new bootstrap.Toast(toastLiveExample2);
                toast2.show();
            }
        </script>

        <nav class="navbar navbar-light bg-light">
            <a style="font-family: 'Major Mono Display', monospace;font-size: 25px;" class="navbar-brand px-3" href="dashboard.php">
                OPEN SHARE !
            </a>
            <div class="d-flex me-4">
                <span style="font-size:18px;"
                    class="text-muted me-3 mt-1 d-none d-lg-block d-xl-block"><?php echo $email?></span>
                <a href='logout.php'><button style="border-radius: 20px;" class="btn btn-primary px-4">Log Out</button></a>
            </div>
        </nav>

        <style>
            #expand-button {
                cursor: pointer;
                border: 0;
                transition: width 0.5s;
            }

            #expand-button #hidden-text {
                max-width: 0;
                display: inline-block;
                transition: color .25s 0.5s, max-width 2s;
                white-space: nowrap;
                overflow: hidden;
            }

            #expand-button:hover  #hidden-text {
                max-width: 300px;
            }
        </style>

        <form method="post">
            <div class="d-flex justify-content-center flex-wrap flex-lg-row mb-3">
                <a href='add_file.php' style='text-decoration:none'><button id='expand-button' style="border-radius: 27px;margin-top: 13px;" class="p-0 d-flex btn bg-white fs-5 border border-2 border-dark" type='button'>
                    <span class="ps-2 pe-1  m-0"><img src="icons/addfile_icon.png" class="ms-1"width='20px' alt=""></span>
                    <span id='hidden-text' style="margin-top: 3px;"class="me-2" >&nbsp;Add File</span>
                </button></a>
                <div class="form-check mt-3 mx-2">
                    <img class='mx-1 fs-5' src="icons/filter_icon.png" width='30px' alt="">
                    <label class="form-check-label fs-5">
                        Filters :
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='word' value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php echo (isset($selected['word'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        Word
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='ppt' value="application/vnd.openxmlformats-officedocument.presentationml.presentation" <?php echo (isset($selected['ppt'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        PPT
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='excel' value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" <?php echo (isset($selected['excel'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        Excel
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='pdf' value="application/pdf" <?php echo (isset($selected['pdf'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        PDF
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='audio' value="audio/" <?php echo (isset($selected['audio'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        Audio
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='video' value="video/" <?php echo (isset($selected['video'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        Video
                    </label>
                </div>
                <div class="form-check mt-3 mx-2 fs-5">
                    <input class="form-check-input" type="checkbox" name='image' value="image/" <?php echo (isset($selected['image'])) ? 'checked' : ''?> >
                    <label class="form-check-label">
                        Image
                    </label>
                </div>
                <div class="mt-3 mx-2 py-0">
                    <button style="border: 2px solid;border-radius: 25px;padding-top: 4px;padding-bottom: 4px;"
                        class="btn btn-outline-primary px-4" name='apply' type='submit'>Apply</button>
                    <button style="border: 2px solid;border-radius: 25px;padding-top: 4px;padding-bottom: 4px;"
                        class="btn btn-outline-danger px-4 ms-2" name='delete' type='submit'>Clear</button>
                </div>
            </div>
        </form>
        <center>
            <hr style="height:2px;" width="80%">
        </center>
        <div style="margin-left: 6%;" class="d-flex flex-wrap flex-lg-row mb-3">
            <?php 
                $result = $dbLink->query($sql);
                echo mysqli_error($dbLink);
                if ($result) {
                    if ($result->num_rows == 0) {
                        echo '<div style="width:30%; margin:auto;" class="mt-4">
                                <center>
                                    <div class="mx-auto text-muted fs-5">No files uploaded yet!</div>
                                    <a href="add_file.php"><button class="btn btn-primary my-2">Add files</button></a>
                                </center>
                            </div>';
                    } 
                    else {
                        $result = $dbLink->query($sql);
                        echo mysqli_error($dbLink);
                        $i = 0;
                        while ($row = $result->fetch_assoc()) {
                            $size = formatBytes($row['file_size']);
                            $id = $row['fid'];
                            if ($row['file_type'] == 'application/pdf'){
                                $icon = 'pdf_icon.png';
                            }
                            else if ($row['file_type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                                $icon = 'word_icon.png';
                            }
                            else if ($row['file_type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
                                $icon = 'ppt_icon.png';
                            }
                            else if ($row['file_type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                                $icon = 'excel_icon.png';
                            }
                            else if ($row['file_type'] == 'audio/mp3'){
                                $icon = 'mp3_icon.png';
                            }
                            else if ($row['file_type'] == 'video/mp4'){
                                $icon = 'mp4_icon.png';
                            }
                            else if ($row['file_type'] == 'image/png'){
                                $icon = 'png_icon.png';
                            }
                            else if ($row['file_type'] == 'image/jpg' or $row['file_type'] == 'image/jpeg'){
                                $icon = 'jpg_icon.png';
                            }
                            else if ($row['file_type'] == 'image/svg+xml'){
                                $icon = 'svg_icon.png';
                            }
                            else if ($row['file_type'] == 'application/x-zip-compressed'){
                                $icon = 'zip_icon.png';
                            }
                            else if ($row['file_type'] == 'text/plain'){
                                $icon = 'text_icon.png';
                            }
                            else{
                                $icon = 'other_icon.png';
                            }
            ?>
                            <div style="height: 290px;width:290px;border-radius: 25px;" class="p-2 m-3 ms-4 border border-3">
                                <center>
                                    <img src="icons/<?php echo $icon ?>" width='100px' class="mt-4 mb-2" alt="">
                                    <div class="m-2 mb-4">
                                        <p class="m-0" style='width: 95%; height: 30px;overflow:hidden'><?php echo $row['fname'] ?></p>
                                        <p class="m-0"><?php echo $size; ?></p>
                                    </div>
                                    <div style="margin-top: 35px !important;" class="row mt-4">
                                        <div class="col icon-hover">
                                            <img src="icons/add_icon.png" width='25px' onclick="addUserScreen('<?php echo $id; ?>')" alt="Edit User Access" class='icons'>
                                        </div>
                                        <div class="col icon-hover">
                                            <img src="icons/copy_icon.png" width='25px' id='copy' onclick="copyLink('<?php echo $id; ?>')" alt="Copy Link" class='icons'>
                                        </div>
                                        <div class="col icon-hover">
                                            <a href='get_file.php?id=<?php echo $id; ?>'><img src="icons/download_icon.png" width='25px' alt="Download File" class='icons'></a>
                                        </div>
                                        <div class="col icon-hover">
                                            <a id="liveToastBtn" onclick="delete_files('<?php echo $id; ?>')"><img src="icons/delete_icon.png" width='25px' alt="Delete File" href='delete_file.php' class='icons'></a>
                                        </div>
                                    </div>
                                </center>
                            </div>
            <?php
                        }
                    }
                }
            ?>
        </div>

        <div class="position-fixed top-50 start-50 translate-middle">
            <div class="toast align-items-center bg-white" id='liveToast1' role="alert" data-bs-autohide="false" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-white">
                    <strong class="me-auto text-muted">Edit User Access</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="window.location.href='list_files.php'"></button>
                </div>
                <div class="d-flex flex-wrap align-items-center mt-2">
                    <div class="toast-body p-0 ms-3 mx-2">
                        <input class='input-text-group bg-white border border-primary email-input' id='email' type='mail' name='tags' autofocus>
                    </div>
                </div>
                <button id='add' class="btn btn-primary ms-3 my-2" data-bs-dismiss="toast" aria-label="Close">Update Access</button>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous">
        </script>
    </body>
</html>
<?php } ?>