<?php
require_once 'includes/constants/sql_constants.php';

/* Function to print a chef card - accepts an array of chef information to print */
function print_chef_card($chef_info_array) {
	$chef_id = $chef_info_array['chef_id'];
	$first_name = $chef_info_array['first_name'];
	$last_name = $chef_info_array['last_name'];
	$about_chef = $chef_info_array['about_chef'];
	$email = $chef_info_array['email'];
	$phone = $chef_info_array['phone'];
	$contact_time_preference = $chef_info_array['contact_time_preference'];
	$delivery_available = $chef_info_array['delivery_available'];
	$pickup_available = $chef_info_array['pickup_available'];
	$payments_accepted = $chef_info_array['payments_accepted'];
	$taking_offline_order = $chef_info_array['taking_offline_order'];
	// $zipcode = $chef_info_array['zipcode'];

	//get the chef's profile picture
	$profile_picture = $chef_info_array['profile_picture'];
	$media_loc_profile = htmlspecialchars($profile_picture);
	$media_loc_profile = PICTURE_LOCATION . $media_loc_profile;

?>
<div class ="card flipper chef_card">
	<div class="front">
		<input type="hidden" value=<?php echo $chef_id; ?> ></input>
		<p style="display:block; position:absolute; top: 1em; left: 1em; font-size:1.5em; font-weight:bold">Foods by <?php echo $first_name . " " .  $last_name; ?></p>
		<p style="display:inline-block; position:absolute; top: 1em; right: 5px;">Phone: <?php echo $phone; ?></p>
		<p style="display:inline-block; position:absolute; top: 2em; right: 5px;">Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
		<p style="display:inline-block; position:absolute; top: 3em; right: 5px;">Contact hour: <?php echo $contact_time_preference; ?></p>
		<br><br><br>

		<table class="foods_table">
		<tbody class="foods_table">
				<?php
				// print foods for the selected chef
				$foods_array = get_foods_by_chef($chef_id);

				// uncomment below to debug
				/*	echo "<br> get_foods_by_chef array is: <br>";
				print_r($foods_array);
				echo "<br>"; */

				if ($foods_array){
					foreach ($foods_array as $row_food) {

						$food_id = $row_food['food_id'];

						$food_picture = $row_food['food_picture'];
						$media_loc = htmlspecialchars($food_picture);
						$media_loc = PICTURE_LOCATION . $media_loc;
						?>

						<tr class="foods_table">
							<td class="foods_table">
							<?php echo $row_food['food_name'] . " - " . $row_food['food_price']; ?><br><br>
							<?php echo $row_food['food_description']; ?><br><br><br><br>
							</td>

							<td><img class="gridimg2" src="<?php echo $media_loc;?>" style="height:8em; float:right;" /></td>

						</tr>

				<?php
					}
				}
				else{
				?>
					<tr class="foods_table">
						<td class="foods_table">This chef has not specified any foods</td>
					</tr>
				<?php
				}
				?>
		</tbody>
		</table>

		<button class="flip" style="position:absolute;bottom:0;right:0;">Chef Info</button>
	</div>

	<div class="back">
		<div class="tl">

			<p class="card_title"><?php echo $first_name . " " . $last_name; ?></p>

			<p class="contact_info">
			Phone: <?php echo $phone; ?><br>
			Email: <a href="<?php echo $email; ?>"><?php echo $email; ?></a><br>
			Contact hours: <?php echo $contact_time_preference; ?>
			</p>
		</div>

		<div class="tr">
			<img class="chef_profile" src="<?php echo $media_loc_profile;?>" />
		</div>

		<div class="bl">
			<h3>About chef:</h3>
			<p><?php echo $about_chef; ?></p>
			<h3>Favorite Dish:</h3>
			<img class="gridimg2" src="http://localhost/havyaka_culture/pictures/1.jpg" />
		</div>

		<div class="br">
			<button class="save_chef" rel="<?php echo $chef_id; ?>" type="submit">Save Chef</button>
			<p style="font-weight:bold;">
			<?php
				if ($delivery_available == "yes")
					echo "Delivery available";
				else echo "Delivery not available";
				echo "<br><br>";

				if ($pickup_available == "yes")
					echo "Pickup available";
				else echo "Pickup not available";
				echo "<br><br>";

				echo "Payments accepted:" . $payments_accepted;
				echo "<br><br>";

				if ($taking_offline_order == "yes")
					echo "Offline orders available";
				else echo "Offline orders not available";
			?>
			</p>

		</div>
	<button class="flip" style="position:absolute;bottom:0;right:0;">See Foods</button>
	</div>

</div>
<?php //end of chef card function
}

