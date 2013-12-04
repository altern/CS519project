<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
//    $scripts_sql = "select script_id, id from scripts where id not in (select script_id from scripts_libraries) ";
//    $scripts_sql = "select script_id, id from scripts where script_id = 'hzawjnll'";
    $tutorials_sql = "select script_id, id from scripts";
    $res = mysql_query($tutorials_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($tutorial_row = mysql_fetch_assoc($res)) {
        $row_count++;
        $url = "http://touchdevelop.com/api/" . $tutorial_row['script_id'];
        $json = file_get_contents($url);
        $script_info = json_decode($json, true);
        print_if_cli(" $row_count / $total_row_count | $url : " . count($script_info['librarydependencyids']));
        if(add_scripts_libraries_mapping($script_info, $tutorial_row['id'])) {
            $mapping_count += count($script_info['librarydependencyids']);
        }
    }
    print $mapping_count;
?>