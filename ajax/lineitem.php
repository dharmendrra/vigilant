<?php
require_once '../connect.php';
if(isset($_REQUEST['status']) && isset($_REQUEST['record_id'])){
    //types from database
    $status = ($_REQUEST['status']=='DENY'?'Denied':'Approved');
    $qry="UPDATE `tblorderlineitem` SET `LineItemApproved` = '".$status."' WHERE `OrdersLineItemID`='".$_REQUEST['record_id']."'";
    $result_lineitem = $mysqli->query($qry);//run created query to feed data into mysql database
    if(!$result_lineitem){
        echo 'ERROR';
    }else{
        if($status=='Approved'){
            echo 'APPROVED';
        }else{
            echo 'DENIED';
        }
    }
}
?>