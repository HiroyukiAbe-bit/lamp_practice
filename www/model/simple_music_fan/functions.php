<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'item.php';


function h($str){
  $escape_str = htmlspecialchars($str,ENT_QUOTES,'utf-8');
  return $escape_str;
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

function is_search_item($dbh,$str){
  $search = is_str_replase($str);

  if(mb_strlen($search) <= 0) {
    $err_msg = '1文字以上で入力してください';
    return $err_msg;
  }

  if(preg_match(SEARCH_STR,$search) == 0){
      $err_msg = '検索不可能な文字列が含まれています。';
    return $err_msg;
  }

  $reverse = array_reverse(get_search_item($dbh,$search)); 
  return $reverse;
}
