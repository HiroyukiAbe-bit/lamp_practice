<?php
require_once '../../conf/simple_music_fan/const.php';

function get_db_connect(){
  $dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset='.DB_CHARSET;

  try {
    //データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

function fetch_all_query($dbh, $sql, $params = array()){
  try{
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }  catch(PDOException $e) {
    throw $e;
  }
}

function fetch_query($dbh, $sql, $params = array()){
  try{
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
  }catch(PDOException $e){
    throw $e;
  }
  return false;
}