<?php
	include('config.php');
	include('functions.php');
    
    error_reporting(E_ERROR);
    
    mysql_query("truncate table tutorials_pages");
    
    $tutorials_sql = "select t.script_id tutorial_id, sl.library_id library_id 
        from tutorials t 
        join scripts s on t.script_id = s.id 
        join scripts_libraries sl on sl.script_id = s.id";
    
    $pages_tutorials_mapping = array(
        1 => array(
            1 => array('script_id' => 'jokl', 'category_name' => 'try one of these first'),
            2 => array('script_id' => 'fraoa', 'category_name' => 'try one of these first'),
            3 => array('script_id' => 'gjrn', 'category_name' => 'try one of these first'),
            4 => array('script_id' => 'pnqo', 'category_name' => 'games'),
            5 => array('script_id' => 'jiqz', 'category_name' => 'games'),
            6 => array('script_id' => 'oplh', 'category_name' => 'games'),
            7 => array('script_id' => 'pxvi', 'category_name' => 'turtle'),
            8 => array('script_id' => 'olbk', 'category_name' => 'turtle'),
            9 => array('script_id' => 'ycbqb', 'category_name' => 'turtle'),
            10 => array('script_id' => 'ayyxc', 'category_name' => 'graphics'),
            11 => array('script_id' => 'zkpi', 'category_name' => 'graphics'),
            12 => array('script_id' => 'jgupa', 'category_name' => 'from blocks to text'),
            13 => array('script_id' => 'hkpua', 'category_name' => 'from blocks to text'),
            14 => array('script_id' => 'vffdb', 'category_name' => 'from blocks to text'),
            15 => array('script_id' => 'vffdb', 'category_name' => 'gadgets'),
        ),
        2 => array(
            1 => array('script_id' => 'jokl', 'category_name' => 'getting started'),
            2 => array('script_id' => 'bbxr', 'category_name' => 'getting started'),
            3 => array('script_id' => 'mizba', 'category_name' => 'getting started'),
            4 => array('script_id' => 'uxkfa', 'category_name' => 'language'),
            5 => array('script_id' => 'sylyb', 'category_name' => 'language'),
            6 => array('script_id' => 'zvub', 'category_name' => 'language'),
            7 => array('script_id' => 'kqow', 'category_name' => 'language'),
            8 => array('script_id' => 'enek', 'category_name' => 'language'),
            9 => array('script_id' => 'nrvba', 'category_name' => 'language'),
            10 => array('script_id' => 'gewda', 'category_name' => 'language'),
            11 => array('script_id' => 'gelka', 'category_name' => 'language'),
            12 => array('script_id' => 'pwiea', 'category_name' => 'language'),
            13 => array('script_id' => '', 'category_name' => 'language'),
            14 => array('script_id' => 'ioxq', 'category_name' => 'insights'),
            15 => array('script_id' => 'vrhra', 'category_name' => 'insights'),
            16 => array('script_id' => 'dfpo', 'category_name' => 'insights'),
            17 => array('script_id' => '', 'category_name' => 'teach'),
            18 => array('script_id' => '', 'category_name' => 'teach'),
            19 => array('script_id' => '', 'category_name' => 'teach'),
            20 => array('script_id' => '', 'category_name' => 'teach'),
            21 => array('script_id' => '', 'category_name' => 'teach'),
            22 => array('script_id' => '', 'category_name' => 'teach'),
            23 => array('script_id' => 'kfzja', 'category_name' => 'games'),
            24 => array('script_id' => 'nsjka', 'category_name' => 'games'),
            25 => array('script_id' => 'xjfw', 'category_name' => 'games'),
            26 => array('script_id' => 'toxzb', 'category_name' => 'games'),
            27 => array('script_id' => 'nmtc', 'category_name' => 'games'),
            28 => array('script_id' => '', 'category_name' => 'games'),
            29 => array('script_id' => 'qaeq', 'category_name' => 'consuming web services'),
            30 => array('script_id' => 'uwkg', 'category_name' => 'consuming web services'),
            31 => array('script_id' => 'pfoo', 'category_name' => 'authenticating web services'),
            32 => array('script_id' => 'yeoq', 'category_name' => 'authenticating web services'),
            33 => array('script_id' => 'efxjb', 'category_name' => 'authenticating web services'),
            34 => array('script_id' => '', 'category_name' => 'authenticating web services'),
            35 => array('script_id' => '', 'category_name' => 'misc'),
            36 => array('script_id' => '', 'category_name' => 'misc'),
            37 => array('script_id' => '', 'category_name' => 'misc'),
            38 => array('script_id' => '', 'category_name' => 'misc'),
            39 => array('script_id' => 'cadl', 'category_name' => 'misc'),
            40 => array('script_id' => '', 'category_name' => 'misc'),
            41 => array('script_id' => '', 'category_name' => 'misc'),
        ),
        3 => array(
            1 => array('script_id' => 'jokl', 'category_name' => 'try one of these') ,
            2 => array('script_id' => 'jiqz', 'category_name' => 'try one of these') ,
            3 => array('script_id' => 'gjrn', 'category_name' => 'try one of these') ,
            4 => array('script_id' => 'jgupa', 'category_name' => 'try one of these') ,
            5 => array('script_id' => 'mizba', 'category_name' => 'try one of these') ,
            6 => array('script_id' => 'jokl', 'category_name' => 'turtle') ,
            7 => array('script_id' => 'pxvi', 'category_name' => 'turtle') ,
            8 => array('script_id' => 'tblca', 'category_name' => 'turtle') ,
            9 => array('script_id' => 'olbk', 'category_name' => 'turtle') ,
            10 => array('script_id' => 'ycbqb', 'category_name' => 'turtle') ,
            11 => array('script_id' => 'zkmr', 'category_name' => 'turtle') ,
            12 => array('script_id' => 'zkpi', 'category_name' => 'graphics and shapes') ,
            13 => array('script_id' => 'ayyxc', 'category_name' => 'graphics and shapes') ,
            14 => array('script_id' => 'fraoa', 'category_name' => 'games') ,
            15 => array('script_id' => 'pnqo', 'category_name' => 'games') ,
            16 => array('script_id' => 'jiqz', 'category_name' => 'games') ,
            17 => array('script_id' => 'oplh', 'category_name' => 'games') ,
            18 => array('script_id' => 'olkn', 'category_name' => 'games') ,
            19 => array('script_id' => 'ycjt', 'category_name' => 'games') ,
            20 => array('script_id' => 'qzvj', 'category_name' => 'math & science') ,
            21 => array('script_id' => 'joxi', 'category_name' => 'math & science') ,
            22 => array('script_id' => 'dwyva', 'category_name' => 'apps') ,
            23 => array('script_id' => 'gjrn', 'category_name' => 'apps') ,
            24 => array('script_id' => 'jyema', 'category_name' => 'apps') ,
            25 => array('script_id' => 'opnza', 'category_name' => 'apps') ,
            26 => array('script_id' => 'kfpna', 'category_name' => 'gadgets') ,
            27 => array('script_id' => 'syuc', 'category_name' => 'create interactive tutorials') ,
        ),
        4 => array(
            1 => array('script_id' => 'jokl', 'category_name' => "if you've never programmed") ,
            2 => array('script_id' => 'ayyxc', 'category_name' => "if you've never programmed") ,
            3 => array('script_id' => 'fraoa', 'category_name' => "if you've never programmed") ,
            4 => array('script_id' => 'jiqz', 'category_name' => "if you've never programmed") ,
            5 => array('script_id' => 'gjrn', 'category_name' => "if you've never programmed") ,
            6 => array('script_id' => 'opnza', 'category_name' => "if you've never programmed") ,
            7 => array('script_id' => 'qzvj', 'category_name' => "if you've never programmed") ,
            8 => array('script_id' => '', 'category_name' => "if you've never programmed") ,
            9 => array('script_id' => 'mizba', 'category_name' => "if you've done block programming") ,
            10 => array('script_id' => '', 'category_name' => "if you've done block programming") ,
            11 => array('script_id' => 'rdjh', 'category_name' => "if you have some programming experience") ,
            12 => array('script_id' => 'bbxr', 'category_name' => "if you have some programming experience") ,
            13 => array('script_id' => 'jiqz', 'category_name' => "if you have some programming experience") ,
            14 => array('script_id' => 'olbk', 'category_name' => "if you have some programming experience") ,
        ),
        5 => array(
            1 => array('script_id' => 'uxkfa', 'category_name' => "script ingredients") ,
            2 => array('script_id' => 'sylyb', 'category_name' => "script ingredients") ,
            3 => array('script_id' => 'kqow', 'category_name' => "script ingredients") ,
            4 => array('script_id' => 'enek', 'category_name' => "script ingredients") ,
            5 => array('script_id' => 'nrvba', 'category_name' => "script ingredients") ,
            6 => array('script_id' => '', 'category_name' => "script ingredients") ,
            7 => array('script_id' => 'gelka', 'category_name' => "script ingredients") ,
            8 => array('script_id' => '', 'category_name' => "code") ,
            9 => array('script_id' => 'wezka', 'category_name' => "code") ,
            10 => array('script_id' => 'cknm', 'category_name' => "code") ,
            11 => array('script_id' => 'wcyp', 'category_name' => "code") ,
            12 => array('script_id' => 'kvkfa', 'category_name' => "code") ,
            13 => array('script_id' => 'pctx', 'category_name' => "code") ,
            14 => array('script_id' => '', 'category_name' => "code") ,
            15 => array('script_id' => '', 'category_name' => "code") ,
            16 => array('script_id' => 'pwiea', 'category_name' => "user interface") ,
            17 => array('script_id' => '', 'category_name' => "user interface") ,
        ),
    );
    foreach ($pages_tutorials_mapping as $page_id => $mappings) {
        foreach($mappings as $seq => $mapping) {
            if(!$mapping['script_id']) {
                print_if_cli("skipped record with empty script_id");
                continue;
            }
            $query_tmpl = "select t.id from tutorials t "
                . "join scripts s on s.id = t.script_id "
                . "where s.script_id = '%s'";
            $query = sprintf($query_tmpl, $mapping['script_id']);
            $res = mysql_query($query);
            $tutorial_id = mysql_result($res, 0);
            if(!$tutorial_id) {
                // downloading script if has not been found in the database
                $script_id = download_script($mapping['script_id']);
                $script_sql = "select * from scripts s where id = ".$script_id;
                $res2 = mysql_query($script_sql);
                $script = mysql_fetch_assoc($res2);
                $hashes = get_hashes($script['description']);
                $features_sql = "select f.id, f.name from features f join scripts_features sc on f.id = sc.feature_id where sc.script_id = ".$tutorial_row['id'];
                $res2 = mysql_query($features_sql);
                $features = array();
                while($features_row = mysql_fetch_assoc($res2)) {
                    $features[] = $features_row['name'];
                }
                if(add_scripts_tutorials_mapping($script_id, $script['name'], $hashes, $features, $script['content'])) {
                    print_if_cli("  Added script-tutorial mapping for script ".$script['script_id']);
                }
                $query2 = sprintf($query_tmpl, $script['script_id']);
                //print_if_cli($query2);
                $res = mysql_query($query2);
                if(!$tutorial_id = mysql_result($res, 0)) {
                    print_if_cli("cannot find added tutorial");
                }
            }
            if(mysql_insert('tutorials_pages', array(
                'seq' => $seq,
                'tutorial_id' => $tutorial_id,
                'page_id' => $page_id,
                'category_name' => $mapping['category_name']
            ))) {
                print_if_cli("({$page_id} => {$seq}) Added tutorial to page mapping for tutorial {$mapping['script_id']} and page $page_id ");
            }
        }
    }
?>
