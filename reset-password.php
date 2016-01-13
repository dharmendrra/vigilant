<?php
session_start();
require_once('connect.php');

//CurrentPassword
//echo '<pre>';print_r($_REQUEST);die;
//if updating password from panel after login
if(!isset($_SESSION['USER_INFO']) && empty($_POST['submit'])){
    //check if request for signup
    if(isset($_REQUEST['token']) && isset($_REQUEST['email'])){
        $findQry="SELECT `Log-inID` FROM `token`,`tblprogramdirectors` WHERE NOW() <=  DATE_ADD(Token_Gen_Time, INTERVAL 2 DAY) AND `tblprogramdirectors`.EmailAddress=`token`.EmailAddress AND `Token`='".base64_decode($_REQUEST['token'])."' AND `token`.`EmailAddress`='".$_REQUEST['email']."' AND IsUsed != 1";
        $result_data=$mysqli->query($findQry);//run created query to feed data into mysql database
        if($result_data->num_rows<=0){
            echo "<script>alert('Your link is Invalid or broken or may be it has been expired already!!');window.location.href='index.php';</script>";
            die;
        }
        $data=$result_data->fetch_assoc();
        //Program Director
        
        //set token as used
        $upQry="UPDATE `token` SET IsUsed = 1 WHERE `Token`='".base64_decode($_REQUEST['token'])."' AND `token`.`EmailAddress`='".$_REQUEST['email']."'";
        $mysqli->query($upQry);
    }
}

function updatePassword($mysqli,$check,$loginid){
    //check if record already exist in tbluserstatic
        //if check is true then insert record else update record
        
        $login_id = ($_SESSION['USER_INFO']['Log-inID'] ? $_SESSION['USER_INFO']['Log-inID'] : $loginid);
        
        if($check==true){
            //update query- activate program director and save added details in the database
            $updateQuery = "INSERT INTO `tbluserstatic` (`Log-inID`,`Password`) VALUES('".$login_id."','".$mysqli->real_escape_string($_POST['NewPassword'])."')";
            $upadted=$mysqli->query($updateQuery) or die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$updateQuery);
            
        }else{
            //update query- activate program director and save added details in the database
            $updateQuery = "UPDATE `tbluserstatic` SET Password = '".$mysqli->real_escape_string($_POST['NewPassword'])."' WHERE `Log-inID`='".$login_id."'";
            $upadted=$mysqli->query($updateQuery) or die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$updateQuery);
        }
        return $upadted;
}

