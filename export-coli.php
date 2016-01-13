<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
/*
$conn = mysql_connect('localhost', 'root', '') or die(mysql_error());
$db=mysql_select_db('parts_ordering', $conn) or die(mysql_error());
*/

if(isset($_POST['export_orders']) && isset($_POST['fileName'])){
    
    $orders = json_decode($_POST['export_orders']);
    $file = $_POST['fileName'];
    $time = time();

    //header to give the order to the browser
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename='.$time.'_COLineItems.csv');
    
    //select table to export the data
    $select_table=$mysqli->query("SELECT tblordersmaster.OrderNumber, PartNumber, PromisedDate, 3 AS HardCodedField ,QtyOrdered FROM tblordersmaster LEFT JOIN `tblorderlineitem` ON tblordersmaster.OrderNumber = `tblorderlineitem`.OrderNumber WHERE LineItemApproved = 'Denied' AND tblordersmaster.OrderNumber IN ('".implode("','",$orders)."')");
    //$select_table=$mysqli->query("SELECT * FROM `TABLE 13`");
    //$rows = $select_table->fetch_assoc();
    
    if ($select_table->num_rows>0)
    {
    //getcsv(array_keys($rows));
    }
    while($row = $select_table->fetch_assoc())
    {
        getcsv($row);
    
    /*    
    getcsv($rows);
    $rows = mysql_fetch_assoc($select_table);
    */
    }
    
}
// get total number of fields present in the database
    function getcsv($no_of_field_names)
    {
    $separate = '';
    
    
    // do the action for all field names as field name
    foreach ($no_of_field_names as $field_name)
    {
    if (preg_match('/\\r|\\n|,|"/', $field_name))
    {
    $field_name = '' . str_replace('', $field_name) . '';
    }
    echo $separate . '"' . $field_name . '"';
    
    //sepearte with the comma
    $separate = ',';
    }
    
    //make new row and line
    echo "\r\n";
    }
?>