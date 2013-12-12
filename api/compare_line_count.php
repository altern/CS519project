<?php

	include('config.php');
	include('functions.php');

	error_reporting(E_ERROR);

	function is_comment($line) {
		$line = preg_replace('/^\s*/', '', $line);

        return strpos($line, "//") !== FALSE ;
	}
	function get_line_count($source) {
		$lines = explode("\n", $source);
		$count = 0;
		foreach($lines as $line) {
			if(!is_comment($line)) {
				$count++;
			} else {
				//print_if_cli($line);
			}
		}
		return $count;
	}
	$line_count_sql = "
	SELECT 
	s.name script_name, 
	s.content script_source,
	t.name tutorial_name,
	tsource.content tutorial_source
	FROM scripts s
	join scripts_tutorials st on st.script_id = s.id
	join tutorials t on st.tutorial_id = t.id
	join scripts tsource on t.script_id = tsource.id
	where t.is_interactive = false
	group by s.id";
	
	$res = mysql_query($line_count_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
	$found = array('positive' => 0, 'negative' => 0);
    while($line_count_row = mysql_fetch_assoc($res)) {
		$source_count = get_line_count($line_count_row['script_source']);
		$tutorial_count = get_line_count($line_count_row['tutorial_source']);
		
		if($source_count >$tutorial_count) {
			$found['positive']++;
		} else {
			$found['negative']++;
		}
	}
	print_r($found);