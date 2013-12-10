<?php

include_once('functions.php');

$url = '';

$tutorial_id = 'esih';
$tutorial_source = file_get_contents("http://www.touchdevelop.com/api/$tutorial_id/text");
$chunks = detect_chunks($tutorial_source);

$scripts_to_test = array('uvzeeoij','vebbxyvk','ljyqxrjn','wfextfvj','iucvkant', 'yibpzupx', 'wgxzovkv',
'jykqhxsu',
'llyyjpxx',
'rhnsa',
'nfjn');

foreach($scripts_to_test as $script_id) {
    $script_source = file_get_contents("http://www.touchdevelop.com/api/$script_id/text");
    print_if_cli("Trying to find chunks from tutorial $tutorial_id in script $script_id... ");
    $result = find_chunks(explode("\n", $script_source), $chunks);
    if($result) {
        print_if_cli("  Found chunk at line $result" );
    } else {
        print_if_cli("  Not found");
    }
}
?>