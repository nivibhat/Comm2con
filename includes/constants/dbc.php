<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

// information about the SQL database -- make sure the database on your end matches the database name, the user and the password
define('DB_HOST', "localhost");
define('DB_USER', "hci573");
define('DB_PASS', "hci573");
define('DB_NAME', "hci573");

//include_once '/includes/constants/dbc.php';
//base in operating system
define ("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/havyaka_culture");

//base URL of site
define ("BASE", "http://".$_SERVER['HTTP_HOST']."/havyaka_culture");

//picture storage location
define ("PICTURE_LOCATION", BASE . "/pictures/");

//tables
define ("PSTORE","community_connect_pstore");
define ("CHEF", "community_connect_chef");
define ("COMMUNITY_TYPE", "community_connect_community");
define ("EVENT", "community_connect_event");
define ("EVENT_PICTURE", "community_connect_event_picture");
define ("EVENT_TYPE", "community_connect_event_type");
define ("FOOD", "community_connect_food");
define ("FOOD_CHEF_DETAILS", "community_connect_food_chef_details");
define ("LOCATION", "community_connect_location");
define ("USERS", "community_connect_user");
define ("USER_SAVED_INFO", "community_connect_user_saved_info");
define ("VENUE", "community_connect_venue");
define ("ATTENDANCE","community_connect_event_attendance");
define ("EVENT_RECURRENCE","community_connect_event_recurrence");

// the email address used for sending activation emails
define ("GLOBAL_EMAIL", "connect.community.culture@gmail.com");

// whether we require users to activate their accounts or not
define("REQUIRE_ACTIVIATION","0");

//our keys -- ideally, those would be stored on a separate machine or server
$salt = "ae4bca65f3283fe26a6d3b10b85c3a308";
global $salt;

$passsalt = "f576c07dbe00e8f07d463bc14dede9e492";
global $passsalt;

$password_store_key = sha1("dsf4dgfd5s2");
global $password_store_key;

//  password :,connectcommunity1;
?>