<?php

function print_if_cli($message) {
    if(php_sapi_name() == 'cli') {
        print "$message \n";
    }
}

function escape_mysql_values($value) {
    if(is_int($value) || is_bool($value) ) {
        return $value;
    } else {
        return "'" . mysql_real_escape_string($value) ."'";
    }
}

function mysql_insert($table, $inserts) {
	$values = array_map('escape_mysql_values', array_values($inserts));
	$keys = array_keys($inserts);
	$sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES ('.implode(',', $values).')';
    mysql_query($sql);
	return mysql_insert_id();
}

function get_hashes($line) {
	preg_match_all('/#(\w*)/', $line, $matches);
	return $matches[1];
}

/**
 * Saves feature in a database
 * @param json array $feature
 * @return int
 */
function add_feature($feature) {
    
    $feature_name = (isset($feature['name']) ? $feature['name'] : $feature);
    $res = mysql_query('select id from features where name = "' . $feature_name . '"');
    $feature_id = mysql_result($res, 0);
    if(empty($feature_id)) {
        $feature_id = mysql_insert('features', array(
            'name' => $feature['name'],
            'description' => $feature['text'],
            'source' => $feature['title']
        ));
    }
    return $feature_id;
}

function add_tutorial($script_id, $script_name, $is_interactive = 1, $features = array()) {
    $res = mysql_query('select id from tutorials where script_id = ' . $script_id . ' and is_interactive = '.$is_interactive);
    $tutorial_id = mysql_result($res, 0);
    if(empty($tutorial_id)) {
        $tutorial_id = mysql_insert('tutorials', array(
            'name' => $script_name,
            'script_id' => $script_id,
            'is_interactive' => $is_interactive
        ));
    }
    if(!empty($features)) {
        foreach($features as $feature) {
            $feature_id = add_feature($feature);
            $res5 = mysql_query('select id 
                from tutorials_features 
                where tutorial_id = "' . $tutorial_id . '" 
                and feature_id = "' . $feature_id . '"');
            $tutorials_features_id = mysql_result($res5, 0);

            if(empty($tutorials_features_id)) {
                $tutorials_features_id = mysql_insert('tutorials_features', array(
                    'tutorial_id' => $tutorial_id,
                    'feature_id' => $feature_id
                ));
            }
        }
    }
    return $tutorial_id;
}

function add_hashtag($hash) {
    $res = mysql_query('select id from hashtags where name = "' . $hash . '"');
    $hashtag_id = mysql_result($res, 0);	
    if(empty($hashtag_id)) {
        $hashtag_id = mysql_insert('hashtags', array('name' => $hash));
    }
    return $hashtag_id;
}

function isLibrary($content) {
    return (strpos($script_content, 'meta isLibrary "yes";') !== FALSE);
}

function add_scripts_libraries_mapping($script_data, $script_id) {
    if(!empty($script_data['librarydependencyids'])) {
        foreach ($script_data['librarydependencyids'] as $library_ref ) {
            $res_lib_ref = mysql_query("select id from libraries where library_id = '$library_ref'");
            $library_id = mysql_result($res_lib_ref , 0);
            if(empty($library_id)) {
                $library_script_id = download_script($library_ref);
                $res_library_id = mysql_query("select script_id from scripts where id = $library_script_id");
                $library_id = mysql_result($res_library_id , 0);
            } 
            
            $res_scripts_libraries = mysql_query('select id 
                from scripts_libraries 
                where script_id = "' . $script_id . '" 
                and library_id = "' . $library_id . '"');
            $scripts_libraries_id = mysql_result($res_scripts_libraries, 0);
            
            if(empty($scripts_libraries_id)) {
                $script_libraries_id = mysql_insert('scripts_libraries', array(
                    'script_id' => $script_id,
                    'library_id' => $library_id
                ));
                print_if_cli("  added mapping $script_id => $library_id");
            }
        }
        if($script_libraries_id) { return TRUE; }
    }
    return FALSE;
}

function add_tutorials_libraries_mapping($tutorial_ids, $library_id) {
    
}

function add_scripts_features_mapping($features, $script_id) {
    if(!empty($features)) {
        foreach($features as $feature) {
            $feature_id = add_feature($feature);
            
            $res = mysql_query('select id 
                from scripts_features 
                where script_id = "' . $script_id . '" 
                and feature_id = "' . $feature_id . '"');
            $tutorials_features_id = mysql_result($res, 0);
            
            if(empty($tutorials_features_id)) {
                $tutorials_features_id = mysql_insert('scripts_features', array(
                    'script_id' => $script_id,
                    'feature_id' => $feature_id
                ));
            }
        }
    }
}

function find_chunks($lines, $chunks) {
    $last_chunk = array_slice($chunks, -1)[0];
    // print_r($last_chunk);
    foreach($lines as $match_line_num => $line) {

        if(strpos($line, $last_chunk) !== FALSE) {
            //echo "Found chunk '$last_chunk' at line $match_line_num: '$line' \n";
            if(count($chunks) > 1) {
                $without_last_chunk = array_slice($chunks, 0, count($chunks) - 1);
                $prev_match_line_num = find_chunks($lines, $without_last_chunk);
                if($prev_match_line_num === FALSE) {return FALSE;}
                if($prev_match_line_num < $match_line_num) {
                    return $match_line_num;
                } else {
                    print_if_cli( "Order of chunks does not match");
                    return FALSE;
                }
            } else {
                return $match_line_num;
            }
        }
    }
    return FALSE;
}

function add_chunk($chunk, $short_script_id, $seq) {
//    print_if_cli('select id from chunks where content = "' .mysql_escape_string($chunk). '"');
    if(!$res = mysql_query('select id from chunks where content = "' .mysql_escape_string($chunk). '"')) {
        print_if_cli(mysql_error());
    }
    $chunk_id = mysql_result($res, 0);
    if(empty($chunk_id)) {
        $chunk_id = mysql_insert('chunks', array('content' => $chunk) );
    }
    
    if(!$res2 = mysql_query('select id from scripts where script_id = "' .$short_script_id. '"')) {
        print_if_cli(mysql_error());
    }
    $script_id = mysql_result($res2, 0);
    
    if(empty($script_id)) {
        $script_id = download_script($short_script_id);
    }
    
    if(!$res3 = mysql_query('select id from scripts_chunks where script_id = ' .$script_id. ' and chunk_id = ' . $chunk_id .' and seq = '.$seq)) {
        print_if_cli(mysql_error());
    }
    $scripts_chunks_id = mysql_result($res3, 0);
    if(empty($scripts_chunks_id)) {
        $scripts_chunks_id = mysql_insert('scripts_chunks', array('script_id' => $script_id, 'chunk_id' => $chunk_id, 'seq' => $seq));
    }
    return $scripts_chunks_id;
}

function detect_chunks($script_source) {
    $lines = array();
    $chunks = array();
    $chunkCount = 0;
    $chunkFlag = false;
    $mainFlag = false;

    $lines = explode("\n", $script_source);

    foreach($lines as $line_with_whitespace) {
        $line = preg_replace('/^\s*/', '', $line_with_whitespace);

        if(strpos($line, "//") === FALSE ) {
            //print_if_cli($line);
            if($mainFlag && strpos($line, '}') !== FALSE ) {
                $mainFlag = false;
                $chunkFlag = false;
            }
            if($mainFlag) {
                $chunkFlag = true;
                $chunks[] = $line;
            } elseif(preg_match("/action (.*)main\(\) {/", $line) ) {
                $mainFlag = true;
            }
        } elseif($chunkFlag) {
            $chunkFlag = false;
        }
    }
    
    return $chunks;
}


function map_tutorials_to_scripts($tutorial_id, $script_id, $is_completed) {
    $query = 'select id 
            from tutorials_by_author 
            where author_id = (select author_id from scripts where id = ' . $script_id . ') 
            and tutorial_id = (select id from tutorials where script_id = ' . $tutorial_id . ')
            and is_completed = ' . $is_completed ;
    // print $query. "\n";
    $res = mysql_query($query);
    $tutorials_by_author_id = mysql_result($res, 0);

    if(empty($tutorials_by_author_id)) {
        $query = 'insert into tutorials_by_author (author_id, tutorial_id, is_completed) values (
            (select author_id from scripts where id = ' . $script_id . '),
            (select id from tutorials where script_id = ' . $tutorial_id . '),
            '.$is_completed.'
        )';
        //print $query."\n";
        $res2 = mysql_query($query);
        if(!$res2) {
            echo mysql_error($res2);
            return FALSE;
        }
    }
    
    $tutorial_id_sql = 'select id from tutorials where script_id = ' . $tutorial_id ;
    $res5 = mysql_query($tutorial_id_sql);
    $tutorial_id = mysql_result($res5, 0);

    $query = 'select id 
            from scripts_tutorials 
            where script_id = ' . $script_id . ' 
            and tutorial_id = ' . $tutorial_id;
    //print $query . "\n";
    $res3 = mysql_query($query);

    $scripts_tutorials_id = mysql_result($res3, 0);

    if(empty($scripts_tutorials_id)) {
        $res4 = mysql_insert('scripts_tutorials', array(
            'script_id' => $script_id,
            'tutorial_id' => $tutorial_id
        ));
        if(!$res4) {
            echo mysql_error($res4);
            return FALSE;
        }
    }
    return TRUE;
}

function add_hash($hash, $script_id) {
    $hashtag_id = add_hashtag($hash);
    $res4 = mysql_query('select id 
                from scripts_hashtags 
                where script_id = "' . $script_id . '" 
                and hashtag_id = "' . $hashtag_id . '"');
    $scripts_hashtags_id = mysql_result($res4, 0);

    if(empty($scripts_hashtags_id)) {
        $scripts_hashtags_id = mysql_insert('scripts_hashtags', array(
            'script_id' => $script_id, 
            'hashtag_id' => $hashtag_id
        ));
    }
}

function add_scripts_tutorials_mapping($script_id, $script_name, $hashes, $features, $script_content) {
    $tutorial_id = null;
	
    foreach($hashes as $hash) {
        add_hash($hash, $script_id);
    }
    
    $tutorial_id = add_tutorial($script_id, $script_name, is_interactive($script_content), $features);
    
	return $tutorial_id;
}


$tutorial_hashes = array('docs', 'tutorials', 'stepbystep');

function is_tutorial($hashes) {
    global $tutorial_hashes;
    foreach($tutorial_hashes as $tutorial_hash) {
        if(in_array($tutorial_hash, $hashes)) {
            return true;
        }
    }
    return false;
}

function generateWhereClauseForTutorials() {
    global $tutorial_hashes;
    return "(" . implode(', ', array_map( function($elem) { return "'" .$elem. "'" ; } , $tutorial_hashes)) . ")";
}

function is_interactive($script_content) {
    return preg_match("/00230_/", $script_content);
//    if (in_array('stepbystep', $hashes) || in_array('stepByStep', $hashes) || in_array('interactiveTutorial', $hashes)) {
//        return TRUE;
//    } elseif( in_array('tutorials', $hashes) || in_array('docs', $hashes)) {
//        return FALSE;
//    } 
}

function download_script($script_short_id) {
	$script_info = json_decode(file_get_contents("http://touchdevelop.com/api/" . $script_short_id ), true);
	$author_info = json_decode(file_get_contents("http://touchdevelop.com/api/" . $script_info['userid'] ), true);
	$features_info = json_decode(file_get_contents("http://touchdevelop.com/api/" . $script_short_id . '/features' ), true);
    $features = $features_info['items'];
	$script_content = file_get_contents("http://touchdevelop.com/api/" . $script_short_id . "/text");
	
	$res1 = mysql_query('select id from authors where author_id = "' . $script_info['userid'] . '"');
	$author_id = mysql_result($res1, 0);
	
	if(empty($author_id)) {
        $author_id = mysql_insert('authors', array(
            'author_id' => $script_info['userid'], 
            'name' => $author_info['name'],
            'join_date' => date("Y-m-d H:i:s", $author_info['time']),
            'features' => $author_info['features'],
            'activedays' => $author_info['activedays'],
            'receivedpositivereviews' => $author_info['receivedpositivereviews'],
            'subscribers' => $author_info['subscribers'],
            'score' => $author_info['score']
        ));
	}
	
	$description = $script_info['description'];
	
    $res2 = mysql_query('select id from scripts where script_id = "' . $script_short_id . '"');
	$script_id = mysql_result($res2, 0);
    
    if(!$script_id) {
        $script_id = mysql_insert('scripts', array(
            'content' => $script_content, 
            'script_id' => $script_short_id, 
            'date' => date("Y-m-d H:i:s", $script_info['time']), 
            'author_id' => $author_id, 
            'name' => $script_info['name'], 
            'description' => $description,
            'positivereviews' => $script_info['positivereviews'],
            'cumulativepositivereviews' => $script_info['cumulativepositivereviews'],
            'installations' => $script_info['installations'],
            'runs' => $script_info['runs']
        ));
    }
    
    
	if($script_info['islibrary'] == 'true') {
        $library_id = mysql_insert('libraries', array(
            'name' => $script_info['name'], 
            'library_id' => $script_short_id,
            'description' => $description
        ));
    }
    
    add_scripts_libraries_mapping($script_info, $script_id);
    
    add_scripts_features_mapping($features, $script_id);
    
    $features = array_map(function($feature) {return $feature['name'];}, $features);
    
    $hashes = get_hashes($description);
    
//	add_scripts_tutorials_mapping($script_id, $script_info['name'], $hashes, $features, $script_content);
    
	return $script_id;
}
	

function output_request_short ($request, $request_num) {
    print '<h2>' .$request['name']. '</h2>';
    print '<div class="sql">' . SqlFormatter::format( $request['query'] ) . '</div>';
    global $script_url;
    if(isset($request['skip']) && $request['skip']) {
        print '<h4>SKIPPED (<a href="'.$script_url.'?request_num='.$request_num.'" target="_new">Run query in separate window</a>)</h4>';
    } else {
        if(!$res = mysql_query($request['query'])) {
            print mysql_error();
        }
        $cols_count = mysql_num_fields($res);
        $rows_count = mysql_num_rows($res);
        $row_count = 0 ;
        $fields = mysql_num_fields($res);
        if($rows_count == 0) {
            print "<h4>EMPTY</h4>";
        } else {
            print '<table border="1">';
            for ($i=0; $i < $fields; $i++) { 
                print "<th>".mysql_field_name($res, $i)."</th>"; 
            }
            while( $row = mysql_fetch_row($res) ) {
                echo "<tr>";
                for ($f=0; $f < $cols_count; $f++) {
                    echo "<td>{$row[$f]}</td>"; 
                }
                if($row_count++ > ROWS_LIMIT) {
                    print '<tr><td colspan="' .$cols_count. '"><a href="'.$script_url.'?request_num='.$request_num.'">More ('.$rows_count.')...</a></td></tr>';
                    break;
                }
                echo "</tr>\n";
            }
            print "</table>";
        }
        print "<hr />";
    }
	//print "<br /><br />";
}

function output_request_long ($request_num) {
    global $data, $script_url, $save_csv_script;
    $request = $data[$request_num];
    print '<h2>' .$request['name']. '</h2>';
    $links_row = '<a href="'.$script_url.'">Go back</a> | <a href="'.$save_csv_script.'?download_csv='.$request_num.'">Download CSV</a>';
    print $links_row;
    print "<br /><br />";
    print '<div class="sql">' . SqlFormatter::format( $request['query'] ) . '</div>';
	$res = mysql_query($request['query']);
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
                echo "<td>{$row[$f]}</td>"; 
            }
//            if($row_count++ > ROWS_LIMIT) {
//                print '<tr><td colspan="' .$cols_count. '"><a href="'.$script_url.'?request_num='.$request_num.'">More...</a></td></tr>';
//                break;
//            }
            echo "</tr>\n";
        }
        print "</table>";
    }
	print "<br />";
    
    print $links_row;
}

function export_query_results($request_num) {
    global $data;
    $request = $data[$request_num];
    $select = $request['query'];
    
    $export = mysql_query ( $select ) or die ( "Sql error : " . mysql_error( ) );

    $fields = mysql_num_fields ( $export );

    for ( $i = 0; $i < $fields; $i++ )
    {
        $header .= mysql_field_name( $export , $i ) . ",\t";
    }

    while( $row = mysql_fetch_row( $export ) )
    {
        $line = '';
        foreach( $row as $value )
        {                                            
            if ( ( !isset( $value ) ) || ( $value == "" ) )
            {
                $value = ",\t";
            }
            else
            {
                //$value = str_replace( '"' , '""' , $value );
//                $value = '"' . $value . '"' . ",\t";
                $value = $value . ",\t";
            }
            $line .= $value;
        }
        $data .= trim( $line ) . "\n";
    }
    $data = str_replace( "\r" , "" , $data );

    if ( $data == "" )
    {
        $data = "\n(0) Records Found!\n";                        
    }

    header("Content-type: application/octet-stream");
    $name = str_replace(" ", "_", $request['name']);
    header("Content-Disposition: attachment; filename=".$name.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$header\n$data";
}