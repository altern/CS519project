<?php

function find_chunks($lines, $chunks) {
	$last_chunk = array_slice($chunks, -1)[0];
	// print_r($last_chunk);
	foreach($lines as $match_line_num => $line) {
		
		if(strpos($line, $last_chunk) !== FALSE) {
			echo "Found chunk '$last_chunk' at line $match_line_num: '$line' \n";
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
	

$lines = array('123', 'aaaa', '567', '678');
$chunks = array('123', '678', '567');

echo "Result of find_chunks: " . find_chunks($lines, $chunks) . "\n";

print_r(array_slice($lines, 0 ,3))

?>