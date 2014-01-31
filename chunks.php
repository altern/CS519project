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

$query = "select count(*) cnt, s.name, s.script_id,
group_concat(c.content SEPARATOR '\n<br />') chunks
from scripts_chunks sc 
join chunks c on c.id = sc.chunk_id 
join scripts s on s.id = sc.script_id
group by sc.script_id
having cnt > 3
ORDER BY `cnt` ASC";

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