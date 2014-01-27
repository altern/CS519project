<?php

include_once ('config.php');
include_once ('functions.php');

$sql = get_missing_scripts_query();

if(!$res = mysql_query($sql)) {
    die(mysql_error() . "<br />" . $sql);
}
$total_count = mysql_num_rows($res);

header('Content-Type: text/plain');
echo $total_count;



