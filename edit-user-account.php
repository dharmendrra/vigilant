<?php 
require_once('../connect.php');
$result="";
//check if form posted
if(isset($_POST['submit'])){
    
    $flag=true;
    $qry="SELECT * FROM `tbluseraccounts` WHERE `tbluseraccounts`.`EmailAddressUser`='".$mysqli->real_escape_string($_POST['EmailAddress'])."' AND UserAccountID!='".$mysqli->real_escape_string($_REQUEST['uid'])."' AND AccountType!='".$mysqli->real_escape_string($_REQUEST['a_t'])."'";
    $result = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result->num_rows<=0){
        //insert query- to feed posted tblprogramdirectors
        $updateQuery = "UPDATE `tbluseraccounts` SET 
        `FirstNameUser` = '".$mysqli->real_escape_string($_POST['FirstName'])."',
        `LastNameUser` = '".$mysqli->real_escape_string($_POST['LastName'])."',
        `EmailAddressUser` = '".$mysqli->real_escape_string($_POST['EmailAddress'])."',
        `PasswordUser` = '".$mysqli->real_escape_string($_POST['Password'])."',
        `ActiveUser` = '".$mysqli->real_escape_string($_POST['Active'])."',
        `AccountType` = '".$mysqli->real_escape_string($_POST['AccountType'])."' WHERE UserAccountID='".$mysqli->real_escape_string($_REQUEST['uid'])."'";//stuartclark@sechristusa.com
        
        $result = $mysqli->query($updateQuery);//run created query to feed data into mysql database
        if(!$result){
            die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$updateQuery);
        }else{
            $msg='User has been updated successfully.';
            $flag='success';
            $_POST=null;
        }
    }else{
        $msg='Email address already exist.';
        $flag='error';
    }
}

//fetch data for the line item
$qry="SELECT * FROM  `tbluseraccounts` WHERE  `UserAccountID`=".$_REQUEST['uid'];
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
$account=$result_data->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Add User Account </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css"  href="../css/smart-forms.css">
    <link rel="stylesheet" type="text/css"  href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"  href="../css/as.css">

    <!--[if lte IE 9]>
    	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>    
        <script type="text/javascript" src="js/jquery.placeholder.min.js"></script>
    <![endif]-->    
    
    <!--[if lte IE 8]>
        <link type="text/css" rel="stylesheet" href="css/smart-forms-ie8.css">
    <![endif]-->
    <script src="../js/jquery.min.js" type="text/javascript"></script>
    <script src="../js/plugins.js" type="text/javascript"></script>
    <script src="../js/scripts.js" type="text/javascript"></script>
    <style>.large-text{font-size: 1.8em!important;font-family: calibri!important;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="dashboard.php" class="site-title">Parts Ordering</a>:: Admin</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="user-account.php" class="">Add User</a></li>
                    <li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="approved-program-director.php" class="">Approved Program Directors</a></li>
                    <li><a href="approved-orders.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /></form></li>
            </div><!-- end .form-header section -->
   	    
            
            <form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_pd();">
            	<div class="form-body">
                    
                    <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-<?php echo $flag;?> spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span class="large-text"> Edit User Account </span></div><!-- .tagline -->
                    </div>
                    <br />
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Edit following details </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="FirstName" class="field-label">First Name</label>
                                <label for="FirstName" class="field">
                                    <input required="true" type="text" name="FirstName" id="FirstName" class="gui-input" placeholder="First Name" value="<?php if(isset($account['FirstNameUser'])){echo $account['FirstNameUser'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="LastName" class="field-label">Last Name</label>
                                <label for="LastName" class="field">
                                    <input required="true" type="text" name="LastName" id="LastName" class="gui-input" placeholder="Last Name" value="<?php if(isset($account['LastNameUser'])){echo $account['LastNameUser'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="EmailAddress" class="field-label">Email Address</label>
                                <label class="field">
                                    <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Email Address" value="<?php if(isset($account['EmailAddressUser'])){echo $account['EmailAddressUser'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="AccountType" class="field-label">User Type</label>
                                <label class="field select">
                                    <select required="true" id="AccountType" name="AccountType">
                                        <option value="CustServiceUser" <?php if(isset($account['AccountType']) && $account['AccountType']=='CustServiceUser'){echo 'selected';}?>>Customer Service</option>
                                        <option value="ComponentUser" <?php if(isset($account['AccountType']) && $account['AccountType']=='ComponentUser'){echo 'selected';}?>>Healogics Administrator</option>
                                    </select>
                                    <i class="arrow"></i>
                                </label>  
                            </div>
                        </div>
                        
                        
                        <div class="colm colm2">    
                            <div class="section heading">
                                <label class="field">
                                    <button class="button" disabled="">Status:</button>
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        <div class="colm colm2">    
                            <div class="section">
                                <label for="Yes" class="option block">
                                    <input type="radio" name="Active" id="Yes" value="Yes" <?php if(isset($account['ActiveUser'])){echo ($account['ActiveUser']=='Yes'?'checked=""':'');} ?>>
                                    <span class="radio"></span> Active
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm2">   
                            <div class="section">
                                <label for="No" class="option block">
                                    <input type="radio" name="Active" id="No" value="No" <?php if(isset($account['ActiveUser'])){echo ($account['ActiveUser']=='No'?'checked=""':'');} ?>>
                                    <span class="radio"></span> In-Active
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        
                                                               
                    </div><!-- end .frm-row section -->
                    
                    
                    <div class="frm-row">
                        <div class="colm colm6">    
                            <div class="section">
                                <label for="SiteName" class="field-label">Password</label>
                                <label class="field">
                                    <input required="true" type="text" name="Password" id="Password" class="gui-input" placeholder="Password" value="<?php if(isset($account['PasswordUser'])){echo ($account['PasswordUser']);} ?>">
                                </label>
                            </div><!-- end section -->
                        
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">    
                            <div class="section heading">
                                <label for="SiteName" class="link-password field" style="display:none;cursor:pointer;" id="confirm">Use Password</label>
                                <label class="field">
                                    <input type="text" name="random" id="random" class="link-password gui-input" style="display:none;">
                                    <button class="button link-password" id="generate">Generate Password</button>
                                </label>
                            </div><!-- end section -->                            
                        </div>
                    </div> 
                    
                                                                                                                 
                </div><!-- end .form-body section -->
                <div class="form-footer">
                	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Update User">
                </div><!-- end .form-footer section -->
            </form>
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- related script-->
<script src="../js/pd-s.js" type="text/javascript"></script>
</body>
</html>