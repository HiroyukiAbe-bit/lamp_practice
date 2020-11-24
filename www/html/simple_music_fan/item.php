<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'user.php';
session_start();

$dbh = get_db_connect();

$err_msg = array();


$user_id = valid_session_user($_SESSION['userid']);

$item_id = (int)get_get('item_id');

$item = get_item($dbh,$item_id);

$is_purchase_history = is_purchase_history($dbh,$user_id,$item_id);

$comments = array_reverse(get_review($dbh,$item_id));

//商品購入者のレビューのバリデーション処理とレビューの書き込み
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'review') {
    $result = valid_and_regist_review($dbh,$_POST['score'],$_POST['review_comment'],$user_id,$item_id);
    if ($result == True) {
        $message = 'レビューを投稿しました';
    } else {
        $err_msg[] = 'レビューの書き込みに失敗しました';
    }
}

//商品レビューの削除
if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'comment_delete'){
    review_delete($dbh,$sql,$_POST['comment_delete']);
    $message = 'コメントを削除しました';
}

include_once VIEW_PATH . 'item_view.php';
