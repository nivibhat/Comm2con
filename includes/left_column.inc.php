<link rel="stylesheet" type="text/css" href="includes/styles/left_column_style.css"/>
<link rel="stylesheet" type="text/css" href="includes/styles/jquery-ui-1.10.4.custom.css"></link> 
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<?php 
require_once 'includes/constants/sql_constants.php';

// select food_name from " . FOOD . " LIMIT 6
//$q = "SELECT food_id, food_name FROM " . FOOD . " LIMIT 8";

//return all the food names for chef_id is associated with that to display in the left column 
$q = "SELECT DISTINCT t1.food_name, t1.food_id FROM " . FOOD . " t1 
	INNER JOIN " . FOOD_CHEF_DETAILS . " t2 ON 
	t1.food_id = t2.food_id WHERE t2.chef_id IS NOT null;";

if($food_query = mysqli_query($link, $q)) {
	while($row = mysqli_fetch_assoc($food_query)) {
		$foods[] = $row;
	}
}
?>

<!-- This column contains the food categories and search options which will be present on each page.  -->
<!-- Column 1 start -->
<div id = "left_column">
	<div class="category_heading">
		<center class='left_menu'>Food Categories</center>
		<div class="categories">
			<p>
			<ul>
				<?php
				foreach ($foods as $food) {
				?>
					<li>
						<a href="searchResults.php?food_id=<?php echo $food['food_id']; ?>"><?php echo $food['food_name']; ?></a>
					</li>
				<?php 
				} ?>
			</ul>
			</p>
		</div>
	</div>
</div>