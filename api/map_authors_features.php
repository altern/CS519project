<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
    $authors_sql = "select f.id feature_id, f.name feature_name, a.id author_id, a.name author_name,
	-- t.name tutorial_name 
	s.name script_name, s.date, s.script_id
	from features f 
    join tutorials_features tf on tf.feature_id = f.id 
	-- join tutorials t on t.id = tf.tutorial_id
	join scripts_tutorials st on st.tutorial_id = tf.tutorial_id
	join scripts s on st.script_id = s.id
	join authors a on s.author_id = a.id
    where tf.feature_id NOT IN (
        SELECT tf2.feature_id
        FROM tutorials_features tf2
        WHERE tf2.id != tf.id
    )";
    
    $res = mysql_query($authors_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
    while($authors_row = mysql_fetch_assoc($res)) {
        $row_count++;
        $res = mysql_query('select id from authors_learned_features where author_id = ' . $authors_row['author_id'] . ' and feature_id = ' . $authors_row['feature_id']);
        $authors_features_id = mysql_result($res, 0);
        if(empty($authors_features_id)) {
            if(mysql_insert('authors_learned_features', array(
                'author_id' => $authors_row['author_id'],
                'feature_id' => $authors_row['feature_id'],
                'date' => $authors_row['date']
            ))) {
                print_if_cli(" $row_count / $total_row_count | Added authors -> features mapping: ".$authors_row['author_name']." (".$authors_row['author_id'].") => ".$authors_row['feature_name']. " (".$authors_row['feature_id'].") ");
                $mapping_count++;
            }
        }
    }
    print_if_cli("Number of added mappings: " . $mapping_count);
?>