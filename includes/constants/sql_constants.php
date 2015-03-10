<?php
/* This file contains variables defining the database for the Havyaka culture site and functions to manipulate the database. */

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include_once 'dbc.php';

$file_location = ROOT."/pictures";

global $file_location;
$max_file_size = 5000000;
global $max_file_size;

// connect to the SQL server and select the database - we can now use $link and $db in pages that include this page
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Couldn't make connection:" . mysqli_error() );
$db = mysqli_select_db($link, DB_NAME) or die("Couldn't select database:" . mysqli_error() );

include_once '/../swift/lib/swift_required.php';

// Function to super sanitize anything going near our DBs
function filter($data) {
	$data = trim(htmlentities(strip_tags($data)));

	if (get_magic_quotes_gpc()) {
		$data = stripslashes($data);
	}

	$data = mysql_real_escape_string($data);
	return $data;
}

// Function to easily output all our css, js, etc...
function return_meta($title = NULL, $keywords = NULL, $description = NULL) {
	if(is_null($title)) {
		$title = "Community Connect - Havyaka Community";
	}

	$meta = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>'.$title.'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="'.$keywords.'" />
	<meta name="description" content="'.$description.'" />
	<meta name="language" content="en-us" />
	<meta name="robots" content="index,follow" />
	<meta name="googlebot" content="index,follow" />
	<meta name="msnbot" content="index,follow" />
	<meta name="revisit-after" content="7 Days" />
	<meta name="url" content="'.BASE.'" />
	<meta name="copyright" content="Copyright '.date("Y").' Community Connect. All rights reserved." />
	<meta name="author" content="Your site name here" />
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="pictures/favicon.ico" />
	';

	echo $meta;
}

// Function to validate email addresses
function check_email($email) {
		return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
}

// Function to check the registration page input fields errors
function error_check($firstname,$username,$password,$confirm_pass,$email,$zipcode) {
	$error= array();
	if(empty($firstname)) {
		$error[] = "You must enter your name";
	}

	if(strlen($firstname) < 2) {
		$error[] = "You must enter your real name";
	}

	if(empty($username)) {
		$error[] = "You must enter a username";
	}

	if(strlen($username) < 4) {
		$error[] = "username must be minimum of 4 letters";
	}

	if(empty($password) || strlen($password) < 4) {
		$error[] = "You must enter a password";
	}

	if(empty($email) || !check_email($email)) {
		$error[] = "Please enter a valid email address.";
	}

	if($password != $confirm_pass) {
		$error[] = "Password and confirm password do not match!";
	}

	if(strlen($zipcode)<5)
	{
		$error[] = "Please enter the right zipcode";
	}
	return $error;
}

// Function to store images in the database
function store_image($file_handler) {
	global $link;
	global $max_file_size;
	global $file_location;

	$allowedExts = array("gif", "jpeg", "jpg", "png","JPEG","JPG","PNG","GIF");

	$temp = explode(".", $file_handler["name"]);

	$extension = end($temp);

	if ((($file_handler["type"] == "image/gif")
		|| ($file_handler["type"] == "image/jpeg")
		|| ($file_handler["type"] == "image/jpg")
		|| ($file_handler["type"] == "image/pjpeg")
		|| ($file_handler["type"] == "image/x-png")
		|| ($file_handler["type"] == "image/png"))
		&& ($file_handler["size"] < $max_file_size)
		&& in_array($extension, $allowedExts)) {
		if ($file_handler["error"] > 0) {
			//echo "Return Code: " . $file_handler["error"] . "<br>";
			// return false;
		}
		else {
			//remove spaces from the filename
			$img_name = str_replace(" ", "", $file_handler["name"]); 

			if (file_exists($file_location."/".$img_name)) {
				$date = new DateTime();
				$x = $date->getTimestamp();
				$img_name = $x.$img_name;
				$new_file_location =$file_location."/".$img_name;
				move_uploaded_file($file_handler["tmp_name"], $new_file_location);
			}
			else {
				$new_file_location = $file_location."/".$img_name;
				move_uploaded_file($file_handler["tmp_name"], $new_file_location);
			}
			return $img_name;
		}
	}
	else {
		return false;
	}
}


/* ---------- begin functions related to users ----------------*/

// Function to send an email message to a user
function send_message($email_to,$msg_subject, $message) {
	global $passsalt;
	global $salt;
	global $link;

	//$result = mysqli_query($link,"SELECT AES_DECRYPT(p_pass,'$salt') AS password FROM ".PSTORE. " WHERE p_email=AES_ENCRYPT('".GLOBAL_EMAIL."','$salt')") or die(mysqli_error($link));
	//$row = mysqli_fetch_assoc($result);

	//$pw = $row['password'];
	$pw = "connectcommunity1";
	
	//we use swift's email function
	$email_to = $email_to; $email_from=GLOBAL_EMAIL;$password = $pw; $subj = $msg_subject;
	$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
	->setUsername($email_from)
	->setPassword($password);

	$mailer = Swift_Mailer::newInstance($transport);

	$message = Swift_Message::newInstance($subj)
	->setFrom(array($email_from => 'Admin'))
	->setTo(array($email_to))
	->setBody($message);

	$result = $mailer->send($message);
	return $result;
}

