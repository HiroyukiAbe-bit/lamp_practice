<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'item.php';

$dbh = get_db_connect();

session_start();

$user_id = valid_session_user($_SESSION['userid']);

$err_msg = array();

$items = array_reverse(get_item($dbh));


if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'search'){

    $items = is_search_item($dbh,$_POST['search']);
    
    if(!is_array($items)) {
        $err_msg[] = $items;
        $items = array_reverse(get_item($dbh));
    }
}   

$item_view_number = 0;

include_once VIEW_PATH . 'index_view.php';
?>
