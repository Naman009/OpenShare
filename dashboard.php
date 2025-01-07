<?php
	session_start();
	function formatBytes($size, $precision = 2)
	{
		if ($size == 0){
			return '0 B';
		}
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB');
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
	if (isset($_SESSION['IS_LOGIN'])) {
	} else {
		header('location:home.php');
		die();
	}
	$dbLink = new mysqli('localhost', 'root', '', 'project_db');
	$email = $_SESSION['EMAIL'];
	if(mysqli_connect_errno()) {
		die("MySQL connection failed: ". mysqli_connect_error());
	}
	$findsize = "SELECT SUM(file_size) FROM fileinfo WHERE email='$email'";
	$result = mysqli_query($dbLink, $findsize);
	while($row = mysqli_fetch_array($result)){
		$used_storage = $row['SUM(file_size)'];
	}
	$storage = (int)$used_storage;

	$sql = "UPDATE user_details SET used_storage=$storage WHERE user_mail='$email'";
	mysqli_query($dbLink, $sql);
	
	$sql = "SELECT used_storage FROM user_details WHERE user_mail='$email'";
	$result = mysqli_query($dbLink, $sql);
	$row=$result->fetch_assoc();
	$used_storage = (float)$row['used_storage'];

	$storage_inwords = formatBytes($used_storage);
	$storage_inpercent = number_format((float)($used_storage/5368709120) * 100, 2, '.', '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<noscript>
		<meta HTTP-EQUIV="refresh" content=0;url="javascriptNotEnabled.html">
    </noscript>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap');

		.second_box {
			display: none;
		}

		.field_error {
			color: red;
		}
	</style>
	<title>Dashboard</title>
</head>

<body onload="qoutabar(<?php echo $storage_inpercent; ?>)">
	<nav class="navbar navbar-light bg-light">
		<a style="font-family: 'Major Mono Display', monospace;font-size: 25px;" class="navbar-brand px-3" href="dashboard.php">
			OPEN SHARE
		</a>
		<div class="d-flex me-4">
			<span style="font-size:18px;"
				class="text-muted me-3 mt-1 d-none d-lg-block d-xl-block"><?php echo $email; ?></span>
			<a href='logout.php'><button style="border-radius: 20px;" class="btn btn-primary px-4">Log Out</button></a>
		</div>
	</nav>

	<div class="row pt-lg-2 pt-sm-2 mx-2">
		<div class="col-lg-6 px-0">
			<center>
				<a class="fs-3 text-muted  text-decoration-none" href="add_file.php">
					<img class="mt-3" style='max-width: 60%;height: auto;' src="icons/upload.svg" title="Upload Files!">
					<p>Upload Files</p>
				</a>
			</center>
		</div>
		<div class="col-lg-6 px-0">
			<center>
				<a class="fs-3 text-muted  text-decoration-none" href="get_file.php">
					<img class="mt-3" style='max-width: 60%;height: auto;' src="icons/download.svg" title="View & Download Files!">
					<p>Download Files</p>
				</a>
			</center>
		</div>
	</div>
	<center style="padding-top:2%;padding-bottom: 1%;">
		<div class="progress w-50 mx-4" style="height:10px;">
			<div class="progress-bar" role="progressbar" style="width: 0%"></div>
		</div>
		<p class="text-muted mb-1"><?php echo $storage_inwords ?> / 5 GB used</p>
		<p style='color:red;' class='mb-0'><?php 
			if ($storage_inpercent > 95){
				echo 'Storage is almost full. Delete some files.';
			}
		?></p>
	</center>
	<script>
		function qoutabar(bar_val){
			bar_width = String(bar_val)+"%";
			width_time = Math.floor(2500/(101-bar_val))
			$(".progress-bar").animate({
				width: bar_width
			},width_time);
		}
	</script>
	
	<?php
		if (isset($_COOKIE['redirect_url'])){
			$id = $_COOKIE['redirect_url'];
	?> 
			<script>
				window.open('http://localhost/WPminiproject/get_file.php?id=<?php echo $id ?>', '_blank').focus();
			</script>;
	<?php
			unset($_COOKIE['redirect_url']);
			setcookie('redirect_url', '', time() - 3600, '/');
		}
	?>
</body>

</html>