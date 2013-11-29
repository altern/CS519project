<?php
	include('config.php');
	include('functions.php');
	error_reporting(E_ERROR);
	
	function find_chunks($lines, $chunks) {
		$last_chunk = array_slice($chunks, -1)[0];
		// print_r($last_chunk);
		foreach($lines as $match_line_num => $line) {
			
			if(strpos($line, $last_chunk) !== FALSE) {
				//echo "Found chunk '$last_chunk' at line $match_line_num: '$line' \n";
				if(count($chunks) > 1) {
					$without_last_chunk = array_slice($chunks, 0, count($chunks) - 1);
					$prev_match_line_num = find_chunks($lines, $without_last_chunk);
					if($prev_match_line_num < $match_line_num) {
						return $match_line_num;
					} else {
						echo "Order of chunks does not match\n";
						return FALSE;
					}
				} else {
					return $match_line_num;
				}
			}
		}
		return FALSE;
	}
	
	function map_tutorials_to_scripts($tutorial_id, $script_id, $is_completed) {
		$query = 'select id 
				from tutorials_by_author 
				where author_id = (select author_id from scripts where id = ' . $script_id . ') 
				and tutorial_id = (select id from tutorials where script_id = ' . $tutorial_id . ')
				and is_completed = ' . $is_completed ;
		// print $query. "\n";
		$res = mysql_query($query);
		$tutorials_by_author_id = mysql_result($res, 0);
		
		if(empty($tutorials_by_author_id)) {
			$query = 'insert into tutorials_by_author (author_id, tutorial_id, is_completed) values (
				(select author_id from scripts where id = ' . $script_id . '),
				(select id from tutorials where script_id = ' . $tutorial_id . '),
				'.$is_completed.'
			)';
			//print $query."\n";
			$res2 = mysql_query($query);
			if(!$res2) {
				echo mysql_error($res2);
				return FALSE;
			}
		}
		
		$query = 'select id 
				from scripts_tutorials 
				where script_id = ' . $script_id . ' 
				and tutorial_id = ' . $tutorial_id ;
		//print $query . "\n";
		$res3 = mysql_query($query);
		
		$scripts_tutorials_id = mysql_result($res3, 0);
		
		if(empty($scripts_tutorials_id)) {
			$res4 = mysql_insert('scripts_tutorials', array(
				'script_id' => $script_id,
				'tutorial_id' => $tutorial_id
			));
			if(!$res4) {
				echo mysql_error($res4);
				return FALSE;
			}
		}
		return TRUE;
	}
	
	$scripts_sql = "
	SELECT distinct s.id id, s.name name
    FROM `hashtags` ht 
    JOIN scripts_hashtags sht ON sht.hashtag_id = ht.id 
    JOIN scripts s ON sht.script_id = s.id 
    where 
    ht.name in ('tutorials', 'stepbystep', 'stepByStep')";
	
	$res = mysql_query($scripts_sql);
	$chunks = array();
    while($script = mysql_fetch_assoc($res)) {
		
		$chunk_sql = "select c.content content from chunks c 
		join scripts_chunks sc on sc.chunk_id = c.id
		join scripts s on s.id = sc.script_id
		where s.id = ".$script['id']. "
		order by c.id asc
		";
		// print $chunk_sql;
		$res2 = mysql_query($chunk_sql);
		while($chunk = mysql_fetch_assoc($res2)) {
			if(!isset($chunks[$script['id']])) {
				$chunks[$script['id']] = array($chunk['content']);
			} else {
				$chunks[$script['id']][] = $chunk['content'];
			}
			//echo  $script['name']. ": " . $chunk['content'] . "\n";
		}
	}
	
	$all_scripts_except_tutorials_sql = "select id, content from scripts where id not in (select script_id from tutorials)";
	
	$res3 = mysql_query($all_scripts_except_tutorials_sql);
	$total_matches = 0;
	$partial_matches = 0;
	while($script = mysql_fetch_assoc($res3)) {
		$lines = explode("\n", $script['content']);
		foreach($chunks as $script_id => $chunks_list) {
			if(find_chunks($lines, $chunks_list)) {
				echo "FOUND all chunks (from script id " .$script_id. ") in script with id " . $script['id'] ."\n";
				if(map_tutorials_to_scripts($script_id, $script['id'], true)) {
					echo "	Mapping has been added to the database\n";
				}
				$total_matches++;
			} elseif (count($chunks_list) > 5) {
				$first_5_chunks = array_slice($chunks_list, 0, 5);
				if(find_chunks($lines, $first_5_chunks)){
					echo "FOUND first 5 chunks (from script id " .$script_id. ") in script with id " . $script['id'] ."\n";
					if(map_tutorials_to_scripts($script_id, $script['id'], false)) {
						echo "	Partial mapping has been added to the database\n";
					}
					$partial_matches++;
				}
			}
		}
	}
    
	echo "Total number of matches: ".$total_matches."\n";
	echo "Number of partial matches: ".$partial_matches."\n";
	
?>