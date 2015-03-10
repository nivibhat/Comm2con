<!DOCTYPE html>
<!--this is contact form, which has a form and unordered list to display name, phone etc and a submit button-->
<html>

<head>
<meta charset="utf-8">
  <title>Contact Us</title>
  <link rel="stylesheet" href="includes/styles/Contact_style_sheet.css"/>
    <script src="includes/js/jquery-1.10.2.js"></script>

</head>

<?php

require_once 'includes/constants/sql_constants.php';

$msg = NULL;
$err=NULL;

if(isset($_POST)) {
	if(isset($_POST['send_message'])) {
		$user_name = filter($_POST['user_name']);
		$user_phone = intval(filter($_POST['phone']));
		$email = filter($_POST['email']);
		$message = filter($_POST['message']);

		if(($_POST['email']<>'')) {
			$email_to = GLOBAL_EMAIL;
			$msg_subject = 'Email from website contact form';

			$message = " Message from the website contact form: \n
				\n\n\n
				Username : .$user_name. \n\n
				User email : .$email.\n\n
				user phone: .$user_phone.\n\n
				Message:  .$message. \n\n
				\n\n ";
			
			$return_val =  send_message($email_to,$msg_subject, $message);
			
			if($return_val) {
				$msg = "Message sent. Thank you!";
			}
			else {
				$err = "Could not send the message, please try again";
			}
		}
	}
}

?>

<head>
	<title>Contact us</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="includes/styles/profile_styles.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
</head>
<body>

<?php
include_once ('includes/header.inc.php');
session_start();

if($_SESSION){
	include('includes/navigation.inc.php');
}
?>

<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			 <?php
			if($_SESSION){
				include('includes/left_column.inc.php');
			}
			?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
			<?php
			if(isset($msg)) {
				echo '<div class="success" >'.$msg.'</div>';
			} 
			elseif (isset($err)) {
				echo '<div class="error">'.$err.'</div>';
			}
			?>
			
			<form class = "contact_form" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method = "post" name = "contact_form">
			<ul>
				<h2>Contact Us:</h2>
				
				<li>
					<label>Name:</label>
					<input type="text" placeholder="Enter your name" name='user_name' required="required" />
				</li>
				
				<li>
					<label>Phone:</label>
					<input type="number" name='phone' placeholder = "123-345-1234"  />
				</li>
				
				<li>
					<label>Email:</label>
					<input type="email" name='email' placeholder="abc@abc.com" required="required" />
				</li>
				
				<li>
					 <label>Message:</label>
					 <textarea name="message" cols="30" rows="10"></textarea>
				</li>
				
				<li>
					 <button class = "submit" name="send_message" type = "submit">Send the message</button>
				</li>
			</ul>
			</form>
		</div>
	</div>
</div>

<?php include('includes/footer.inc.php'); ?>
</body>
</html>