/* Function to print event cards - accepts an array of event information */
function print_event_card ($r) {
	global $link;

	$event_id = $r['event_id'];
	$zipcode = $r['zipcode'];
	$event_name = $r['event_name'];
	$event_desc = $r['event_desc'];
	$event_date = $r['event_date'];
	$venue_name = $r['venue_name'];
	$venue_address = $r['venue_address'];
	$city = $r['city'];
	$state = $r['state'];
	$first_name = $r['first_name'];
	$last_name = $r['last_name'];
	$email = $r['email'];
	$phone = $r['phone'];

	$attending_radio = "attending_radio_".$event_id;
	$map_canvas = "map_canvas_".$event_id;
	$save_event = "saveevent_".$event_id;

	$q3 = "SELECT image_location FROM " . EVENT_PICTURE . " WHERE event_id = ".$event_id. " LIMIT 1";
	$query = mysqli_query($link,$q3) or (die(mysqli_error($link)));
	$row_image = mysqli_fetch_row($query);

	// get image for event, if event image isn't specified, use a default image
	$image = $row_image[0];
	if (empty($image)){
		$media_loc = PICTURE_LOCATION . "default_event.jpg";
	}
	else {
		$media_loc = htmlspecialchars($image);
	}
	$media_loc = PICTURE_LOCATION . $media_loc;

	//back of the card: I am attending option, list users attending add to calender, google map
	$q = "SELECT u.username, u.first_name, u.last_name FROM " . USERS . " AS u INNER JOIN " . ATTENDANCE . " AS et ON u.user_id = et.user_id AND et.event_id = ".$event_id;

	$query1 = mysqli_query($link,$q) or (die(mysqli_error($link)));
	$user_list = array();
	While($row = mysqli_fetch_assoc($query1)) {
		$user_list[]=$row;
	}

?>
	<div class ="card flipper event_card">
		<div class="back">
			<input type="hidden" class='event_id' rel="<?php echo $event_id; ?>" name ='event_id'></input>
			<input type="hidden" class="zipcode" rel="<?php echo $zipcode; ?>"  name="zipcode"></input>

			<div class="event_tl">
				<p class="event_name card_title"><?php echo $event_name; ?></p>
				<p class="event_date">on: <?php echo $event_date; ?></p>

				<p class="venue_location"><?php
				echo $venue_name . "<br>";
				echo $venue_address . "<br>";
				echo $city . ", " . $state . " " . $zipcode; ?>
				</p>
				<p class="event_description"><?php echo $event_desc; ?></p>

			</div>
			<div class="event_bl">
				<p class="contact_info">For more information, contact:<br>
				<?php echo $first_name . " " . $last_name . "<br>" . $email . "<br>" . $phone; ?></p>
			</div>

			<div class="event_right">
				<p class="image_holder"><img class="event_image" src="<?php echo $media_loc;?>" /></p>
				<br>
				<input type="checkbox"  class="attending_radio" rel="<?php echo $r['event_id']; ?>" id= "<?php echo $attending_radio;?>" name="attending" value="attending" ><label id="attending_label">I am attending!</label></input>
				<button class="save_event" rel="<?php echo $event_id; ?>" id="<?php echo $save_event;?>" type="button" name="save_event">Save</button>
			</div>
			<button type="button" class="flip" style="position:absolute;bottom:1%;right:1%;">More Details</button>
		</div>

		<div class="front">
			<p class="event_name"><?php echo $event_name; ?></p>
			<p class="event_date">on: <?php echo $event_date; ?></p>


			<p><?php
			// adapt text to the number of attendees for the event
			$number_attendees = count($user_list);
			echo $number_attendees;
			switch ($number_attendees) {
			case 0:
				echo " attendees so far.";
				break;
			case 1:
				echo " friend attending:";
				break;
			default:
				echo " friends attending:";
				break;
			}
			echo "<br>";
			?>
			<span style="border:none"><?php
			// prints out the list of users attending
			if(!empty($user_list)) {
				foreach($user_list as $user) {
					echo $user['first_name'] . " " . $user['last_name'] . "<br>";
				}
			}
			else {
				echo "You can be the first!";
			} ?>
			</span>
			</p>
			<!-- div that holds the google map for the card -->
			<!-- <div id="<?php echo $map_canvas;?>" rel="<?php echo $event_id; ?>" class = "map_canvas">
			</div> -->
			<div class="map_canvas"  style="background:lightgrey; position:absolute; top:7em; left:50%; right:1em; padding:.5em; margin:.25em;">
				Placeholder for Google Map

			</div>

			<button class="flip" type="button" style="position:absolute;bottom:0;right:0;">Event Info</button>
		</div>
	</div>
	<?php

} // end of event card function


