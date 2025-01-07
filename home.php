<!DOCTYPE html>
<html lang="en">

<head>
	<noscript>
		<meta HTTP-EQUIV="refresh" content=0;url="javascriptNotEnabled.html">
    </noscript>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap');

		.second_box {
			display: none;
		}

		.field_error {
			color: red;
		}
	</style>
	<script>
		function send_otp() {
			var email = jQuery('#email').val();
			jQuery.ajax({
				url: 'send_otp.php',
				type: 'post',
				data: 'email=' + email,
				success: function(result) {
					if (result == 'yes') {
						jQuery('#multiplelogin').hide();
						jQuery('#email').removeClass('is-invalid');
						jQuery('.second_box').show();
						jQuery('.first_box').hide();
					} else if (result == 'multiple login'){
						jQuery('#multiplelogin').show();
					} else {
						jQuery('#email').addClass('is-invalid');
					}
				}
			});
		}

		function submit_otp() {
			var otp = jQuery('#otp').val();
			jQuery.ajax({
				url: 'check_otp.php',
				type: 'post',
				data: 'otp=' + otp,
				success: function(result) {
					if (result == 'yes') {
						window.location = 'dashboard.php';
					}
					else if (result == 'otp expired'){
						jQuery('#otp_expired').show();
						jQuery('#otp').addClass('is-invalid');
						send_otp();
					}
					if (result == 'not_exist') {
						jQuery('#otp').addClass('is-invalid');
					}
				}
			});
		}
	</script>
</head>

<body oncontextmenu="return false;">
	<nav class="navbar navbar-light bg-light">
		<a style="font-family: 'Major Mono Display', monospace;font-size: 25px;" class="navbar-brand px-3" href="#">
			OPEN SHARE !
		</a>
		</div>
	</nav>
	<div style='padding: 4rem;'>
		<div class="row">
			<div class="col-lg-6">
				<img style='max-width: 85%; height: auto;' class='ps-4' src="icons/home_illustration.svg" />
			</div>
			<div class="col-lg-6 pt-4 mt-lg-3 pe-lg-4">
				<div class="pe-lg-4 me-lg-4">
					<div>
						<h2>Sign Up / Login</h2>
						<p class="text-muted">Be assured, your Email Address is Safe with Us.</p>
					</div>
					<form method="POST">
						<div class="form-floating mb-3">
							<input style="border-radius: 15px;" type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
							<label for="email">Email Address</label>
						</div>
						<div class="alert alert-danger py-2 mt-1 mb-3" role="alert" id='multiplelogin' style='border-radius:10px; display:none'>
							You already have one active instance of OPEN SHARE.&nbsp;Please log out to continue.
						</div>
						<div class="form-floating second_box">
							<input style="border-radius: 15px;" type="text" class="form-control" id="otp" name="otp" placeholder="One-Time Password">
							<label for="otp">One-Time Password</label>
						</div>
						<div class="alert alert-danger py-2 mt-3 mb-0" role="alert" id='otp_expired' style='border-radius:10px; display:none'>
							Previous OTP expired. Please enter new OTP!
						</div>
						<button style="border-radius: 25px;" type='button' class="btn btn-primary p-2 px-3 first_box" onclick="send_otp()">Send OTP to above Email</button>
						<button style="border-radius: 25px;" type='button' class="btn btn-primary my-3 p-2 px-3 second_box" onclick="submit_otp()">Start Sharing!</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>