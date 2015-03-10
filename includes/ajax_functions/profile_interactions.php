<?php
require_once '../constants/sql_constants.php';
secure_page();

if($_POST and $_GET) {
	if ($_GET['cmd'] == 'update_user'){
		$user_id = $_POST['user_id'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$phone = $_POST['phone'];
		$email = $_POST['email'];
		$profile_picture = NULL;
		$public_info = $_POST['public_info'];
		
		// function to update an event
		if (update_user_info($user_id, $first_name, $last_name, $email, $phone, $profile_picture)) {
			$results = array(
				"success" => true,
				"message" => "User profile update was successful"
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "User profile update failed"
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}
	
	if($_GET['cmd'] == 'update_chef') {
		$about_chef = filter($_POST['about_chef']);
		$contact_time_preference = $_POST['contact_time_preference'];
		$payments_accepted = $_POST['payments_accepted'];
		$chef_id = $_POST['chef_id'];
		$pickup = $_POST['pickup'];
		$offline = $_POST['offline'];
		$delivery = $_POST['delivery'];
		$user_id = $_POST['user_id'];
		
		if($chef_id == NULL) {
			$chef_profile_edit = create_update_chef_profile($about_chef,$contact_time_preference,$payments_accepted,$pickup,$offline,$delivery,$user_id);
		}
		else {
			$chef_profile_edit = create_update_chef_profile($about_chef,$contact_time_preference,$payments_accepted,$pickup,$offline,$delivery,$user_id,$chef_id);
		}
		
		if($chef_profile_edit) {
			$results = array(
				"success" => true,
				"message" => "Chef profile update was successful"
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "Chef profile update failed"
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}
	
	
	// if the user is adding a picture, add it to the file system and reference in user table
	if ($_GET['cmd'] == 'add_picture' || $_GET['cmd'] == 'add_event_picture' || $_GET['cmd'] == 'add_food_picture'){
		$user_id = $user_id = $_SESSION['user_id'];
		if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			$file_handler = $_FILES["file"];
			$picture = store_image($file_handler);
			//$picture_loc = "/".$picture;
			$picture_loc = $picture;
			if($_GET['cmd'] == 'add_picture') {
				//call the update_user_info function defined in sql_constants.php
				$profile_update = update_user_info($user_id, NULL, NULL, NULL, NULL, $picture_loc);
                                if($profile_update) {
					$msg="Picture updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}                                

			}
			elseif ($_GET['cmd'] == 'add_event_picture') {
				$event_id = $_POST['event_id'];
				//defined in sql_constants.php
				$event_update = update_event_picture($picture_loc,$event_id);
                                if($event_update) {
					$msg="Food details updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}
			}
			elseif ($_GET['cmd'] == 'add_food_picture') {
				$food_id=$_POST['food_id'];
				$food_update = update_foods_of_chef(NULL,$food_id,NULL,NULL,$picture_loc);
				if($food_update) {
					$msg="Food details updated successfully";
				} 
				else {
					$msg="Could not update this time, Please try again";
				}
			}
		}
	}

	if($_GET['cmd'] == 'delete_food') {
		$food_id = $_POST['food_id'];
		$chef_id = $_POST['chef_id'];
				
		$q= "DELETE FROM " . FOOD_CHEF_DETAILS . " WHERE food_id =" . $food_id . " AND chef_id =" . $chef_id . ";";

		if($food_q = mysqli_query($link,$q)) {
			$results = array(
				"success" => true,
				"message" => "Food has been removed from food bucket"
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "Food bucket update failed"
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}

	if($_GET['cmd'] == 'add_selected_food') {
		$chef_id = $_POST['chef_id'];
		$food_id = $_POST['food_id'];
		
		$add_selected_food = add_selected_food($food_id,$chef_id);
		
		if($add_selected_food == false) {
			print_r($add_selected_food);
			$results = array(
				"success" => false,
				"message" => "Adding food failed"
			);
		}
		else {
			$results = array(
				"success" => true,
				"message" => "Food has been added",
				"added_food" => $add_selected_food
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}

	if($_GET['cmd'] == 'update_food') {
		$chef_id = $_POST['chef_id'];
		$food_description = filter($_POST['food_description']);

		$food_id = $_POST['food_id'];

		$food_update = update_foods_of_chef($chef_id,$food_id,$food_description,NULL);
		if($food_update) {
			$results = array(
				"success" => true,
				"message" => "Food updated",
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "Food update failed"
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}
	
	if($_GET['cmd'] == 'add_new_food') {
		$chef_id=$_POST['chef_id'];
		$food_name=filter($_POST['food_name']);
		$food_description=filter($_POST['food_description']);
		$file_handler = $_FILES["file"];
		
		$picture = store_image($file_handler);
		
		$new_food = add_new_food($chef_id,$food_name,$food_description,$picture);
		
		if(!$new_food) {
			$results = array(
				"success" => false,
				"message" => "Food addition failed"
			);
		}
		else {
			$results = array(
				"success" => true,
				"message" => "Food added to list",
				"new_food" => $new_food
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
		
	}
}
?>