<?php
session_start();
include_once ('../auth.php');
require_once('../connect.php');
$result="";$items="";
//check if form posted
if(isset($_POST['submitOrder'])){
    
    //select Order Number
    $OrderNumber=0;
    $oQuery = "SELECT MAX(`OrderNumber`) AS OrdNum FROM `tblorderlineitem`";
    $oresult = $mysqli->query($oQuery);//run created query to feed data into mysql database
    $on=$oresult->fetch_assoc();
    $OrdNum=$on['OrdNum'];
    
    //insert query- to feed posted tblitemmaster
    $insertOrdQuery = "INSERT INTO `tblordersmaster` (`OrderNumber`, `SiteName`, `FirstName`, `LastName`, `EmailAddress`, `CustomerID`, `ShipToID`) VALUES ('".($OrdNum+1)."', '".$_SESSION['USER_INFO']['SiteName']."', '".$_SESSION['USER_INFO']['FirstName']."', '".$_SESSION['USER_INFO']['LastName']."', '".$_SESSION['USER_INFO']['EmailAddress']."', '".$_SESSION['USER_INFO']['CustomerID']."', '".$_SESSION['USER_INFO']['ShipToID']."')";
    
    $resultOrd = $mysqli->query($insertOrdQuery);//run created query to feed data into mysql database
    /*
    //$OrderNumber=$resultOrd->lastInsertRowid();
    $OrderNumber=$mysqli->lastInsertRowid();
    */
    
    //line item
    $data="";
    $jsondata=json_decode($_POST['OrderLineItemJSONdata']);
    foreach($jsondata as $row){
        $json=json_decode($row);
        
        if($json->MaxUnit<$_POST['MAX_QTY']){$LineItemApproved='Approved';}
        else{$LineItemApproved='Pending';}
        $string="('".($OrdNum+1)."','".$mysqli->real_escape_string($json->EquipmentID)."',
        '".$mysqli->real_escape_string($json->Equipment)."',
        '".$mysqli->real_escape_string($json->PartNumber)."',
        '".$mysqli->real_escape_string($json->MaxUnit)."',
        '".$mysqli->real_escape_string($json->ItemDescription)."',
        '".date('Y-m-d h:i:s')."',
        '".$LineItemApproved."')";
        $data[]=$string;
    }
    $data = implode(',',$data);
    
    //insert query- to feed posted tblitemmaster
    $insertQuery = "INSERT INTO `tblorderlineitem` (`OrderNumber`,`EquipmentID`,`Equipment`,`PartNumber`, `QtyOrdered`,`ItemDescription`,`DateOrdered`,`LineItemApproved`) VALUES 
    ".$data.";";
    
    $result = $mysqli->query($insertQuery);//run created query to feed data into mysql database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$insertQuery);
    }else{
        $msg = 'Line Item Order have been added successfully.';
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

//fetch data for the line item
$qry="SELECT * FROM  `tblordersmaster` WHERE `OrderApproved`='Approved'";
$orderBy=(isset($_REQUEST['order'])?$_REQUEST['order']:'DESC');
$orderBy=($orderBy=='ASC'?'DESC':'ASC');
$column=(isset($_REQUEST['column'])?$_REQUEST['column']:'OrderNumber');
$qry.='ORDER BY '.$column.' '.$orderBy;
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Parts Ordering:: Approved Orders </title>
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.8em!important;font-family: calibri!important;}</style>
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Parts Ordering</a>:: Admin</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
                    <li><a href="user-account.php" class="">Add User</a></li>
                    <li><a href="parts.php" class="">Parts List</a></li>
                    <li><a href="approved-program-director.php" class="">Approved Program Directors</a></li>
                    <li><a href="approved-orders.php" class="">Approved Orders</a></li>
                    <li><a href="#" onclick="$('#logoutForm').submit();" class="">Logout</a><form action="index.php" style="display: none;" method="POST" id="logoutForm"><input type="hidden" name="logout" value="true" /></form></li>                    
    			</ul>
            </div><!-- end .form-header section -->
   	    
        	<div class="form-body">
                
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
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">APPROVED ORDERS </span></div><!-- .tagline -->
                </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead><tr><th>Order Number</th><th>Facility Name</th><th>Name Of Person</th><th>Order Date</th><th colspan="2">Controls</th></tr></thead>
                    <tbody>
                    <?php  while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['OrderNumber'];?></td>
                            <td><?php echo $item['SiteName'];?></td>
                            <td><?php echo $item['FirstName'].' '.$item['LastName'];?></td>
                            <td><?php echo date('M d,Y  h:i:s A',strtotime($item['DateOrdered']));?></td>
                            <?php /*if($item['OrderApproved']=='Approved'){ ?>
                            <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Deny" href="javascript:void(0);" onclick="updateStatus('ORDER','DENY','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-deny"></i></a></td>
                            <?php }else{ ?>
                             <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Approve" href="javascript:void(0);" onclick="updateStatus('ORDER','APPROVE','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-approve"></i></a></td>   
                            <?php } */?>
                            <td><a title="View Order" href="caap-order.php?oid=<?php echo $item['OrdersMasterID'];?>&status=apr"><i class="icon-view"></i></a></td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
                <?php } ?>
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
var form_details = [];

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(function() {
    $('#lineItemForm').submit(function() {
        //$('#result').text(JSON.stringify($('#lineItemForm').serializeObject()));
        form_details.push(JSON.stringify($('#lineItemForm').serializeObject()));
        drawTable();
        //$('#lineItemForm').resetForm();
        document.getElementById('lineItemForm').reset();
        $('#ItemDescription').html('');
        return false;
    });
});

//table
function drawTable(record_id){
    var tabdata;
    console.log(form_details);
    if(form_details.length>0){
        $.each(form_details,function(index,details){
            details=JSON.parse(details);
            var temp_tabdata;
            temp_tabdata+="<tr id='tr_"+index+"'><td>"+details.Equipment+"</td>";
            temp_tabdata+="<td>"+details.PartNumber+"</td>";
            temp_tabdata+="<td>"+details.ItemDescription+"</td>";
            temp_tabdata+="<td>"+details.MaxUnit+"</td>";
            temp_tabdata+="<td><a title='Delete' href='javascript:void(0);' onclick=\"removeRow('tr_"+index+"');\"><i class='icon-remove'></i></a></td></tr>";
            tabdata=temp_tabdata+tabdata;
            if(parseInt(lineItemMaxUnit)<parseInt(details.MaxUnit)){
                lineItemMaxUnit=parseInt(details.MaxUnit);
            }
        });
        $('#pd-table').show();
        $('#pd-table tbody').html(tabdata);
        makeTableResponsive();
    }else{
        $('#pd-table').hide();
    }
}

//remove raw- DELETE
function removeRow(record_id){
    $('#'+record_id).fadeOut().remove();
    //document.getElementById(record_id).removeChild();
    form_details.splice(record_id, 1);
    drawTable();
}
</script>
</body>
</html>