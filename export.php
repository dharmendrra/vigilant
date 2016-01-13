<?php
session_start();
include_once ('auth.php');
require_once('connect.php');
$result="";$items="";
//check if form posted
if(isset($_POST['export_orders'])){
    
    $orders = json_decode($_POST['export_orders']);
    $file = $_POST['fileName'];
    $time = time();
    
    if($file=='CO'){
        //select Order Number
        $oQuery = "SELECT OrderNumber,CustomerID,ShipToID,PONumber,ShipType FROM `tblordersmaster` WHERE OrderNumber IN ('".implode("','",$orders)."')";
        $oresult = $mysqli->query($oQuery) or die($mysqli->error);//run created query to feed data into mysql database
        
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$time.'_CreateCO.csv');
        
        // create a file pointer connected to the output stream
        $output = fopen($time.'_CreateCO.csv', 'w');
        
        // output the column headings
        fputcsv($output, array('OrderNumber', 'CustomerID', 'ShipToID', 'PONumber', 'ShipType'));
        
        // loop over the rows, outputting them
        while($on=$oresult->fetch_assoc()){ fputcsv($output, $on); }
        fclose($output);
        readfile($time.'_CreateCO.csv');
    }
    else if($file=='COLI'){
        $oQuery = "SELECT tblordersmaster.OrderNumber, PartNumber, PromisedDate, 3 AS HardCodedField ,QtyOrdered FROM tblordersmaster LEFT JOIN `tblorderlineitem` ON tblordersmaster.OrderNumber = `tblorderlineitem`.OrderNumber WHERE LineItemApproved = 'Denied' AND tblordersmaster.OrderNumber IN ('".implode("','",$orders)."')";
        $oresult = $mysqli->query($oQuery) or die($mysqli->error);//run created query to feed data into mysql database
        
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$time.'_COLineItems.csv');
        
        // create a file pointer connected to the output stream
        $output = fopen($time.'_COLineItems.csv', 'w');
        
        // output the column headings
        fputcsv($output, array('OrderNumber', 'PartNumber', 'PromisedDate', 'HardCodedField', 'QtyOrdered'));
        
        // loop over the rows, outputting them
        while($on=$oresult->fetch_assoc()){ fputcsv($output, $on); }
        fclose($output);
        readfile($time.'_COLineItems.csv');
        
        $oQuery = "UPDATE tblordersmaster SET ProcessedFS = 'Yes' WHERE tblordersmaster.OrderNumber IN ('".implode("','",$orders)."')";
        $oresult = $mysqli->query($oQuery) or die($mysqli->error);//run created query to feed data into mysql database
    }
    
    
}