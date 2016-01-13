<?php
require_once '../connect.php';
if(isset($_POST)){
    
    //fetch data for the program directors
    $qry="SELECT tpd.`Log-inID`,tpd.EmailAddress FROM `tblprogramdirectors` tpd LEFT JOIN `tblhealogicssitemaster` thsm ON `tpd`.BlueBookCode = `thsm`.BlueBookCode WHERE EEStatus='A' AND `Log-inID` ='".$_POST['loginid']."'";
    $result_data = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
    if($result_data->num_rows>0){
        $data = $result_data->fetch_assoc();
    
        
        $to = $data['EmailAddress'];
        $subject = "Parts Ordering:: Your password reset link";//Program Director
        //CustomerServiceNow@TekkiesU.com
        $password=generate_password(10);
        $message = "
            <html>
            <head><title>SECHRIST INDUSTRIES,INC.</title></head>
            <body>
            <p>Your reset password link is given in the email. Please <a href='".SITE_PATH."reset-password.php?token=".base64_encode($password)."&email=".$data['EmailAddress']."'>click here</a> to reset your password. This link is valid for 2 days.<br><br> If you have additional questions or concerns regarding account please call or email Customer Service at (714) 555-1212 and CustomerServiceNow@TekkiesU.com</p>
            <table style='border-collapse: collapse;'><tr style='background-color: #DEE0EC;'></tr></table>
            </body>
            </html>
            ";//Program Director
        
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
        $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
        
        
        if(mail($to,$subject,$message,$headers)){
            $mail_info_sent=true;
                //update query- activate program director and save added details in the database
                $insertQuery = "INSERT INTO `token` (`Token`, `EmailAddress`,`Token_Gen_Time`) VALUES ('".$password."', '".$data['EmailAddress']."','".date('Y-m-d h:i:s')."');";
        $mysqli->query($insertQuery);//run created query to feed data into mysql database
            $return['RESPONSE']= 'SENT';
        }else{
            $return['RESPONSE']= 'ERROR';
        }
        echo json_encode($return);
    }else{
        $return['RESPONSE']= 'ERROR_FIND';
        echo json_encode($return);
    }
}
?>