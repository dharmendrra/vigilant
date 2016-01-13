<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";
//check if form posted
if(isset($_POST['export_orders'])){
    
    
}

//fetch data for the line item
$qry="SELECT tom.*,tpd.EmailAddress AS OrderedBy FROM  `tblordersmaster` tom LEFT JOIN tblprogramdirectors tpd ON tom.ProgramDirectorsID = tpd.ProgramDirectorsID WHERE tom.`ProcessedFS` = 'No' ";
//$qry="SELECT tom.*,tpd.EmailAddress AS OrderedBy FROM  `tblordersmaster` tom LEFT JOIN tblprogramdirectors tpd ON tom.ProgramDirectorsID = tpd.ProgramDirectorsID ";
$orderBy=(isset($_REQUEST['order'])?$_REQUEST['order']:'ASC');
$orderBy=($orderBy=='DESC'?'ASC':'DESC');
$column=(isset($_REQUEST['column'])?$_REQUEST['column']:'OrdersMasterID');
$qry.='ORDER BY '.$column.' '.$orderBy;
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: UnProcessed Orders </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 1150px;}.large-text{font-size: 1.8em!important;font-family: calibri!important;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Customer Service</h4>
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
   	    
            <div class="wrap-2 form-body">
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">UnProcessed ORDERS </span></div><!-- .tagline -->
                </div>
                
                <div class="frm-row">
                    <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                </div>
                
                <div class="frm-controls">
                    <div class="frm-row">
                        <div class="colm colm3">
                            <button id="export_co" class="export_co button btn-primary">Submit & Create CO Export</button>
                        </div>
                        <div class="colm colm3">
                            <button id="export_coli" class="button btn-primary">Submit & Create COLI Export</button>
                        </div>
                    </div>
                </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead>
                        <tr>
                            <th>
                                <label class="option block">
                                    <input type="checkbox" name="check-all">
                                    <span class="checkbox"></span>
                                </label>
                            </th>
                            <th>Order Number</th>
                            <th>Facility Name</th>
                            <th>Ordered By</th>
                            <th>Order Placed By Address</th>
                            <th>Order Date</th>
                            <th colspan="2">Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php  while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td>
                                <label class="option block">
                                    <input type="checkbox" name="up_orders" id="up_orders<?php echo $item['OrderNumber'];?>" value="<?php echo $item['OrderNumber'];?>">
                                    <span class="checkbox"></span>
                                </label>
                                
                            </td>
                            <td><?php echo $item['OrderNumber'];?></td>
                            <td><?php echo $item['SiteName'];?></td>
                            <td><?php echo $item['OrderedBy'];?></td>
                            <td><?php echo $item['OrderPlacedByAddress'];?></td>
                            <td><?php echo date('M d,Y  h:i:s A',strtotime($item['DateOrdered']));?></td>
                            <?php /*if($item['OrderApproved']=='Approved'){ ?>
                            <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Deny" href="javascript:void(0);" onclick="updateStatus('ORDER','DENY','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-deny"></i></a></td>
                            <?php }else{ ?>
                             <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Approve" href="javascript:void(0);" onclick="updateStatus('ORDER','APPROVE','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-approve"></i></a></td>   
                            <?php } */?>
                            <td><a title="View Order" href="caap-order.php?oid=<?php echo $item['OrderNumber'];?>&status=apr"><i class="icon-view"></i></a></td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
                <?php } ?>
                <div class="form-footer">
                    <div class="colm colm3">
                        <button id="export_trigger" class="export_co button btn-primary">Submit & Create Export</button>
                    </div>
                </div><!-- end .form-footer section -->
            </div>
        </div><!-- end .smart-forms section -->
        
    </div><!-- end .smart-wrap section --><!-- end .form-body section -->
    
    <div></div><!-- end section -->
<!-- delete record -->
<form target="_blank" action="export.php" name="export_form" id="export_form" method="POST" ><input type="hidden" name="export_orders" id="export_orders" /><input type="hidden" name="fileName" /></form>
<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>
</body>
</html>