// Function to secure pages and check users
function secure_page() {
	session_start();
	global $db;
	global $link;

	//Secure against Session Hijacking by checking user agent
	if(isset($_SESSION['HTTP_USER_AGENT'])) {
		//Make sure values match!
		if($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']) or $_SESSION['logged'] != true) {
			logout();

			exit;
		}
		//We can only check the DB IF the session has specified a user id
		if(isset($_SESSION['user_id'])) {
			$details = mysqli_query($link,"SELECT ckey, ctime FROM " . USERS . " WHERE user_id ='".$_SESSION['user_id']."'") or die(mysqli_error($link));
			list($ckey, $ctime) = mysqli_fetch_row($details);

			//We know that we've declared the variables below, so if they aren't set, or don't match the DB values, force exit
			if(!isset($_SESSION['stamp']) && $_SESSION['stamp'] != $ctime || !isset($_SESSION['key']) && $_SESSION['key'] != $ckey) {
				logout();

				exit;
			}
		}
	}
	//if we get to this, then the $_SESSION['HTTP_USER_AGENT'] was not set and the user cannot be validated
	else {
		logout();

		exit;
	}
}

// Function to generate key for login.php
function generate_key($length = 7) {
	$password = "";
	$possible = "0123456789abcdefghijkmnopqrstuvwxyz";

	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}
	}

	return $password;
}

// Function to get the logged in users location, city name and state
// is required display all the local events, chef details for the user
function get_loggedin_user_location($user_id) {
	global $link;
	$q1 = "SELECT e_loc_id FROM " . USERS . " WHERE  user_id = ".$user_id;
	$query = mysqli_query($link,$q1) or (die(mysqli_error($link)));

	$row = mysqli_fetch_assoc($query);
	$location_id = $row['e_loc_id'];
	mysqli_free_result($query);

	return $location_id;
}

// Function to retrieve a user's information
function get_user_info($user_id) {
	// select * from user where user_id = 1;
	global $link;
	global $salt;

	// to do: return user profile picture
	$select = "SELECT first_name, last_name, AES_DECRYPT(email,'$salt') as email, phone, profile_picture";
	// $select = "SELECT first_name, last_name, email as email, phone";

	$from = " FROM " . USERS;

	$where = " WHERE user_id=" . $user_id;

	// build the query
	$q = $select . $from . $where . ";";

	// execute the query
	if($event_query = mysqli_query($link,$q)) {
		$results[] = mysqli_fetch_assoc($event_query);
	}

	mysqli_free_result($event_query);

	return $results;
}

