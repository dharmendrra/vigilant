<?php
require_once 'connect.php';
if(isset($_REQUEST['EQ_ID'])){
    //fetch soil types from database
    $qry="SELECT * FROM `tblitemmaster` WHERE EquipmentID='".$_REQUEST['EQ_ID']."'";
    $result_lineitem = $mysqli->query($qry);//run created query to feed data into mysql database
    if(!$result_lineitem){
        echo('<option value=""> No Part Number </option>');
    }else{
            ?>
            <option value=""> Select Part Number </option>
        <?php
        while($lineitem=$result_lineitem->fetch_assoc()){
            echo '<option value="'.$lineitem['PartNumber'].'" data-value="'.$lineitem['ItemID'].'" data-id="'.$lineitem['EquipmentID'].'" data-desc="'.$lineitem['ItemDescription'].'" data-qty="'.$lineitem['MaxUnit'].'">'.$lineitem['PartNumber'].'</option>';
        }
    }
}
?>