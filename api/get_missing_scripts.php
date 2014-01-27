<?php

include_once ('config.php');
include_once ('functions.php');

$sql = get_missing_scripts_query();

$CHUNK_SIZE = 20;
$start = 0;

if(isset($_REQUEST['continuation']) && $_REQUEST['continuation']) {
    $start = intval($_REQUEST['continuation']);
} 
$sql .= " LIMIT {$start}, {$CHUNK_SIZE} ";

if(!$res = mysql_query($sql)) {
    die(mysql_error() . "<br />" . $sql);
}

$data = array(
    'continuation' => $start + $CHUNK_SIZE,
    'items' => array()
);

while($row = mysql_fetch_row($res)) {
    $data['items'][] = $row[0];
}

header('Content-Type: application/json');
echo json_encode($data);



