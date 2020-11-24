<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'item.php';


function h($str){
  $escape_str = htmlspecialchars($str,ENT_QUOTES,'utf-8');
  return $escape_str;
}

function get_get($str){
  if(isset($_GET[$str]) === TRUE){
    return $_GET[$str];
  }
  return '';
}

function valid_int_or_float($num){
  $score = $num;
  //$priceに小数点が含まれているか確認、含まれていればfloat型、なければint型にキャスト
  if(preg_match('/\./',$score) == TRUE) {
    $score = (float)$score;
    return $score;
  } else if (preg_match('/\./',$score) == FALSE) {
    $score = (int)$score;
    return $score;
  }
}


//$_SESSION['user_id]の判別用関数
function valid_session_user($user_id){
    if(!isset($user_id)) {
        $user_id = 'ユーザー';
        $_SESSION['userid'] = $user_id;
        return $user_id;
    }
    return $_SESSION['userid'];
}

function is_str_replase($search){
  $valid_str = '';
  $valid_str = str_replace([' ','　'],'',h($search));
  return $valid_str;
}

function is_search_item($dbh,$now,$str){
  $search = is_str_replase($str);

  if(mb_strlen($search) <= 0) {
    $err_msg = '1文字以上で入力してください';
    return $err_msg;
  }

  if(preg_match(SEARCH_STR,$search) == 0){
      $err_msg = '検索不可能な文字列が含まれています。';
    return $err_msg;
  }
  
  //ページネーションのトータルページ数を取得
  $page_data = get_pages_count($dbh,$search);
  //現在のページID取得
  $now = get_page_id();
  //現在のページのアイテム表示件数の開始数
  $start_item_number = ($now - 1) * MAX_VIEW + 1;
  //現在のページのアイテム表示件数の終了数
  $end_item_number = min($now * MAX_VIEW,$page_data['total_count']);

  $reverse = array_reverse(get_open_items($dbh,$now,$search)); 

  return array("total_pages" => $page_data['total_pages'], "total_count" => $page_data['total_count'], "now" => $now, "start_item_number" => $start_item_number,"end_item_number" => $end_item_number,"items" => $reverse);
}
