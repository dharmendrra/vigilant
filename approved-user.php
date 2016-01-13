<?php 
require_once('connect.php');
$result="";
//check if form posted
if(isset($_POST['submit'])){
    
    $flag=true;
    $qry="SELECT * FROM `tblapproveduserlist` WHERE `tblapproveduserlist`.`EmailAddress`='".$mysqli->real_escape_string($_POST['EmailAddress'])."'";
    $result = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result->num_rows<=0){
        //insert query- to feed posted tblprogramdirectors
        $insertQuery = "INSERT INTO `tblapproveduserlist` (`FirstName`,`LastName`, `SiteName`,`EmailAddress`, `Active`) VALUES 
        ('".$mysqli->real_escape_string($_POST['FirstName'])."',
         '".$mysqli->real_escape_string($_POST['LastName'])."',
         '".$mysqli->real_escape_string($_POST['SiteName'])."',
         '".$mysqli->real_escape_string($_POST['EmailAddress'])."',
         '".$mysqli->real_escape_string($_POST['Active'])."');";
        
        $result = $mysqli->query($insertQuery);//run created query to feed data into mysql database
        if(!$result){
            die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$insertQuery);
        }else{
            $msg='User has been added successfully.';
            $flag='success';
            $to = 'WebSiteOrders@Sechristusa.com';
            $subject = "Parts Ordering:: New Approved Healogics User";//Program Director
            //CustomerServiceNow@TekkiesU.com
            
            $message = "
                <html>
                <head>
                <title>SECHRIST INDUSTRIES,INC.</title>
                </head>
                <body>
                <p>A new approved user has been added. Here are the added details.</p>
                <table style='border-collapse: collapse;'>
                <tr style='background-color: #DEE0EC;'>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>First Name</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Last Name</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Facility Name</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Email Address</th>
                </tr>
                <tr>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['FirstName']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['LastName']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['SiteName']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$_POST['EmailAddress']."</td>
                </tr>
                </table>
                <p><a href=".SITE_PATH.">Click Here</a> to visit Parts Ordering Portal.</p>                
                </body>
                </html>
                ";//Approved User
            
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            
            mail($to,$subject,$message,$headers);
            $_POST=null;
        }
    }else{
        $msg='Email address already exist.';
        $flag='error';
    }
}

$items="";
//fetch data for the approved user
$qry="SELECT * FROM  `tblapproveduserlist`";
$accounts = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($accounts->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
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
    <style>.large-text{font-size: 1.8em!important;font-family: calibri!important;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Healogics Administrator</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="approved-user.php" class="">Add User</a></li>
                    <li><a href="caap-pending-order.php" class="">Pending Orders</a></li>
                    <li><a href="caap-approved-order.php" class="">Approved Orders</a></li>
                    <li><a href="place-order.php" class="">Place Order</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>                    
    			</ul>
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
                    	<div class="tagline"><span class="large-text"> Add Approved Users </span></div><!-- .tagline -->
                    </div>
                    <br />
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Add following details </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="FirstName" class="field-label">First Name</label>
                                <label for="FirstName" class="field">
                                    <input required="true" type="text" name="FirstName" id="FirstName" class="gui-input" placeholder="First Name" value="<?php if(isset($_POST['FirstName'])){echo $_POST['FirstName'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="LastName" class="field-label">Last Name</label>
                                <label for="LastName" class="field">
                                    <input required="true" type="text" name="LastName" id="LastName" class="gui-input" placeholder="Last Name" value="<?php if(isset($_POST['LastName'])){echo $_POST['LastName'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">    
                            <div class="section">
                                <label for="SiteName" class="field-label">Facility Name</label>
                                <label class="field">
                                    <input required="true" type="text" name="SiteName" id="SiteName" class="gui-input" placeholder="Facility Name" value="<?php if(isset($_POST['SiteName'])){echo $_POST['SiteName'];} ?>">
                                </label>
                            </div><!-- end section -->
                        
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="EmailAddress" class="field-label">Email Address</label>
                                <label class="field">
                                    <input required="true" type="text" name="EmailAddress" id="EmailAddress" class="gui-input" placeholder="Email Address" value="<?php if(isset($_POST['EmailAddress'])){echo $_POST['EmailAddress'];} ?>">
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm4">    
                            <div class="section heading">
                                <label class="field">
                                    <button class="button" disabled="">Status:</button>
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        <div class="colm colm2">    
                            <div class="section">
                                <label for="Yes" class="option block">
                                    <input type="radio" name="Active" id="Yes" value="Yes" <?php if(isset($_POST['Active'])){echo ($_POST['Active']=='Yes'?'checked=""':'');} ?>>
                                    <span class="radio"></span> Active
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm2">   
                            <div class="section">
                                <label for="No" class="option block">
                                    <input type="radio" name="Active" id="No" value="No" <?php if(isset($_POST['Active'])){echo ($_POST['Active']=='No'?'checked=""':'');} ?>>
                                    <span class="radio"></span> In-Active
                                </label>
                            </div><!-- end section -->                            
                        </div>
                                                               
                    </div><!-- end .frm-row section --> 
                    <div class="spacer-t40 spacer-b30">
                	   <div class="tagline">
                        <span>
                            <input type="submit" name="submit" id="submit" class="button btn-primary" value="Add User">
                        </span>
                        </div><!-- .tagline -->
                    </div>
                                                                                                                 
                </div><!-- end .form-body section -->
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text"> Approved User(s) </span></div><!-- .tagline -->
                </div>
                
                <div class="frm-row">
                    <?php 
                    if($items!='NOT_FOUND'){
                    ?>
                    <table id="pd-table">
                        <thead><tr><th>First Name</th><th>Last Name</th><th>Facility Name</th><th>Email</th><th>Active</th><th>Controls</th></tr></thead>
                        <?php while($account=$accounts->fetch_assoc()){ ?>
                            <tr>
                                <td><?php echo $account['FirstName'];?></td>
                                <td><?php echo $account['LastName'];?></td>
                                <td><?php echo $account['SiteName'];?></td>
                                <td><?php echo $account['EmailAddress'];?></td>
                                <td><?php echo $account['Active'];?></td>
                                <td><a title="Edit" href="edit-approved-user.php?uid=<?php echo $account['ApprovedUserID'];?>"><i class="icon-edit"></i></a></td>
                            </tr>
                        <?php }  ?>
                    </table>
                    <?php } ?>
                    
                                                           
                </div>
                
                <div class="form-footer">
                	Footer
                </div><!-- end .form-footer section -->
            </form>
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html> 