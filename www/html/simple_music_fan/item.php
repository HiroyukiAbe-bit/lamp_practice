<?php
$host = 'localhost';
$username = 'codecamp37046';
$password = 'codecamp37046';
$dbname = 'codecamp37046';
$charset = 'utf8';

//MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir = './img/';

$err_msg = array();

$item_number = 0;
$amount = 0;
$userid = '';
$comments = array();
$score = 0;

session_start();

if(!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 'ユーザー';
}

$item_id = (int)$_GET['item_id'];


if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'review') {
    $score = $_POST['score'];
    //$priceに小数点が含まれているか確認、含まれていればfloat型、なければint型にキャスト
    if(preg_match('/\./',$score) == TRUE) {
        $score = (float)$score;
    } else if (preg_match('/\./',$score) == FALSE) {
        $score = (int)$score;
    }
    
    //float型の場合はエラーメッセージを代入する。
    if(is_float($score) === TRUE) {
        $err_msg[] = '正しい値で評価してください';
    }
    
    //$nameにPOSTされた商品名を代入、その際半角、全角空白を置換
    $review_comment = str_replace([' ','　'],'',$_POST['review_comment']);
    
    if(mb_strlen($review_comment) === 0) {
        $err_msg[] ='コメントを入力してください';
    }
}


try {
    //データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    
    try{
        //SQL文を作成
        $sql = 'SELECT id,name,stock,img,price,comment,category FROM items';
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
    
    try{
        //SQL文を作成
        $sql = 'SELECT count(*) FROM puchase WHERE user_id = "' . $_SESSION['userid'] . '" AND item_id = ' . $item_id;
        //SQL文を実行するための準備
        $stmt = $dbh->prepare($sql);
        //SQLの実行
        $stmt->execute();
        //レコードを取得
        $puchase_num = $stmt->fetchColumn();
        
    } catch(PDOException $e) {
        throw $e;
    }
    
    //エラーがなければ、アップロードした情報をDBへ登録。
    if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] ==='POST' && $_POST['type'] === 'review'){
        $userid = $_SESSION['userid'];
        
        $create_datetime = date('Y-m-d H:i:s');
        try {
            //SQL文を作成
            // $sql = 'INSERT INTO reviews(userid,comment,score,createdate) VALUES (:userid,:comment,:score,:createdate)';
            $sql = 'INSERT INTO reviews(userid,item_id,comment,score,createdate) VALUES (:userid,:itemid,:comment,:score,:createdate)';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':userid', $userid);
            $stmt->bindValue(':itemid', $item_id);
            $stmt->bindValue(':comment', $review_comment);
            $stmt->bindValue(':score', $score, PDO::PARAM_INT);
            $stmt->bindValue(':createdate', $create_datetime);
            //SQLを実行
            $stmt->execute();
            
            $message = 'レビューを投稿しました';
            //最後にインサートしたIDを取得
            $id = $dbh->LastInsertID();
        } catch (PDOException $e){
            throw $e;
        }
    }
    
    //商品の削除
    if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'comment_delete'){
        $comment_id = $_POST['comment_delete'];
        $message = 'アイテムを削除しました';
        
        try {
            //SQL文を作成
            $sql = 'DELETE FROM reviews WHERE id = :id';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':id',$comment_id, PDO::PARAM_INT);
            //$SQlを実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    
    try{
        //SQL文を作成
        // $sql = 'SELECT id, userid, comment, score, createdate FROM reviews';
        $sql = 'SELECT id, userid, comment, score, createdate FROM reviews WHERE item_id = ' . $item_id;
        //SQL文を実行するための準備
        $stmt = $dbh->prepare($sql);
        //SQLの実行
        $stmt->execute();
        //レコードを取得
        $rows = $stmt->fetchAll();
        //配列の情報の順番を逆にする。
        $comments = array_reverse($rows);
        
    } catch(PDOException $e) {
        throw $e;
    }
    
    
} catch(PDOException $e) {
    $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
}

