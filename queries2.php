<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>TouchDevelop Queries results</title>
  
  <link rel="stylesheet" href="css/style.css?v=1.0">

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>

<?php
include_once 'lib/sqlFormatter.php';
$phpmyadmin_url = "http://".$_SERVER['HTTP_HOST']."/phpmyadmin";
//$script_url = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$script_url = basename($_SERVER['PHP_SELF']);

include_once('api/config.php');
include_once('api/functions.php');
include_once('api/queries_list2.php');

define('ROWS_LIMIT', 20);

if(!isset($_GET['request_num'])) {
    foreach($data as $request_num => $request) {
        output_request_short($request, $request_num);
    }
} else {
    output_request_long($_GET['request_num']);
}

?>
</body>
</html>
