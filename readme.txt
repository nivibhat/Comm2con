
					Community Connect

Team:
	Nivedita Bhat
	Caleb Carroll


		Read the instructions before starting installing!.
		
		
		Steps to install and test the Community Connect website:

1.	Start your wamp server 
2.	Open your web browser (Mozilla Firefox or Google Chrome are recommended)
3.	Unzip the havyaka_culture folder into your www folder for WAMP


		To Install the database tables and sample data:
4.	Type in the URL : localhost/havyaka_culture/install.php
5.	Click on the Install button to install all the required tables into HCI573 database.
6.	Wait for few seconds so all the tables gets created into database.
7.	Then, click on the button dummy_data to install the sample data into tables.
8.	Once all the data gets populated, then you are all set to test the website.
9.	If you find that the sample data did not get loaded, click on the Install button again, wait for a minute or so, and then click the dummy_data button. Depending on the speed of your computer, it may take a few seconds to install the tables into the database.


		To start testing the website:

1.		To register a new user:
1.1.	Enter your first name, last name, valid email address, zipcode (recommend 52402 for testing purposes), username, and password.
1.2.	Get the activation code from your email and activate your account.
1.3.	Login to the site using the username and password you provided.

2.		Login with the existing account:
2.1.	For testing purposes, you can log in as:  savita / password

3.		After Logging in, you can do the following things:
3.1.	Check for local chefs and local events.
3.2.	Search for chefs who provide foods.
3.3.	Save chefs or event details to your dashboard.
3.4.	Indicate you will attend an event.
3.5.	Create a new chef profile.
3.6.	Add foods to your food bucket that you are willing to prepare.
3.7.	Create or manage your events.
3.8.	View or delete saved events or chef.
3.9.	Visit FAQ, Contact Us, or About Us pages.


		Code used for this site:
		
1.	MySQL Workbench was used to generate the database schema and install queries.
2.	Card flipping functionality was modified from examples found here:
		http://forum.jquery.com/topic/jquery-flippy-plugin-reverse-issue
		http://home.jejaju.com/play/flipCards/simple
	The code for card flipping can be found in:
		includes/js/jquery_custom_flip.js
		includes/styles/card_style.css
3.	Google maps API was used on several pages to look up user's location and to check zipcodes.