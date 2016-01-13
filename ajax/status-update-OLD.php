<?php
require_once '../connect.php';

//update status of order - pending/approved
if(isset($_REQUEST['type']) && $_REQUEST['type']=='ORDER'){
    if(isset($_REQUEST['status']) && isset($_REQUEST['record_id'])){
        //types from database
        $status = ($_REQUEST['status']=='DENY'?'Denied':'Approved');
        $qry="UPDATE `tblordersmaster` SET `OrderApproved` = '".$status."' WHERE `OrdersMasterID`='".$_REQUEST['record_id']."'";
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
}

//update status of line item - pending/approved
if(isset($_REQUEST['type']) && $_REQUEST['type']=='LINE_ITEM'){
    if(isset($_REQUEST['status']) && isset($_REQUEST['record_id'])){
        //types from database
        $status = ($_REQUEST['status']=='DENY'?'Denied':'Approved');
        $qry="UPDATE `tblorderlineitem` SET `LineItemApproved` = '".$status."' WHERE `OrdersLineItemID`='".$_REQUEST['record_id']."'";
        $result_lineitem = $mysqli->query($qry);//run created query to feed data into mysql database
        if(!$result_lineitem){
            $data['RESPONSE']= 'ERROR';
        }else{
            if($status=='Approved'){
                $data['RESPONSE']= 'APPROVED';
            }else{
                $data['RESPONSE']= 'DENIED';
                notifyProgramDirector($_REQUEST['record_id'],$mysqli);
            }
            $data['SET_BUTTON']=getLineItem($_REQUEST['record_id'],$mysqli);
        }
        echo json_encode($data);
        
    }
}

//get all line item approved or not response
function getLineItem($lineitemid,$mysqli){
    //fetch data for the line item
    $qry="SELECT * FROM (SELECT COUNT(*) AS `Rows`, `LineItemApproved` FROM `tblorderlineitem` WHERE `OrderNumber`IN (SELECT OrderNumber FROM  `tblorderlineitem` WHERE `OrdersLineItemID`='".$lineitemid."') GROUP BY `LineItemApproved` ORDER BY `LineItemApproved`) AS templim";
    
    $qry="SELECT * FROM `tblorderlineitem` WHERE `OrderNumber`IN (SELECT OrderNumber FROM `tblorderlineitem` WHERE `OrdersLineItemID`='".$lineitemid."') AND `LineItemApproved`='Pending' GROUP BY `LineItemApproved` ORDER BY `LineItemApproved`";
    $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result_data->num_rows>1){return 'NOT_DONE';}
    else if($result_data->num_rows==0){return 'ALL_DONE';}
    
}

//notify Program Director with an email
function notifyProgramDirector($lineitem_id,$mysqli){
    //get Program Director info
    $qryLI="SELECT * FROM `tblordersmaster`,`tblorderlineitem` WHERE  `tblordersmaster`.`OrderNumber`=`tblorderlineitem`.`OrderNumber`";
    if(isset($lineitem_id)){$qryLI.=" AND tblorderlineitem.`OrdersLineItemID`='".$lineitem_id."'";}
    $result_rs_LI = $mysqli->query($qryLI) or die($mysqli->error.' '.$qryLI);
    if($result_rs_LI->num_rows>0){
        $result_LI=$result_rs_LI->fetch_assoc();
        $to = $result_LI['EmailAddress'];
        $subject = "Parts Ordering:: Line Item Denied";//
        //CustomerServiceNow@TekkiesU.com
        $message = "
            <html>
            <head>
            <title>SECHRIST INDUSTRIES,INC.</title>
            </head>
            <body>
                <p>This is a notification that an item has been denied on the Order Number <u>".$result_LI['OrderNumber']."</u>. Please contact Healogics Administration for details.</p>
                <table style='border-collapse: collapse;'>
                <tr style='background-color: #DEE0EC;'>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Equipment</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Part Number</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Description</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Quantity</th>
                    <th style='border: 1px solid rgb(123, 148, 210);padding: 5px 30px;'>Status</th>
                </tr>
                <tr>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$result_LI['Equipment']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$result_LI['PartNumber']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$result_LI['ItemDescription']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$result_LI['QtyOrdered']."</td>
                    <td style='border: 1px solid rgb(123, 148, 210);padding: 10px 30px;'>".$result_LI['LineItemApproved']."</td>
                </tr>
                </table>
            <p><a href='".SITE_PATH."'>Click here</a> to visit our portal.</p>
            </body>
            </html>
            ";//Customer Service
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
        $headers .= 'Cc: WebSiteOrders@Sechristusa.com' . "\r\n";
        $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
        
        //$to='dharmendra402@gmail.com';
        mail($to,$subject,$message,$headers);//Program Director
    }
}

?>