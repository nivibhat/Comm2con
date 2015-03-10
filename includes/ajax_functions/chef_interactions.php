<?php
require_once '../constants/sql_constants.php';
if(isset($_GET['cmd'])){
	if ($_GET['cmd'] == 'save_chef'){
		$chef_id = $_POST['chef_id'];
		$user_id = $_POST['user_id'];
     //Check if the user is saved the chef details, if not insert it to user_saved_info table.
              if($stmt = mysqli_prepare($link, "SELECT * FROM ".USER_SAVED_INFO. " WHERE user_id = ".$user_id." AND chef_id= " .$chef_id)) {
                        //execute the query
                        mysqli_stmt_execute($stmt);
                        //store the result
                        mysqli_stmt_store_result($stmt);

		  if(mysqli_stmt_num_rows($stmt) == 0) {
                        if(save_info("chef", $user_id, $chef_id)){
                                $results = array(
                                        "success" => true,
                                        "message" => "Save was successful"
                                );
                        }
                    } else {
                                $results = array(
                                        "success" => false,
                                        "message" => "This chef details are already saved to your profile."
                                );
                        }
		
		$json_response = json_encode($results);
		echo $json_response;
	}
    }
}
?>