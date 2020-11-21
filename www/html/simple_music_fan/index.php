<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'item.php';

$err_msg = array();

$dbh = get_db_connect();

session_start();

$user_id = valid_session_user($_SESSION['userid']);


//ページネーションのトータルページ数を取得
$page_data = get_pages_count($dbh);

//現在のページID取得
$now = get_page_id();

//現在のページのアイテム表示件数の開始数
$start_item_number = ($now - 1) * MAX_VIEW + 1;

//現在のページのアイテム表示件数の終了数
$end_item_number = min($now * MAX_VIEW,$page_data['total_count']);

$items = get_open_items($dbh,$now);


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
