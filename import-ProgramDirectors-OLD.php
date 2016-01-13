<?php
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '-1');
include_once 'connect.php';
require_once '../../Classes/PHPExcel/IOFactory.php';
$filename = "excel/SechristUserImport.xls";
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
            $mysqli->query("CREATE TABLE IF NOT EXISTS `tblprogramdirectors` (
                                  `ProgramDirectorsID` int(11) NOT NULL AUTO_INCREMENT,
				  `FirstName` varchar(13) DEFAULT NULL,
				  `LastName` varchar(19) DEFAULT NULL,
				  `Title` varchar(25) DEFAULT NULL,
				  `FacilityName` varchar(35) DEFAULT NULL,
				  `EmailAddress` varchar(41) DEFAULT NULL,
				  `PhoneNumber` varchar(16) DEFAULT NULL,
				  `BlueBookCode` varchar(5) DEFAULT NULL,
				  `CompanyAddress1` varchar(49) DEFAULT NULL,
				  `CompanyAddress2` varchar(28) DEFAULT NULL,
				  `City` varchar(20) DEFAULT NULL,
				  `State` varchar(2) DEFAULT NULL,
				  `Country` varchar(3) DEFAULT NULL,
				  `Zip` varchar(9) DEFAULT NULL,
				  `EEID` varchar(12) DEFAULT NULL,
				  `Supervisor` varchar(12) DEFAULT NULL,
				  `Log-inID` varchar(23) DEFAULT NULL,
				  `Password` varchar(255) DEFAULT NULL,
				  `EEStatus` varchar(1) DEFAULT NULL,
				   PRIMARY KEY (`ProgramDirectorsID`),
                                  KEY `BlueBookCode` (`BlueBookCode`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
                                
           /* $mysqli->query("CREATE TABLE IF NOT EXISTS `tblprogramdirectors` (
                                  `ProgramDirectorsID` int(11) NOT NULL AUTO_INCREMENT,
                                  `FirstName` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
                                  `LastName` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
                                  `Title` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
                                  `EmailAddress` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                                  `Password` varchar(255) NOT NULL,
                                  `PhoneNumber` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
                                  `BlueBookCode` varchar(14) CHARACTER SET utf8 DEFAULT NULL,
                                  `EEID` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
                                  `Log-inID` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
                                  `EEStatus` varchar(9) CHARACTER SET utf8 DEFAULT NULL,
                                  PRIMARY KEY (`ProgramDirectorsID`),
                                  KEY `BlueBookCode` (`BlueBookCode`)
                                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");  
                                */          
            $mysqli->query("TRUNCATE TABLE `tblprogramdirectors`");
        }else{
            $count=0;$address="";
            $skip = array(3,7,8,9,10,11,12);
            for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                //if(in_array($col,$skip)){continue;}
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                //if(trim($val)){$array[$row][$nameArray[$count]] = $val;$count++;}
                $array[$row][$count] = $val;
                $fieldValues[$count] = $mysqli->real_escape_string($val);
                $count++;
            }$temp = $fieldValues[$count-1];$fieldValues[$count-1] = $fieldValues[$count-2];$fieldValues[$count] = $temp;
            $qry="INSERT INTO `tblprogramdirectors` (`FirstName`, `LastName`, `Title`, `FacilityName`, `EmailAddress`, `PhoneNumber`, `BlueBookCode`, `CompanyAddress1`, `CompanyAddress2`, `City`, `State`, `Country`, `Zip`, `EEID`, `Supervisor`, `Log-inID`, `Password`, `EEStatus`) VALUES('".implode("','",$fieldValues)."');";
            if(!$mysqli->query($qry)){
                //mail('dharmendra402@gmail.com','QUERY FAILS.-http://sechristmanuals.com/','DATA:'.$qry.'    ERROR:'.$mysqli->error);
                echo $qry.'<br><br>'.$mysqli->error;
                break;
            }
            
        }
        $qry="UPDATE `tblprogramdirectors` SET `Password` = '123456'";
        $mysqli->query($qry);
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