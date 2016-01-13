<?php 
require_once('../connect.php');
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
            $_POST=null;
        }
    }else{
        $msg='Email address already exist.';
        $flag='error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Dashboard </title>
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
    <style>.large-text{font-size: 1.8em!important;font-family: calibri!important;}.medium-text{font-size: 1.4em!important;font-family: calibri!important;}</style>
       
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
                    	<div class="tagline"><span class="large-text"> Important Links </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="section">
                    	<p class="medium-text fine-grey"> 1 - <a href="user-account.php">Add User</a></p>
                        <p class="medium-text fine-grey"> 2 - <a href="parts.php">Parts List</a> </p>
                        <p class="medium-text fine-grey"> 3 - <a href="approved-program-director.php" class="">Approved Program Directors</a> </p>
                    </div><!-- end section -->
                    
                    <br /> 
                    
                                                                                                                 
                </div><!-- end .form-body section -->
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