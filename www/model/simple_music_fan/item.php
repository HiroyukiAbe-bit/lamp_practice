<?php

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
      id,
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


function get_items_count($db){
  $sql = "
  SELECT 
    COUNT(*) AS count 
  FROM 
    items
  WHERE
    status = 1";

    return fetch_query($db,$sql);
}
