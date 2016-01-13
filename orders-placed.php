<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";


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
  	<title> Parts Ordering:: Orders Placed </title>
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
    <!-- style -->
    <style>
    table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.6em!important;font-family: calibri!important;line-height: 1.3;text-shadow: 0 0.4px 1px rgb(74, 73, 87);}.align-div{width: 90px;float: left;text-decoration: underline;}
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
                
                <?php if(isset($msg)){ ?>
                    <div class="section notification-close">
                    
                        <div class="notification alert-success spacer-t10">
                            <p><?php echo $msg;?></p>
                            <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                        </div><!-- end .notification section -->
                                           
                    </div>
                <?php } ?>
                                                                            
            </div><!-- end .form-body section -->
            <div class="form-body">
                
                <div class="spacer-t40 spacer-b40">
                	<div class="tagline"><span class="large-text">ORDERS PLACED</span></div><!-- .tagline -->
                </div>
                
                <?php if($display == true){ ?>
                            <div class="colm colm6">
                                <div class="section">
                                    <label for="ProgramDirectorID" class="field-label">Site Selector<span id="pd-count"></span></label>
                                    <label for="ProgramDirectorID" class="field select">
                                           <select name="bbcode" id="bbcode" onchange="if(this.value){window.location.href='orders-placed.php?bbcode='+this.value;}">
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
                <?php }            ?>
                
                <?php 
                if($displayTab == true && $items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Order Placed By Address</th>
                            <th>PO Number</th>
                            <th>Order Date</th>
                            <th colspan="2">Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php  while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['OrderNumber'];?></td>
                            <td><?php echo $item['OrderPlacedByAddress'];?></td>
                            <td><?php echo $item['PONumber'];?></td>
                            <td><?php echo date('M d,Y  h:i:s A',strtotime($item['DateOrdered']));?></td>
                            <?php /*if($item['OrderApproved']=='Approved'){ ?>
                            <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Deny" href="javascript:void(0);" onclick="updateStatus('ORDER','DENY','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-deny"></i></a></td>
                            <?php }else{ ?>
                             <td id="icon_<?php echo $item['OrdersMasterID'];?>"><a title="Approve" href="javascript:void(0);" onclick="updateStatus('ORDER','APPROVE','<?php echo $item['OrdersMasterID'];?>');"><i class="icon-approve"></i></a></td>   
                            <?php } */?>
                            <td><a title="View Order" href="view-order-placed.php?order=<?php echo $item['OrderNumber'];?>"><i class="icon-view"></i></a></td>
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
<script src="js/pd-s.js" type="text/javascript"></script>

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
if(this.value){window.location.href='orders-placed.php?bbcode='+this.value;}
</script>
</body>
</html>