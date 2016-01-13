<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";



//fetch data for the line item
$qry="SELECT * FROM  `tblorderlineitem` WHERE `LineItemApproved`!=''";
if(isset($_REQUEST['order'])){$qry.=" AND OrderNumber='".$_REQUEST['order']."'";}
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
Address,SiteName,OrderPlacedByAddress,`to`.DateOrdered ,`to`.OrderNumber,`to`.FirstName,`to`.LastName,`to`.EmailAddress FROM  tblordersmaster `to` LEFT JOIN tblprogramdirectors `tp` ON `to`.ProgramDirectorsID = `tp`.ProgramDirectorsID WHERE `OrderApproved`!=''";
if(isset($_REQUEST['order'])){$qryLI.=" AND OrderNumber='".$_REQUEST['order']."'";}
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
                                                                            
            </div><!-- end .form-body section -->
            <div class="wrap-2">
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> ::Line Item(s) </span></div><!-- .tagline -->
                    </div>
                <?php 
                if($items!='NOT_FOUND'){
                ?>
                <table id="pd-table">
                    <thead><tr><th>Equipment</th><th>Part Number</th><th>Description</th><th>Quantity</th><th>Status</th></tr></thead>
                    <tbody></tbody>
                    <?php  
                    $orderButton='ENABLE';
                    while($item=$result_data->fetch_assoc()){  ?>
                        <tr>
                            <td><?php echo $item['Equipment'];?></td>
                            <td><?php echo $item['PartNumber'];?></td>
                            <td><?php echo $item['ItemDescription'];?></td>
                            <td><?php echo $item['QtyOrdered'];?></td>
                            <td id="status_<?php echo $item['OrdersLineItemID'];?>"><?php echo $item['LineItemApproved'];?></td>
                        </tr>
                    <?php }  ?>
                </table>
                <?php } ?>
                <div class="form-footer">
                    <div class="spacer-t40 spacer-b30">
                        <div class="tagline">
                            <span><input type="button" name="orderAction" id="orderAction" class="button btn-primary" value="Cancel/Change Order"></span>
                        </div><!-- .tagline -->
                    </div>
                </div><!-- end .form-footer section -->
            </div>
        </div><!-- end .smart-forms section -->
        
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->
<!-- delete record -->
<div id="test-popup" class="white-popup mfp-hide smart-forms smart-container">
    <form method="POST" action="" id="orderChangeRequestform" enctype="multipart/form-data">
            	<div class="form-body">
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Place Your request </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        <div class="colm colm4">    
                            <div class="section heading">
                                <label class="field">
                                    <button class="button" disabled="">Choose your action</button>
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        <div class="colm colm2">    
                            <div class="section">
                                <label for="CANCEL" class="option block">
                                    <input type="radio" name="userAction" id="CANCEL" value="CANCEL">
                                    <span class="radio"></span> Cancel
                                </label>
                            </div><!-- end section -->
                        </div>
                        <div class="colm colm2">   
                            <div class="section">
                                <label for="CHANGE" class="option block">
                                    <input type="radio" name="userAction" id="CHANGE" value="CHANGE" checked="true">
                                    <span class="radio"></span> Change
                                </label>
                            </div><!-- end section -->                            
                        </div>
                        
                        <div class="colm colm12">
                            <div class="section">
                            	<label for="RequestNotes" class="field-label">Add Notes</label>
                                <label for="RequestNotes" class="field prepend-icon">
                                	<textarea required="true" class="gui-textarea" id="RequestNotes" name="RequestNotes" placeholder="Add Notes"></textarea>
                                    <label for="RequestNotes" class="field-icon"><i class="fa fa-comments"></i></label>
                                    <span class="input-hint"> 
                                    	<strong>Hint:</strong> Don't be negative or off topic! just be awesome... 
                                    </span>   
                                </label>
                            </div><!-- end section -->
                        </div>
                                                               
                    </div><!-- end .frm-row section -->                    
                                                                                             
                </div><!-- end .form-body section -->
                <div class="spacer-t40 spacer-b30">
                	<div class="tagline"><span><input type="submit" name="submit" id="submit" class="button btn-primary" value="Submit"></span></div><!-- .tagline -->
                </div>
                <input type="hidden" name="OrderNumber" value="<?php echo $result_LI['OrderNumber'];?>" />
                <input type="hidden" name="OrderPlacedbyAddress" value="<?php echo $result_LI['OrderPlacedByAddress'];?>" />
                <input type="hidden" name="DateOrdered" value="<?php echo $result_LI['DateOrdered'];?>" />
                <input type="hidden" name="RequestAuthor" value="<?php echo $_SESSION['USER_INFO']['FacilityName'].'['.$_SESSION['USER_INFO']['BlueBookCode'].']';?>" />
            </form><br />
</div>
<a href="#test-popup" class="open-popup-link" style="display: none;">Show inline popup</a>

<!-- related script-->
<script src="js/pd-s.js" type="text/javascript"></script>

<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="css/magnific-popup.css" />
<!-- Magnific Popup core JS file -->
<script src="js/jquery.magnific-popup.min.js"></script>
<!-- javascript temp table -->
<script type="text/javascript">
$(function() {                    
    $('#orderAction').click(function() {
        if(!confirm('"Your order can only be changed or cancelled same day before order ships. Please enter details of change or cancel request."')){
            return false;
        }else{
            $('.open-popup-link').magnificPopup({type:'inline'});
            $('.open-popup-link').trigger('click');
        }
    });
    
    ///send request via email
    $('#orderChangeRequestform').submit(function(){
        $.post( "ajax/order-change-request.php", $( "#orderChangeRequestform" ).serialize())
          .done(function( response ) {
            console.log(response);
            var data = JSON.parse(response);
            console.log(data);
            if(data.RESPONSE=='SENT'){
                alert('Your request has been sent successfully.');
                $('.mfp-close').trigger('click');
            }else{
                alert('Some error');
            }
          });
          return false;          
    });
});

</script>
</body>
</html>