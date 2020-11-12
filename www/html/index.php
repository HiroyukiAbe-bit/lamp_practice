<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

//ページネーションのトータルページ数を取得
$page_data = get_pages_count($db);

//現在のページID取得
$now = get_page_id();

//現在のページのアイテム表示件数の開始数
$start_item_number = ($now - 1) * MAX_VIEW + 1;
//現在のページのアイテム表示件数の終了数
$end_item_number = min($now * MAX_VIEW,$page_data['total_count']);

$items = get_open_items($db,$now);

include_once VIEW_PATH . 'index_view.php';