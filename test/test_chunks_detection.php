<?php

include_once('../api/functions.php');

$url = '';

//$script_ids = array('gjrn');
$script_ids = array('vozf');

foreach($script_ids as $script_id) {

    $tutorial_source = file_get_contents("http://www.touchdevelop.com/api/$script_id/text");
    $chunks = detect_chunks($tutorial_source);
    print_r($chunks);
    //$scripts_to_test = array('oepu','dwyva', 'gejr');
    
    
}

$a = array
(
    'codeâ†’tree(100, 5);',
    'meta private;',
    'codeâ†’tree(100, 3);
meta private;'
);
$b = array
(
    'codeâ†’tree(100, 5);',
    'meta private;',
    'codeâ†’tree(100, 3);
meta private;'
);
echo $a == $b;
?>