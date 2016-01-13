<?php
session_start();
include_once ('../auth.php');
require_once('../connect.php');
$result="";$items="";

//delete record
if(isset($_POST['OrderNumber'])){
    //update order status as approved- tblordersmaster
    $upQry = "UPDATE `tblordersmaster` SET `OrderApproved` = 'Approved' WHERE `OrdersMasterID` = '".$_POST['OrderNumber']."'";
    $result = $mysqli->query($upQry);//run query to delete record from database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$del_Qry);
    }else{
        $msg='Order has been approved successfully. <a href="component-approval-admin.php">Click Here </a> to view Pending Orders.';
    }
}

//fetch data for the line item
$qry="SELECT * FROM  `tblorderlineitem` WHERE `LineItemApproved`!=''";
if(isset($_REQUEST['oid'])){$qry.=" AND OrderNumber='".$_REQUEST['oid']."'";}
$orderBy=(isset($_REQUEST['order'])?$_REQUEST['order']:'DESC');
$orderBy=($orderBy=='ASC'?'DESC':'ASC');
$column=(isset($_REQUEST['column'])?$_REQUEST['column']:'OrderNumber');
$qry.='ORDER BY '.$column.' '.$orderBy;
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}

//get Program Director info
$qryLI="SELECT * FROM  tblordersmaster WHERE `OrderApproved`!=''";
if(isset($_REQUEST['oid'])){$qryLI.=" AND OrderNumber='".$_REQUEST['oid']."'";}
$result_data_LI = $mysqli->query($qryLI) or die($mysqli->error.' '.$qryLI);
if($result_data_LI->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
else{$result_LI=$result_data_LI->fetch_assoc();}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Healogics Administrator </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.6em!important;font-family: calibri!important;line-height: 1.3;text-shadow: 0 0.4px 1px rgb(74, 73, 87);}.align-div{width: 90px;float: left;text-decoration: underline;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <?php if(isset($_REQUEST['user']) && $_REQUEST['user']=='caap'){?>
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Healogics Administrator</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="approved-user.php" class="">Add User</a></li>
                    <li><a href="caap-pending-order.php" class="">Pending Orders</a></li>
                    <li><a href="caap-approved-order.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>                    
    			</ul>
            </div><!-- end .form-header section -->
            <?php }else{ ?>
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Admin </h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="user-account.php" class="">Add User</a></li>
                    <li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="approved-program-director.php" class="">Approved Program Directors</a></li>
                    <li><a href="approved-orders.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /></form></li>
                    <!--<li><a href="#">|| <?php echo $_SESSION['USER_INFO']['FirstName'].' -'.$_SESSION['USER_INFO']['SiteName'];?></a></li>-->
                    
    			</ul>
            </div>
                
            <?php } ?>
   	    
        	<div class="form-body">
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">HYPERBARIC PARTS ORDERING SYSTEM </span></div><!-- .tagline -->
                </div>
                
                <div class="frm-row">
                    <!-- program director info -->
                    <div class="colm colm6">                   
                        <label class="field large-text fine-black"> <div class="align-div">Facility</div><?php echo $result_LI['SiteName'];?> </label>
                        <label class="field large-text fine-black"> <div class="align-div">Name</div><?php echo $result_LI['FirstName'].' '.$result_LI['LastName'];?></label>
                        <label class="field large-text fine-black"> <div class="align-div">Email</div><?php echo $result_LI['EmailAddress'];?></label>
                    </div>
                    
                    <!-- program director info -->
                    <div class="colm colm6">
                        <label class="field large-text fine-black"> 
                        <?php 
                            echo $result_LI['Address'];
                        ?>  
                        </label>
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
                                                                            
            </div><!-- end .form-body section -->
            <div class="wrap-2">
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Line Item(s) </span></div><!-- .tagline -->
                    </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead><tr><th>Equipment</th><th>Part Number</th><th>Description</th><th>Quantity</th><th><a href="caap-order.php?oid=<?php echo $_REQUEST['oid'];?>&order=<?php echo $orderBy;?>&column=LineItemApproved">Status</a></th><?php if(!isset($_REQUEST['status']) || $_REQUEST['status']!='apr'){ ?><th>Controls</th><?php } ?></tr></thead>
                    <tbody></tbody>
                    <?php  while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['Equipment'];?></td>
                            <td><?php echo $item['PartNumber'];?></td>
                            <td><?php echo $item['ItemDescription'];?></td>
                            <td><?php echo $item['QtyOrdered'];?></td>
                            <td id="status_<?php echo $item['OrdersLineItemID'];?>"><?php echo $item['LineItemApproved'];?></td>
                            <?php
                            if(!isset($_REQUEST['status']) || $_REQUEST['status']!='apr'){
                                $orderButton='ENABLE';
                                if($item['LineItemApproved']=='Pending'){$orderButton='DISABLE';}
                                if($item['LineItemApproved']=='Approved'){ ?>
                                <td id="icon_<?php echo $item['OrdersLineItemID'];?>"><a title="Deny" href="javascript:void(0);" onclick="updateStatus('LINE_ITEM','DENY','<?php echo $item['OrdersLineItemID'];?>');"><i class="icon-deny"></i></a></td>
                                <?php }else{ ?>
                                 <td id="icon_<?php echo $item['OrdersLineItemID'];?>"><a title="Approve" href="javascript:void(0);" onclick="updateStatus('LINE_ITEM','APPROVE','<?php echo $item['OrdersLineItemID'];?>');"><i class="icon-approve"></i></a></td>   
                                <?php }
                            }
                            ?>
                        </tr>
                    <?php }  ?>
                </table>
                <?php } ?>
                <?php if(!isset($_REQUEST['status']) || $_REQUEST['status']!='apr'){?>
                <div class="spacer-t40 spacer-b30">
                	<form name="orderLineItem" id="orderLineItem" method="POST">
                        <input type="hidden" name="OrderNumber" value="<?php echo $_REQUEST['oid'];?>" />
                        <div class="tagline">
                            <span><input <?php if($orderButton=='DISABLE'){echo 'disabled="true"';}?> type="submit" name="submitOrder" id="submitOrder" class="button btn-primary" value="Submit Order Approved"></span>
                        </div><!-- .tagline -->
                    </form>
                </div>
                <?php }?>
                <div class="form-footer">
                	<!--<form name="orderLineItem" id="orderLineItem" method="POST" onsubmit="return validate_lineItem();"><input type="hidden" name="OrderLineItemJSONdata" id="OrderLineItemJSONdata" /><input type="submit" name="submitOrder" id="submitOrder" class="button btn-primary" value="Submit Order"></form><!-- .tagline -->
                </div><!-- end .form-footer section -->
            </div>
        </div><!-- end .smart-forms section -->
        
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- delete record -->
<form action="" name="del_form" id="del_form" method="POST" ><input type="hidden" name="del_id" id="del_id" /></form>
<!-- related script-->
<script src="../js/pd-s.js" type="text/javascript"></script>

<!-- javascript temp table -->
<script type="text/javascript">
$(function() {
    $('#orderLineItem').submit(function() {
        if(!confirm('Do you want to mark this order as "Approved" ?\n\nClick "OK" to submit and "Cancel" to update the line item status.')){
            return false;
        }
    });
});

</script>
</body>
</html>