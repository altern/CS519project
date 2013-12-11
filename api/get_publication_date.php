<?php

include_once 'config.php';
include_once 'functions.php';

function get_base($script_id) {
    $url = "http://touchdevelop.com/api/$script_id/base";
    print "fetching $url ... \n";
    $json = file_get_contents($url);
    if(!empty($json)) {
        $json_arr = json_decode($json, true);
        $base_script_id = $json_arr['id'];
        $base_date = get_base($base_script_id);
        if($base_date === false) {
            $date = date('d/m/Y H:s', intval($json_arr['time']));
            //print " $date\n";
            return $date;
        } else {
            return $base_date;
        }
    } else {
        return false;
    }
}

$script_ids = array(
//    'wieya',
//    'esih',
//    'rdjh',
//    'ybkm',
//    'nuri',
//    'inebpzcv',
//    'vvpx',
//    'ldsoa',
    
    'lfzo',
    'bbxr',
    'psbd',
    'cbqyhblv',
    'tuxi',
    'wwcv',
    'uweda',

);

foreach($script_ids as $script_id ) {
    $publication_date = get_base($script_id);
    print_if_cli("$script_id => $publication_date");
}