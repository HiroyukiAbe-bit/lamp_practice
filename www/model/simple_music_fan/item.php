<?php
require_once '../../conf/simple_music_fan/const.php';
require_once MODEL_PATH . 'db.php';

function get_item($dbh,$item_id) {
  $sql= "
  SELECT 
    item_id,
    name,
    stock,
    img,
    price,
    comment,
    category 
  FROM 
    items
  WHERE
    item_id = ?";
  
  return fetch_query($dbh,$sql,[$item_id]);
}



function get_items_count($dbh,$search = null){
  $params = [];
  $sql = "
  SELECT 
    COUNT(*) AS count 
  FROM 
    items
  WHERE
    status = 1";
  if($search !== null) {
    $params[] = "%" . $search . "%";
    $sql .="
    AND name LIKE ?
    ";
    }

    return fetch_query($dbh,$sql,$params);
}

function get_open_items($dbh,$now,$search = null){
  return get_items($dbh, true ,$now,$search);
}

function get_items($dbh, $is_open = false, $now = null , $search = null){
  $params = [];
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      img,
      status
    FROM
      items
  ";
  if($is_open === true){
    $sql .="
      WHERE status = 1
    ";
  }
  if($search !== null) {
    $params[] = "%" . $search . "%";
    $sql .="
    AND name LIKE ?
    ";
    }
  if($now !== null){
    if($now == 1){
      $params[] = $now -1;
      $params[] = MAX_VIEW;
    } else {
      $params[] = ($now -1)*MAX_VIEW;
      $params[] = MAX_VIEW;
    }  
    $sql .="
     ORDER BY item_id DESC LIMIT ?,?
    ";
  }

  return fetch_all_query($dbh,$sql,$params);
}

function is_purchase_history($dbh,$user_id,$item_id){
  //SQL文を作成
  $sql = '
  SELECT 
    count(*) 
  FROM 
    purchase 
  WHERE 
    user_id = ? 
  AND 
    item_id = ?';

  return fetch_Column($dbh, $sql, [$user_id,$item_id]);
}


//非DB関数


function get_pages_count($dbh,$search = null){
  
  $total_count = get_items_count($dbh,$search);

  $total_count = $total_count['count'];
  
  $pages = (int)ceil($total_count / MAX_VIEW);

  
  return array("total_count" => $total_count, "total_pages" => $pages);
} 


function get_page_id(){
  if(!isset($_GET['page_id'])){
    $now = 1;
    return $now;
  } else {
    $now = $_GET['page_id'];
    return $now;
  }
}

