<?php
$host = 'mysql';
$username = 'hiroyuki';
$password = 'password';
$dbname = 'simple_music_fan';
$charset = 'utf8';

//MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir = './img/';

$err_msg = array();

$seach = '';

session_start();

if(!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 'ユーザー';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'seach'){
    $seach = htmlspecialchars($_POST['seach'],ENT_QUOTES,'utf-8');
    $seach = str_replace([' ','　'],'',$seach);
    if(preg_match('/^[a-zA-Zａ-ｚＡ-Ｚ0-9０-９ぁ-んァ-ヶｦ-ﾟー-龥]+$/u',$seach) == 0){
        $err_msg = '正しい文字列で入力してください。';
    }
}

try {
    //データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    
    //アップロードしたファイル名を取得
    try{
        //SQL文を作成
        $sql = 'SELECT id,name,status,img,price FROM items';
        //SQL文を実行するための準備
        $stmt = $dbh->prepare($sql);
        //SQLの実行
        $stmt->execute();
        //レコードを取得
        $rows = $stmt->fetchAll();
        //配列の情報の順番を逆にする。
        $reverse = array_reverse($rows);
        
    } catch(PDOException $e) {
        throw $e;
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'seach'){
        try {
            //SQL文を作成
            $sql = 'SELECT id,name,status,img,price FROM items WHERE name LIKE :name';
            //SQL文を実行するための準備
            $stmt = $dbh->prepare($sql);
            // //プレースホルダに値をバインド
            $stmt->bindValue(':name','%'.$seach.'%');
            //SQLの実行
            $stmt->execute();
            //レコードを取得
            $rows = $stmt->fetchAll();
            
            $reverse = array_reverse($rows);
        } catch(PDOException $e) {
            throw $e;
        }
    }   
    
    
} catch(PDOException $e) {
    $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
}

$i = 0;
?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/top.css">
    </head>
    <body>
        <header>
            <div class="top_menu">
                <div><a href="./index.php"><img src="./img/logo.png"></a></div>
                <div class="top_right">
                    <div class="top_item">
                        <div class="center">
                            <form method="post">
                                <input type="text" name="seach" value="<?php if($seach !== '') { echo $seach;}?>">
                                <input type="submit" value="商品検索">
                                <input type="hidden" name="type" value="seach">
                            </form>
                            <?php 
                                if (count($err_msg) > 0) {
                                    foreach ((array)$err_msg as $value): ?>
                                        <ul>
                                            <li>
                                                <?php print $value; ?>    
                                            </li>
                                        </ul>
                                    <?php endforeach; ?>
                            <?php  } ?>
                        </div>
                    </div>
                    <div class="top_item">
                        <div class="center">
                            <a href="./carts.php">
                                <img src="./img/cart_none.png">
                            </a>
                        </div>
                    </div>
                    <div class="top_item">
                        <div class="center">
                            <div>
                                <?php if(isset($_SESSION['userid'])){
                                    print 'ようこそ!' . $_SESSION['userid'] . 'さん';
                                }
                                ?>
                            </div>
                            <div class="right">
                                <?php if(isset($_SESSION['userid'])){
                                        if($_SESSION['userid'] === 'ユーザー') {?>
                                        <a href="./login.php">ログイン</a>
                                    <?php } else {?>
                                        <a href="./logout.php">ログアウト</a>
                                    <?php } 
                                 } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="main_menu">
            <nav>
                <ul>
                    <li>
                        <div class="menu_h">
                            <img width="150px" src="./img/category.png">
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/guiter.png">
                                <!--<a class="noline" href="./guiter.php">ギター</a>-->
                                <a class="noline" href="./category.php?category=ギター">ギター</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/bass.png">
                                <!--<a class="noline" href="./base.php">ベース</a>-->
                                <a class="noline" href="./category.php?category=ベース">ベース</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/piano.png">
                                <!--<a class="noline" href="./piano.php">キーボード</a>-->
                                <a class="noline" href="./category.php?category=キーボード">キーボード</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/drum.png">
                                <!--<a class="noline" href="./drum.php">ドラム</a>-->
                                <a class="noline" href="./category.php?category=ドラム">ドラム</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/speaker.png">
                                <!--<a class="noline" href="./speaker.php">スピーカー</a>-->
                                <a class="noline" href="./category.php?category=スピーカー">スピーカー</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/mente.png">
                                <!--<a class="noline" href="./mente.php">メンテ用品</a>-->
                                <a class="noline" href="./category.php?category=メンテ用品">メンテ用品</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/other.png">
                                <!--<a class="noline" href="./other.php">その他</a>-->
                                <a class="noline" href="./category.php?category=その他">その他</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>
            <main>
                <div class="item_table">
                    <div class="item_table2">
                        <div class="center">
                            <?php if(isset($_POST['type'])) {
                                    if($_POST['type'] === 'seach') {
                                        print '【検索結果】';
                                        if(count($reverse) === 0) { ?>
                                            <p>    
                                                <?php print '該当する検索結果がありません'; ?>
                                            </p>
                                        <?php } 
                                    }
                                } else {
                                    print '【新規アイテム】';
                                } ?>
                        </div>
                        <?php if(count($err_msg) === 0 && isset($_POST['type'])) {
                            foreach ($reverse as $value) : 
                                if ($value['status'] === 1) {?>
                                    <table>
                                        <tr>
                                            <td><p class="center"><?php print $value['name']; ?></p>
                                                <div class="center2">
                                                    <a class="center" href="<?php print './item.php?item_id=' . $value['id']; ?>">
                                                        <img class="img_size" src="<?php print $img_dir . $value['img']; ?>">
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                            <?php } 
                            endforeach; ?>
                        <?php } else {
                            foreach ($reverse as $value) : 
                                if ($value['status'] === 1) {
                                    if($i >= 4) {
                                        break; 
                                    } ?>
                                    <table>
                                        <tr>
                                            <td><p class="center"><?php print $value['name']; ?></p>
                                                <div class="center2">
                                                    <a class="center" href="<?php print './item.php?item_id=' . $value['id']; ?>">
                                                        <img class="img_size" src="<?php print $img_dir . $value['img']; ?>">
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                        <?php $i++;
                                }
                        endforeach; ?>
                        <?php } ?>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>