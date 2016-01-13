<?php
session_start();
require_once('connect.php');
/*
$conn = mysql_connect('localhost', 'root', '') or die(mysql_error());
$db=mysql_select_db('parts_ordering', $conn) or die(mysql_error());
*/

$csv = "";
$file = "COLineItems.csv";
    //header to give the order to the browser
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename='.$file);
    
    //select table to export the data
    $select_table=$mysqli->query("SELECT tblordersmaster.OrderNumber, PartNumber, PromisedDate, 3 AS HardCodedField ,QtyOrdered FROM tblordersmaster LEFT JOIN `tblorderlineitem` ON tblordersmaster.OrderNumber = `tblorderlineitem`.OrderNumber WHERE tblordersmaster.OrderNumber IN (SELECT OrderNumber FROM `tblordersmaster` tom LEFT JOIN tblprogramdirectors tpd ON tom.ProgramDirectorsID = tpd.ProgramDirectorsID WHERE tom.`ProcessedFS` = 'No')");
    //$select_table=$mysqli->query("SELECT * FROM `TABLE 13`");
    //$rows = $select_table->fetch_assoc();
    
    $fileRS = fopen($file,'w');
    if ($select_table->num_rows>0)
    {
    //getcsv(array_keys($rows));
    }
    while($row = $select_table->fetch_assoc())
    {
        $csv = getcsv($csv,$row);
    
    /*    
    getcsv($rows);
    $rows = mysql_fetch_assoc($select_table);
    */
    }
    
    $oQuery = "UPDATE tblordersmaster SET ProcessedFS = 'Yes' WHERE tblordersmaster.ProcessedFS = 'No'";
    $oresult = $mysqli->query($oQuery) or die($mysqli->error);//run created query to feed data into mysql database
    
    
// get total number of fields present in the database
    function getcsv($csv,$no_of_field_names)
    {
    $separate = '';
    
    
    // do the action for all field names as field name
    foreach ($no_of_field_names as $field_name)
    {
    if (preg_match('/\\r|\\n|,|"/', $field_name))
    {
    $field_name = '' . str_replace('', $field_name) . '';
    }
    $csv .= $separate . '"' . $field_name . '"';
    
    //sepearte with the comma
    $separate = ',';
    }
    
    //make new row and line
    $csv .= "\r\n";
    return $csv;
    }
    fwrite($fileRS,$csv);
    fclose($fileRS);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    copy('COLineItems.csv','extract/COLineItems.csv');
    unlink('COLineItems.csv');
?>