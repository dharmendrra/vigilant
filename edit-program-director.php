<?php 
session_start();
require_once('connect.php');

$result="";$popup_msg="";

//check if form posted
if(isset($_POST['submit'])){
    $updateQuery="";
    //fetch data for the matched email for program director which is being added
    $qry="SELECT * FROM `tblprogramdirectors`,`tblapproveduserlist` WHERE `tblprogramdirectors`.CustomerID='0' AND `tblprogramdirectors`.ShipToID='0' AND `tblprogramdirectors`.EEStatus!='A' AND `tblprogramdirectors`.`ProgramDirectorsID`=".$mysqli->real_escape_string($_REQUEST['pd'])." AND `tblprogramdirectors`.`EmailAddress`=`tblapproveduserlist`.`EmailAddress`";
    $result = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result->num_rows>0 || 1==1){    
        $result_data=$result->fetch_assoc();
        //update query- activate program director and save added details in the database
        $updateQuery = "UPDATE `tblprogramdirectors` SET 
        `CustomerID`='".$mysqli->real_escape_string($_POST['CustomerID'])."',
        `ShipToID`='".$mysqli->real_escape_string($_POST['ShipToID'])."',
        `EEStatus`='".$mysqli->real_escape_string($_POST['Active'])."',
        `FirstName`='".$mysqli->real_escape_string($_POST['FirstName'])."',
        `LastName`='".$mysqli->real_escape_string($_POST['LastName'])."',
        `Title`='".$mysqli->real_escape_string($_POST['Title'])."',
        `FacilityName`='".$mysqli->real_escape_string($_POST['FacilityName'])."',
        `PhoneNumber`='".$mysqli->real_escape_string($_POST['PhoneNumber'])."',
        `BlueBookCode`='".$mysqli->real_escape_string($_POST['BlueBookCode'])."',
        `CompanyAddress1`='".$mysqli->real_escape_string($_POST['CompanyAddress1'])."',
        `CompanyAddress2`='".$mysqli->real_escape_string($_POST['CompanyAddress2'])."',
        `City`='".$mysqli->real_escape_string($_POST['City'])."',
        `State`='".$mysqli->real_escape_string($_POST['State'])."',
        `Country`='".$mysqli->real_escape_string($_POST['Country'])."',
        `Zip`='".$mysqli->real_escape_string($_POST['Zip'])."',
        `EEID`='".$mysqli->real_escape_string($_POST['EEID'])."',
        `Log-inID`='".$mysqli->real_escape_string($_POST['Log-inID'])."'
         WHERE `ProgramDirectorsID`=".$_REQUEST['pd'].";";
        $result = $mysqli->query($updateQuery) or die('ERROR ON edit-program-director.php line 34. '.$mysqli->error);//run created query to feed data into mysql database
         
         /*
         //update query- activate program director and save added details in the database
        $updateQuery = "UPDATE `tblapproveduserlist` SET
        `Active`='".$mysqli->real_escape_string($_POST['Active'])."'
         WHERE `EmailAddress`='".$mysqli->real_escape_string($result_data['EmailAddress'])."';";
        $result = $mysqli->query($updateQuery);//run created query to feed data into mysql database
        
        if(!$result){
            die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$updateQuery);
        }else{
            $popup_msg='SEND PROGRAM DIRECTOR PASSWORD &nbsp;&nbsp;<a href="approved-program-director.php?pd='.$_REQUEST['pd'].'">Click Here</a>';
            $msg='Program Director details have been successfully updated.';
            
        }
        */
        $msg='Program Director details have been successfully updated.';
    }else{
        $popup_msg='Email Address NOT APPROVED on the approved Program Director List ';
        $msg='Email Address NOT APPROVED on the approved Program Director List ';
    }
}
//fetch data for the program director which is being approved
$qry="SELECT * FROM `tblprogramdirectors` WHERE `ProgramDirectorsID`=".$mysqli->real_escape_string($_REQUEST['pd'])."";
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
else{$item=$result_data->fetch_assoc();}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Approve Program Director </title>
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
    <!-- Magnific Popup core CSS file -->
    <link rel="stylesheet" href="css/magnific-popup.css" />
    <!-- Magnific Popup core JS file -->
    <script src="js/jquery.magnific-popup.min.js"></script>
    <style>.smart-forms button[disabled]{opacity: 1!important;background-color: #319ACD!important;}
    .smart-forms .gui-input.extra-light{font-size:15px;border: 1px solid black!important;color: black!important;}.smart-forms .gui-input[disabled]{color: gray!important;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}</style>
        
       
</head>

<body class="woodbg">

	<?php if($popup_msg){ ?>
    <div id="test-popup" class="white-popup mfp-hide">
    <?php echo $popup_msg;?> 
    </div>
    <a href="#test-popup" class="open-popup-link" style="display: none;">Show inline popup</a>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.open-popup-link').magnificPopup({type:'inline'});
        $('.open-popup-link').trigger('click');
    });
    </script>
    <?php } ?>
    <div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Customer Service </h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="unprocessed-site-id.php" class="">UnProcessed SITE-ID</a></li>
                    <li><a href="approved-program-director.php" class="">Program Directors</a></li>
                    <li><a href="unprocessed-orders.php" class="">UnProcessed Orders</a></li>
                    <li><a href="approved-orders.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" class="hidden" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /></form></li>
    			</ul>
            </div><!-- end .form-header section -->
   	    
            
            <form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate_pd();">
            	<div class="form-body">
                    
                    <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Additional details Filled by Customer Service </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="CustomerID" class="field-label">Enter CustomerID</label>
                                <label for="CustomerID" class="field">
                                    <input required="true" type="text" name="CustomerID" id="CustomerID" class="gui-input extra-light" placeholder="Enter CustomerID" value="<?php echo $item['CustomerID'];?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm6">    
                            <div class="section">
                                <label for="ShipToID" class="field-label">Enter ShipToID</label>
                                <label for="ShipToID" class="field">
                                    <input required="true" type="text" name="ShipToID" id="ShipToID" class="gui-input extra-light" placeholder="Enter ShipToID" value="<?php echo $item['ShipToID'];?>">
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        <div class="colm colm4">    
                            <div class="section heading">
                                <label class="field">
                                    <button class="button" disabled="">Director's Status:</button>
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        <div class="colm colm2">    
                            <div class="section">
                                <label for="A" class="option block">
                                    <input type="radio" name="Active" id="A" value="A" <?php if(isset($item['EEStatus'])){echo ($item['EEStatus']=='A'?'checked=""':'');} ?>>
                                    <span class="radio"></span> Active
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm2">   
                            <div class="section">
                                <label for="L" class="option block">
                                    <input type="radio" name="Active" id="L" value="L" <?php if(isset($item['EEStatus'])){echo ($item['EEStatus']=='L'?'checked=""':'');} ?>>
                                    <span class="radio"></span> In-Active
                                </label>
                            </div><!-- end section -->                            
                        </div>  
                    
                    </div><!-- end .frm-row section -->

                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Details Filled By Program Director </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="FirstName" class="field-label">First Name</label>
                                <label class="field">
                                    <input required="true" type="text" name="FirstName" id="FirstName" class="gui-input" placeholder="First Name" value="<?php echo $item['FirstName'];?>" >
                                </label>
                            </div><!-- end section -->
                          </div>
                          <div class="colm colm6">  
                            <div class="section">
                                <label for="Title" class="field-label">Title</label>
                                <label class="field">
                                    <input required="true" type="text" name="Title" id="Title" class="gui-input" placeholder="Title" value="<?php echo $item['Title'];?>" >
                                </label>
                            </div><!-- end section -->
                          </div>
                          <div class="colm colm6">  
                            <div class="section">
                                <label for="FirstName" class="field-label">Email Address</label>
                                <label class="field">
                                    <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Email Address" value="<?php echo $item['EmailAddress'];?>" disabled="">
                                </label>
                            </div><!-- end section -->
                            
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="LastName" class="field-label">Last Name</label>
                                <label class="field">
                                    <input required="true" type="text" name="LastName" id="LastName" class="gui-input" placeholder="Last Name" value="<?php echo $item['LastName'];?>" >
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm6">
                            <div class="section">
                                <label for="FacilityName" class="field-label">Facility Name</label>
                                <label class="field">
                                    <input required="true" type="text" name="FacilityName" id="FacilityName" class="gui-input" placeholder="Facility Name" value="<?php echo $item['FacilityName'];?>" >
                                </label>
                            </div><!-- end section -->
                         </div>
                         <div class="colm colm6">   
                            <div class="section">
                                <label for="PhoneNumber" class="field-label">Phone Number</label>
                                <label class="field">
                                    <input required="true" type="text" name="PhoneNumber" id="PhoneNumber" class="gui-input" placeholder="Phone Number" value="<?php echo $item['PhoneNumber'];?>" >
                                </label>
                            </div><!-- end section -->
                        
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="BlueBookCode" class="field-label">Blue Book Code</label>
                                <label class="field">
                                    <input required="true" type="text" name="BlueBookCode" id="BlueBookCode" class="gui-input" placeholder="Blue Book Code" value="<?php echo $item['BlueBookCode'];?>" >
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="section colm colm6">
                            <label for="Zip" class="field-label">EEID</label>
                            <label class="field select">
                                <input required="true" type="text" name="EEID" id="EEID" class="gui-input" placeholder="EEID" value="<?php echo $item['EEID'];?>" >     
                            </label>  
                        </div><!-- end section -->
                        
                        <div class="section colm colm6">
                            <label for="Country" class="field-label">Log In ID</label>
                            <label class="field select">
                                <input required="true" type="text" name="Log-inID" id="Log-inID" class="gui-input" placeholder="Log-inID" value="<?php echo $item['Log-inID'];?>" >
                            </label>  
                        </div><!-- end section -->                     
                        
                                                               
                    </div><!-- end .frm-row section --> 
                    
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Address </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="CompanyAddress1" class="field-label">Company Address 1</label>
                            <label class="field">
                                <input required="true" type="text" name="CompanyAddress1" id="CompanyAddress1" class="gui-input" placeholder="Company Address 1"  value="<?php echo $item['CompanyAddress1'];?>" >
                            </label>
                        </div><!-- end section -->
                        
                        <div class="section colm colm6">
                            <label for="CompanyAddress2" class="field-label">Company Address 2</label>
                            <label class="field">
                                <input type="text" name="CompanyAddress2" id="CompanyAddress2" class="gui-input" placeholder="Company Address 2"  value="<?php echo $item['CompanyAddress2'];?>" >
                            </label>
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="City" class="field-label">City</label>
                            <label class="field select">
                                <input required="true" type="text" name="City" id="City" class="gui-input" placeholder="City"  value="<?php echo $item['City'];?>" >
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label for="State" class="field-label">State</label>
                            <label class="field select">
                                <input required="true" type="text" name="State" id="State" class="gui-input" placeholder="State" value="<?php echo $item['State'];?>" >     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label for="Country" class="field-label">Country</label>
                            <label class="field select">
                                <input required="true" type="text" name="Country" id="Country" class="gui-input" placeholder="Country" value="<?php echo $item['Country'];?>" >
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label for="Zip" class="field-label">Zip Code</label>
                            <label class="field select">
                                <input required="true" type="text" name="Zip" id="Zip" class="gui-input" placeholder="Zip Code" value="<?php echo $item['Zip'];?>" >     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                                                                                             
                </div><!-- end .form-body section -->
                <div class="form-footer">
                	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Proceed to Update">
                </div><!-- end .form-footer section -->
            </form>
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
    <!-- related script-->
    <script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>