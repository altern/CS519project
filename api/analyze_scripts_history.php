<?php

include('config.php');
include('functions.php');
//	ini_set('max_execution_time', 60*20);

error_reporting(E_ERROR);

mysql_query('truncate table scripts_scores');
mysql_query('truncate table scripts_history');

$scripts_sql = "select s.id sid, s.name script_name, s.script_id, s.date script_date, 
s.positivereviews, s.cumulativepositivereviews, s.installations, s.runs,
a.id aid, a.name author_name, a.author_id author_id
from scripts s 
join authors a on a.id = s.author_id 
where s.id not in (select script_id from tutorials)";

$res = mysql_query($scripts_sql);
$count = 0;
$row_count = 0;
$total_row_count = mysql_num_rows($res);
while($script_row = mysql_fetch_assoc($res)) {
    $row_count++;
        
    //$features_sql = "select f.id, f.name from features f join scripts_features sc on f.id = sc.feature_id where sc.script_id = ".$tutorial_row['id'];
//    $res2 = mysql_query($features_sql);
//    $features = array();
//    while($features_row = mysql_fetch_assoc($res2)) {
//        $features[] = $features_row['name'];
//    }
//
//    $hashes = get_hashes($tutorial_row['description']);
    $script_id_to_download = $script_row['script_id'];
    $url_template = 'http://touchdevelop.com/api/%s/base' ;
    $url_of_base_script = sprintf($url_template, $script_id_to_download);
    $succ_id = $script_id_to_download;
    $base_id = null;
    $scores = array(
        'positivereviews' => $script_row['positivereviews'],
        'cumulativepositivereviews' => $script_row['cumulativepositivereviews'],
        'installations' => $script_row['installations'],
        'runs' => $script_row['runs']
    );
    $history_count = 0;
    print_if_cli(" $row_count / $total_row_count | Adding history for script {$script_row['script_id']}, author ({$script_row['author_id']} => {$script_row['author_name']}) ...");
    print_if_cli("Initial scores for script {$script_id_to_download}:" . var_export($scores, true));
    while($base_script_contents = file_get_contents($url_of_base_script)) {
        $base_script_json = json_decode($base_script_contents, true);
        $date = date("Y-m-d H:i:s", $base_script_json['time']);
        
        if($script_history_id = add_script_history($script_row['sid'], $script_row['aid'], $base_script_json['id'], $date, $base_id, $succ_id )) {
            //$features_dump = var_export($features, true);
            print_if_cli("  Added record to the table scripts_history ({$script_history_id}) : "
            . " script_id => {$script_row['sid']},"
            . " author_id => {$script_row['aid']},"
            . " node_id => {$base_script_json['id']},"
            . " date => {$date},"
            . " {$base_id}, {$succ_id}");
            
            if($script_row['author_id'] == $base_script_json['userid']) {
                $score_metric = array_keys($scores);
                foreach($score_metric as $score_metric) {
                    $scores[$score_metric] += $base_script_json[$score_metric];
                }
            }
        }
        $history_count++;
        
        $succ_id = $base_script_json['id'];
        $url_of_base_script = sprintf($url_template, $base_script_json['id']);
    }
    print_if_cli("Length of history tail for script {$script_id_to_download} is {$history_count}");
    $scores['script_id'] = $script_row['sid'];
    if(mysql_insert('scripts_scores', $scores)) {
        print_if_cli("Aggregated scores for script {$script_id_to_download}:" . var_export($scores, true));
    }
    $count++;
}