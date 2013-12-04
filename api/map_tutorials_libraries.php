<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
//    $scripts_sql = "select script_id, id from scripts where id not in (select script_id from scripts_libraries) ";
//    $scripts_sql = "select script_id, id from scripts where script_id = 'hzawjnll'";
    $tutorials_sql = "select t.script_id tutorial_id, sl.library_id library_id from tutorials t join scripts s on t.script_id = s.id join scripts_libraries sl on sl.script_id = s.id";
    
    $res = mysql_query($tutorials_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($tutorials_libraries_row = mysql_fetch_assoc($res)) {
//        print_r($tutorials_libraries_row);
        $tutorial_id = $tutorials_libraries_row['tutorial_id'];
        $library_id = $tutorials_libraries_row['library_id'];

        $res2 = mysql_query('select id 
            from tutorials_libraries 
            where tutorial_id = "' . $tutorial_id . '" 
            and library_id = "' . $library_id . '"');

        $tutorials_libraries_id = mysql_result($res2, 0);

        if(empty($tutorials_libraries_id)) {
            $tutorials_libraries_id = mysql_insert('tutorials_libraries', array(
                'tutorial_id' => $tutorial_id,
                'library_id' => $library_id
            ));
            $row_count++;
            print_if_cli(" $row_count / $total_row_count | Added mapping: $tutorial_id => $library_id");
        }
    }
    
?>