// Function to add new users to the database
function add_user($firstname,$lastname=NULL,$username,$password,$confirm_pass,$email,$zipcode,$date,$user_ip,$activation_code,$community_type) {

	$msg = NULL;
	$err = array();
	global $salt;
	global $link;
	$err =error_check($firstname,$username,$password,$confirm_pass,$email,$zipcode);

	if($stmt = mysqli_prepare($link, "SELECT username, email FROM " . USERS . " WHERE username = '$username' OR email = AES_ENCRYPT('$email', '$salt')") or die(mysqli_error($link))) {
		//execute the query
		mysqli_stmt_execute($stmt);
		//store the result
		mysqli_stmt_store_result($stmt);

		if(mysqli_stmt_num_rows($stmt) > 0) {
			$err[] = "User already exists";
		}

		mysqli_stmt_close($stmt);
	}

	if(empty($err)) {
		//check if the zipcode is already in the table, if not insert into the table.
		$e_loc_id = insert_zipcode_location($zipcode);

		//get the community id based on the community name
		$q = "SELECT community_id FROM " . COMMUNITY_TYPE . " WHERE community_name = '$community_type' LIMIT 1";

		$query = mysqli_query($link,$q) or (die(mysqli_error($link)));
		$row = mysqli_fetch_assoc($query);
		$community_id = $row['community_id'];

		$password = hash_pass($password);

		$query = "INSERT INTO " . USERS . " (first_name, last_name,username, e_loc_id, user_password, email, registration_date, user_ip, activation_code,community_id) VALUES ('$firstname','$lastname', '$username', '$e_loc_id', '$password', AES_ENCRYPT('$email', '$salt'), '$date', '$user_ip', '$activation_code',$community_id)";

		if ($q1 = mysqli_query($link,$query)) {
			//Generate rough hash based on user id from above insertion statement
			$user_id = mysqli_insert_id($link); //get the id of the last inserted item

			$md5_id = md5($user_id);

			mysqli_query($link, "UPDATE " . USERS . " SET md5_id='$md5_id' WHERE user_id='$user_id'");

			if(REQUIRE_ACTIVIATION != 1) {
				//echo "activation " .REQUIRE_ACTIVIATION;

				//Build a message to email for confirmation
				$message = "<p>Hi ".$firstname."!</p>
					<p>Thank you for registering with us. Here are your login details...<br />

					User ID: ".$username."<br />
					Email: ".$email."<br />
					Activation code: ".$activation_code."</p>

					<p>You must activate your account before you can actually do anything. <br>You can do that by clicking on the below link or entering the activation code in the website:<br />
					".BASE."/users/activate.php?user=".$md5_id."&activ_code=".$activation_code."</p>

					<p>Thank You<br/>

					Administrator<br />
					".BASE."</p>";

				//activate user by only through activation
				// set the approved field to 0 to activate the account

				$rs_activ = mysqli_query($link, "UPDATE " . USERS . " SET approved='0' WHERE
				md5_id='". $md5_id. "' AND activation_code = '" . $activation_code ."' ") or die(mysql_error());

				$result = send_message($email,$msg,$message);
				if(!$result) {
					$err[]= "message is not sent";
				}
			}
			else {
				//activate user by default
				// set the approved field to 1 to activate the account

				$rs_activ = mysqli_query($link, "UPDATE " . USERS . " SET approved='1' WHERE
				user_id='". $user_id. "'") or die(mysqli_error($link));
			}
			//mysqli_free_result($q1);
		}
		else {
			$err[] ="Something happened!, please try again!";
		}
	}
	return $err;
}

// Function to update user information
function update_user_info($user_id, $first_name, $last_name, $email, $phone, $profile_picture=NULL) {
	global $link;
	global $salt;

	$q = "UPDATE " . USERS . " SET ";

	// adds first name if specified
	if (!is_null($first_name)){
		$q .= "first_name='$first_name'";
	}

		// adds last name if specified
	if (!is_null($last_name)){
		if (strpos($q,'=') !== false) {
			$q .= ", ";
		}
		$q .= "last_name='$last_name'";
	}

		// adds email if specified
	if (!is_null($email)){
		if (strpos($q,'=') !== false) {
			$q .= ", ";
		}
		$q .= "email=AES_ENCRYPT('$email','$salt')";
	}

		// adds phone if specified
	if (!is_null($phone)){
		if (strpos($q,'=') !== false) {
			$q .= ", ";
		}
		$q .= "phone='$phone'";
	}

	// adds profile picture if specified
	if (!is_null($profile_picture)){
		if (strpos($q,'=') !== false) {
			$q .= ", ";
		}
		$q .= "profile_picture='$profile_picture'";
	}

	$q .= " WHERE user_id = $user_id";

	if (mysqli_query($link,$q)){

		return true;
		// echo "User updated successfully";
	}
	else {

		return false;
		// echo "User update failed";
	}
}

// get the zipcode and check if the zipcode already exists in the location table, or insert into it
function insert_zipcode_location ($zipcode) {
	global $link;

	// query to see if the zipcode exists
	if($loc_query = mysqli_query($link,"SELECT e_loc_id FROM ".LOCATION. " WHERE zipcode = $zipcode LIMIT 1") or die(mysqli_error($link))) {
		// if the zipcode does not exist, insert it into the database
		if(mysqli_num_rows($loc_query) == 0) {
			$q_loc = mysqli_query($link, "INSERT INTO " . LOCATION . " (zipcode) VALUES ('$zipcode')") or die(mysqli_error($link));
			//get the last inserted id from the location table
			$e_loc_id = mysqli_insert_id($link);
		}
		else {
			
			$row = mysqli_fetch_assoc($loc_query);
			$e_loc_id = $row['e_loc_id'];
		}
	}

	mysqli_free_result($loc_query);

	return $e_loc_id;
}

// Function to logout users securely
function logout($lm = NULL) {
	global $link;

	if(!isset($_SESSION)) {
		session_start();
	}

	//If the user is 'partially' set for some reason, we'll want to unset the db session vars
	if(isset($_SESSION['user_id'])) {
		global $db;
		mysqli_query($link,"UPDATE " . USERS . " SET ckey= '', ctime= '' WHERE user_id='".$_SESSION['user_id']."'") or die(mysqli_error($link));
		unset($_SESSION['user_id']);
	}
		unset($_SESSION['user_name']);
		unset($_SESSION['user_level']);
		unset($_SESSION['HTTP_USER_AGENT']);
		unset($_SESSION['stamp']);
		unset($_SESSION['key']);
		unset($_SESSION['fullname']);
		unset($_SESSION['logged']);
		session_unset();
		session_destroy();

	if(isset($lm)) {
		header("Location: ".BASE."/index.php?msg=".$lm);
	}
	else {
		header("Location: ".BASE."/index.php");
	}
}

// Function to has the password for the user
function hash_pass($pass) {
	global $passsalt;
	$hashed = md5(sha1($pass));
	$hashed = crypt($hashed, $passsalt);
	$hashed = sha1(md5($hashed));
	return $hashed;
}

/* ---------- end functions related to users ----------------*/


/* ---------- begin functions related to chefs ----------------*/

/* Function to retrieve a user's information */
 function get_chef_details_logged_in_user($user_id) {
	// select * from user where user_id = 1;
	global $link;
	global $salt;
	$results = array();
	// to do: return user profile picture
	$select = "SELECT chef_id,about_chef, pickup_available, contact_time_preference, payments_accepted, delivery_available, taking_offline_order";

	$from = " FROM " . CHEF;

	// will always return events that are active
	$where = " WHERE user_id=" . $user_id;

	// build the query
	$q = $select . $from . $where . ";";

	// execute the query
	if($event_query = mysqli_query($link,$q)) {
		$results[] = mysqli_fetch_assoc($event_query);
		mysqli_free_result($event_query);
		return $results;

	} else {
	   // $results = NULL;
	}

	//return $results;
}

function  create_update_chef_profile($about_chef,$contact_time_preference,$accepted_payment_type,$pickup,$offline,$delivery,$user_id,$chef_id = NULL) {
	global $link;

	if($chef_id != NULL) {
		$q = "UPDATE " . CHEF . " SET about_chef ='" .$about_chef. "', contact_time_preference ='" .$contact_time_preference. "', payments_accepted ='" .$accepted_payment_type. "', pickup_available='" .$pickup. "', delivery_available='".$delivery. "', taking_offline_order='".$offline."' WHERE chef_id =".$chef_id. ";";
	}
	else {
		$q = "INSERT INTO " . CHEF . " (about_chef,contact_time_preference,payments_accepted,pickup_available,delivery_available,taking_offline_order,user_id,community_id) VALUES ( '".$about_chef. "','".$contact_time_preference. "','".$accepted_payment_type. "','" .$pickup. "','" .$delivery. "','" .$offline. "',".$user_id. ",1);";
	}

	if($q_execute = mysqli_query($link,$q)) {
		return true;
	}
	else {
		return false;
	}
}

//get all the food names
function get_all_food_names() {
	global $link;
	$results = array();
	$q = mysqli_query($link,"SELECT * FROM " . FOOD . ";") or die(mysqli_error($link));

	while ($q_food = mysqli_fetch_assoc($q)) {
		$results[] = $q_food;
	}

	mysqli_free_result($q);

	return $results;
}

//get the foods that the chef is preparing
function get_foods_of_chef($chef_id) {
	global $link;

	$q = "SELECT t1.food_name,t1.food_description,t1.food_picture,t1.food_id,t2.food_price FROM " . FOOD . " t1,
		" . FOOD_CHEF_DETAILS . " t2 WHERE t1.food_id=t2.food_id AND t2.chef_id = " . $chef_id;

	$results =array();

		$query = mysqli_query($link,$q) or die (mysqli_error($link));
			if(mysqli_num_rows($query) !=0) {
				while ($row = mysqli_fetch_assoc($query)) {
					$results[] =$row;
				}
			}
			else {
				$results = NULL;
			}




	mysqli_free_result($query);

	return $results;
}

// Update the food table when the chef updates in his profile
function update_foods_of_chef($chef_id=NULL,$food_id,$food_description=NULL,$food_price=NULL,$food_picture=NULL) {
	global $link;

	//update teh description, price and picture
	if($food_description != NULL || $food_picture != NULL) {
		$q = "UPDATE " . FOOD . " SET ";

		if (!is_null($food_description)) {
			$q .= "food_description='$food_description'";
		}

		if (!is_null($food_price)) {
			$q .= "food_price='$food_price'";
		}

		 if (!is_null($food_picture)){
			$q .= "food_picture='$food_picture'";
		}

		$q .= " WHERE food_id = $food_id";

		if(mysqli_query($link,$q)) {

			return true;
		}
		else {

			return false;
		}
	}
	elseif($food_price !=NULL) {
		$q1 = "UPDATE " . FOOD_CHEF_DETAILS . " SET food_price=" .$food_price. " WHERE food_id = ".$food_id. " AND chef_id =".$chef_id;

		if(mysqli_query($link,$q1)) {

			return true;
		}
		else {

			return false;
		}
	}


}

// add a new food to food table requested by the chef from his dashboard
function add_new_food($chef_id,$food_name,$food_description,$picture_loc) {
	//check if the requested new food exists in the database already using string match. if not add one to the db
	global $link;
	$q_new_food = mysqli_query($link, "SELECT food_id, food_name, food_description FROM " . FOOD . " WHERE food_name ='" .$food_name. "';") or(die(mysqli_error($link)));

	if(mysqli_num_rows($q_new_food) == 0) {
		$q_food_insert = mysqli_query($link,"INSERT INTO " . FOOD . " (food_name, food_description,food_picture,community_id) VALUES ('".$food_name. "','" .$food_description. "','".$picture_loc. "',1)") or die(mysqli_error($link));
		$food_id = mysqli_insert_id($link);
		// $add_selected_food = add_selected_food($food_id,$chef_id);

		$new_food = array(
			"food_id" => $food_id,
			"food_name" => $food_name,
			"food_description" => $food_description,
			"picture_location" => $picture_loc
		);

		return $new_food;
	}
	else {
		return false;
	}
}

/* Function to retrieve info for a specific chef */
function get_chef_info($chef_id) {
	global $link;
	global $salt;

	// SELECT * from CHEF as t1 LEFT JOIN FOOD_CHEF_DETAILS as t2 ON t1.chef_id = t2.chef_id LEFT JOIN FOOD as t3 ON t2.food_id = t3.food_id WHERE t2.food_id = 1;
	$q = "SELECT t1.chef_id, t1.about_chef, t1.contact_time_preference, t1.delivery_available, t1.payments_accepted, t1.pickup_available, t1.taking_offline_order, t4.first_name, t4.last_name, t4.user_id, AES_DECRYPT(t4.email, '$salt') as email, t4.phone, t4.profile_picture
	FROM " . CHEF . " AS t1
	LEFT JOIN " . FOOD_CHEF_DETAILS . " AS t2 ON t1.chef_id = t2.chef_id
	LEFT JOIN " . FOOD . " AS t3 ON t2.food_id = t3.food_id
	LEFT JOIN " . USERS . " AS t4 on t4.user_id = t1.user_id
	WHERE t1.chef_id = $chef_id ;";

	// execute the query
	if($query = mysqli_query($link,$q)) {
		$results = mysqli_fetch_assoc($query);
	}
	return $results;
}

//function to add the selected food from the dropdown into food_chef_details table.
function add_selected_food($food_id,$chef_id) {
	global $link;
	$q_food = "SELECT t1.food_id, t1.chef_id, t2.food_name, t2.food_description, t2.food_picture FROM " . FOOD_CHEF_DETAILS . " AS t1
	LEFT JOIN " . FOOD . " AS t2 ON t1.food_id = t2.food_id
	WHERE t1.food_id= " . $food_id . " AND t1.chef_id = ".$chef_id;

	$food_query = mysqli_query($link,$q_food) or die(mysqli_error($link));

	// check if the food is already associated with the chef
	if(mysqli_num_rows($food_query) == 0) {
		$q_food_insert = mysqli_query($link,"INSERT INTO " . FOOD_CHEF_DETAILS . " (food_id, chef_id) VALUES ($food_id, $chef_id);");

		// get the details of the newly added food
		$newly_added_association_query = mysqli_query($link,$q_food);
		$results = mysqli_fetch_assoc($newly_added_association_query);

		return $results;
	}
	else {
		return false;
	}
}

/* Function to retrieve all chefs that cook a certain type of food */
function get_chefs_by_food($food_type_id) {
	global $link;

	$q = "SELECT t1.chef_id, t1.about_chef, t1.contact_time_preference, t1.delivery_available, t1.payments_accepted, t1.pickup_available, t1.taking_offline_order, t4.first_name, t4.last_name, t4.user_id, t4.email, t4.phone, t4.profile_picture
	FROM " . CHEF . " AS t1
	LEFT JOIN " . FOOD_CHEF_DETAILS . " AS t2 ON t1.chef_id = t2.chef_id
	LEFT JOIN " . FOOD . " AS t3 ON t2.food_id = t3.food_id
	LEFT JOIN " . USERS . " AS t4 on t4.user_id = t1.user_id
	WHERE t3.food_id = $food_type_id;";


	// execute the query
	if($food_query = mysqli_query($link,$q)) {
		while ($row = mysqli_fetch_assoc($food_query)) {
			$results[] =$row;
		}
	}
	mysqli_free_result($food_query);

	return $results;
}

/* display chef details and food details on the card based on the logged in user's location*/
function get_localchef_details($user_id,$row_limit = NULL) {
	global $link;
        $results = array();
        if ($row_limit !=NULL)
        {
            $row_limit_set = $row_limit;
        } else { $row_limit_set = 10;}

	//get the logged in user's location
	$e_loc_id= get_loggedin_user_location($user_id);

	$get_city_state = mysqli_query($link, "SELECT city, state FROM " . LOCATION . " WHERE e_loc_id = $e_loc_id;") or die(mysqli_error($link));

	list($city, $state) = mysqli_fetch_row($get_city_state);

	//query to get the chef details based on the logged in user's location
	$q = "SELECT t1.chef_id FROM " . CHEF . " AS t1
		LEFT JOIN " . USERS . " AS t2 ON t2.user_id = t1.user_id
		LEFT JOIN " . LOCATION . " AS t3 ON t2.e_loc_id = t3.e_loc_id
		WHERE  (t3.city = '$city' OR t3.state = '$state')  LIMIT $row_limit_set;";

	// echo "<br>q is: " . $q . "<br>";

	if($chef_query = mysqli_query($link, $q)) {
		while($row = mysqli_fetch_assoc($chef_query)) {
			$results[] = $row;
		}
	} else
        {
            $results[] = NULL;
        }
	mysqli_free_result($chef_query);

	return $results;
}

// Function to get all foods prepared by a specified chef
function get_foods_by_chef($chef_id) {
	global $link;
	// SELECT t1.food_id, t1.food_name, t1.food_description, t1.availability, t1.food_picture, t1.community_id, t2.food_price
	// FROM food AS t1 LEFT JOIN food_chef_details AS t2 ON t1.food_id = t2.food_id
	// WHERE t2.chef_id = $chef_id

	$select = "SELECT t1.food_id, t1.food_name, t1.food_description, t1.availability, t1.food_picture, t1.community_id, t2.food_price";

	$from = " FROM " . FOOD . " AS t1 LEFT JOIN " . FOOD_CHEF_DETAILS . " AS t2 ON t1.food_id = t2.food_id";

	$where = " WHERE t2.chef_id = $chef_id;";

	$q = $select . $from . $where;


	// execute the query
	if($query = mysqli_query($link,$q)) {
		$results = false;
		while ($row = mysqli_fetch_assoc($query)) {
			$results[] =$row;
		}
	}

	mysqli_free_result($query);

	return $results;
}

// Function to get information for a specified food
function get_food_info($food_id){
	global $link;

	$select = "SELECT t1.food_id, t1.food_name, t1.food_description, t1.availability, t1.food_picture, t1.community_id, t2.food_price";

	$from = " FROM " . FOOD . " AS t1 LEFT JOIN " . FOOD_CHEF_DETAILS . " AS t2 ON t1.food_id = t2.food_id";

	$where = " WHERE t1.food_id = $food_id;";

	$q = $select . $from . $where;


	// execute the query
	if($query = mysqli_query($link,$q)) {
		$results = mysqli_fetch_assoc($query);
	}

	return $results;
}

/* ---------- end functions related to chefs ----------------*/


/* ---------- begin functions related to events ----------------*/

// Function to create an event
function add_event($event_name, $event_date, $event_desc, $event_scope, $e_type_id, $user_id, $venue_name,$venue_address,$event_zipcode, $community_id, $e_recurring_id) {
	global $link;
	// check if the zipcode already in the location table, if not insert and get the e_loc_id
	// insert the venue name, address, e_loc_id into venue table and get the last inserted venue_id
	//then insert the event details into event table.

	//get the e_loc_id
	$e_loc_id = insert_zipcode_location($event_zipcode);

        $q_venue_check = mysqli_query($link,"SELECT venue_id FROM " . VENUE . " WHERE venue_name LIKE '" . $venue_name . "' and venue_address LIKE '" . $venue_address . "' AND e_loc_id =" . $e_loc_id . " LIMIT 1;") or die(mysqli_error($link));

        if(mysqli_num_rows($q_venue_check) !=0)
        {
            $row = mysqli_fetch_assoc($q_venue_check);
            $venue_id = $row['venue_id'];
        }  else {

            //insert venue details into venue table
            $q_venue = mysqli_query($link,"INSERT INTO " . VENUE . " (venue_name,venue_address,e_loc_id) VALUES ('$venue_name','$venue_address',$e_loc_id)") or die(mysqli_error($link));
            $venue_id = mysqli_insert_id($link);
        }

	$q = "INSERT INTO " . EVENT . "(event_name, event_date, event_desc, event_scope, e_type_id,event_status, user_id, venue_id, community_id, e_recurring_id) VALUES ('$event_name', '$event_date', '$event_desc', '$event_scope', '$e_type_id','1', '$user_id', '$venue_id', '$community_id', '$e_recurring_id')";

	if (mysqli_query($link,$q)){

		return true;
	}
	else {

		return false;
	}

}

// Function to update events
function update_event($event_name, $event_date, $event_desc, $event_scope, $e_type_id, $venue_name, $venue_address, $venue_city, $venue_state, $venue_zipcode, $e_recurring_id, $event_id) {
	global $link;

	// check if the zipcode already in the location table, if not insert and get the e_loc_id
	// insert the venue name, address, e_loc_id into venue table and get the last inserted venue_id
	//then insert the event details into event table.

	// create new location or update existing location
	$q = "INSERT INTO " . LOCATION . "(city, state, zipcode) VALUES ('$venue_city', '$venue_state', '$venue_zipcode') ON DUPLICATE KEY UPDATE city='$venue_city', state='$venue_state'";
	// echo $q . "|||";

	mysqli_query($link,$q) or die(mysqli_error($link));

	// get the location id for the location updated above
	$e_loc_id = mysqli_insert_id($link);
	if ($e_loc_id == 0) {
		$e_loc_id = insert_zipcode_location($venue_zipcode);
	}

	//insert venue details into venue table
	$q_venue_check = mysqli_query($link,"SELECT venue_id from ".VENUE. " WHERE venue_name LIKE '".$venue_name. "' and venue_address LIKE '".$venue_address."' AND e_loc_id =".$e_loc_id. " LIMIT 1;") or die(mysqli_error($link));
	if(mysqli_num_rows($q_venue_check) !=0) {
		$row = mysqli_fetch_assoc($q_venue_check);
		$venue_id = $row['venue_id'];
	}
	else {
		$q_venue = mysqli_query($link,"INSERT INTO " .VENUE. " (venue_name,venue_address,e_loc_id) VALUES ('$venue_name','$venue_address',$e_loc_id)") or die(mysqli_error($link));
	 $venue_id = mysqli_insert_id($link);
		// echo $venue_id;
	}

	// query to update the event
	$q = "UPDATE " . EVENT . " SET event_name='$event_name', event_date='$event_date', event_desc='$event_desc', event_scope='$event_scope', e_type_id='$e_type_id', venue_id='$venue_id', e_recurring_id='$e_recurring_id' WHERE event_id = $event_id";

	mysqli_query($link,$q) or die(mysqli_error($link));

	// Query for updated event details, including location information and users
	$select = "SELECT t1.event_id, t1.venue_id, t1.event_name, t3.venue_name, t3.venue_address, t4.city, t4.state, t4.zipcode, t2.event_type, t1.event_date, t1.event_desc,t1.event_scope, t1.user_id";

	$from = " FROM " . EVENT . " AS t1
		LEFT JOIN " . USERS . " AS t5 ON t1.user_id = t5.user_id
		LEFT JOIN " . EVENT_TYPE . " AS t2 ON t1.e_type_id = t2.e_type_id
		LEFT JOIN " . VENUE . " AS t3 ON t1.venue_id = t3.venue_id
		LEFT JOIN " . LOCATION . " AS t4 ON t3.e_loc_id = t4.e_loc_id";

	// returns only the updated event id
	$where = " WHERE event_id = " . $event_id;

	// build the query
	$q = $select . $from . $where . ";";
	// echo $q . "|||";

	// execute the query
	if($event_query = mysqli_query($link,$q)) {
		while ($row = mysqli_fetch_assoc($event_query)) {
			$results[] =$row;
			// print_r($results);
		}
	}

	return $results;
}

// Function fetch the event types to display it in the user's create or edit event card
function get_event_types() {
	global $link;
	$q_e_type = mysqli_query($link,"SELECT * FROM " .EVENT_TYPE) or die(mysqli_error($link));

	$row = array();
	while($q_event = mysqli_fetch_array($q_e_type)) {
		$row[]=$q_event;
	}

	return $row;
}

// Function to delete events
function delete_event($event_id) {
	global $link;
	$q = "DELETE FROM " . EVENT . " WHERE event_id = $event_id";

	if (mysqli_query($link,$q)){
		return true;
	}
	else {
		return false;
	}
}

// Function to get the food picture to display it in the home page carousal
function fetch_food_event_picture($chef_id = NULL) {
	global $link;
	$results = array();

	$q_picture = "(SELECT food_picture from community_connect_food WHERE food_picture IS NOT NULL)
                    UNION (SELECT image_location FROM community_connect_event_picture WHERE image_location is not null) ORDER BY RAND() LIMIT 7;";
	if($picture_query = mysqli_query($link,$q_picture)) {
		while ($row = mysqli_fetch_assoc($picture_query)) {
			$results[] =$row;
		}
	}

	mysqli_free_result($picture_query);
	return $results;
}

// function to retrieve an event based on user's location.
function retrieve_future_event($user_id,$row_limit = NULL) {
	global $link;
	global $salt;

	$err = array();
	$results = NULL;

	if ($row_limit !=NULL) {
		$row_limit_set = $row_limit;
	}
	else {
		$row_limit_set = 50;
	}

	/*
	* step 1: Get the logged in user's location , city, zip code or state
	* step 2: based on the location id, get the venue details
	* step 3: fetch the events based on venue- location
	* select * from event where venue_id in (select venue_id from venue where fk_venue_location = 1);
	* if no events are found in his location, display all events.
	*/
	$e_loc_id= get_loggedin_user_location($user_id);

	$get_city_state = mysqli_query($link,"SELECT city,state FROM " . LOCATION . " WHERE e_loc_id= ".$e_loc_id. ";") or die(mysqli_error($link));
	list($city,$state) = mysqli_fetch_row($get_city_state);

	$q2 = "SELECT t1.event_date, t1.event_desc, t1.event_id, t1.event_name, t5.first_name, AES_DECRYPT(t5.email, '$salt') as email, t5.phone, t5.last_name, t3.venue_address, t3.venue_name, t4.city, t4.zipcode, t4.state
		FROM " . EVENT . " AS t1
		LEFT JOIN " . EVENT_TYPE . " AS t2 ON t1.e_type_id = t2.e_type_id
		LEFT JOIN " . VENUE . " AS t3 ON t1.venue_id = t3.venue_id
		LEFT JOIN " . LOCATION . " AS t4 ON t3.e_loc_id = t4.e_loc_id
		LEFT JOIN " . USERS . " AS t5 ON t1.user_id = t5.user_id
		WHERE event_status =1
		AND t1.event_date > CURDATE( )
		AND (t4.city = '".$city."' OR t4.state = '".$state. "') LIMIT $row_limit_set;";

	if($event_query = mysqli_query($link,$q2)) {
		if(mysqli_num_rows($event_query) > 0) {
			while ($row = mysqli_fetch_assoc($event_query)) {
				$results[] =$row;
			}
		}
		else {
			//if no events found at his exact location, extend the search to different location in his state
		}
	}

	mysqli_free_result($event_query);

	return $results;
}

/* Function to retrieve events information. Accepts arguments for visibility and user_id */
function get_events($user_id = NULL, $visibility = NULL) {
	global $link;
	$results = array();
	// to do: return picture
	// to do: return if the event is editable by the current user
	// set up query with all of the tables tied together
	$select = "SELECT t1.event_id, t1.event_name, t3.venue_name, t3.venue_address, t4.city, t4.state, t4.zipcode, t2.event_type, t1.event_date, t1.event_desc,t1.event_scope, t1.user_id";

	$from = " FROM " . EVENT . " AS t1
		LEFT JOIN " . EVENT_TYPE . " AS t2 ON t1.e_type_id = t2.e_type_id
		LEFT JOIN " . VENUE . " AS t3 ON t1.venue_id = t3.venue_id
		LEFT JOIN " . LOCATION . " AS t4 ON t3.e_loc_id = t4.e_loc_id
		LEFT JOIN " . USERS . " AS t5 ON t1.user_id = t5.user_id ";

	// will always return events that are active
	$where = " where event_status=1";

	// if visibility is specified, add it to the query
	if (!is_null($visibility)){
		$where .= " AND event_scope = '" . $visibility . "'";
	}

	// if user is provided, add it to the query
	if (!is_null($user_id)){
		$where .= " AND t5.user_id = " . $user_id;
	}

	// build the query
	$q = $select . $from . $where . ";";

	// execute the query
	if($event_query = mysqli_query($link,$q)) {
		while ($row = mysqli_fetch_assoc($event_query)) {
			$results[] =$row;
		}
        } else { $results =NULL; }

	mysqli_free_result($event_query);

	return $results;
}

// Function to update the picture on an event
function update_event_picture($image_location,$event_id) {
	global $link;

	// echo $image_location;
	//Check if the event_picture is inserted before or first time inserting.

	if($q_event = mysqli_query($link,"SELECT * FROM " . EVENT_PICTURE . " WHERE event_id = ".$event_id)) {
		// if picture is not inserted, insert one.
		if(mysqli_num_rows($q_event) == 0) {
			//  echo "inserting";
			$q = mysqli_query($link, "INSERT INTO " . EVENT_PICTURE . " (image_location,event_id) VALUES ('$image_location',$event_id)");
		}
		else {
			//  echo "updating";
			$q = "UPDATE " . EVENT_PICTURE . " SET image_location = '".$image_location."' WHERE event_id = ". $event_id;
		}

		if (mysqli_query($link,$q)){

			return true;
			// echo "User updated successfully";
		}
		else {

			return false;
			// echo "User update failed";
		}
	}
}

// Function to retrieve the picture on an event
function get_event_picture($event_id) {
	global $link;

	if($q_event = mysqli_query($link,"SELECT * FROM " . EVENT_PICTURE . " WHERE event_id = ".$event_id)) {

		$row = mysqli_fetch_assoc($q_event);
		$event_image = $row['image_location'];
		return $event_image;
	}
	else {
		return "/pictures/default.jpg";
	}
}

// Function to retrieve the list of attendees for a given event
function get_attendance_count_list($event_id) {
	global $link;

	if ($q_att = mysqli_query($link,"SELECT count(event_attendance_id) AS e_count FROM " . ATTENDANCE . " WHERE event_id = ".$event_id)) {
		// if picture is not inserted, insert one.
		if(mysqli_num_rows($q_att) == 0) {

			mysqli_free_result($q_att);

			return NULL;
		}
		else {
			 $row = mysqli_fetch_assoc($q_att);
			 $event_attendance_count = $row['e_count'];

			 mysqli_free_result($q_att);

			 return $event_attendance_count;
		}
	}
	else {

		return NULL;
	}
}

/* ---------- end functions related to events ----------------*/


/* ---------- begin functions related to saving information to user profiles ----------------*/

// function to save things to user profiles. Function can specify event, chef, or contact to save
function save_info($info_type, $user_id, $info_id) {
	global $link;

	$q = "INSERT INTO " . USER_SAVED_INFO . " (user_id, event_id, chef_id, contact_id) VALUES ('" . $user_id . "', ";

	// builds the query based on the info type supplied
	switch ($info_type) {
	case "event":
		$q .= "'" . $info_id . "', NULL, NULL)";
		break;
	case "chef":
		$q .= "NULL, '" . $info_id . "', NULL)";
		break;
	case "contact":
		$q .= "NULL, NULL, '" . $info_id . "')";
		break;
	}

	// echo $q;

	if (mysqli_query($link,$q)){
		return true;
	}
	else {
		return false;
	}
}

// functions to get the saved events of the specific user to display it in his dashboard
function get_saved_events($user_id) {
	global $link;
	global $salt;
	$results = array();
	$q = "SELECT t1.event_date, t1.event_desc, t1.event_id, t1.event_name, t5.first_name, AES_DECRYPT(t5.email, '$salt') as email, t5.phone, t5.last_name, t3.venue_address, t3.venue_name, t4.city, t4.zipcode, t4.state
				FROM " . EVENT . " AS t1
				right JOIN " . EVENT_TYPE . " AS t2 ON t1.e_type_id = t2.e_type_id
				right JOIN " . VENUE . " AS t3 ON t1.venue_id = t3.venue_id
				right JOIN " . LOCATION . " AS t4 ON t3.e_loc_id = t4.e_loc_id
				right JOIN " . USERS . " AS t5 ON t1.user_id = t5.user_id
				left JOIN " . USER_SAVED_INFO . " AS t6 on t1.event_id = t6.event_id
				WHERE t1.event_status = 1 AND t6.user_id = " . $user_id . ";";


	$q_saved_events = mysqli_query($link,$q) or die(mysqli_error($link));
	if(mysqli_num_rows($q_saved_events) !=0) {
		while($r = mysqli_fetch_array($q_saved_events)) {
			$results[]=$r;
		}
	}
	else {
		$results=NULL;
	}

	return $results;
}

// function to get the saved chef details of the specific user to display it in his dashboard
function get_saved_chef($user_id) {
	global $link;
	global $salt;
	$results = array();
	$q = "SELECT t1.chef_id, t1.about_chef, t1.contact_time_preference, t1.delivery_available,t1.payments_accepted,t1.pickup_available,t1.taking_offline_order, t2.first_name, AES_DECRYPT(t2.email, '$salt') as email, t2.phone, t2.last_name,t4.city,t4.state,t4.zipcode
		FROM " . CHEF . " AS t1
		RIGHT JOIN " . USERS . " AS t2 ON t1.user_id = t2.user_id
		RIGHT JOIN " . LOCATION . " AS t4 ON t2.e_loc_id = t4.e_loc_id
		LEFT JOIN " . USER_SAVED_INFO . " AS t5 ON t1.chef_id = t5.chef_id
		WHERE  t5.user_id =" . $user_id . ";";

	$q_saved_chef = mysqli_query($link,$q) or die(mysqli_error($link));
	if(mysqli_num_rows($q_saved_chef) !=0) {
		while($r = mysqli_fetch_array($q_saved_chef)) {
			$results[]=$r;
		}
	}
	else {
		$results=NULL;
	}

	return $results;
}

// function to delete the saved data from the user's dashboard
function delete_saved_data($delete_id,$delete_type,$user_id) {
	global $link;
	$q_delete = "DELETE FROM " . USER_SAVED_INFO . " WHERE ";

	if($delete_type == "event") {
		$q_delete .= "event_id=".$delete_id;
	}
	else {
		$q_delete .="chef_id=".$delete_id;
	}
	$q_delete .=" AND user_id =" .$user_id;


	if(mysqli_query($link,$q_delete)) {
		return true;
	}
	else {
		return false;
	}
}

/* ---------- end functions related to saving information to user profiles ----------------*/
?>