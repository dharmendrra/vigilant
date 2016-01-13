<?php 
require_once('connect.php');
$result="";
//check if form posted
if(isset($_POST['submit'])){
    
    //insert query- to feed posted tblprogramdirectors
    $insertQuery = "INSERT INTO `tblprogramdirectors` (`FirstName`,`LastName`, `Title`, `FacilityName`, `Address1`, `Address2`, `City`, `State`, `ZipCode`, `Country`, `EmailAddress`, `PhoneNumber`, `BlueBookCode`, `Active`) VALUES 
    ('".$mysqli->real_escape_string($_POST['FirstName'])."',
     '".$mysqli->real_escape_string($_POST['LastName'])."',
     '".$mysqli->real_escape_string($_POST['Title'])."',
     '".$mysqli->real_escape_string($_POST['FacilityName'])."',
     '".$mysqli->real_escape_string($_POST['Address1'])."',
     '".$mysqli->real_escape_string($_POST['Address2'])."',
     '".$mysqli->real_escape_string($_POST['City'])."',
     '".$mysqli->real_escape_string($_POST['State'])."',
     '".$mysqli->real_escape_string($_POST['ZipCode'])."',
     '".$mysqli->real_escape_string($_POST['Country'])."',
     '".$mysqli->real_escape_string($_POST['EmailAddress'])."',
     '".$mysqli->real_escape_string($_POST['PhoneNumber'])."',
     '".$mysqli->real_escape_string($_POST['BlueBookCode'])."',
     '".$mysqli->real_escape_string($_POST['Active'])."');";
    
    $result = $mysqli->query($insertQuery);//run created query to feed data into mysql database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$insertQuery);
    }else{
            
            $to = $_POST['EmailAddress'];
            $subject_pd = "Parts Ordering:: Program Director Verification";//Program Director
            $subject_cs = "Parts Ordering:: Registration Request from Parts Website";//Customer Service
            //CustomerServiceNow@TekkiesU.com
            
            $message_pd = "
                <html>
                <head><title>SECHRIST INDUSTRIES,INC.</title></head>
                <body>
                <p>Thank you for registering on the Parts Ordering Portal. Your account is being configured and approved. You will receive an email shortly with account information and details. If you have additional questions or concerns regarding account please call or email Customer Service at (714) 555-1212 and CustomerServiceNow@TekkiesU.com</p>
                <table style='border-collapse: collapse;'><tr style='background-color: #DEE0EC;'></tr></table>
                </body>
                </html>
                ";//Program Director
            $message_cs = "
                <html>
                <head>
                <title>SECHRIST INDUSTRIES,INC.</title>
                </head>
                <body>
                <p>Registration Request from Parts Website</p>
                <table style='border-collapse: collapse;'>
                <tr style='background-color: #DEE0EC;'>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>First Name</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Last Name</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Title</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Address 1</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Address 2</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>City</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>State</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>ZipCode</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Country</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Email Address</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Phone Number</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Blue Book Code</th>
                </tr>
                <tr>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['FirstName']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['LastName']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['Title']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['Address1']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['Address2']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['City']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['State']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['ZipCode']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['Country']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['EmailAddress']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['PhoneNumber']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['BlueBookCode']."</td>
                </tr>
                </table>
                <p><a href='".SITE_PATH."unprocessed-program-director.php'>Click here</a> to process Program Director Request</p>
                </body>
                </html>
                ";//Customer Service
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Cc: WebSiteOrders@Sechristusa.com' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            
            mail($to,$subject,$message,$headers);//Program Director
            mail($to,$subject_cs,$message_cs,$headers);//CustomerServiceNow@TekkiesU.com--Customer Service
                                    
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Register Program Director </title>
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
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Register Program Director</h4>
                <!--<a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="index.php" class="">Home</a></li>
    			</ul>-->
            </div><!-- end .form-header section -->
   	    
            
            <form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_pd();">
            	<div class="form-body">
                    
                    <?php if($result){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p>Program Director details have been successfully saved.</p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    

                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Add your following details </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="FirstName" class="field-label">First Name</label>
                                <label for="FirstName" class="field">
                                    <input required="true" type="text" name="FirstName" id="FirstName" class="gui-input" placeholder="First Name">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="LastName" class="field-label">Last Name</label>
                                <label for="LastName" class="field">
                                    <input required="true" type="text" name="LastName" id="LastName" class="gui-input" placeholder="Last Name">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">    
                            <div class="section">
                                <label for="Title" class="field-label">Title</label>
                                <label for="Title" class="field">
                                    <input required="true" type="text" name="Title" id="Title" class="gui-input" placeholder="Title">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">    
                            <div class="section">
                                <label for="FacilityName" class="field-label">Facility Name</label>
                                <label for="FacilityName" class="field">
                                    <input required="true" type="text" name="FacilityName" id="FacilityName" class="gui-input" placeholder="Facility Name">
                                </label>
                            </div><!-- end section -->
                        
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="EmailAddress" class="field-label">Email Address</label>
                                <label for="EmailAddress" class="field">
                                    <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Email Address">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="ConfirmEmailAddress" class="field-label">Confirm Email Address</label>
                                <label class="field">
                                    <input required="true" type="text" name="ConfirmEmailAddress" id="ConfirmEmailAddress" class="gui-input" placeholder="Confirm Email Address">
                                </label>
                            </div><!-- end section -->    
                        </div>
                        
                        <div class="colm colm6">
                             <div class="section">
                                <label for="PhoneNumber" class="field-label">Phone Number</label>
                                <label for="PhoneNumber" class="field">
                                    <input required="true" type="text" name="PhoneNumber" id="PhoneNumber" class="gui-input" placeholder="Phone Number">
                                </label>
                            </div><!-- end section -->
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">
                             <div class="section">
                                <label for="BlueBookCode" class="field-label">Blue Book Code</label>
                                <label for="BlueBookCode" class="field">
                                    <input required="true" type="text" name="BlueBookCode" id="BlueBookCode" class="gui-input" placeholder="Blue Book Code">
                                </label>
                            </div><!-- end section -->
                        </div><!-- end .colm6 section -->
                                                               
                    </div><!-- end .frm-row section --> 
                    
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Address </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="Address1" class="field-label">Address 1</label>
                            <label class="field">
                                <input required="true" type="text" name="Address1" id="Address1" class="gui-input" placeholder="Address 1">
                            </label>
                        </div><!-- end section -->
                        
                        <div class="section colm colm6">
                            <label for="Address2" class="field-label">Address 2</label>
                            <label class="field">
                                <input type="text" name="Address2" id="Address2" class="gui-input" placeholder="Address 2 (optional)">
                            </label>
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="City" class="field-label">City</label>
                            <label for="City" class="field select">
                                <input required="true" type="text" name="City" id="City" class="gui-input" placeholder="City">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label for="State" class="field-label">State</label>
                            <label for="State" class="field select">
                                <input required="true" type="text" name="State" id="State" class="gui-input" placeholder="State">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="Country" class="field-label">Country</label>
                            <label for="Country" class="field select">
                                <input required="true" type="text" name="Country" id="Country" class="gui-input" placeholder="Country">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label for="ZipCode" class="field-label">Zip Code</label>
                            <label for="ZipCode" class="field select">
                                <input required="true" type="text" name="ZipCode" id="ZipCode" class="gui-input" placeholder="Zip Code">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                                                                                             
                </div><!-- end .form-body section -->
                <div class="form-footer">
                	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Register">
                </div><!-- end .form-footer section -->
            </form>
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>