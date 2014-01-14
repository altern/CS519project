<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$start_url = 'http://www.touchdevelop.com/api/scripts';
$script_to_find = 'fojb';

$continuation = '';

$temp_continuation = '';

while(!$continuation) {
    if($temp_continuation) {
        $url = $start_url . '?continuation=' . $temp_continuation;
    } else {
        $url = $start_url;
    }
    print "Fetching $url ...\n";
    $scripts_list_json = json_decode(file_get_contents($url), true);
    foreach($scripts_list_json['items'] as $item) {
        if( $item['id'] == $script_to_find ) {
            $continuation = $temp_continuation;
            //break;
        }
    }
    $temp_continuation = $scripts_list_json['continuation'];
}
print "     Found continuation for $script_to_find: $continuation\n";

