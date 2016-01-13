<?php
if($_SERVER['SERVER_ADDR']!='127.0.0.1' && $_SERVER['HTTP_HOST']!='localhost'){    
       // Using mysqli (connecting from App Engine)
    // Using mysqli (connecting from App Engine)
    $mysqli = new mysqli("localhost", "dharmendra2014", "Map4noone!", "parts_ordering_staging");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL Livess: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    //echo $mysqli->host_info . "\n";
}else{
    $mysqli = new mysqli("localhost", "root", "", "parts_ordering");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    //echo $mysqli->host_info . "\n";;
    
    $mysqli = new mysqli("127.0.0.1", "root", "", "parts_ordering", 3306);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    
    //echo $mysqli->host_info . "\n";
}


//site
define('SITE_PATH','http://hyperbariclocations.com/vigilant/');//akent@sechristusa.com
date_default_timezone_set('America/Los_Angeles');
ini_set('disable_functions','mail');


//functions
function generate_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

//Ship Type
function renderShipType($view,$selected=""){
    $values=array('Ground','Second Day','Standard Overnight','Priority Overnight','First Overnight');
    if($view!='option'){
        
    }else{
        //$optionHTML='<option value="">Choose Ship Type</option>';
        $optionHTML="";
        foreach($values as $option){
            $optionHTML.='<option '.((isset($selected) && $selected==$option)?'selected':'').' value="'.$option.'">'.$option.'</option>';
        }
        echo $optionHTML;      
    }
}

//Ship Type
function PONumber($mysqli){
    $query = "SELECT PONumber FROM `tblblankerponumber` WHERE NOW() BETWEEN StartDate AND EndDate";
    $rs = $mysqli->query($query);
    if($rs->num_rows>0){
        $row = $rs->fetch_assoc();
        return $row['PONumber'];
    }else{
        return "NO PO ENTERED";
    }
}
?>