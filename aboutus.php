<!DOCTYPE html>
 <!--About us page to explain about the developer and website -->
<html>
<head>

<meta charset="utf-8">
<title>about us</title>
<script src="includes/js/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles/style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/card_style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/footer_header_style.css"/>
<script src="includes/js/jquery_custom_flip.js"></script>

</head>

<body>

<?php 
include_once ('includes/header.inc.php'); 
session_start();
if($_SESSION) {
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
		<?php 
		if(isset($msg)) {
			echo '<div class="success" >'.$msg.'</div>';
		} 
		elseif (isset($err)) {
			echo '<div class="error">'.$err.'</div>';
		}
		?>
			<div class="card flipper" style="width: 40em;height: 42em;">
				<div class="back">
					<h2>What is Community Connect?</h2>
					<p>
						Community Connect is a website designed to bring together people of a similar community, culture or religion. Connecting with people of your similar community is essential to understanding your own traditions and cherishing your own culture. This makes local communities strong and healthy.
					</p>
					
					<h2>What are the community types supported now?</h2>
					<p>
						Initially, this website is focused on the Havyaka culture. Our intention is to add as many cultures and religions as possible. In a future version of this site, we intend to allow users to create their sites for cultures.
					</p>
					
					<h2>What is Havyaka Culture?</h2>
					<p>
						Havyakan's are a Hindu brahmin subsect, primarily from Indian states of Karnataka, Keral and Kashmir. These days, they are spread all over the world. It is hard to find the events or authentic foods that few people can prepare. This website is an effort to make it easy for them to relish and cherish their tradition. More information on Havyaka is <a href="http://www.havyak.com/" target="_blank">here</a>
					</p>
					
					<h2>Thank you</h2>
					<p>
						Thank you visiting <b>Community Connect.</b> We hope you enjoy your tradition!.
					</p>
					<button class="flip">Who are we?</button>
				</div>
				
				<div class="front">
					<h2>Who are we?</h2>
					<p>
						<b><i>Caleb Carroll</b></i>
					</p>
					<img src="pictures/calebc_profile.jpg" style="max-width: 200px;max-height: 150px;"/> 
					
					<p>
						<b><i>Nivedita Bhat</i></b>
					</p>
					<img src="pictures/nivi_profile.jpg" style="max-width: 200px;max-height: 150px;"/> <br><br>
					
					<p>
						We are HCI 573 students. We developed the 'Community Connect' site as a project for our course. We have mainly used PHP, MySQL, and javascript to build this site. <br>
						Thank you for visiting. Have fun!
					</p>
					<button class="flip">About this site</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('includes/footer.inc.php'); ?>
</body>
</html>