<?php
require_once '../../conf/simple_music_fan/const.php';

function get_db_connect(){
  $dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset='.DB_CHARSET;

  try {
    //データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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

function fetch_Column($dbh, $sql, $params = array()){
  try{
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
  }catch(PDOException $e){
    throw $e;
  }
  return false;
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
    throw $e;
  }
  return false;
}