<?php
require '../constants/sql_constants.php';


if(isset($_POST) and isset($_GET)) {
	if (!empty($_GET['cmd'])) {
		if($_GET['cmd']== 'updatecitystate') {
				//check if that city, zipcode already exists, if not insert the city, zipcode,state into location table.
				// query to check if the city, zip code,  and state exists in the table.
				 // select city, zipcode,
				$city= trim(mysql_real_escape_string($_POST['city']));
				
				$state = trim(mysql_real_escape_string($_POST['state']));
				$zipcode = trim(mysql_real_escape_string($_POST['zipcode']));
				// echo "city state zip " .$city.$state.$zipcode;
				
				$q = "UPDATE ".LOCATION." SET city = '$city', state = '$state' WHERE zipcode = '$zipcode'";
			  
				$query=mysqli_query($link,$q)or die(mysqli_error($link));
		}
	}
}
?>