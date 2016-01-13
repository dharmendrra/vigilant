<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";
//check if form posted
if(isset($_POST['submitOrder'])){
    
    //select Order Number
    $OrderNumber=0;
    $oQuery = "SELECT MAX(REPLACE(`OrderNumber`,'HO','')) AS OrdNum FROM `tblorderlineitem`";
    $oresult = $mysqli->query($oQuery);//run created query to feed data into mysql database
    $on=$oresult->fetch_assoc();
    $OrdNum=$on['OrdNum'];
    $OrdNum='HO'.($OrdNum?($OrdNum+1):'900001');
    $jsondata=json_decode($_POST['OrderLineItemJSONdata']);
    $shipType=json_decode($jsondata[0]);
    $shipType=$shipType->ShipType;
    
    $add='';$add1='';$add2='';
    $add1[]=$_SESSION['PD_INFO']['CompanyAddress1'];
    $add1[]=$_SESSION['PD_INFO']['CompanyAddress2'];
    $add2[]=$_SESSION['PD_INFO']['City'];
    $add2[]=$_SESSION['PD_INFO']['State'];
    $add3[]=$_SESSION['PD_INFO']['Zip'];
    $add3[]=$_SESSION['PD_INFO']['Country'];
    $add = implode(', ',$add1).'<br>'.implode(', ',$add2).'<br>'.implode(', ',$add3);
    
    //insert query- to feed posted tblitemmaster
    $insertOrdQuery = "INSERT INTO `tblordersmaster` (`OrderNumber`,`ProgramDirectorsID`, `SiteName`,`Address`, `FirstName`, `LastName`, `EmailAddress`, `CustomerID`, `ShipToID`,`ShipType`,`DateOrdered`,`OrderApproved`) VALUES ('".($OrdNum)."', '".$_SESSION['PD_INFO']['ProgramDirectorsID']."', '".$_SESSION['PD_INFO']['FacilityName']."', '".$mysqli->real_escape_string($add)."', '".$_SESSION['PD_INFO']['FirstName']."', '".$_SESSION['PD_INFO']['LastName']."', '".$_SESSION['PD_INFO']['EmailAddress']."', '".$_SESSION['PD_INFO']['CustomerID']."', '".$_SESSION['PD_INFO']['ShipToID']."','".$mysqli->real_escape_string($shipType)."','".date('Y-m-d h:i:s')."','Pending')";
    
    
    $resultOrd = $mysqli->query($insertOrdQuery);//run created query to feed data into mysql database
    
            $to = 'WebSiteOrders@Sechristusa.com';
            $subject = "Parts Ordering:: New Order placed.";//Program Director
            //CustomerServiceNow@TekkiesU.com
            
            $message = "
                <html>
                <head>
                <title>SECHRIST INDUSTRIES,INC.</title>
                </head>
                <body>
                <p>A new order has been placed. Please login and visit your dashboard for the Newly placed orders.</p>
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
    
    /*
    //$OrderNumber=$resultOrd->lastInsertRowid();
    $OrderNumber=$mysqli->lastInsertRowid();
    */
    
    //line item
    $data="";$orderApproved="Approved";
    foreach($jsondata as $row){
        $json=json_decode($row);
        
        if($json->MaxUnit<=$json->Org_MaxUnit){$LineItemApproved='Approved';}
        else{$LineItemApproved='Pending';$orderApproved='Pending';}
        $string="('".($OrdNum)."','".$mysqli->real_escape_string($json->EquipmentID)."',
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
    $upOrdQuery = "UPDATE `tblordersmaster` SET `OrderApproved` = '".($orderApproved)."' WHERE `OrderNumber`='".($OrdNum)."'";
    
    $mysqli->query($upOrdQuery);//run created query to update order status mysql database
    
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
$qry="SELECT * FROM  `tblitemmaster` , tblequipment WHERE  `tblitemmaster`.`EquipmentID` = tblequipment.`EquipmentID`";
$result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result_data->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}

//find max quantity
$qry="SELECT MAX(MaxUnit) as MAX_QTY FROM  `tblitemmaster`";
$MAX_QTY = $mysqli->query($qry) or die($mysqli->error.' '.$qry);

//list all Program Directors
$qry="SELECT * FROM  `tblprogramdirectors` WHERE EEStatus='A'";
$rs_pd = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
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
    <style>table#pd-table thead th, table#pd-table tbody td {padding: 0.5em 0.4em .5em .4em;}.wrap-2{max-width: 950px;}.large-text{font-size: 1.6em!important;font-family: calibri!important;line-height: 1.3;text-shadow: 0 0.4px 1px rgb(74, 73, 87);}.align-div{width: 90px;float: left;text-decoration: underline;}#menu ul{right:10px!important;}#menu ul li a{padding:15px 5px 20px 10px!important;}</style>
       
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
   	    
            
            <form method="post" action="" id="lineItemForm" enctype="multipart/form-data" onsubmit="//return validate_lineItem();">
            	<div class="form-body">
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span class="large-text">HYPERBARIC PARTS ORDERING SYSTEM </span></div><!-- .tagline -->
                    </div>
                    
                    <?php if(isset($msg)){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p><?php echo $msg;?></p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->
                                               
                        </div>
                    <?php } ?>
                    
                    <div class="colm colm6">
                        <div class="section">
                            <label for="pd-selector" class="field-label">Minimize Program Director List</label>
                            <label for="pd-selector" class="field select">
                                   <input type="text" id="pd-selector" class="gui-input" autocomplete="off" value="<?php if(isset($_GET['q']) && $_GET['q']){echo $_GET['q'];}?>" />
                            </label>
                        </div>
                    </div>
                    
                    <div class="colm colm6">
                        <div class="section">
                            <label for="ProgramDirectorID" class="field-label">Program Director<span id="pd-count"></span></label>
                            <label for="ProgramDirectorID" class="field select">
                                   <select name="ProgramDirectorID" id="ProgramDirectorID" onchange="if(this.value){window.location.href='place-order.php?pd='+this.value+'&q='+$.trim($('#pd-selector').val());}">
                                   <option value="">Choose Program Director</option>
                                        <?php
                                        while($pd=$rs_pd->fetch_assoc()){
                                            if(isset($_REQUEST['pd'])&& $_REQUEST['pd']==$pd['ProgramDirectorsID']){
                                                $_SESSION['PD_INFO']=$pd;
                                            }
                                            if($pd['BlueBookCode']){
                                                $optionName=$pd['BlueBookCode'];
                                            }else{
                                                $optionName=$pd['FirstName'].' '.$pd['LastName'];
                                            }
                                            $dataName = $optionName;
                                            if($pd['FacilityName']){
                                                $optionName.=' - '.$pd['FacilityName'];
                                            }else{
                                                $optionName.='['.$pd['EmailAddress'].']';
                                            }
                                            echo '<option '.((isset($_REQUEST['pd'])&& $_REQUEST['pd']==$pd['ProgramDirectorsID'])?'selected':'').' value="'.$pd['ProgramDirectorsID'].'" data-name="'.$dataName.'">'.($optionName).'</option>';
                                        }
                                        ?>
                                    </select>
                                    <!--<i class="arrow"></i>-->
                            </label>
                        </div>
                    </div>
                    <?php if(!isset($_REQUEST['pd'])){die('<script src="js/pd-s.js" type="text/javascript"></script>');}?>
                    
                    <div class="frm-row">
                        <!-- program director info -->
                        <div class="colm colm6">                   
                            <label class="field large-text fine-black"> <div class="align-div">Facility</div><?php echo $_SESSION['PD_INFO']['FacilityName'];?> </label>
                            <label class="field large-text fine-black"> <div class="align-div">Name</div><?php echo $_SESSION['PD_INFO']['FirstName'].' '.$_SESSION['PD_INFO']['LastName'];?></label>
                            <label class="field large-text fine-black"> <div class="align-div">Email</div><?php echo $_SESSION['PD_INFO']['EmailAddress'];?></label>
                        </div>
                        
                        <!-- program director info -->
                        <div class="colm colm6">
                                <label class="field large-text fine-black"> 
                                <?php 
                                $address='';$address1='';$address2='';$name='';
                                $address[]=$_SESSION['PD_INFO']['CompanyAddress1'];
                                if($_SESSION['PD_INFO']['CompanyAddress2']):
                                $address[]=$_SESSION['PD_INFO']['CompanyAddress2'];
                                endif;
                                $address1[]=$_SESSION['PD_INFO']['City'];
                                $address1[]=$_SESSION['PD_INFO']['State'];
                                $address2[]=$_SESSION['PD_INFO']['Zip'];
                                $address2[]=$_SESSION['PD_INFO']['Country'];
                                echo implode(', ',$address).'<br>';
                                echo implode(', ',$address1).'<br>';
                                echo implode(', ',$address2);?>  
                                </label>
                        </div>
                    </div>
                    
                    <div class="spacer-t40 spacer-b40">
                    	   <div class="tagline"><span>::Add Ship Type </span></div><!-- .tagline -->
                        </div>
                        
                    <div class="colm colm6">
                        <div class="section">
                            <label for="ShipType" class="field-label">Ship Type</label>
                            <label for="ShipType" class="field select">
                               <select name="ShipType" id="ShipType" onchange="if(this.value){$('#OrderLineShipType').val(this.value);}">
                                   <?php renderShipType('option');?>
                               </select>
                               <i class="arrow"></i>
                            </label>
                        </div>
                    </div>                      
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Add Line Item details </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                                            
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label for="Equipment" class="field-label">Equipment</label>
                                <label for="Equipment" class="field select">
                                <?php
                                //fetch soil types from database
                                $qry="SELECT * FROM `tblequipment`";
                                $result_equipment = $mysqli->query($qry);//run created query to feed data into mysql database
                                if(!$result_equipment){
                                    echo('There is no Equipment in the database.');
                                }else{   ?>
                                    <select required="true" id="Equipment" name="Equipment">
                                        <option value=""> Select Equipment </option>
                                    <?php
                                    while($equipment=$result_equipment->fetch_assoc()){
                                        echo '<option value="'.$equipment['Equipment'].'" data-id="'.$equipment['EquipmentID'].'">'.$equipment['Equipment'].'</option>';
                                    }?>
                                    </select>
                                    <i class="arrow"></i>
                                <?php } ?>     
                                </label>  
                            </div><!-- end section -->
                            
                            <div class="section">
                                <label for="PartNumber" class="field-label">Part Number</label>
                                <label class="field select">
                                <?php
                                //fetch soil types from database
                                $qry="SELECT * FROM `tblitemmaster`";
                                $result_lineitem = $mysqli->query($qry);//run created query to feed data into mysql database
                                if(!$result_lineitem){
                                    echo('There is no Part Number in the database.');
                                }else{
                                        ?>
                                    <select required="true" id="PartNumber" name="PartNumber">
                                        <option value=""> Select Part Number </option>
                                    <?php
                                    while($lineitem=$result_lineitem->fetch_assoc()){
                                        echo '<option value="'.$lineitem['ItemID'].'" data-id="'.$lineitem['EquipmentID'].'">'.$lineitem['PartNumber'].'</option>';
                                    }
                                    ?>
                                    </select>
                                    <i class="arrow"></i>
                                <?php } ?>
                                </label>
                            </div><!-- end section -->
                        </div>
                        
                        <div class="colm colm6">
                            <div class="section">
                                <label for="MaxUnit" class="field-label">Enter Quantity</label>
                                <label for="MaxUnit" class="field">
                                    <input required="true" type="text" name="MaxUnit" id="MaxUnit" class="gui-input" placeholder="Enter Quantity">
                                </label>
                            </div><!-- end section -->
                        </div><!-- end .colm6 section -->
                                                               
                    </div><!-- end .frm-row section -->                    
                                                                                             
                </div><!-- end .form-body section -->
                <div class="spacer-t40 spacer-b30">
                	<div class="tagline"><span><input type="submit" name="submit" id="submit" class="button btn-primary" value="Add Line Item"></span></div><!-- .tagline -->
                </div>
                <?php
                if($MAX_QTY->num_rows>0){
                    $MAX_QTY=$MAX_QTY->fetch_assoc();
                    $MAX_QTY=$MAX_QTY['MAX_QTY'];
                }
                ?>
                <input type="hidden" id="MAX_QTY" value="<?php echo $MAX_QTY;?>" />
                <input type="hidden" id="Org_MaxUnit" name="Org_MaxUnit" />
                <input type="hidden" name="EquipmentID" id="EquipmentID" >
                <input type="hidden" name="ItemDescription" id="ItemDescription" />
            </form><br />
            <div class="wrap-2">
                <div class="spacer-t40 spacer-b30">
                	<div class="tagline"><span> ::Line Item(s) </span></div><!-- .tagline -->
                </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table" style="display: none;">
                    <thead><tr><th>Equipment</th><th>Part Number</th><th>Description</th><th>Quantity</th><th>Controls</th></tr></thead>
                    <tbody></tbody>
                    <?php /* while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['Equipment'];?></td>
                            <td><?php echo $item['PartNumber'];?></td>
                            <td><?php echo $item['ItemDescription'];?></td>
                            <td><?php echo $item['MaxUnit'];?></td>
                            <td><a title="Delete" href="javascript:void(0);" onclick="del_rec('<?php echo $item['ItemID'];?>');"><i class="icon-delete"></i></a></td>
                        </tr>
                    <?php } */ ?>
                </table>
                <?php } ?>
                <div class="spacer-t40 spacer-b30">
                	<form name="orderLineItem" id="orderLineItem" method="POST" onsubmit="return validate_lineItem();">
                            <input type="hidden" name="OrderLineItemJSONdata" id="OrderLineItemJSONdata" />
                            <input type="hidden" name="ShipType" id="OrderLineShipType" />
                            <input type="hidden" name="MAX_QTY" id="MAX_QTY" value="<?php echo $MAX_QTY;?>" />
                            <div class="tagline">
                            <span><input type="submit" name="submitOrder" id="submitOrder" class="button btn-primary" value="Submit Order"></span>
                        </div><!-- .tagline -->
                    </form>
                </div>
                <div class="form-footer">
                	<!-- .tagline -->
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
        /*
        if($.trim($('#MaxUnit').val())=="" || $.trim($('#MaxUnit').val())<=0){
            alert("Please update quantity for the line item. It couldn't be zero.");
            $('#MaxUnit').focus();
            return false;
        }
        */
        var JSONstring = "";
        JSONstring = JSON.stringify($('#lineItemForm').serializeObject());
        if(jQuery.inArray( JSONstring, form_details )>0){
            alert("A line item with these details is already exist in the Order. Please update quantity or add a new line item.");
            return false;
        }
        if(parseInt($.trim($('#MaxUnit').val()))>parseInt(currentMaxUnit)){
	        if(!confirm('Quantity Exceeds Maximum Order Value. This order will need to additional approval and may delay order. Please adjust quantity or submit completed order for automatic notification of additional approval.\nClick "Ok" to add this line item or click "Cancel" to adjust/edit the quantity.')){
	            return false;
		}
	}
        form_details.push(JSONstring);
        drawTable();
        //$('#lineItemForm').resetForm();
        //document.getElementById('lineItemForm').reset();
        $('#ItemDescription').html('');
        $('#PartNumber').val('');
        $('#Equipment').val('');
        $('#MaxUnit').val('');
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
            
            if(parseInt(details.MaxUnit)>parseInt(details.Org_MaxUnit)){                
                temp_tabdata+="<tr id='tr_"+index+"'><td style='color:red;'>"+details.Equipment+"</td>";
                temp_tabdata+="<td style='color:red;'>"+details.PartNumber+"</td>";
                temp_tabdata+="<td style='color:red;'>"+details.ItemDescription+"</td>";
                //temp_tabdata+="<td style='color:red;'>"+details.PartNumber+":"+details.ItemDescription+"</td>";
                temp_tabdata+="<td style='color:red;'>"+details.MaxUnit+"</td>";
                temp_tabdata+="<td style='color:red;'><a title='Delete' href='javascript:void(0);' onclick=\"removeRow('tr_"+index+"');\"><i class='icon-delete'></i></a></td></tr>";
            }else{
                temp_tabdata+="<tr id='tr_"+index+"'><td>"+details.Equipment+"</td>";
                temp_tabdata+="<td>"+details.PartNumber+"</td>";
                temp_tabdata+="<td>"+details.ItemDescription+"</td>";
                //temp_tabdata+="<td style='color:red;'>"+details.PartNumber+":"+details.ItemDescription+"</td>";
                temp_tabdata+="<td>"+details.MaxUnit+"</td>";
                temp_tabdata+="<td><a title='Delete' href='javascript:void(0);' onclick=\"removeRow('tr_"+index+"');\"><i class='icon-delete'></i></a></td></tr>";
            }
            
	
            if(parseInt(lineItemMaxUnit)<parseInt(details.MaxUnit)){
                lineItemMaxUnit=parseInt(details.MaxUnit);
            }
            tabdata=temp_tabdata+tabdata;
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