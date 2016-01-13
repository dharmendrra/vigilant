<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";


if(isset($_POST)){
    //types from database
       $qryRAC = "INSERT INTO tblAddressChangeRequest (FacilityName,CompanyAddress1,CompanyAddress2)VALUES(
                    '".$_REQUEST['FacilityName']."',
                    '".$_REQUEST['CompanyAddress1']."',
                    '".$_REQUEST['CompanyAddress2']."',
                    '".$_REQUEST['City']."',
                    '".$_REQUEST['State']."',
                    '".$_REQUEST['Zip']."',
                    '".$_REQUEST['Country']."')";
       $mysqli->query($qryRAC) or die($mysqli->error.' '.$qryRAC);
            $message = '
                <html>
                <head><title>SECHRIST INDUSTRIES,INC.</title></head>
                <body>
                <p>An Address Change Request has been made by <b>'.$_REQUEST['RequestAuthor'].'</b></p>
                <table class="poTab" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">FacilityName</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['FacilityName'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">CompanyAddress1</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['CompanyAddress1'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">CompanyAddress2</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['CompanyAddress2'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">City</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['City'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">State</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['State'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">Zip</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['Zip'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">Country</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['Country'].'</td>
                    </tr>
                    <style>
                .poTab{border-collapse: collapse;}
                .poTab tr{padding: 2px;}
                .poTab td:nth-child(even){padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;}
                .poTab td:nth-child(odd){padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;}
                </style>
                </table>
                <p><a href="'.SITE_PATH.'">Click here</a> for the follow up on request.</p>
                </body>
                </html>
                ';//Program Director
            
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            $to = "WebOrderChangeRequest@Sechristusa.com";
            $to = "dharmendra402@gmail.com";
            $subject = "Parts Ordering:: Order Change Request #".$_REQUEST['OrderNumber'];//Program Director
            if(mail($to,$subject,$message,$headers)){
                $msg = 'Your request for address change has been successfully sent.';
            }else{
                $msg = 'System error. Your request for address change has been failed.';
            }
}


//fetch data for the BBCodes/Sites managed by Program Director
$qry_bbc="SELECT BlueBookCode,FacilityName FROM tblprogramdirectors WHERE `Log-inID` LIKE '".$_SESSION['USER_INFO']['Log-inID']."'";
$result_bbc = $mysqli->query($qry_bbc) or die($mysqli->error.' '.$qry_bbc);
if($result_bbc->num_rows<=0){$msg='No matched bbcodes for the pd in the database!!!';}
else{
    $bbcode = false;
    if($result_bbc->num_rows>1){
        $display = true;
    }else{
        $display = false;
        $bbcode = $result_bbc->fetch_row();
    }
}

if($display == false || $_REQUEST['bbcode']){
    if($_REQUEST['bbcode']){
        $display = true;
    }
    //fetch data for the line item
    $qry="SELECT tom.*,tpd.EmailAddress AS OrderedBy FROM `tblordersmaster` tom LEFT JOIN tblprogramdirectors tpd ON tom.BlueBookCode = tpd.BlueBookCode WHERE tpd.`Log-inID` LIKE '".$_SESSION['USER_INFO']['Log-inID']."' AND tom.`BlueBookCode` LIKE '".($_REQUEST['bbcode'] ? $_REQUEST['bbcode'] : $bbcode)."'";
    
    $orderBy=(isset($_REQUEST['order'])?$_REQUEST['order']:'ASC');
    $orderBy=($orderBy=='DESC'?'ASC':'DESC');
    $column=(isset($_REQUEST['column'])?$_REQUEST['column']:'DateOrdered');
    $qry.='ORDER BY '.$column.' '.$orderBy;
    $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result_data->num_rows<=0){
        $msg='No matched items in the database!!!';
        $items='NOT_FOUND';
        $displayTab = false;
    }else{
        $displayTab = true;
    }   
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Healogics Administrator </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.6em!important;font-family: calibri!important;line-height: 1.3;text-shadow: 0 0.4px 1px rgb(74, 73, 87);}.align-div{width: 90px;float: left;text-decoration: underline;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}
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
   	    
        	<div class="form-body">
                <form method="POST" action="" id="addressChangeRequestform" enctype="multipart/form-data">
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">HYPERBARIC PARTS ORDERING SYSTEM </span></div><!-- .tagline -->
                </div>
                
                <div class="frm-row">
                    <!-- program director info -->
                    <div class="colm colm6">                   
                        <label class="field large-text fine-black"> <div class="align-div">CustomerID</div><?php echo $result_LI['CustomerID'];?> </label>
                        <label class="field large-text fine-black"> <div class="align-div">ShipID</div><?php echo $result_LI['ShipID'];?></label>
                        <label class="field large-text fine-black"> <div class="align-div">BlueBookCode</div><?php echo $result_LI['BlueBookCode'];?></label>
                        <label class="field large-text fine-black"> <div class="align-div">FacilityStatus</div><?php echo $result_LI['FacilityStatus'];?> </label>
                    </div>
                </div>
                
                <?php if(isset($msg)){ ?>
                    <div class="section notification-close">
                    
                        <div class="notification alert-success spacer-t10">
                            <p><?php echo $msg;?></p>
                            <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                        </div><!-- end .notification section -->
                                           
                    </div>
                <?php } ?>
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">Address Change Request</span></div><!-- .tagline -->
                </div>
                
                <p>Address changes are not immediate and must be approved. Please submit address change and confirmation will be sent when updated. Process may take up to 24 hours for change approval.</p>
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="bbcode" class="field-label">Site Selector</label>
                        <label for="bbcode" class="field select">
                               <select name="bbcode" id="bbcode" onchange="if(this.value){window.location.href='address-change-request.php?bbcode='+this.value;}">
                               <option value="">Choose Site</option>
                                    <?php
                                    while($bbc=$result_bbc->fetch_assoc()){
                                        echo '<option '.((isset($_REQUEST['bbcode'])&& $_REQUEST['bbcode']==$bbc['BlueBookCode'])?'selected':'').' value="'.$bbc['BlueBookCode'].'">'.($bbc['FacilityName'].' - '.$bbc['BlueBookCode']).'</option>';
                                    }
                                    ?>
                                </select>
                                <!--<i class="arrow"></i>-->
                        </label>
                    </div>
                </div>
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">Enter details</span></div><!-- .tagline -->
                </div>
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="FacilityName" class="field-label">Enter Facility Name</label>
                        <label for="FacilityName" class="field">
                            <input required="true" type="text" name="FacilityName" id="FacilityName" class="gui-input" placeholder="Enter Facility Name">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="CompanyAddress1" class="field-label">Enter CompanyAddress1</label>
                        <label for="CompanyAddress1" class="field">
                            <input required="true" type="text" name="CompanyAddress1" id="CompanyAddress1" class="gui-input" placeholder="Enter CompanyAddress1">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="CompanyAddress2" class="field-label">Enter CompanyAddress2</label>
                        <label for="CompanyAddress2" class="field">
                            <input required="true" type="text" name="CompanyAddress2" id="CompanyAddress2" class="gui-input" placeholder="Enter CompanyAddress2">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="City" class="field-label">Enter City</label>
                        <label for="City" class="field">
                            <input required="true" type="text" name="City" id="City" class="gui-input" placeholder="Enter City">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="State" class="field-label">Enter State</label>
                        <label for="State" class="field">
                            <input required="true" type="text" name="State" id="State" class="gui-input" placeholder="Enter State">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="Zip" class="field-label">Enter Zip</label>
                        <label for="Zip" class="field">
                            <input required="true" type="text" name="Zip" id="Zip" class="gui-input" placeholder="Enter Zip">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                
                <div class="colm colm6">
                    <div class="section">
                        <label for="Country" class="field-label">Enter Country</label>
                        <label for="Country" class="field">
                            <input required="true" type="text" name="Country" id="Country" class="gui-input" placeholder="Enter Country">
                        </label>
                    </div><!-- end section -->
                </div><!-- end .colm6 section -->
                <input type="hidden" name="RequestAuthor" value="<?php echo $_SESSION['USER_INFO']['FacilityName'].'['.$_SESSION['USER_INFO']['BlueBookCode'].']';?>" />
                </form>                                                                
            </div><!-- end .form-body section -->
            
        </div><!-- end .smart-forms section -->
        
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->

<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>