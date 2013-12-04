<?php
	// error_reporting(E_ALL);
	include('config.php');
	include('functions.php');
	
	$script_id = mysql_escape_string($_REQUEST['id']);
	$chunk = mysql_escape_string($_REQUEST['chunk']);
	
	if($script_id && $chunk) {
		if(add_chunk($chunk, $script_id)) {
			echo "OK";
		}
	}
?>