<?php

include_once ('config.php');
include_once ('functions.php');

$sql = get_missing_scripts_query();

$res = mysql_query($sql);

$total_count = mysql_num_rows($res);
$count = 1;

while($row = mysql_fetch_row($res)) {
    $script_id = $row[0];
    if($id = download_script($script_id)) {
        print_if_cli("[$count/$total_count]: script $script_id has been added to the database with id $id");
    }
    $count++;
}




