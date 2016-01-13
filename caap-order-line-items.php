<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";

//delete record
if(isset($_POST['OrderNumber'])){
    //update order status as approved- tblordersmaster
    $upQry = "UPDATE `tblordersmaster` SET `OrderApproved` = 'Approved' WHERE `OrderNumber` = '".$_POST['OrderNumber']."'";
    $result = $mysqli->query($upQry);//run query to delete record from database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$del_Qry);
    }else{
        $msg='Order has been approved successfully.';
    }
}

//update order
if(isset($_POST['updateOrder'])){
    //update order status as approved- tblordersmaster
    $upQry = "UPDATE `tblordersmaster` SET `ShipType` = '".$_POST['ShipType']."' WHERE `OrderNumber` = '".$_REQUEST['oid']."'";
    $result = $mysqli->query($upQry);//run query to delete record from database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$del_Qry);
    }else{
        $msg='Order has been updated with Ship Type successfully.';
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
$qryLI="SELECT CASE 
WHEN (`to`.CompanyAddress1 IS NULL AND `to`.CompanyAddress1 = '') THEN CONCAT(`tp`.CompanyAddress1,`tp`.CompanyAddress2,'<br>',`tp`.City,', ',`tp`.State,'<br>',`tp`.Zip,', ',`tp`.Country)
WHEN (`to`.CompanyAddress1 IS NOT NULL OR `to`.CompanyAddress1 != '') THEN  CONCAT(`to`.CompanyAddress1,`to`.CompanyAddress2,'<br>',`to`.City,', ',`to`.State,'<br>',`to`.Zip,', ',`to`.Country)
END
Address,SiteName,OrderPlacedByAddress ,`to`.FirstName,`to`.LastName,`to`.EmailAddress FROM  tblordersmaster `to` LEFT JOIN tblprogramdirectors `tp` ON `to`.ProgramDirectorsID = `tp`.ProgramDirectorsID WHERE `OrderApproved`!=''";
if(isset($_REQUEST['oid'])){$qryLI.=" AND OrderNumber='".$_REQUEST['oid']."'";}
$result_data_LI = $mysqli->query($qryLI) or die($mysqli->error.' '.$qryLI);
if($result_data_LI->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
else{
    $result_LI=$result_data_LI->fetch_assoc();
    function address($data){
        return $data['Address'];
        $add='';$add1='';$add2='';
        $add1[]=$data['CompanyAddress1'];
        $add1[]=$data['CompanyAddress2'];
        $add2[]=$data['City'];
        $add2[]=$data['State'];
        $add3[]=$data['Zip'];
        $add3[]=$data['Country'];
        $add = implode(', ',$add1).'<br>'.implode(', ',$add2).'<br>'.implode(', ',$add3);
        return $add;
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;border-right: 1px solid #ddd;
  border-left: 1px solid #ddd;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.6em!important;font-family: calibri!important;line-height: 1.3;text-shadow: 0 0.4px 1px rgb(74, 73, 87);}.align-div{width: 90px;float: left;text-decoration: underline;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}.icon-submit{background-position: -144px -72px;}.small-input{width: 100px;padding: 1px 3px;border-radius: 2px;height: 25px;}.none{display: none;}.hovered{background-color:green;color:#FFF;}form[name=LineItemQty]{background-color: #FFF;box-shadow: 0px 0px 53px gray;}form[name=LineItemQty] input[type=submit]{line-height: 4;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Healogics Administrator</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <!--<li><a href="approved-user.php" class="">Add User</a></li>-->
                    <li><a href="caap-pending-order.php" class="">Pending Orders</a></li>
                    <li><a href="caap-approved-order.php" class="">Approved Orders</a></li>
                    <li><a href="place-order.php" class="">Place Order</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /><input type="submit" value="Logout" /></form></li>                    
    			</ul>
            </div><!-- end .form-header section -->
   	    
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
                            echo address($result_LI);
                        ?>  
                        </label>
                    </div>
                </div>
                
                <div class="frm-row">
                    <div class="colm colm12"><br />
                        <label style="width:20%" class="align-div field large-text fine-black">Order Placed By</label>
                        <label class="field large-text fine-black">
                            &nbsp;<?php echo $result_LI['OrderPlacedByAddress'];?>
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
                
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?oid=<?php echo $_REQUEST['oid']?>&user=caap" method="POST" id="updateOrderForm">
                    <div class="spacer-t40 spacer-b40">
                	   <div class="tagline"><span>::Add Ship Type </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                        <div class="colm colm6">
                            
                                <label for="ShipType" class="field-label">Ship Type</label>
                                <label for="ShipType" class="field select">
                                   <select name="ShipType" id="ShipType">
                                       <?php renderShipType('option',((isset($result_LI['ShipType']) && $result_LI['ShipType']!="")?$result_LI['ShipType']:''));?>
                                   </select>
                                   <i class="arrow"></i>
                                </label>
                        </div>
                        
                        <div class="colm colm6">
                            <label for="ShipType" class="field-label" style="color: #FFF;">Ship Type</label>
                                <input type="submit" name="updateOrder" id="updateOrder" class="button btn-primary" value="Update Order" title="Update Order">
                            
                        </div>
                    </div>
                </form>
                                                                            
            </div><!-- end .form-body section -->
            
                        
            
            <div class="wrap-2">
                    
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Line Item(s) </span></div><!-- .tagline -->
                    </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead><tr><th>Equipment</th><th>Part Number</th><th>Description</th><th colspan="2">Quantity</th><th><a href="caap-order-line-items.php?oid=<?php echo $_REQUEST['oid'];?>&order=<?php echo $orderBy;?>&column=LineItemApproved&status=<?php echo (isset($_REQUEST['status'])?$_REQUEST['status']:'')?>&user=<?php echo (isset($_REQUEST['user'])?$_REQUEST['user']:'')?>">Status</a></th><?php if(!isset($_REQUEST['status']) || $_REQUEST['status']!='apr'){ ?><th>Controls</th><?php } ?></tr></thead>
                    <tbody></tbody>
                    <?php  
                    $orderButton='ENABLE';
                    while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['Equipment'];?></td>
                            <td><?php echo $item['PartNumber'];?></td>
                            <td><?php echo $item['ItemDescription'];?></td>
                            <td id="Qty_<?php echo $item['OrdersLineItemID'];?>"><span><?php echo $item['QtyOrdered'];?></span>
                            </td>
                            <td><i title="Edit Quantity" onclick="$('#Qty_<?php echo $item['OrdersLineItemID'];?>').hide();$('#QtyForm_<?php echo $item['OrdersLineItemID'];?>').fadeIn();" class="icon-edit"></i></td>
                            <td id="QtyForm_<?php echo $item['OrdersLineItemID'];?>" class="none">
                                <form name="LineItemQty" id="LineItem_<?php echo $item['OrdersLineItemID'];?>" method="POST" onsubmit="//return update_lineItem();"><input type="text" name="QtyOrdered" id="QtyOrdered" value="<?php echo $item['QtyOrdered'];?>" class="small-input" /><input type="hidden" id="RecordId" value="<?php echo $item['OrdersLineItemID'];?>" /><input type="submit" name="submit" id="submit" class="icon-submit" value="Submit Order" title="Save"></form>
                            </td>
                            <td id="status_<?php echo $item['OrdersLineItemID'];?>"><?php echo $item['LineItemApproved'];?></td>
                            <?php
                            if(!isset($_REQUEST['status']) || $_REQUEST['status']!='apr'){
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
<script src="js/pd-s.js" type="text/javascript"></script>

<!-- javascript temp table -->
<script type="text/javascript">
$(function() {
    $('#orderLineItem').submit(function() {
        if(!confirm('Do you want to mark this order as "Approved" ?\n\nClick "OK" to submit and "Cancel" to update the line item status.')){
            return false;
        }
    });
});

//
/*
function update_lineItem(){
    if($.trim($('#').val()))
}
*/
$('form[name="LineItemQty"]').submit(function(){
    var frmid=this.id;
    $.get("ajax/update-lineitem.php?qty="+$('#'+this.id+' #QtyOrdered').val()+"&record_id="+$('#'+this.id+' #RecordId').val(),function(json){
        var data = JSON.parse(json);
        console.log(data);
        if(data.RESPONSE=='UPDATED'){
            $('#Qty_'+$('#'+frmid+' #RecordId').val()+' span').html($('#'+frmid+' #QtyOrdered').val());
            $('#QtyForm_'+$('#'+frmid+' #RecordId').val()).hide();
            $('#Qty_'+$('#'+frmid+' #RecordId').val()).show().addClass('hovered');
            $('#Qty_'+$('#'+frmid+' #RecordId').val()).fadeIn().removeClass('hovered');
            alert('Quantity has been updated.');
        }
    });
    return false;
});
</script>
</body>
</html>