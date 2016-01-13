<?php 
$host =  $_SERVER['HTTP_HOST'];

 $ip = gethostname();

 echo $ip; 
if ( preg_match("/dev/", $host) ) {
    $output = `/usr/bin/git pull origin dev-rc1`;
}
else if ( preg_match("/staging/", $host) ) {
    $output = `/usr/bin/git pull origin staging`;
}
else {
    $output = `/usr/bin/git pull`;
}


echo "$output";
?>

