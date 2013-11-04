<?php

function mysql_insert($table, $inserts) {
	$values = array_map('mysql_real_escape_string', array_values($inserts));
	$keys = array_keys($inserts);
		
	$sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')';
	//print_r($sql);
	return mysql_query($sql);
}
function get_hashes($line) {
	preg_match_all('/#(\w*)/', $line, $matches);
	return $matches[1];
}

function download_script($script_id) {
	$script_info = json_decode(file_get_contents("http://touchdevelop.com/api/" . $script_id ), true);
	$author_info = json_decode(file_get_contents("http://touchdevelop.com/api/" . $script_info['userid'] ), true);
	$script_content = file_get_contents("http://touchdevelop.com/api/" . $script_id . "/text");
	
	$res = mysql_query('select id from authors where author_id = "' . $script_info['userid'] . '"');
	$author_id = mysql_result($res, 0);
	
	if(!$author_id) {
		mysql_insert('authors', array('author_id' => $script_info['userid'], 'name' => $author_info['name']));
		$author_id = mysql_insert_id();
	}
	
	$description = $script_info['description'];
	
	mysql_insert('scripts', array(
		'content' => $script_content, 
		'script_id' => $script_id, 
		'date' => date("Y-m-d H:i:s", $script_info['time']), 
		'author_id' => $author_id, 
		'name' => $script_info['name'], 
		'description' => $description
	));
	
	$id = mysql_insert_id();
	
	$hashes = get_hashes($description);
	if(!empty($hashes)) {
		foreach($hashes as $hash) {
			$res = mysql_query('select id from hashtags where name = "' . $hash . '"');
			$hashtag_id = mysql_result($res, 0);	
			if(!$hashtag_id) {
				mysql_insert('hashtags', array('name' => $hash));
				$hashtag_id = mysql_insert_id();
			}
			mysql_insert('script_hashtags', array('script_id' => $id, 'hashtag_id' => $hashtag_id));
		}
	}
	return $id;
}
	
	