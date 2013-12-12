<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
    $authors_libraries_sql = "select l.id library_id, l.name library_name, a.id author_id, a.name author_name,
	s.name script_name, s.date, s.script_id
	from libraries l 
    join tutorials_libraries tl on tl.library_id = l.id 
	join scripts_tutorials st on st.tutorial_id = tl.tutorial_id
	join scripts s on st.script_id = s.id
	join authors a on s.author_id = a.id
    where tl.library_id NOT IN (
        SELECT tl2.library_id
        FROM tutorials_libraries tl2
        WHERE tl2.id != tl.id
    )";
    
    $res = mysql_query($authors_libraries_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($authors_libraries_row = mysql_fetch_assoc($res)) {
        $row_count++;
        $res = mysql_query('select id from authors_learned_libraries where author_id = ' . $authors_libraries_row['author_id'] . ' and library_id = ' . $authors_libraries_row['library_id']);
        $authors_libraries_id = mysql_result($res, 0);
        if(empty($authors_libraries_id)) {
            if(mysql_insert('authors_learned_libraries', array(
                'author_id' => $authors_row['author_id'],
                'library_id' => $authors_row['library_id'],
                'date' => $authors_row['date']
            ))) {
                print_if_cli(" $row_count / $total_row_count | Added authors -> libraries mapping: ".$authors_libraries_row['author_name']." (".$authors_libraries_row['author_id'].") => ".$authors_libraries_row['feature_name']. " (".$authors_libraries_row['feature_id'].") ");
                $mapping_count++;
            }
        }
    }
    print_if_cli("Number of added mappings: " . $mapping_count);
?>