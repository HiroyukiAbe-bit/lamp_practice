<?php
require_once MODEL_PATH . 'db.php';

function insert_review($dbh,$user_id,$item_id,$comment,$score){
  $create_datetime = date('Y-m-d H:i:s');
    $sql = "
    INSERT INTO 
      reviews(
        userid,
        item_id,
        comment,
        score,
        createdate) 
      VALUES (?,?,?,?,?)";
  return execute_query($dbh,$sql,[$user_id,$item_id,$comment,$score,$create_datetime]);
}


function valid_score_review($num,$comment){
  $score = valid_int_or_float($num);
  $err_msg = '';

  //float型の場合はエラーメッセージを代入する。
  if(is_float($score) === TRUE) {
    $err_msg = '正しい値で評価してください';
    return $err_msg;
  }
  
  $review_comment = str_replace([' ','　'],'',$comment);

  if(mb_strlen($review_comment) === 0) {
    $err_msg ='コメントを入力してください';
    return $err_msg;
  }

  return array("score" => $score, "review_comment" => $comment);
}


function valid_and_regist_review($dbh,$num,$comment,$user_id,$item_id){
  $err_msg = '';
  $review_parts = valid_score_review($num,$comment);
    if(!is_array($review_parts)){
        $err_msg = $review_parts;
        return $err_msg;
    } else {
        $score = $review_parts['score'];
        $review_comment = $review_parts['review_comment'];
    }
    //レビューの投稿内容をデータベースへ書き込み
    return insert_review($dbh,$user_id,$item_id,$review_comment,$score);
}


function review_delete($dbh,$comment_id){
  $sql ="
  DELETE 
    FROM 
  reviews 
  WHERE id = ?";

  return execute_query($dbh,$sql,[$comment_id]);
}

function get_review($dbh,$item_id){
    $sql = "
    SELECT 
      id, 
      userid, 
      comment, 
      score, 
      createdate 
    FROM 
      reviews 
    WHERE 
      item_id = ?";
    
    return fetch_all_query($dbh,$sql,[$item_id]);
}