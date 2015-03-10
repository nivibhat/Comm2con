<?php
require_once '../constants/sql_constants.php';
secure_page();

if($_GET){
	if($_GET['cmd']== 'attending') {	
            $user_id = $_SESSION['user_id'];
		$event_id = $_POST['event_id'];
		$attending = $_POST['attending'];
		
		//check if the logged in user is already attending the event, if not insert into the table
		if ($attending == 'true'){
			if($stmt = mysqli_prepare($link, "SELECT * FROM ".ATTENDANCE. " WHERE user_id = " . $user_id . " AND event_id= " . $event_id) or die(mysqli_error($link))) {
				//execute the query
				mysqli_stmt_execute($stmt);
				//store the result
				mysqli_stmt_store_result($stmt);

				if(mysqli_stmt_num_rows($stmt) == 0) {
					$q = mysqli_query($link, "INSERT INTO ". ATTENDANCE . " (event_id,user_id) VALUES(" . $event_id . "," . $user_id . ")") or die(mysqli_error($link));
					
					$results = array(
						"success" => true,
						"message" => "You are now marked as attending!"
					);
				}
				else {
					$results = array(
						"success" => false,
						"message" => "You are already attending this event."
					);
				}
				
				$json_response = json_encode($results);
				
				echo $json_response;
				
				mysqli_stmt_close($stmt);
				
			}
		}
		else{
			$q = "DELETE FROM " . ATTENDANCE . " WHERE event_id= " . $event_id . " AND user_id= " . $user_id;

			if(mysqli_query($link,$q)) {
				$results = array(
					"success" => true,
					"message" => "You are no longer marked as attending!"
				);
			}
			else {
				$results = array(
					"success" => false,
					"message" => "You are not attending this event."
				);
			}
			
			$json_response = json_encode($results);
				
			echo $json_response;
		
		}
	}

	if ($_GET['cmd'] == 'update_event'){
		$event_name = $_POST['event_name'];
		$event_date = $_POST['event_date'];
		$event_desc = $_POST['event_desc'];
		$event_scope = $_POST['event_scope'];
		$e_type_id = $_POST['event_type'];
		$venue_name = $_POST['venue_name'];
		$venue_city = $_POST['venue_city'];
		$venue_state = $_POST['venue_state'];
		$venue_address = $_POST['venue_address'];
		
		$event_zipcode = $_POST['event_zipcode'];
		$e_recurring_id = 1;
		$event_id = $_POST['event_id'];
		
		$updated_event_info = update_event($event_name, $event_date, $event_desc, $event_scope, $e_type_id, $venue_name, $venue_address, $venue_city, $venue_state, $event_zipcode, $e_recurring_id, $event_id);
		
		//convert to JSON format
		$json_response = json_encode($updated_event_info);
		//echo the response -- the client will pick it up 
		echo $json_response;
	}
	
	if ($_GET['cmd'] == 'delete_event'){
		$event_id = $_POST['event_id'];
                $user_id = $_SESSION['user_id'];
		if(delete_event($event_id)){
			$results = array(
				"success" => true,
				"message" => "Delete was successful"
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "Delete failed"
			);
		}
		
		// print_r($results);
		
		$json_response = json_encode($results);
		
		echo $json_response;
	}
	
		// to do: create form that calls this code
	if ($_GET['cmd'] == 'add_event'){
		$event_name = filter($_POST['event_name']);
		$event_date =filter($_POST['event_date']);
		$event_desc = filter($_POST['event_desc']);
		$event_scope = filter($_POST['event_scope']);
		$e_type_id = filter($_POST['event_type']);
		$venue_name = filter($_POST['venue_name']);
		$venue_address = filter($_POST['venue_address']);
		$event_zipcode = filter($_POST['event_zipcode']);
		$user_id = $_POST['user_id'];
		$e_recurring_id = 1;
		$community_id = 1;
		
		// function to add an event 
		if (add_event($event_name, $event_date, $event_desc, $event_scope, $e_type_id, $user_id, $venue_name,$venue_address,$event_zipcode, $community_id, $e_recurring_id)) {
			$results = array(
				"success" => true,
				"message" => "Add event was successful"
			);
		}
		else {
			$results = array(
				"success" => false,
				"message" => "Add event failed"
			);
		}
		
		$json_response = json_encode($results);
		
		echo $json_response;
		
	}
	
	if ($_GET['cmd'] == 'save_event'){
		$event_id = $_POST['event_id'];
                $user_id = $_SESSION['user_id'];
                
                if($stmt = mysqli_prepare($link, "SELECT * FROM ".USER_SAVED_INFO. " WHERE user_id = ".$_SESSION['user_id']." AND event_id= " .$event_id) or die(mysqli_error($link)))
                {
                    //execute the query
                     mysqli_stmt_execute($stmt);
                     //store the result
                     mysqli_stmt_store_result($stmt);
                }
                     if(mysqli_stmt_num_rows($stmt) == 0) {
                           
                        if(save_info("event", $user_id, $event_id)){
                            $results = array(
                                    "success" => true,
                                    "message" => "Save was successful"
                            );
                        }
                     }
		else {
			$results = array(
				"success" => false,
				"message" => "You have already saved this event."
			);
		}
		
		$json_response = json_encode($results);
		echo $json_response;
	}
}
?>