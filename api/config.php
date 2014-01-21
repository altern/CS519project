<?php
	error_reporting(0);
	$conn = mysql_connect("localhost", 'root', 'My5ql123');
	$db = mysql_select_db('cs519');
    set_time_limit(0);   
    ini_set('mysql.connect_timeout','0');   
    ini_set('max_execution_time', '0');
?>