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

include_once('api/config.php');
include_once('api/functions.php');
include_once('api/queries_list.php');

error_reporting(E_ALL);
$script_url = basename($_SERVER['PHP_SELF']);

$query = "(select t.*, count(*) duplicates, max(s.date) publication_date, concat('http://touchdevelop.com/', s.script_id) tutorial_url,"
        . " concat('http://touchdevelop.com/api/', s.script_id, '/text') source_url "
        . " from tutorials t join scripts s on t.script_id = s.id "
        . " where t.is_interactive = true and s.id in (select script_id from scripts_tutorials) "
        . " group by s.name"
        . " order by duplicates desc"
        . ") union ("
        . " select t.*, count(*) duplicates, max(s.date) publication_date, concat('http://touchdevelop.com/', s.script_id) tutorial_url,"
        . " concat('http://touchdevelop.com/api/', s.script_id, '/text') source_url "
        . " from tutorials t join scripts s on t.script_id = s.id "
        . " where t.is_interactive = false and s.id in (select script_id from scripts_tutorials)"
        . " group by s.name"
        . " order by duplicates desc "
        . " )";

//print_r($query);

//$url_field_names = array('tutorial_url', 'source_url');

print '<div class="sql">' . SqlFormatter::format( $query ) . '</div>';
$res = mysql_query($query);
$cols_count = mysql_num_fields($res);
$rows_count = mysql_num_rows($res);
$row_count = 0 ;
$fields = mysql_num_fields($res);
if($rows_count == 0) {
    print "<h3>EMPTY</h3>";
} else {
    print '<table border="1">';
    for ($i=0; $i < $fields; $i++) { 
        print "<th>".mysql_field_name($res, $i)."</th>"; 
    }
    while( $row = mysql_fetch_row($res) ) {
        echo "<tr>";
        for ($f=0; $f < $cols_count; $f++) {
            if(strpos($row[$f], "http://") === FALSE) {
                echo "<td>{$row[$f]}</td>"; 
            } else {
                echo "<td><a href=\"{$row[$f]}\" target=\"_blank\">{$row[$f]}</a></td>"; 
            }
        }
        echo "</tr>\n";
    }
    print "</table>";
}
print "<br />";

?> 
    
</body>
</html>