<?php
require_once '../connect.php';
if(isset($_REQUEST['qty']) && isset($_REQUEST['record_id'])){
    //types from database
    $qry="UPDATE `tblorderlineitem` SET `QtyOrdered` = '".$mysqli->real_escape_string($_REQUEST['qty'])."' WHERE `OrdersLineItemID`='".$mysqli->real_escape_string($_REQUEST['record_id'])."'";
    $result_lineitem = $mysqli->query($qry);//run created query to feed data into mysql database
    if(!$result_lineitem){
        $return['RESPONSE']= 'ERROR';
    }else{
        $return['RESPONSE']= 'UPDATED';
    }
    echo json_encode($return);
}
?>