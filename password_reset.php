<?php
include_once 'includes/constants/sql_constants.php';

//Pre-assign our variables to avoid undefined indexes
$username = NULL;
$pass2 = NULL;
$msg = NULL;
$email = NULL;
//$err = array();

//See if form was submitted, if so, execute...
if(isset($_POST['reset'])) {
	//Assigning vars and sanitizing user input
	$email = filter($_POST['email']);
	$pass = filter($_POST['pass']);  
	$pass2 = filter($_POST['pass2']); 

	if(empty($email) || strlen($email) < 4) {
		$err[] = "Please enter the email address you have used to register with us!";
	}
	if(strcasecmp($pass,$pass2) != 0) {
		$err[] = "Password and confirm password do not match";
	}
	
	$email_check = mysqli_query($link,"SELECT username from ".USERS. " WHERE email = AES_ENCRYPT('$email', '$salt')") or die(mysqli_error($link));
	if(mysqli_num_rows($email_check) == 0) {
		$err[] = "Invalid email address. Please check again!";
	} 
	else {
		if(empty($err)) {
			$password = hash_pass($pass);
			
			if($update_user_pass = mysqli_query($link,"UPDATE ".USERS. " SET user_password ='".$password."' WHERE email = AES_ENCRYPT('$email', '$salt')")) {
				$msg = "Your password has been changed. You may now <a href=\"".BASE."/index.php\">login</a>. and get busy!";
			}
			else {
				//Passwords don't match, issue an error
				$err[] = "Could not change password, please try again!";
			}
		}
	}
}
?>

<head>
	<title>Reset your password</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="includes/styles/profile_styles.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/login.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
</head>

<body>
<?php
	include_once ('includes/header.inc.php');
?>
	
<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			<?php// include('../includes/left_column.inc.php'); ?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
			<?php 
			if(isset($msg)) {
				echo '<div class="success" >'.$msg.'</div>';
			} 
			elseif (isset($err)) {
				if(!empty($err)) {
					echo '<div class="err">';
					
					foreach($err as $e) {
						echo $e.'<br />';
					}
					
					echo '</div>';
				}
			}
			?>
			<div class="card " id="user_profile_div" style="width: 45em;height: 40em; overflow-y: auto;">
				<span class="success" style="display:none;"></span>
				<span class="error" style="display:none;">Please enter some text</span>
					
				<div class="front">
					<h3>Reset your password</h3>
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="p_reset_form">
						<label class="label_class" for="user">Email</label>
							<input class="input_class" type="email"  name="email" value="" /><br>
							<label class="label_class" for="pass">New Password</label>
							<input class="input_class" type="password" name="pass" value=""  />
							<br>
							<label class="label_class" for="pass" >confirm Password</label>
							<input class="input_class" type="password"  name="pass2" value="" /><br><br>
							<a href="index.php"> Back to Login! 
							<button id ="reset"  style="float:right; margin-right: 5em;" name="reset" type="submit">Change Password</button> 
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>