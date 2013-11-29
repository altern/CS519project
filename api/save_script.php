<?php
	//error_reporting(E_ALL);
	include('config.php');
	include('functions.php');
	//error_reporting(E_ERROR);
	//error_reporting(0);
    
	$script_id = mysql_real_escape_string($_REQUEST['id']);
	
	if(download_script($script_id)) {
		$data = array();
		foreach(array('scripts', 'authors', 'tutorials', 'features', 'hashtags', 'libraries') as $table) {
			if($res = mysql_query('select count(id) from '.$table)) {
				$data[$table] = mysql_result($res, 0);
			}
		}
        echo json_encode($data);
    }
    
?>