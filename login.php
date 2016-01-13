<?php
session_start();
require_once('connect.php');
if(isset($_POST['logout']) && $_POST['logout']=='true'){
    session_destroy();
}
//check if request for signup
if(isset($_POST['submit'])){
    if($_POST['AccountType']=='ProgramDirector'){
        //fetch data for the area which is being edited
        $qry="SELECT * FROM `tblprogramdirectors` WHERE `Log-inID`='".$mysqli->real_escape_string($_POST['LOG-INID'])."' AND `Password`='".$mysqli->real_escape_string($_POST['Password'])."'";
        $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
        if($result_data->num_rows<=0){$msg='Invalid login credential!!!';}
        else{
            $data=$result_data->fetch_assoc();
            $_SESSION['USER_INFO']=$data;
            $_SESSION['AccountType']='ComponentUser';
            header('location:program-director-dashboard.php');
        }   
    }else{
        //fetch data for the area which is being edited
        $qry="SELECT * FROM `tbluseraccounts` WHERE `EmailAddressUser`='".$mysqli->real_escape_string($_POST['EmailAddress'])."' AND `PasswordUser`='".$mysqli->real_escape_string($_POST['Password'])."'";
        if($_POST['AccountType']){$qry.=' AND `AccountType`="'.$_POST['AccountType'].'"';}
        $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
        if($result_data->num_rows<=0){$msg='Invalid login credential!!!';}
        else{
            $data=$result_data->fetch_assoc();
            $_SESSION['USER_INFO']=$data;
            $_SESSION['AccountType']=$data['AccountType'];
            //die($_SESSION['AccountType']);
            if($_SESSION['AccountType']=='CustServiceUser'){header('location:unprocessed-site-id.php');}
            else if($_SESSION['AccountType']=='ComponentUser'){header('location:caap-pending-order.php');}
            
        }   
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Login </title>
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
            	<h4><i class="fa fa-flask"><!--<img width="423" height="211" src="images/SECHRIST-small-color.gif">--></i><a href="index.php" class="site-title">Parts Ordering</a>:: Login</h4>
                <!--
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="index.php" class="">Home</a></li>
                    <li><a href="area-creator.php" class="">Add Area</a></li>
    			</ul>-->
            </div><!-- end .form-header section -->
   	    
        	<form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_pd();">
                <div class="form-body">
                
                    <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-error spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    
                    <div class="spacer-b30">
                    	<div class="tagline"><span> Login </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="section">
                        <label class="field select">
                            <select required="true" id="AccountType" name="AccountType">
                                <option value=""> Login As </option>
                                <option value="CustServiceUser" data-id="EmailAddress" data-disable="LOG-INID">Customer Service</option>
                                <option value="ComponentUser" data-id="EmailAddress" data-disbale="LOG-INID">Healogics Administrator</option>
                                <option value="ProgramDirector" data-id="LOG-INID" data-disable="EmailAddress">Program Director</option>
                            </select>
                            <i class="arrow"></i>
                        </label>  
                    </div>
                    
                    <div class="section login-field active" id="EmailAddress">
                        <label for="EmailAddress" class="field-label">Email Address</label>
                        <label for="EmailAddress" class="field">
                            <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Email Address">
                        </label>
                    </div><!-- end section -->
                    
                    <div class="section login-field" id="LOG-INID">
                        <label for="LOG-INID" class="field-label">LOG-INID</label>
                        <label for="LOG-INID" class="field">
                            <input required="true" type="text" name="LOG-INID" id="LOG-INID" class="gui-input" placeholder="LOG-INID">
                        </label>
                    </div><!-- end section -->
                    
                    <div class="section">
                        <label for="Password" class="field-label">Password</label>
                        <label class="field">
                            <input required="true" type="password" name="Password" id="Password" class="gui-input" placeholder="Password">
                        </label>
                    </div><!-- end section -->
                    
                    <div class="form-footer">
                    	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Login">
                        <div style="  margin-top: 1em;float: right;"> 
                        	<a href="f_pass.php">Forget Password ?</a> 
                        </div>
                    </div><!-- end .form-footer section -->
                </form>
            </div>
         </div>
         <!-- pd script -->
         <script src="js/pd-s.js" type="text/javascript"></script>
  </body>
</html>