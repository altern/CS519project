<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
    $tutorials_sql = "SELECT distinct s.id, ht.name, s.script_id, s.description, s.name script_name, s.content content 
        FROM scripts s 
        JOIN scripts_hashtags sht ON sht.script_id = s.id 
        join `hashtags` ht 
		on sht.hashtag_id = ht.id 
		where ht.name in ('stepbystep', 'stepByStep', 'tutorials', 'interactiveTutorial') 
        order by s.id asc";
    
    $res = mysql_query($tutorials_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($tutorial_row = mysql_fetch_assoc($res)) {
        $row_count++;
        
        $features_sql = "select f.id, f.name from features f join scripts_features sc on f.id = sc.feature_id where sc.script_id = ".$tutorial_row['id'];
        $res2 = mysql_query($features_sql);
        $features = array();
        while($features_row = mysql_fetch_assoc($res2)) {
            $features[] = $features_row['name'];
        }
        
        $hashes = get_hashes($tutorial_row['description']);
        
        if(add_scripts_tutorials_mapping($tutorial_row['id'], $tutorial_row['script_name'], $hashes, $features, $tutorial_row['content'])) {
            print_if_cli(" $row_count / $total_row_count | Added scripts -> tutorials mapping mapping for ".$tutorial_row['script_id']. " (".$tutorial_row['id'].") ");
            $mapping_count++;
        }
    }
    print_if_cli("Added number of mappings: " . $mapping_count);
?>