<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items='';$mail_info_sent=false;$password='';

//if a program director is being sent information
if(isset($_REQUEST['pd'])){
    $qry="SELECT * FROM  `tblapproveduserlist` WHERE  `EmailAddress` LIKE  (SELECT EmailAddress FROM `tblprogramdirectors` WHERE `tblprogramdirectors`.Active='Yes' AND `tblprogramdirectors`.`ProgramDirectorsID`='".$mysqli->real_escape_string($_REQUEST['pd'])."')";
    $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result_data->num_rows>0){
        $result_data_array=$result_data->fetch_assoc();
        $to = $result_data_array['EmailAddress'];
            $subject = "Parts Ordering:: Your profile has been approved";//Program Director
            //CustomerServiceNow@TekkiesU.com
            $password=generate_password();
            $message = "
                <html>
                <head><title>SECHRIST INDUSTRIES,INC.</title></head>
                <body>
                <p>Thank you for registering on the Parts Ordering Portal. Your account is being configured and approved. <br><br>Your temporary random password is <u style='border:1px solid gray;background-color:gray;color:#FFF'>".$password."</u><br><br> If you have additional questions or concerns regarding account please call or email Customer Service at (714) 555-1212 and CustomerServiceNow@TekkiesU.com</p>
                <table style='border-collapse: collapse;'><tr style='background-color: #DEE0EC;'></tr></table>
                <p><a href='".SITE_PATH."'>Click here</a> to visit your dashboard.</p>
                </body>
                </html>
                ";//Program Director
            
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Cc: WebSiteOrders@Sechristusa.com' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            
            if(mail($to,$subject,$message,$headers)){
                $mail_info_sent=true;
                    //update query- activate program director and save added details in the database
                    $updateQuery = "UPDATE `tblprogramdirectors` SET 
                    `Password`='".$mysqli->real_escape_string($password)."'
                     WHERE `ProgramDirectorsID`=".$_REQUEST['pd'].";";
                    $mysqli->query($updateQuery);//run created query to feed data into mysql database
            }//Program Director
    }else{
        $msg='There is no matching data in "Approved User" table. Please take a look the approved user table first then process the account.';
    }
}

//fetch data for the program directors
$qry="SELECT * FROM `tblprogramdirectors` WHERE EEStatus!='A' ORDER BY BlueBookCode";
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}

//check if exist in database approved users
function ifExist($email,$mysqli){
    $qry="SELECT * FROM `tblapproveduserlist` WHERE EmailAddress='".trim($email)."'";
    $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result_data->num_rows<=0){return false;}return true;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: In-Active Program Directors </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.smart-forms .form-body {min-height: 500px;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}/*.edit-pencil-square{background-image: url('images/edit.png');height: 45px;width: 54px;}*/.approved{background-color:green!important;}.not-approved{background-color:red!important;}</style>
</head>

<body class="woodbg">

	<?php if($mail_info_sent==true){ ?>
    <div id="test-popup" class="white-popup mfp-hide">
    An email has been sent to the Program Director with Following content.<br /><br /><?php echo $message;?> 
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
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Customer Service</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="inactive-program-director.php" class="">In-Active Program Directors</a></li>
                    <li><a href="approved-program-director.php" class="">Approved Program Directors</a></li>
                    <li><a href="approved-orders.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" class="hidden" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /></form></li>
    			</ul>
            </div><!-- end .form-header section -->
   	    
            <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
            
            	<div class="form-body">
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Unprocessed Program Directors </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                        <?php 
                        if($items!='NOT_FOUND'){
                        ?>
                        <label style="background-color:green;color:green;">AB</label>&nbsp;Healogics has added these in approved user list &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <label style="background-color:red;color:red;">AB</label>&nbsp;There is no data added by Healogics Admin in approved user list<br /><br />
                        <table id="pd-table">
                            <thead><tr><th colspan="2">Name</th><th>Title</th><th>Facility Name</th><th>Email Address</th><th>Phone Number</th><th>Blue Book Code</th><th>Address</th><th>Controls</th></tr></thead>
                            <?php while($item=$result_data->fetch_assoc()){ 
                                $address='';$name='';
                                $address[]=$item['CompanyAddress1'];
                                $address[]=$item['CompanyAddress2'];
                                $address[]=$item['City'];
                                $address[]=$item['State'];
                                $address[]=$item['Zip'];
                                $address[]=$item['Country'];
                                
                                //name
                                $name[]=$item['FirstName'];
                                $name[]=$item['LastName'];
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        if(ifExist($item['EmailAddress'],$mysqli)){echo '<label style="background-color:green;color:green;">AB</label>';}
                                        else{echo '<label style="background-color:red;color:red;">AB</label>';}
                                        ?></td><td>
                                        <?php echo implode(' ',$name);?>
                                    </td>
                                    <td><?php echo $item['Title'];?></td>
                                    <td><?php echo $item['FacilityName'];?></td>
                                    <td><?php echo $item['EmailAddress'];?></td>
                                    <td><?php echo ($item['PhoneNumber']?"(".substr($item['PhoneNumber'], 0, 3).") ".substr($item['PhoneNumber'], 3, 3)."-".substr($item['PhoneNumber'],6):'-');?></td>
                                    <td><?php echo $item['BlueBookCode'];?></td>
                                    <td><?php echo implode(',',$address);?></td>
                                    <td><a title="Edit" href="approve-program-director.php?pd=<?php echo $item['ProgramDirectorsID'];?>"><i class="icon-edit"></i></a></td>
                                </tr>
                            <?php }  ?>
                        </table>
                        <?php } ?>
                        
                                                               
                    </div><!-- end .frm-row section --> 
                                                                                             
                </div><!-- end .form-body section -->
                <div class="form-footer">
                	
                </div><!-- end .form-footer section -->
            
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->

<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>