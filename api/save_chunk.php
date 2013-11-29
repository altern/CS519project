<?php
	// error_reporting(E_ALL);
	include('config.php');
	include('functions.php');
	
	$script_id = mysql_escape_string($_REQUEST['id']);
	$chunk = mysql_escape_string($_REQUEST['chunk']);
	
	if($script_id && $chunk) {
		//print_r(array('id' => $id, 'chunk' => $chunk));
		mysql_insert('chunks', array('content' => $chunk) );
		$chunk_id = mysql_insert_id();
		$res = mysql_query('select id from scripts where script_id = "' .$script_id. '"');
		$id = mysql_result($res, 0);
		if($id) {
			mysql_insert('scripts_chunks', array('script_id' => $id, 'chunk_id' => $chunk_id));
		} else {
			$id = download_script($script_id);
			mysql_insert('scripts_chunks', array('script_id' => $id, 'chunk_id' => $chunk_id));
		}
		if(mysql_insert_id()) {
			echo "OK";
		}
	}
?>