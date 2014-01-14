<?php

include_once('api/config.php');
include_once('api/functions.php');

if(isset($_GET['download_csv'])) {
    include_once('api/queries_list.php');
    export_query_results($_GET['download_csv']);
}
if(isset($_GET['download_csv2'])) {
    include_once('api/queries_list2.php');
    export_query_results($_GET['download_csv']);
}