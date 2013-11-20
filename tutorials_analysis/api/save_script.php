<?php
	 error_reporting(E_ALL);
	include('config.php');
	include('functions.php');
	error_reporting(E_ERROR);
    
	$script_id = mysql_escape_string($_REQUEST['id']);
	
	if(download_script($script_id)) {
        echo "OK";
    }
    
?>