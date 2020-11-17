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

$seach ='';

$message = '';

$change_amount = '';

session_start();

if(!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 'ユーザー';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'change_amount') {
    $change_amount = htmlspecialchars($_POST['change_amount']);
    
    //$priceに小数点が含まれているか確認、含まれていればfloat型、なければint型にキャスト
    if(preg_match('/\./',$change_amount) == TRUE) {
        $change_amount = (float)$change_amount;
    } else if (preg_match('/\./',$change_amount) == FALSE) {
        $change_amount = (int)$change_amount;
    }
    
    //float型の場合はエラーメッセージを代入する。
    if(is_float($change_amount) === TRUE) {
        $err_msg[] = '注文数量は整数で入力してください';
    } elseif ($change_amount <=0 ) {
        $err_msg[] = '注文数量は1以上で入力してださい';
    } 
    
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
    
    
    //商品ページよりカートにものを入れる。
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'cart_insert' && count($err_msg) === 0 && isset($_SESSION['userid'])) {
        $item_number = (int)$_POST['item_number'];
        $amount = (int)htmlspecialchars($_POST['amount']);
        $_SESSION['cart'] = $_POST['type'];
        $userid = $_SESSION['userid'];
        $create_datetime = date('Y-m-d H:i:s');
        $message = '商品をカートに入れました';
        
        try{
            //SQL文の作成
            $sql = 'INSERT INTO carts(user_id,item_id,amount,createdate) VALUE(:user_id,:item_id,:amount,:createdate)';
            //SQL文の準備
            $stmt = $dbh->prepare($sql);
            //プレースホルダに値をバインド
            $stmt->bindValue(':user_id',$userid);
            $stmt->bindValue(':item_id',$item_number,PDO::PARAM_INT);
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
    
    //カートのアイテム数を変更する。
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'change_amount' && count($err_msg) === 0) {
        $change_cart_id = $_POST['change_cart_id'];
        $update_datetime = date('Y-m-d H:i:s');
        $message = '商品数量を変更しました';
        
        try {
            //SQL文の作成
            $sql = 'UPDATE carts SET amount = :amount ,updatedate = :update WHERE cart_id = :id';
            //SQL文実行の為の準備
            $stmt = $dbh->prepare($sql);
            //プレースホルダに値をバインド
            $stmt->bindValue(':amount',$change_amount,PDO::PARAM_INT);
            $stmt->bindValue(':update',$update_datetime);
            $stmt->bindValue(':id',$change_cart_id,PDO::PARAM_INT);
            
            //SQL文の実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    //カートから中身を削除する。
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'delete') {
        $delete_item_id = $_POST['delete_item_id'];
        unset($_SESSION['cart']);
        
        $message = 'カートの中から商品を削除しました';
        try {
            //SQL文の作成
            $sql = 'DELETE FROM carts WHERE cart_id = :id';
            //SQL文実行の為の準備
            $stmt = $dbh->prepare($sql);
            //プレースホルダに値をバイン���
            $stmt->bindValue('id',$delete_item_id);
            //SQL文の実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    
    try{
        //SQL文を作成
        //$sql = 'SELECT carts.cart_id, items.id,items.name,items.img,items.price,items.category,carts.amount FROM items LEFT OUTER JOIN carts ON items.id = carts.item_id';
        $sql = 'SELECT carts.cart_id, items.id,items.name,items.img,items.price,items.category,carts.amount FROM items JOIN carts ON items.id = carts.item_id';
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
        <link rel="stylesheet" href="/simple_music_fan/css/carts.css">
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
                        } elseif (mb_strlen($message)) {
                                print $message;
                            }?>
                        <div class="center">
                            <form method="post">
                            <input type="text" name="seach" value="<?php if($seach !== '') { echo $seach;}?>">
                            <input type="submit" value="商品検索"></form>
                        </div>
                    </div>
                    <div class="top_item">
                        <div class="center">
                            <a href="">
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
                    <div class="cart_h">【カートの中身】</div>
                    <div class="carts">
                        <?php 
                        if($_SESSION['userid'] === 'ユーザー') { ?>
                            <div class="width">
                                <div>
                                    <p>ログインしてからカートをご利用ください</p>
                                    <p><a href="./index.php">戻る</a></p>
                                </div>
                            </div>
                        <?php } else {
                                // echo '<pre>';
                                // var_dump($reverse);
                                // echo '</pre>';
                                ?>
                                <?php if (count($reverse) === 0) : ?>
                                    <p>カート内に商品がありません。</p>
                                <?php else : ?>
                                    <?php
                                    foreach ($reverse as $value) {
                                        /*if($value['amount'] > 0){*/ ?>
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
                                                        ¥<?php print $value['price']; ?>(税抜)
                                                    </td>
                                                    <td>
                                                        <form method="post">
                                                            <input class="amount_num" type="text" name="change_amount" value="<?php print $value['amount'] ?>" >
                                                            <input type="hidden" name="type" value="change_amount">
                                                            <input type="hidden" name="change_cart_id" value="<?php print $value['cart_id']; ?>">
                                                            <input type="submit" value="数量を変更する">
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form method="post">
                                                            <input type="hidden" name="type" value="delete">
                                                            <input type="hidden" name="delete_item_id" value="<?php print $value['cart_id']; ?>">
                                                            <input type="submit" value="削除">
                                                        </form>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php    
                                        /*}*/
                                    }
                                    ?>
                                    <?php if(isset($_SESSION['userid'])) { ?>
                                        <div class="cart_sum">
                                            合計金額：¥<?php print $sum; ?>(税込)
                                            <form method="post" action="./puchase.php">
                                                <input type="submit" value="購入する">
                                                <input type="hidden" name="type" value="puchase">
                                            </form>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>
                                
                                <?php
                            }
                        
                        ?>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>