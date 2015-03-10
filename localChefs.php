<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
<head>
	<title>Community Resource</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="LocalChefs" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="includes/styles/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/chef_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="includes/styles/footer_header_style.css" media="screen" />
	
	<script src="includes/js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
	<script type=text/javascript src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>
	<script src="includes/js/jquery_custom_flip.js"></script>
	<meta charset="utf-8">

<?php
	require_once 'includes/constants/sql_constants.php';
	require_once 'includes/constants/card_print.php';
	secure_page();  
	return_meta("Local Chefs!");
	$msg = NULL;
	$user_id =  $_SESSION['user_id'];
?>
<input style="display:none" type="text" id="user_id" value="<?php echo $user_id ?>">
<script>
 
$(function() {
	
	$(".save_chef").click(function() {
		var chef_id = $(this).attr('rel');
		var user_id = $('#user_id').val();
		var datastring = "chef_id=" + chef_id + "&user_id=" + user_id;
		console.log(datastring);
		
		$.ajax({
			type: "POST",
			url: "<?php echo BASE; ?>/includes/ajax_functions/chef_interactions.php?cmd=save_chef", 
			data: datastring,
			success: function(response) {
				console.log(response);
				var results = JSON.parse(response);
				
				var status = results['success'];
				var message = results['message'];
				if(status === 'true') {
				$('.success').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
				$('.error').fadeOut(2000).hide(); //If showing error, fade out
				} 
				else {
					$('.error').fadeIn(2000).show().html(message).fadeOut(6000); //Show, then hide success msg
					$('.success').fadeOut(2000).hide();
				}
			}
		});
		
		return false;
	});
});

</script>
</head>

<body>
<?php
include_once ('includes/header.inc.php');
include('includes/navigation.inc.php'); 

?>

<div class="content leftmenu">
	<div class="colright">
		<div class="col1">
			<!-- Left Column start -->
			<?php include('includes/left_column.inc.php'); ?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
			<span class="success" style="display:none;"></span>
			<span class="error" style="display:none;">Please enter some text</span>
			<!-- Middle Column start -->
			<style>img {width: 160px;}</style> 
			<div id ="chef_holder">
				<h1>Local chefs in your area</h1>
				
				<?php
				// This section gets all chefs in the user's area, then prints them into a card
				// functions below are defined in sql_constants
				$chefs_list = get_localchef_details($user_id);
				
				// prints a card for each chef associated with a food type
				foreach ($chefs_list as $chef) {
					// gets the chef info and loads it into an array
					$chef_info_array = get_chef_info($chef['chef_id']);
					
					// uses the chef info array to print cards
					print_chef_card($chef_info_array);
				}
				?>
			</div>
		</div>   <!-- end of col2-->
	</div>
</div>

<?php include('includes/footer.inc.php'); ?>

</body>
</html>

