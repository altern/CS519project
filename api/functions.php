<?php

function print_if_cli($message) {
    if(php_sapi_name() == 'cli') {
        print "$message \n";
    }
}

function escape_mysql_values($value) {
    if(is_int($value)) {
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

function add_feature($feature) {
    $res = mysql_query('select id from features where name = "' . $feature['name'] . '"');
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

function add_tutorial($script_id, $script_name) {
    $res = mysql_query('select id from tutorials where script_id = "' . $script_id . '"');
    $tutorial_id = mysql_result($res, 0);
    if(empty($tutorial_id)) {
        if( in_array('stepbystep', $hashes) || in_array('stepByStep', $hashes)) {
            $tutorial_id = mysql_insert('tutorials', array(
                'name' => $script_name,
                'script_id' => $script_id,
                'is_interactive' => 1
            ));
        } else {
            $tutorial_id = mysql_insert('tutorials', array(
                'name' => $script_name,
                'script_id' => $script_id,
                'is_interactive' => 0
            ));
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
    
    if(!empty($features)) {
        foreach($features as $feature) {
            $feature_id = add_feature($feature);
            
            $res3 = mysql_query('select id 
                from scripts_features 
                where script_id = "' . $script_id . '" 
                and feature_id = "' . $feature_id . '"');
            $tutorials_features_id = mysql_result($res3, 0);
            
            if(empty($tutorials_features_id)) {
                $tutorials_features_id = mysql_insert('scripts_features', array(
                    'script_id' => $script_id,
                    'feature_id' => $feature_id
                ));
            }
        }
    }
    
	$hashes = get_hashes($description);
    
	if(!empty($hashes)) {
		foreach($hashes as $hash) {
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
        if( in_array('tutorials', $hashes) ) {
            
            $tutorial_id = add_tutorial($script_id, $script_info['name']);
            
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
        }
	}
    
	return $script_id;
}
	