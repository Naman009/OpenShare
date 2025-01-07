<?php 
    session_start();
    if ($_SESSION['IS_LOGIN']){
        $email=$_SESSION['EMAIL'];
    }
    else{
        header('location: home.php');
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
    </script>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap');
</style>

<body>
    <nav class="navbar navbar-light bg-light">
        <a style="font-family: 'Major Mono Display', monospace;font-size: 25px;" class="navbar-brand px-3" href="dashboard.php">
            OPEN SHARE !
        </a>
        <div class="d-flex me-4">
            <span style="font-size:18px;"
                class="text-muted me-3 mt-1 d-none d-lg-block d-xl-block"><?php echo $email ?></span>
            <a href='logout.php'><button style="border-radius: 20px;" class="btn btn-primary px-4" >Log Out</button></a>
        </div>
    </nav>

    <div class="row pt-lg-4 pt-sm-4 mx-2">
        <div class="col-lg-3 px-0">
           
        </div>
        <div class="col-lg-6 px-0 pt-4">
            <center>
                <a class="fs-3 text-muted  text-decoration-none" href="#"><img class="mt-3"
                        style='max-width: 60%;height: auto;' src="icons\denied_icon.svg" title="Upload Files!">
                    <p class="mt-4">You Do Not Have Access to The Requested File.</p>
                </a>
            </center>
        </div>
        <div class="col-lg-3 px-0">
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>
</html>
<?php header('refresh:5; location: dashboard.php'); ?>
