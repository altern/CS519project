<?php

include_once('config.php');
include_once('functions.php');

error_reporting(E_ALL);

function getData($url, $data) {
    $request_url = $url."?".http_build_query($data);
    print_if_cli("Fetching from $request_url ...");
    $json = file_get_contents($request_url);
    $json_array = json_decode($json, TRUE);
    foreach($json_array['items'] as $item) {
        $script_id = $item['id'];
        $successors_json = file_get_contents("http://www.touchdevelop.com/api/$script_id/successors");
        $successors_array = json_decode($successors_json, true);
        // save script if it does not have successors
        if(empty($successors_array['items'])) {
            print_if_cli("  Downloading script $script_id...");
            if(download_script($script_id)) {
                print_if_cli("  DONE!");
            } else {
                print_if_cli("  FAILED! :( ");
            }
        }
    }
    
    if(!empty($json_array['continuation']) && $json_array['continuation'] > 0) {
        $data['continuation'] = $json_array['continuation'];
        getData($url, $data);
    }
}
$scriptsUrl = 'http://www.touchdevelop.com/api/scripts';
$data = array( 'count' => 100);

getData($scriptsUrl, $data);

?>
