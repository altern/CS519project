<?php
	// error_reporting(E_ALL);
	include('config.php');
	include('functions.php');
	
	$script_id = mysql_escape_string($_POST['id']);
	
	if($script_id) {
		$res = mysql_query('select id from scripts where script_id = "' .$script_id. '"');
		$id = mysql_result($res, 0);
		if(!$id) {
			$id = download_script($script_id);
		}
		if($id) {
			echo "OK";
		}
	}
?>