// function to print the editable event cards for a certain user
function print_user_manage_events_card($user_id) {

	$results = get_events($user_id);
	$event_types = get_event_types();
	if($results) {
		foreach ($results as $r) {
			//get event_picture
			$event_id = $r['event_id'];
			$event_name = $r['event_name'];
			$venue_name = $r['venue_name'];
			$venue_address = $r['venue_address'];
			$zip_code = $r['zipcode'];
			$event_date = $r['event_date'];
			$event_desc = $r['event_desc'];
			$city = $r['city'];
			$state = $r['state'];
			$zipcode = $r['zipcode'];
			$event_type = $r['event_type'];
			$event_scope = $r['event_scope'];

			$event_image = get_event_picture($event_id);
			$event_image_loc = htmlspecialchars($event_image);
			$event_image_loc = PICTURE_LOCATION . $event_image_loc;

			// foreach of the event, get the number of attendance

			$event_attendance = get_attendance_count_list($event_id);

			if($event_attendance !=NULL) {
				$count=$event_attendance;
			}
			else {
				$count = "No attandance!";
			}
			?>

			<div class="card flipper manage_event" id="event_<?php echo $event_id; ?>">
		<!-- Event editing section below -->
				<div class="front">
					<form action="<?php echo basename($_SERVER['PHP_SELF']);?>?cmd=update_event" method="post" class='update_event_form'>
					<div class="event_edit_left">
						<input style="display:none" type="text" name="event_id" value="<?php echo $event_id ?>">

						<label for="event_name">Event Name</label>
						<input type="text" name="event_name" class="get_event_name" value="<?php echo $event_name; ?>">

						<label for="event_date">Event Date</label>
						<input type="text" class="datepicker" class="get_event_date" name="event_date" value="<?php echo $event_date ?>">

						<label for="venue_name">Venue Name</label>
						<input type="text" name="venue_name" class="get_venue_name" value="<?php echo $venue_name ?>" >

						<label for="venue_address">Venue Street Address</label>
						<input type="text" name="venue_address" class="get_venue_address" value="<?php echo $venue_address ?>">

						<label for="venue_city">Venue City</label>
						<input type="text" name="venue_city" class="get_venue_city" value="<?php echo $city ?>">

						<label for="venue_address">Venue State</label>
						<input type="text" name="venue_state" class="get_venue_state" value="<?php echo $state ?>">

						<label for="event_zipcode">Venue Zipcode</label>
						<input type="text" name="event_zipcode" class="get_event_zipcode" value="<?php echo $zipcode ?>" >
					</div>
					
					<div class="event_edit_right">   
						<p class="image_holder"><img class="card_image" style="max-height:20%" src="<?php echo $event_image_loc; ?>" /></p>
						<label for="event_type">Event Type</label>
						<select name="event_type" class="get_event_type">
						<?php
								foreach($event_types as $row) {
								echo $row['event_type'];
								?>
								<option value="<?php echo $row['e_type_id']; ?>" ><?php echo $row['event_type']; ?></option>
						<?php } ?>
						</select>

						<label for="event_scope">Event Scope</label>
						<select name="event_scope" id="get_event_scope">
								<option value="public">Public</option>
								<option value="private">Private</option>
						</select>
						<label for="event_desc">Event Description</label>
						<textarea name="event_desc" class="get_event_desc" cols=20 rows=3><?php echo $event_desc ?></textarea>
					</div>
					<div class="event_edit_bottom">
						<button type="button" class="delete_event_button">Delete Event</button>
						<button type="button" class="update_event_button">Save Changes</button>
						<button type="button" class="flip">Cancel</button>
					</div>
					</form>
					<div class="event_edit_right_picture" style="position: absolute; top: 5.7em; left: 76%; right: 0em; padding:.1em;margin:.1em;">
						<form action="<?php echo basename($_SERVER['PHP_SELF']);?>?cmd=add_event_picture" method="post" enctype="multipart/form-data">
								<input style="display:none" type="text" name="event_id" value="<?php echo $event_id ?>">
								<label for="file">Change picture:</label>
								<input type="file" name="file" id="file_event" style="max-width:100%">
								<input type="submit" name="submit" value="Update Image">
						</form>
					</div>
				</div>
		<!-- END Event editing section -->

		<!-- Event information display section below -->
				<div class="back">
					<div class="event_tl">
						<p class="card_name"><?php echo $event_name; ?></p>
						<p class="event_date">on: <?php echo $event_date; ?></p>

						<p class="venue_location"><?php
						echo $venue_name . "<br>";
						echo $venue_address . "<br>";
						echo $city . ", " . $state . " " . $zipcode; ?>
						</p>

						<p class="event_desc"><?php echo $event_desc; ?></p>
					</div>
					<div class="event_right">
						<p class="image_holder"><img class="card_image" src="<?php echo $event_image_loc; ?>" /></p>
						<p class="attendance_count">Attendance count:<br> <?php echo $count; ?></p>
						<p class="event_type">Event Type: <?php echo $event_type; ?></p>
						<p class="event_scope">Event Scope: <?php echo $event_scope; ?></p>
					</div>
					<div class="event_edit_bottom">
						<button type="button" class="flip">Edit Event</button>
					</div>
				</div>
		<!-- END Event information display section -->
			</div>
		<?php
		}
	}
	else {
		echo "<h2> No events found!. Add one now!</h2>";
	}
}
?>