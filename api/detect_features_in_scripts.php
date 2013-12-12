<?php
	include('config.php');
	include('functions.php');
//	ini_set('max_execution_time', 60*20);
    function get_precedence_flag($script_date, $learn_date) {
        return (strtotime($script_date) > strtotime($learn_date));
    }
    error_reporting(E_ERROR);
    
    $authors_sql = "select a.id author_id, a.name author_name 
    from authors a where a.id in (select author_id from authors_learned_features)";
	
	$res = mysql_query($authors_sql);
    $mapping_count = 0;
    $row_count = 0;
    $total_row_count = mysql_num_rows($res);
	$found = array();
    exec("touch authors_features.csv");
    exec("echo author,feature,script_date,learn_date,precedence_flag > authors_features.csv ");
    exec("touch temp_feature_precedence_count.csv");
    exec("echo feature_name,positive,negative > temp_feature_precedence_count.csv");
    while($authors_row = mysql_fetch_assoc($res)) {
		$scripts_sql = " select s.name script_name, s.script_id, s.date script_date, f.id feature_id, f.name feature_name 
		from scripts s 
		join scripts_features sf on sf.script_id = s.id
		join features f on sf.feature_id = f.id
		where s.author_id = ".$authors_row['author_id'];
		$res2 = mysql_query($scripts_sql);
		
        $features_sql = "select f.id feature_id, f.name feature_name, af.date learn_date 
        from authors_learned_features af 
        join features f on af.feature_id = f.id
        where af.author_id = ".$authors_row['author_id'];

        $res3 = mysql_query($features_sql);
        
		$row_count++;
		print_if_cli("Analyzing scripts of author ".$authors_row['author_name']." ($row_count / $total_row_count) ... ");
		
        while($feature_row = mysql_fetch_assoc($res3)) {
            while($scripts_row = mysql_fetch_assoc($res2)) {
                if( $scripts_row['feature_id'] == $feature_row['feature_id'] 
                    && strtotime($scripts_row['script_date']) != strtotime($feature_row['learn_date'])) {
                    
                    print_if_cli("Found the same feature " . $feature_row['feature_name'] . " in " .$scripts_row['script_id']. " (" . $scripts_row['script_date'] . ", ". $feature_row['learn_date'] . " -> " . (get_precedence_flag($scripts_row['script_date'], $feature_row['learn_date']) ). ")");
                    exec("echo {$authors_row['author_name']},{$feature_row['feature_name']},{$scripts_row['script_date']},{$feature_row['learn_date']},".get_precedence_flag($scripts_row['script_date'], $feature_row['learn_date'])." >> authors_features.csv ");
                    if(!isset($found[$feature_row['feature_name']])) {
                        $found[$feature_row['feature_name']] = array(
                            'positive' => 0,
                            'negative' => 0
                        );
                    }
                    if(get_precedence_flag($scripts_row['script_date'], $feature_row['learn_date'])) {
                        $found[$feature_row['feature_name']]['positive']++;
                    } else {
                        $found[$feature_row['feature_name']]['negative']++;
                    }
                    $mapping_count++;
                }
            }
		}
        if(!empty($found)) {
            exec("echo ============================================= >> temp_feature_precedence_count.csv");
        }
		foreach($found as $feature => $count) {
            exec("echo '$feature,{$count['positive']},{$count['negative']}' >> temp_feature_precedence_count.csv");
        }
	}
    print_r($found);
    exec("touch feature_precedence_count.csv");
    exec("echo 'feature_name,positive,negative' > feature_precedence_count.csv");
    foreach($found as $feature => $count) {
        exec("echo '$feature,{$count['positive']},{$count['negative']}' >> feature_precedence_count.csv");
    }