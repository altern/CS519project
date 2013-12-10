<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
//    $scripts_sql = "select script_id, id from scripts where id not in (select script_id from scripts_libraries) ";
//    $scripts_sql = "select script_id, id from scripts where script_id = 'hzawjnll'";
    $tutorials_sql = "SELECT ht.name hashtag, s.id, s.script_id, s.name script_name, s.content content, s.description 
        FROM `hashtags` ht 
        JOIN scripts_hashtags sht ON sht.hashtag_id = ht.id 
        JOIN scripts s ON sht.script_id = s.id 
        where ht.name in ('stepbystep', 'stepByStep', 'tutorials', 'interactiveTutorial', 'docs') 
        -- where ht.name in ('stepbystep', 'stepByStep', 'tutorials', 'interactiveTutorial') 
        order by s.id asc ";
    
    if(!$res = mysql_query($tutorials_sql)) {
        print mysql_error();
    }
    
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($tutorial_row = mysql_fetch_assoc($res)) {
        
        $chunks = detect_chunks($tutorial_row['content']);
        
        $row_count++;
        
        foreach($chunks as $i => $ch) {
            if(add_chunk($ch, $tutorial_row['script_id'], $i+1)) {
                print_if_cli(" $row_count/$total_row_count (". ($i+1) . ") | Added mapping {$tutorial_row['script_id']} => $ch");
            }
        }
    }
    
?>