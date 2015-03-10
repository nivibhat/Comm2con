<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">

<head>
<meta charset="utf-8">
<title>FAQs</title>

<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/footer_header_style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/jquery-ui-1.10.4.custom.css"></link> 
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<link rel="stylesheet" href="includes/styles/faq_style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/left_column_style.css"/>

<script>
//function to execute the accordion style
$(function() {
	$( "#accordion" ).accordion({
		collapsible: true
	});
});
</script>

</head>

<body>
<?php
//if the session is not started then display only header and not the navigation which should be available only for the logged in users
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
			if($_SESSION) {
				include('includes/left_column.inc.php'); 
			}
			?>
			<!-- Left Column end -->
		</div>
		
		<div class="col2">
			<h2>Frequently Asked Questions</h2>
			<div id = "accordion">
				<h3>What is Community Connect?</h3>
				<div class = "acc">
					<p class = "para">
						Connect with your community, culture, religion, people, and cherish your tradition.
					</p>
				</div>
				
				<h3>How can I benefit from this site?</h3>
				<div>
					<p class = "para">
						You can benefit in at least 4 ways:
						<ol>
							<li>You can connect with your local community and people.</li>
							<li>You can create and attend public or private events.</li>
							<li>You can find a local chef who prepares authentic Havyaka foods.</li>
							<li>You can become a chef and market yourself.</li>
						</ol>
					</p>
				</div>
				
				<h3>What kind of events are here?</h3>
				<div>
					<p class = "para">
						On this site, you can find events related to your community, culture, and religion.
					</p>
				</div>
				
				<h3>Who are the chefs on this site?</h3>
				<div>
					<p class = "para">
						These chefs are just like you. You can contact them to order foods for pickup or delivery.
					</p>
				</div>
				
				<h3>Can I post events or become a chef?</h3>
				<div>
					<p class = "para">
						Absolutely! Use your My Dashboard to create new events or become a chef.
					</p>
				</div>
				
				<h3>Do you have any more questions?</h3>
				<div>
					<p class = "para">
						<a href="ContactForm.php">Contact us</a> and let us know your comments and questions. <br>
						We will be in touch with you.
					</p>
				</div>
				
			</div>
			<!-- END OF FAQ ACCORDION -->
		</div>
	</div>
</div>
<?php include('includes/footer.inc.php'); ?>
</body>
</html>