//set password
if(isset($_POST['submit'])){    
    if(isset($_SESSION['USER_INFO'])){
        //if user is logged in
        //check if password set in tbluserstatic
        $findQry="SELECT id,tus.Password FROM tblprogramdirectors `tpd` , `tbluserstatic` `tus` WHERE `tpd`.`Log-inID` = `tus`.`Log-inID` AND `tpd`.`Log-inID`='".$_SESSION['USER_INFO']['Log-inID']."'";
        $rs=$mysqli->query($findQry) or die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$findQry);
        $isCheck = $rs->fetch_assoc();
        //check if password is set in tbl
        if($isCheck['Password']){
            //check if old password match
            if(isset($_REQUEST['logged']) || $isCheck['Password'] == $_REQUEST['CurrentPassword']){
                //check if record already exist in tbluserstatic
                if(updatePassword($mysqli,false,'')){
                    $msg = 'Your password has been successfully updated.';
                    if(isset($_REQUEST['logged'])){
                        $msg = 'Your password has been created successfully. Please wait whil we are redirecting you to dashboard....<img src="images/loader.GIF"><br/>Or you can <a href="program-director-dashboard.php">Click Here</a> if it\'s taking too long.';
                        $redirect = true;
                    }
                }
            }else{
                $msg = 'You have entered the wrong current password.Please enter your current password.';
            }   
        }else{
            if(isset($_REQUEST['logged'])){
                $findQry="SELECT * FROM `tblprogramdirectors` WHERE `tblprogramdirectors`.`Log-inID`='".$_SESSION['USER_INFO']['Log-inID']."'";
            }else{
                $findQry="SELECT * FROM `tblprogramdirectors` WHERE `tblprogramdirectors`.Password='".$_REQUEST['CurrentPassword']."' AND `tblprogramdirectors`.`Log-inID`='".$_SESSION['USER_INFO']['Log-inID']."'";
            }
            $result_data=$mysqli->query($findQry) or die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$findQry);
            if($result_data->num_rows>0){
                if(updatePassword($mysqli,true,'')){
                    $msg = 'Your password has been successfully updated.';
                    if(isset($_REQUEST['logged'])){
                        $msg = 'Your password has been created successfully. Please wait whil we are redirecting you to dashboard....<img src="images/loader.GIF"><br/>Or you can <a href="program-director-dashboard.php">Click Here</a> if it\'s taking too long.';
                        $redirect = true;
                    }
                }
            }else{
                $msg = 'You have entered the wrong current password.Please enter your current password.';
            }
        }    
    }else{
        //else case means user comes through the token link
        //check if password set in tbluserstatic
        $findQry="SELECT id,tpd.Password FROM tblprogramdirectors `tpd` , `tbluserstatic` `tus` WHERE `tpd`.`Log-inID` = `tus`.`Log-inID` AND `tpd`.`Log-inID`='".$_REQUEST['Log-inID']."'";
        $rs=$mysqli->query($findQry) or die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$findQry);
        $isCheck = $rs->fetch_assoc();
        //check if password is set in tbl
        if($isCheck['Password']){
            $check = false;
        }else{
            $check = true;
        }
        if(updatePassword($mysqli,$check,$_REQUEST['Log-inID'])){
            $msg = 'Your password has been successfully updated. <a href="'.SITE_PATH.'">Click Here</a> to Login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Send Password Link </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css"  href="css/smart-forms.css">
    <link rel="stylesheet" type="text/css"  href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"  href="css/as.css">
    <link rel="STYLESHEET" type="text/css" href="css/pwdwidget.css" />

    <!--[if lte IE 9]>
    	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>    
        <script type="text/javascript" src="js/jquery.placeholder.min.js"></script>
    <![endif]-->    
    
    <!--[if lte IE 8]>
        <link type="text/css" rel="stylesheet" href="css/smart-forms-ie8.css">
    <![endif]-->
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/plugins.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
    <script src="js/pwdwidget.js" type="text/javascript"></script>
    <?php
    if(!isset($_SESSION['USER_INFO'])){
    echo '<style>#menu .toggle,#menuse,#menuse-alone{display:none!important}</style>';
    }
    ?>
        
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Program Director</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="program-director-dashboard.php" class="">Place Order</a></li>
                    <li><a href="orders-placed.php" class="">Order History</a></li>
                    <li><a href="request-address-change.php" class="">Request Address Change</a></li>
                    <li><a href="reset-password.php">Reset Password</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>                
    			</ul>
            </div><!-- end .form-header section -->
            <ul id="menuse-alone">
                    <li>
                        <a class="menuser-toggle" href="#"><i class="fa fa-bars"></i>Menu</a><!--<a href="#">Menu &#9662;</a>-->
                        <ul>
                            <li><a href="program-director-dashboard.php" class="">Place Order</a></li>
                            <li><a href="orders-placed.php" class="">Order History</a></li>
                            <li><a href="request-address-change.php" class="">Request Address Change</a></li>
                            <li><a href="reset-password.php">Reset Password</a></li>
                            <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>
                        </ul>
                    </li>                    
    			</ul>
  	    
        	<form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_rsetPass();">
                <div class="form-body">
                
                    <?php if(isset($msg)){
                        if(isset($redirect) && $redirect == true){
                            echo '<script>setTimeout(function(){ window.location.href="program-director-dashboard.php"; }, 5000);</script>';
                        }?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p><?php echo $msg?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    
                    <div class="spacer-b30">
                    	<div class="tagline"><span>Program Director Reset Password</span></div><!-- .tagline -->
                    </div>
                    <?php if(isset($_SESSION['USER_INFO']) && !isset($_REQUEST['logged'])){ ?>
                        <div class="section">
                            <label class="field">
                                <input required="true" type="password" name="CurrentPassword" id="CurrentPassword" class="gui-input" placeholder="Enter Current Password">
                            </label>
                        </div><!-- end section -->
                    <?php }  ?>
                    <div class="section">
                        <label class="field">
                            <div class='pwdwidgetdiv' id='thepwddiv'></div>
                    		<script  type="text/javascript" >
                    		var pwdwidget = new PasswordWidget('thepwddiv','NewPassword');
                    		pwdwidget.MakePWDWidget();
                    		</script>
                    		<noscript>
                    		<div><input required="true" type="password" name="NewPassword" id="NewPassword" class="gui-input" placeholder="Enter New Password"></div>		
                    		</noscript>
                        </label>
                    </div><!-- end section -->
                    
                    <div class="section">
                        <label class="field">
                            <input required="true" type="password" name="ConfirmPassword" id="ConfirmPassword" class="gui-input" placeholder="Confirm Your Password">
                        </label>
                    </div><!-- end section -->
                    <input type="hidden" name="Log-inID" value="<?php if(isset($data)){echo $data['Log-inID'];}?>" />
                    <div class="form-footer">
                    	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Set Password">
                    </div><!-- end .form-footer section -->
                </form>
            </div>
         </div>
         <!-- related script-->
    <script src="js/pd-s.js" type="text/javascript"></script>
  </body>
  <!-- style -->
    <style>
    /*responsive */
    @media only screen and (min-width: 801px)
    {
        #menuse{display:none;}
    }
    @media only screen and (max-width: 801px)
    {
        #menuse-alone{display:none;}
    }
    </style>
</html>