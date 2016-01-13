<?php
session_start();
require_once('connect.php');

//check if request for signup
if(isset($_POST['submit'])){
    $to = $_REQUEST['EmailAddress'];
            $subject = "Parts Ordering:: Your password reset link";//Program Director
            //CustomerServiceNow@TekkiesU.com
            $password=generate_password(10);
            $message = "
                <html>
                <head><title>SECHRIST INDUSTRIES,INC.</title></head>
                <body>
                <p>Your reset password link is given in the email. Please <a href='".SITE_PATH."reset-password.php?token=".base64_encode($password)."&email=".$_POST['EmailAddress']."'>click here</a> to reset your password. This link is valid for 2 days.<br><br> If you have additional questions or concerns regarding account please call or email Customer Service at (714) 555-1212 and CustomerServiceNow@TekkiesU.com</p>
                <table style='border-collapse: collapse;'><tr style='background-color: #DEE0EC;'></tr></table>
                </body>
                </html>
                ";//Program Director
            
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            
            if(mail($to,$subject,$message,$headers)){
                $mail_info_sent=true;
                    //update query- activate program director and save added details in the database
                    $insertQuery = "INSERT INTO `token` (`Token`, `EmailAddress`,`Token_Gen_Time`) VALUES ('".$password."', '".$_POST['EmailAddress']."','".date('Y-m-d h:i:s')."');";
                    $mysqli->query($insertQuery);//run created query to feed data into mysql database
            }//Program Director
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
        
       
</head>

<body class="woodbg">

    <div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
        
        	<div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Reset Password</h4>
                <!--
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="index.php" class="">Home</a></li>
                    <li><a href="area-creator.php" class="">Add Area</a></li>
    			</ul>-->
            </div><!-- end .form-header section -->
   	    
        	<form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_pd();">
                <div class="form-body">
                
                    <?php if(isset($mail_info_sent)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p>Reset Password link have been sent to your email address.</p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                
                    <div class="spacer-b30">
                    	<div class="tagline"><span>Program Director </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="section">
                        <label class="field">
                            <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Enter Your Email Address">
                        </label>
                    </div><!-- end section -->
                    
                    <div class="form-footer">
                    	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Email Reset Password Link">
                    </div><!-- end .form-footer section -->
                </form>
            </div>
         </div>
         <!-- related script-->
    <script src="js/pd-s.js" type="text/javascript"></script>
  </body>
</html>