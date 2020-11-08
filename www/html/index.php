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

//items総数の取得
$total_count = get_items_count($db);

//ページネーションのトータルページ数を取得
$pages = get_pages_count($db);

//現在のページID取得
$now = get_page_id();

$items = get_open_items($db,$now);



include_once VIEW_PATH . 'index_view.php';