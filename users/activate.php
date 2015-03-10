<?php
require_once '../includes/constants/sql_constants.php';
include_once '../includes/constants/dbc.php';

return_meta("Activate your account");

$err = array();
$user = NULL;
$activ = NULL;
$user_email = NULL;

/******** EMAIL ACTIVATION LINK**********************/
if(isset($_GET['user']) && !empty($_GET['activ_code']) && !empty($_GET['user']) && is_numeric($_GET['activ_code']) )
{

	$user = filter($_GET['user']);
	$activ = filter($_GET['activ_code']);

	//check if activ code and user is valid
	$rs_check = mysqli_query($link,"SELECT user_id FROM ".USERS." WHERE md5_id='$user' AND activation_code='$activ'") or die (mysqli_error($link));
	$num = mysqli_num_rows($rs_check);
	// Match row found with more than 1 results  - the user is authenticated.
	if ( $num <= 0 )
	{
		$err[] = "Unable to verify account";
	}

	if(empty($err))
	{
		// set the approved field to 1 to activate the account
		$rs_activ = mysqli_query($link,"UPDATE ".USERS." SET approved='1' WHERE
		md5_id='$user' AND activation_code = '$activ' ") or die(mysqli_error($link));
                
                $redirect = "/../login.php";
                header('Location: ' . $redirect);
                   exit();
                
		//$msg = "Account activated successfully!  You may now <a href=\"".BASE."/login.php\">login</a>.";
	}
}

/******************* ACTIVATION BY FORM**************************/
if (isset($_POST['activate']))
{

	$user_email = filter($_POST['user_email']);
	$activ = filter($_POST['activ_code']);
	//check if activ code and user is valid as precaution
	$rs_check = mysqli_query($link,"SELECT user_id FROM ".USERS." WHERE email = AES_ENCRYPT('$user_email', '$salt') AND activation_code='$activ'") or die (mysqli_error($link));
	$num = mysqli_num_rows($rs_check);
	// Match row found with more than 1 results  - the user is authenticated.
	if ( $num <= 0 )
	{
		$err[] = "Unable to verify account"; 
                $msg = "Did you create with the wrong email address? !  Try registring using new email <a href=\"".BASE."/index.php\">Register new account</a>.";
	} else {
	//set approved field to 1 to activate the user
            if(empty($err))
            {
                    $rs_activ = mysqli_query($link,"UPDATE ".USERS." SET approved='1' WHERE
                    email= AES_ENCRYPT('$user_email', '$salt') AND activation_code = '$activ' ") or die(mysqli_error($link));

                      
                    $msg = "<h3>Account activated successfully!  You may now <a href=\"".BASE."/index.php\">login</a>.</h3>";
            }
        }
}
?>

</script>
</head>

<head>
	<title>Activate your account</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="../includes/styles/profile_styles.css"/>
        <link rel="stylesheet" type="text/css" href="../includes/styles/style.css"/>
	<link rel="stylesheet" type="text/css" href="../includes/styles/card_style.css"/>
        
	<link rel="stylesheet" type="text/css" href="/../includes/styles/footer_header_style.css" media="screen" />
</head>
<body>
	
	  <?php
          include_once ('/../includes/header.inc.php'); ?>	
	
<div class="content leftmenu">
    <div class="colright">
	<div class="col1">
            <!-- Left Column start -->
            <?php// include('../includes/left_column.inc.php'); ?>
            <!-- Left Column end -->
		</div>
        <div class="col2">
            <?php 
            if(isset($msg))
                {
                        echo '<div class="success" >'.$msg.'</div>';
                } elseif (isset($err))
                {
                    if(!empty($err))
                     {
                        echo '<div class="err">';
                        foreach($err as $e)
                        {
                        echo $e.'<br />';
                        }
                        echo '</div>';
                    }
                }
            ?>
                <div class="card " id="user_profile_div">
                    
                      <span class="success" style="display:none;"></span>
                      <span class="error" style="display:none;">Please enter some text</span>
                      
                      <div class="front">                         
                          <h2>Activate your account here:</h2>
                         <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="activ_form">
                            <table cellpadding="5" cellspacing="5" border="0">
                                <tr>
                                <td>Email:</td>
                                <td><input type="text" name="user_email" value="<?php echo stripslashes($user_email); ?>" class="required" /></td>
                                </tr>
                                <td>Activation Code:</td>
                                <td><input type="text" name="activ_code" value="<?php echo stripslashes($activ); ?>" class="required" /></td>
                                </tr>
                                <tr>
                                <td colspan="2" align="center"><input type="submit" name="activate" value="Activate Account" /></td>
                                </tr>
                            </table>
                     </form>
                  </div>
            </div>
</body>
</html>