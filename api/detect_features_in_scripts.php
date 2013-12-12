<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    
    error_reporting(E_ERROR);
    
    $authors_features_sql = "select a.id author_id, a.name author_name, f.id feature_id, f.name feature_name, af.date learn_date 
	from authors a 
	join authors_learned_features af on af.author_id = a.id
	join features f on af.feature_id = f.id";
	
	$res = mysql_query($authors_features_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
	$found = array();
    while($authors_features_row = mysql_fetch_assoc($res)) {
		$scripts_sql = " select s.name script_name, s.script_id, s.date script_date, f.id feature_id 
		from scripts s 
		join scripts_features sf on sf.script_id = s.id
		join features f on sf.feature_id = f.id
		where s.author_id = ".$authors_features_row['author_id'];
		$res2 = mysql_query($scripts_sql);
		
		$row_count++;
		print_if_cli("Analyzing scripts of author ".$authors_features_row['author_id']." ($row_count / $total_row_count) ... ");
		
		while($scripts_row = mysql_fetch_assoc($res2)) {
			if( $scripts_row['feature_id'] == $authors_features_row['feature_id'] 
				&& strtotime($scripts_row['script_date']) != strtotime($authors_features_row['learn_date'])
				) {
				print_if_cli("Found the same feature " . $authors_features_row['feature_name'] . " in " .$scripts_row['script_id']. " (" . $scripts_row['script_date'] . ", ". $authors_features_row['learn_date'] . " -> " . (strtotime($scripts_row['script_date']) > strtotime($authors_features_row['learn_date'])). ")");
				if(!isset($found[$authors_features_row['feature_name']])) {
					$found[$authors_features_row['feature_name']] = array(
						'positive' => 0,
						'negative' => 0
					);
				}
				if(strtotime($scripts_row['date']) > strtotime($authors_features_row['learn_date'])) {
					$found[$authors_features_row['feature_name']]['positive']++;
				} else {
					$found[$authors_features_row['feature_name']]['negative']++;
				}
				$mapping_count++;
			}
		}
		print_r($found);
	}
	print_r($found);