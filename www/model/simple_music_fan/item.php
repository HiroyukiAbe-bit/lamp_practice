<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'db.php';

function get_item($dbh) {
  $sql = "
  SELECT 
    id,
    name,
    status,
    img,
    price 
  FROM 
    items";
  
  return fetch_all_query($dbh,$sql);
}


//先生に質問する。
function get_search_item($dbh,$search = null){
  $params = [];
    $sql = "
    SELECT 
      item_id,
      name,
      status,
      img,
      price 
    FROM 
      items 
    WHERE 
      name";
    if($search !== null) {
    $params[] = "%" . $search . "%";
    $sql .="
    LIKE 
      ?";
    }
    return fetch_all_query($dbh,$sql,$params);
}


function get_items_count($dbh){
  $sql = "
  SELECT 
    COUNT(*) AS count 
  FROM 
    items
  WHERE
    status = 1";

    return fetch_query($dbh,$sql);
}

function get_open_items($dbh,$now){
  return get_items($dbh, true ,$now);
}

function get_items($dbh, $is_open = false, $now = null){
  $params = [];
  if($now !== null){
    if($now == 1){
      $params[] = $now -1;
      $params[] = MAX_VIEW;
    } else {
      $params[] = ($now -1)*MAX_VIEW;
      $params[] = MAX_VIEW;
    }  
  }
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      img,
      status
    FROM
      items

  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
    $sql .='
    ORDER BY item_id DESC LIMIT ?,?
    ';
  }

  return fetch_all_query($dbh, $sql,$params);
}

//ページネーションのトータルページ数を入れる関数
function get_pages_count($dbh){
  //itemsテーブル内に入っているレコードの数を変数に入れる
  $total_count = get_items_count($dbh);

  $total_count = $total_count['count'];
  //ページ数を変数に代入
  $pages = (int)ceil($total_count / MAX_VIEW);

  //ページ数を返す
  return array("total_count" => $total_count, "total_pages" => $pages);
} 

//現在いるページのIDを取得する関数
function get_page_id(){
  if(!isset($_GET['page_id'])){
    $now = 1;
    return $now;
  } else {
    $now = $_GET['page_id'];
    return $now;
  }
}