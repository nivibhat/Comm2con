<?php
/*Secured user only page*/
include '../includes/constant/config.inc.php';
secure_page();
if(!is_admin())
{
	header("Location: ".SITE_BASE."/login.php");
}

return_meta("Administrator User listing");
$msg = array();
$err = array();

/*Update user account
===================================================*/

if(isset($_POST['update']))
{
	$update = "UPDATE ".USERS." SET full_name = '".filter($_POST['fullname'])."', user_name = '".filter($_POST['username'])."', usr_email = AES_ENCRYPT('".filter($_POST['email'])."', '$salt'), user_level = '".filter($_POST['user_level'])."'";

	if(!empty($_POST['newpass']))
	{
		$update .= ", usr_pwd = '".hash_pass(filter($_POST['newpass']))."'";
	}

	$update .= " WHERE id = '".filter($_POST['user_id'])."'";

	$run_update = mysql_query($update) or die(mysql_error());

	if($run_update)
	{
		$msg[] = stripslashes(ucfirst($_POST['fullname'])) . "'s Profile updated successfully!";
	}

}

/*Delete user
===================================================*/

if(isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id']))
{
	$dq = mysql_query("DELETE FROM ".USERS." WHERE id = '".filter($_GET['id'])."' LIMIT 1") or die(mysql_error());
	if($dq)
	{
		$msg[] = "Successfully deleted user.";
	}
	else
	{
		$err[] = "Unable to remove user";
	}
}

/*Create new user
===================================================*/

$pass = NULL;
$new_user_name = NULL;
$new_user_email = NULL;
if(isset($_POST['add_user']))
{
	$pass1 = generate_key();
	$pass = hash_pass($pass1);
	$new_user_name = filter($_POST['new_user_name']);
	$new_user_email = filter($_POST['new_user_email']);
	$today = date('Y-m-d');

	$check = mysql_query("SELECT user_name, usr_email FROM ".USERS." WHERE user_name = '$new_user_name' OR usr_email = AES_ENCRYPT('$new_user_email', '$salt')") or die(mysql_error());
	if(mysql_num_rows($check) > 0)
	{
		$err[] = "A user with the username or email address already exists";
	}
	if(!check_email($new_user_email))
	{
		$err[] = "You must enter a valid email";
	}

	if(empty($err))
	{
		$add_user = mysql_query("INSERT INTO ".USERS." (`user_name`, `usr_email`, `user_level`, `usr_pwd`, `date`, `approved`) VALUES ('$new_user_name', AES_ENCRYPT('$new_user_email', '$salt'), 1, '$pass', '$today', 1)") or die(mysql_error());

		$message = "Hello,
		You have been registered as a user with SOMEWEBSITE by an administrator.
		You may login to your account by going to:

		".SITE_BASE."/login.php

		And logging in with the following information:
		Username: ".$new_user_name."
		Password: ". $pass1."

		Thank you,
		Admin";

		send_msg($new_user_email, "User Registration", $message);

		$msg[] = "Successfully added ".$new_user_name . " and an email has been sent to the user.";
	}
}
?>
<script>
$(document).ready(function(){
	$("#profile_form").validate();
	$("#new_user_form").validate();
});
</script>
</head>
<body>
<div id="container">

	<?php include '../includes/constant/nav.inc.php'; ?>

	<h1>Hey <?php echo $_SESSION['user_name']; ?>, Manage your users</h1>

	<?php
	if(!empty($msg))
	{
		echo '<div class="success">';
		foreach($msg as $m)
		{
			echo $m.'<br />';
		}
		echo '</div>';
	}
	if(!empty($err))
	{
		echo '<div class="err">';
		foreach($err as $e)
		{
			echo $e . '<br />';
		}
		echo '</div>';
	}
	?>
	<table cellpadding="5" cellspacing="5" border="0" width="100%">
	<tr>
		<th width="22%" align="left">Name</th>
		<th width="22%" align="left">Username</th>
		<th width="22%" align="left">Active</th>
		<th width="22%" align="left">Actions</th>
	</tr>

	<?php
	$in = mysql_query("SELECT *, AES_DECRYPT(usr_email, '$salt') AS email FROM ".USERS."") or die("Unable to get the info!");
	while($r = mysql_fetch_array($in))
	{
	?>
	<tr>
		<td><?php echo $r['full_name']; ?></td>
		<td><?php echo $r['user_name']; ?></td>
		<td>
			<?php
				$app = "No";
				if($r['approved'] == 1)
				{
					$app = "Yes";
				}
				echo $app;
			?>
		</td>
		<td><a href="javascript:void(0);" onclick='$("#form_<?php echo $r['id']; ?>").toggle("slow");'>Edit</a> |
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&id=<?php echo $r['id']; ?>">Delete</a></td>
	</tr>
		<tr>
		<td id="form_<?php echo $r['id']; ?>" style="display:none;">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="user_id" value="<?php echo $r['id']; ?>" />
			<table cellspacing="5" cellpadding="5" border="0" width="100%">
			<tr>
			<td>Name</td>
			<td><input type="text" name="fullname" value="<?php echo $r['full_name']; ?>" /></td>
			</tr>
			<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?php echo $r['user_name']; ?>" /></td>
			</tr>
			<tr>
			<td>Email</td>
			<td><input type="text" name="email" value="<?php echo $r['email']; ?>" /></td>
			</tr>
			<tr>
			<td>New Password</td>
			<td><input type="text" name="newpass" /></td>
			</tr>
			<tr>
			<td>User Level</td>
			<td>
				<select name="user_level">
				<option value="">Select</option>
				<?php
				$levels = array('User' => 1, 'Administrator' => 5);
				foreach($levels as $name => $level)
				{
					$selected = NULL;
					if($r['user_level'] == $level)
					{
						$selected = "selected=\"selected\"";
					}
					echo '<option value="'.$level.'" '.$selected.'>'.$name.'</option>';
				}
				?>
				</select>

			</td>
			</tr>
			<tr>
			<td>Login Information:</td>
			<td>Last login: <?php echo $r['last_login']; ?>, total number of logins: <?php echo $r['num_logins']; ?></td>
			</tr>
			<tr>
			<td colspan="4" align="center">
				<input type="submit" name="update" value="Update Profile" />
			</td>
			</tr>
			</table>
			<hr />
			</form>
		</td>
		</tr>

	<?php
	}
	?>
	</table>

	<h1>Create New User</h1>
	<form name="create_new" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="new_user_form">
	<table cellspacing="5" cellpadding="5" border="0">
	<tr>
	<td>Username</td>
	<td><input type="text" name="new_user_name" class="required" /></td>
	</tr>
	<tr>
	<td>Email</td>
	<td><input type="text" name="new_user_email" class="required email" /></td>
	</tr>
	<tr>
	<td colspan="2">Password is automatically generated and sent to user</td>
	</tr>
	<tr>
	<td colspan="2">
		<input type="submit" name="add_user" value="Create User" />
	</td>
	</tr>
	</table>
	</form>
</div>
</body>
</html>