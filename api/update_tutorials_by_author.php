<?php
	include('config.php');
	include('functions.php');
	error_reporting(E_ERROR);
	
    mysql_query('truncate table tutorials_by_author;');
    mysql_query('truncate table scripts_tutorials;');
    
	$tutorials_sql = "
	SELECT distinct s.id id, s.name name
    FROM `hashtags` ht 
    JOIN scripts_hashtags sht ON sht.hashtag_id = ht.id 
    JOIN scripts s ON sht.script_id = s.id 
    where 
    ht.name in " . generateWhereClauseForTutorials();
	
	$res = mysql_query($tutorials_sql);
	$chunks = array();
    while($script = mysql_fetch_assoc($res)) {
		
		$chunk_sql = "select c.content from chunks c 
		join scripts_chunks sc on sc.chunk_id = c.id
		join scripts s on s.id = sc.script_id
		where s.id = ".$script['id']. "
		order by sc.seq asc";
        
		$res2 = mysql_query($chunk_sql);
		while($chunk = mysql_fetch_assoc($res2)) {
			if(!isset($chunks[$script['id']])) {
				$chunks[$script['id']] = array($chunk['content']);
			} else {
				$chunks[$script['id']][] = $chunk['content'];
			}
		}
	}
	
	$all_scripts_except_tutorials_sql = "select id, content from scripts where id not in (select script_id from tutorials)";
	
	$res3 = mysql_query($all_scripts_except_tutorials_sql);
	$total_matches = 0;
	$partial_matches = 0;
	while($script = mysql_fetch_assoc($res3)) {
		$lines = explode("\n", $script['content']);
		foreach($chunks as $script_id => $chunks_list) {
            if(count(strip_statement_with_curly_braces($chunks_list)) < 3) {
                print_if_cli("Skipping set of chunks. " .count(strip_statement_with_curly_braces($chunks_list)). " chunk(s) is not enough to establish mapping ");
                continue;
            }
			if(find_chunks($lines, $chunks_list)) {
				print_if_cli("FOUND all chunks (from script id " .$script_id. ") in script with id " . $script['id']);
                $is_completed = 1;
				if(map_tutorials_to_scripts($script_id, $script['id'], $is_completed)) {
					print_if_cli("	Mapping has been added to the database" );
				}
				$total_matches++;
			} elseif (count($chunks_list) > 5) {
				$first_5_chunks = array_slice($chunks_list, 0, 5);
				if(find_chunks($lines, $first_5_chunks)){
					print_if_cli("FOUND first 5 chunks (from script id " .$script_id. ") in script with id " . $script['id'] );
                    $is_completed = 0;
					if(map_tutorials_to_scripts($script_id, $script['id'], $is_completed)) {
						print_if_cli("	Partial mapping has been added to the database");
					}
					$partial_matches++;
				}
			}
		}
	}
    
	print_if_cli("Total number of matches: ".$total_matches);
	print_if_cli("Number of partial matches: ".$partial_matches);
	
?>