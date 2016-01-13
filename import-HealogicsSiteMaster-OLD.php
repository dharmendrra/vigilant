<?php
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '-1');
include_once 'connect.php';
require_once '../../Classes/PHPExcel/IOFactory.php';
$filename = "excel/HealogicsSiteMaster.xls";
$objPHPExcel = PHPExcel_IOFactory::load($filename);
//echo 'enter';
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
    /*
    echo "<br>The worksheet ".$worksheetTitle." has ";
    echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
    echo ' and ' . $highestRow . ' row.';
    echo '<br>Data: <table border="1"><tr>';
    */
    $previousAddress="";$getLatLong="";$apicall=0;
    for ($row = 1; $row <= $highestRow; ++ $row) {
        if($row==1){
            for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                $fields[]=$val;
            }
            $mysqli->query("CREATE TABLE IF NOT EXISTS `tblhealogicssitemaster` (
                                  `CustomerID` varchar(10) DEFAULT NULL,
                                  `ShipToID` varchar(8) DEFAULT NULL,
                                  `FacilityName` varchar(55) DEFAULT NULL,
                                  `CompanyAddress1` varchar(60) DEFAULT NULL,
                                  `CompanyAddress2` varchar(59) DEFAULT NULL,
                                  `City` varchar(15) DEFAULT NULL,
                                  `State` varchar(5) DEFAULT NULL,
                                  `Zip` varchar(10) DEFAULT NULL,
                                  `Country` varchar(7) DEFAULT NULL,
                                  `BlueBookCode` varchar(12) DEFAULT NULL,
                                  `FacilityStatus` varchar(14) DEFAULT NULL,
                                  KEY `BlueBookCode` (`BlueBookCode`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");            
            $mysqli->query("TRUNCATE TABLE `tblhealogicssitemaster`");
        }else{
            $count=0;$address="";
            for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                //if(trim($val)){$array[$row][$nameArray[$count]] = $val;$count++;}
                $array[$row][$count] = $val;
                $fieldValues[$count] = $mysqli->real_escape_string($val);
                $count++;
            }
            $qry="INSERT INTO `tblhealogicssitemaster` (`CustomerID`, `ShipToID`, `FacilityName`, `CompanyAddress1`, `CompanyAddress2`, `City`, `State`, `Zip`, `Country`, `BlueBookCode`, `FacilityStatus`) VALUES('".implode("','",$fieldValues)."');";
            if(!$mysqli->query($qry)){
                //mail('dharmendra402@gmail.com','QUERY FAILS.-http://sechristmanuals.com/','DATA:'.$qry.'    ERROR:'.$mysqli->error);
                echo $qry.'<br><br>'.$mysqli->error;
                break;
            }
            
        }
    }
}

echo 'apicalled: '.$apicall;
echo '<pre>';
print_r($array);
echo '</pre>';
echo '<pre>';
print_r($fields);
echo '</pre>';

?>