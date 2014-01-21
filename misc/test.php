<?php

include_once('../api/functions.php');

$url = '';

$tutorial_id = 'esih';
$tutorial_source = file_get_contents("http://www.touchdevelop.com/api/$tutorial_id/text");
$chunks = detect_chunks($tutorial_source);

$scripts_to_test = array('oepu','dwyva', 'gejr');

print generateWhereClauseForTutorials();

return ;

foreach($scripts_to_test as $script_id) {
    $url = "http://www.touchdevelop.com/api/$script_id/text";
    $script_source = file_get_contents($url );
//    print_if_cli("Trying to find chunks from tutorial $tutorial_id in script $script_id... ");
//    $result = find_chunks(explode("\n", $script_source), $chunks);
//    if($result) {
//        print_if_cli("  Found chunk at line $result" );
//    } else {
//        print_if_cli("  Not found");
//    }
    print_r(array('id' => $script_id, 'url' => $url, 'interactive' => is_interactive($script_source)));
}


?>