?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/item.css">
    </head>
    <body>
        <header>
            <div class="top_menu">
                <div><a href="./index.php"><img src="./img/logo.png"></a></div>
                <div class="top_right">
                    <div class="top_item">
                        <?php
                        if(count($err_msg) > 0) {
                            foreach ($err_msg as $value) {
                                print $value;
                            }
                        } elseif (count($err_msg) === 0 && isset($_POST['type'])) {
                                if($_POST['type'] === 'cart_insert') {
                                    print 'カートに追加しました。';
                                }
                            }?>
                        <div class="center">
                            <form method="post" action="./index.php">
                            <input type="text" name="seach" value="">
                            <input type="hidden" name="type" value="seach">
                            <input type="submit" value="商品検索"></form>
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
                        <?php 
                        foreach ($reverse as $value) :
                            if ($value['id'] === (int)$item_id) { ?>
                            <div class="center">【<?php print $value['name']; ?>】</div>
                                <table>
                                    <tr>
                                        <td>
                                            <div class="details">
                                                <div>
                                                    <img class="img_size" src="<?php print $img_dir . $value['img']; ?>">
                                                </div>
                                                <div>
                                                    <div>カテゴリー：<?php print $value['category']; ?></div>
                                                    <div>商品金額：<?php print $value['price'];?>円</div>
                                                    <div>
                                                        <form method="post" action="./carts.php">
                                                            <div>数量：<input type="text" name="amount" value=""></div>
                                                            <div>
                                                                <?php if ($value['stock'] === 0) : ?>
                                                                    <p class ="red">売り切れ</p>
                                                                <?php elseif ($value['stock'] > 0) : ?>
                                                                    <input type="submit" value="カートに追加">
                                                                <?php endif; ?>
                                                            </div>
                                                            <input type="hidden" name="type" value="cart_insert">
                                                            <input type="hidden" name="item_number" value="<?php print $value['id']; ?>">
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div><?php print $value['comment']; ?></div>
                                            <div>【お客様からの評価レビュー】</div>
                                            <div>
                                                <?php foreach ($comments as $value) : ?>
                                                    <div class="comment">
                                                        <div class="flex">
                                                            <div class="speace">
                                                                ユーザ名:<?php print $value['userid']; ?>
                                                            </div>
                                                            <div class="speace">
                                                                評価:<?php print str_repeat("★",$value['score']); ?> 
                                                            </div>
                                                            <div class="speace">
                                                                <?php print $value['createdate']; ?>
                                                            </div>
                                                            <div class="speace">
                                                                <?php if ($value['userid'] === $_SESSION['userid']) : ?>
                                                                    <form method="post"><input type="submit" value="コメント削除">
                                                                    <input type="hidden" name="type" value="comment_delete">
                                                                    <input type="hidden" name="comment_delete" value="<?php print $value['id']; ?>">
                                                                    </form> 
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="speace">
                                                            コメント:<?php print $value['comment']; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if(isset($_SESSION['userid']) && $puchase_num !== 0): ?>
                                                        <div class="right">
                                                            <form method="post">
                                                                <div class="padding">
                                                                    製品評価をご入力ください
                                                                    <select name="score">
                                                                        <option value="0">--評価点--</option>
                                                                        <option value="1">★</option>
                                                                        <option value="2">★★</option>
                                                                        <option value="3">★★★</option>
                                                                        <option value="4">★★★★</option>
                                                                        <option value="5">★★★★★</option>
                                                                    </select>
                                                                </div>
                                                                <div class="padding">
                                                                    <textarea name="review_comment"  rows="4" cols="40" value=""></textarea>
                                                                </div>
                                                                <div class="padding">
                                                                    <input type="hidden" name="type" value="review">
                                                                    <input type="submit" value="レビューを投稿">
                                                                </div>
                                                            </form>
                                                        </div>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                    <?php } endforeach; ?>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>