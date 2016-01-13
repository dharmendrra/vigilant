<?php
require_once '../connect.php';
if(isset($_POST)){
    //types from database
            $message = '
                <html>
                <head><title>SECHRIST INDUSTRIES,INC.</title></head>
                <body>
                <p>An Order Change Request has been made by <b>'.$_REQUEST['RequestAuthor'].'</b></p>
                <table class="poTab" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">OrderNumber</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['OrderNumber'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">OrderPlacedbyAddress</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['OrderPlacedbyAddress'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">DateOrdered</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['DateOrdered'].'</td>
                    </tr>
                    <tr>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">User Request</td>
                        <td style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['userAction'].' Order</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;">Notes</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;">'.$_REQUEST['RequestNotes'].'</td>
                    </tr><style>
                .poTab{border-collapse: collapse;}
                .poTab tr{padding: 2px;}
                .poTab td:nth-child(even){padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;}
                .poTab td:nth-child(odd){padding: 7px 10px;color: #777;border: 1px solid #d9d9d9;font-weight: bold;}
                </style>
                </table>
                <p><a href="'.SITE_PATH.'">Click here</a> for the follow up on request.</p>
                </body>
                </html>
                ';//Program Director
            
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <DoNotReply@SechristIndustries.com>' . "\r\n";
            $headers .= 'Bcc: devoo055@gmail.com' . "\r\n";
            
            $to = "WebOrderChangeRequest@Sechristusa.com";
            //$to = "dharmendra402@gmail.com";
            $subject = "Parts Ordering:: Order Change Request #".$_REQUEST['OrderNumber'];//Program Director
            if(mail($to,$subject,$message,$headers)){
                $return['RESPONSE']= 'SENT';
            }else{
                $return['RESPONSE']= 'ERROR';
            }
    echo json_encode($return);
}
?>