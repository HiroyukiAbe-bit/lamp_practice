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
$itemname = '';
$amount = 0;
$userid = '';
$message = '';
$seach = '';

$reverse = array();
$small_sum = 0;
session_start();

if(!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 'ユーザー';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'seach'){
    $seach = htmlspecialchars($_POST['seach'],ENT_QUOTES,'utf-8');
    $seach = str_replace([' ','　'],'',$seach);
    if(preg_match('/^[a-zA-Zａ-ｚＡ-Ｚ0-9０-９ぁ-んァ-ヶｦ-ﾟー-龥]+$/u',$seach) == 0){
        $err_msg[] = '正しい文字列で入力してください。';
    }
}


try {
    //データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    
    //商品ページよりカートにものを入れる。
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'puchase' && count($err_msg) === 0) {
        $create_datetime = date('Y-m-d H:i:s');
        
        $dbh->beginTransaction();
        
        try{
        //SQL文を作成
        $sql = 'SELECT items.id, items.name,items.category,items.price, items.stock, items.img, carts.user_id, carts.amount FROM items JOIN carts ON items.id = carts.item_id';
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
        
        
        foreach ($reverse as $value) {
            if ($value['amount'] > 0) {
                $item_number = $value['id'];
                $itemname = $value['name'];
                $userid = $value['user_id'];
                $amount = $value['amount'];
                
                
                $update_stock = $value['stock'] - $value['amount']; 
                
                
                if ($update_stock < 0) {
                    $err_msg[] = '在庫数が足りません';
                } 
                
                if (count($err_msg) === 0) {
                    $update_datetime = date('Y-m-d H:i:s');
                    
                    try {
                        //SQL文の作成
                        $sql = 'UPDATE items SET stock = :stock ,updatedate = :update WHERE id = :id';
                        //SQL文実行の為の準備
                        $stmt = $dbh->prepare($sql);
                        //プレースホルダに値をバインド
                        $stmt->bindValue(':stock',$update_stock,PDO::PARAM_INT);
                        $stmt->bindValue(':update',$update_datetime);
                        $stmt->bindValue(':id',$item_number,PDO::PARAM_INT);
                        
                        //SQL文の実行
                        $stmt->execute();
                    } catch (PDOException $e) {
                        throw $e;
                    }
                    
                    
                    try{
                    //SQL文の作成
                    $sql = 'INSERT INTO puchase(item_id,itemname,user_id,amount,createdate) VALUE(:item_id,:itemname,:user_id,:amount,:createdate)';
                    //SQL文の準備
                    $stmt = $dbh->prepare($sql);
                    //プレースホルダに値をバインド
                    $stmt->bindValue(':item_id',$item_number,PDO::PARAM_INT);
                    $stmt->bindValue(':itemname',$itemname);
                    $stmt->bindValue(':user_id',$userid);
                    $stmt->bindValue(':amount',$amount,PDO::PARAM_INT);
                    $stmt->bindValue(':createdate',$create_datetime);
                    //SQL文の実行
                    $stmt->execute();
                    
                    //最後にインサートしたIDを取得
                    $id = $dbh->LastInsertID();
                
                    } catch(PDOException $e) {
                        throw $e;
                    }
                }
                
            }
        }
        
        try {
            //SQL文の作成
            $sql = 'TRUNCATE TABLE carts';
            //SQL文の準備
            $stmt = $dbh->prepare($sql);
            //SQLの実行
            $stmt->execute();
            
            $dbh->commit();
            $message = '購入が完了しました';
            
        } catch (PDOException $e) {
            //ロールバック処理
            $dbh->rollback();
            //例外をスロー
            throw $e;
        }
    }
} catch(PDOException $e) {
    $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
}

$sum = 0;
foreach ($reverse as $value) {
    if($value['amount'] > 0) {
        $sum += $value['price'] * $value['amount'];
    }
    $sum = floor($sum * 1.1);
}


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/puchase.css">
    </head>
    <body>
        <header>
            <div class="top_menu">
                <div><a href="./index.php"><img src="./img/logo.png"></a></div>
                <div class="top_right">
                    <div class="top_item">
                        <?php if (count($err_msg) > 0) {
                            foreach ($err_msg as $value) {
                                print $value;
                            }
                        } else {
                            print $message;     
                        }
                        ?>
                        <div class="center">
                            <form method="post">
                            <input type="text" name="seach" value="<?php if($seach !== '') { echo $seach;}?>">
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
                <div>
                    <div class="cart_h"></div>
                    <div class="center">【購入結果】</div>
                    <div class="carts">
                        <div class="width">
                            <?php 
                            if(count($err_msg) === 0) :
                                foreach ($reverse as $value): ?>
                               <table>
                                    <tr>
                                        <td>
                                            <img class="item_size" src="<?php print $img_dir . $value['img']; ?>">
                                        </td>
                                        <td>
                                            <?php print $value['name']; ?>
                                        </td>
                                        <td>
                                            カテゴリー:<?php print $value['category']; ?>
                                        </td>
                                        <td>
                                            購入数量:<?php print $value['amount']; ?>個
                                        </td>
                                        <td>
                                            ¥<?php print $value['price']; ?>
                                        </td>
                                        <td>
                                            <?php if($value['amount'] > 1) : ?>
                                            小計:¥<?php $small_sum = $value['amount'] * $value['price']; 
                                            print $small_sum; 
                                            ?>(税抜)
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table> 
                           <?php endforeach; ?>
                        </div>
                        <div class="cart_sum">
                            <p>合計金額：¥<?php print $sum; ?>(税込)</p>
                        </div>
                        <?php else : ?>
                            <div>
                                <p>一部商品の在庫が不足しております</p>
                                <p>大変申し訳ありませんが、購入可能な数量に修正し再度ご購入ください</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>