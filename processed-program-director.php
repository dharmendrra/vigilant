<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";
//check if form posted
if(isset($_POST['submit'])){
    
    //insert query- to feed posted tblitemmaster
    $insertQuery = "INSERT INTO `tblitemmaster` (`EquipmentID`,`PartNumber`, `MaxUnit`,`ItemDescription`) VALUES 
    ('".$mysqli->real_escape_string($_POST['EquipmentID'])."',
     '".$mysqli->real_escape_string($_POST['PartNumber'])."',
     '".$mysqli->real_escape_string($_POST['MaxUnit'])."',
     '".$mysqli->real_escape_string($_POST['ItemDescription'])."');";
    
    $result = $mysqli->query($insertQuery);//run created query to feed data into mysql database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$insertQuery);
    }
}

//delete record
if(isset($_POST['del_id'])){
    //insert query- to feed posted tblitemmaster
    $del_Qry = "UPDATE `tblitemmaster` SET RECORD_STATUS = 'DELETED' WHERE ItemID = '".$_POST['del_id']."'";
    $result = $mysqli->query($del_Qry);//run query to delete record from database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$del_Qry);
    }else{
        $msg='Line Item has been deleted successfully.';
    }
}

//fetch data for the program directors
$qry="SELECT * FROM `tblprogramdirectors` WHERE CustomerID!='0' AND ShipToID!='0' AND Active='Yes'";
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Program Director </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.sub-menu{}.sub-menu li{}.sub-menu li a{}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Program Director</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="unprocessed-program-director.php" class="">Program Directors</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" class="hidden" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>
                    <!--<li><a href="area-creator.php" class="">Add Area</a></li>-->
    			</ul>
            </div><!-- end .form-header section -->
   	    
            
            <br />
            <div class="spacer-t40 spacer-t10">
            	<div class="tagline"><span> ::Approved Program Directors </span></div><!-- .tagline -->
            </div>
            <div class="form-body">
                    <div class="frm-row">
                        <?php 
                        if($items!='NOT_FOUND'){
                        ?>
                        <table id="pd-table">
                            <thead><tr><th>First Name</th><th>Title</th><th>Facility Name</th><th>Email Address</th><th>Phone Number</th><th>Address</th><th>Controls</th></tr></thead>
                            <?php while($item=$result_data->fetch_assoc()){ 
                                $address='';$name='';
                                $address[]=$item['Address1'];
                                $address[]=$item['Address2'];
                                $address[]=$item['City'];
                                $address[]=$item['State'];
                                $address[]=$item['ZipCode'];
                                $address[]=$item['Country'];
                                
                                //name
                                $name[]=$item['FirstName'];
                                $name[]=$item['LastName'];
                                ?>
                                <tr>
                                    <td><?php echo implode(' ',$name);?></td>
                                    <td><?php echo $item['Title'];?></td>
                                    <td><?php echo $item['SiteName'];?></td>
                                    <td><?php echo $item['EmailAddress'];?></td>
                                    <td><?php echo $item['PhoneNumber'];?></td>
                                    <td><?php echo implode(',',$address);?></td>
                                    <td><a title="Edit" href="approve-program-director.php?pd=<?php echo $item['ProgramDirectorsID'];?>"><i class="icon-edit"></i></a></td>
                                </tr>
                            <?php }  ?>
                        </table>
                        <?php } ?>
                        
                                                               
                    </div><!-- end .frm-row section --> 
                                                                                             
                </div>
        </div><!-- end .smart-forms section -->
        
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- delete record -->
<form action="" name="del_form" id="del_form" method="POST" ><input type="hidden" name="del_id" id="del_id" /></form>
<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>