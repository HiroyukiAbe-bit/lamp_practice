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

$message = '';

$seach = '';

$userid = '';
$user_password = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'seach'){
    $seach = htmlspecialchars($_POST['seach'],ENT_QUOTES,'utf-8');
    $seach = str_replace([' ','　'],'',$seach);
    if(preg_match('/^[a-zA-Zａ-ｚＡ-Ｚ0-9０-９ぁ-んァ-ヶｦ-ﾟー-龥]+$/u',$seach) == 0){
        $err_msg[] = '正しい文字列で入力してください。';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'login') {
    $userid = $_POST['userid'];
    $user_password = $_POST['password'];
    
    //useridにPOSTされた文字列をチェック
    if (preg_match("/^[0-9a-zA-Z]{6,10}$/",$userid) === 0) {
        $err_msg[] = 'USERIDは6文字以上、10文字以内の半角英数字で入力願います。'; 
    }
    if (preg_match("/^[0-9a-zA-Z]{6,10}$/",$user_password) === 0) {
        $err_msg[] = 'PASSWORDは6文字以上、10文字以内の半角英数字で入力願います。';
    }
}

session_start();

if(!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 'ユーザー';
}

//DB内でPOSTされたメールアドレスを検索
try {
    //データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    
    //データベースからuserid一致の確認。
    if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
        $sql = 'SELECT userid,password FROM users WHERE userid = :userid';
        //SQL文の準備
        $stmt = $dbh->prepare($sql);
        //プレースホルダにバインド
        $stmt->bindValue(':userid', $userid);
        //SQL文を実行
        $stmt->execute();
        //変数に一致したものを入れる
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw $e;
        }
        
        //useridがDB内に存在しているか確認
        if ($row['userid'] !== $userid) {
          $err_msg[] = 'ご指定のUSERIDは存在しません';
        }
        
        if (count($err_msg) === 0) {
            //パスワード確認後sessionにメールアドレスを渡す
            if ($user_password === $row['password']) {
                session_regenerate_id(true); //session_idを新しく生成し、置き換える
                $_SESSION['userid'] = $row['userid'];
                $message = 'ログインしました';
                header('Location: index.php');
                exit;
            }    
        }
         
        
    }
}  catch(PDOException $e) {
   $err_msg[] = $e->getMessage();
}

?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/login.css">
    </head>
    <body>
        <header>
            <div class="top_menu">
                <div><a href="./index.php"><img src="./img/logo.png"></a></div>
                <div class="top_right">
                    <div class="top_item">
                        <div class="center"><form method="post">
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
                <div class="item_table">
                    <div class="item_table2">
                        <table>
                            <tr>
                                <th>
                                    【ログイン画面】
                                </th>
                            </tr>
                            <tr>
                                <td class="center">
                                    <ul>
                                        <?php foreach ($err_msg as $value) : ?>
                                            <li> 
                                                <?php  print $value; ?> 
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php if(mb_strlen($message) > 0) : ?>
                                        <p>
                                            <?php print $message; ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <form method="post">
                                        <div class="login_submit">
                                            USERID：<input type="text" name="userid" value="">
                                        </div>
                                        <div class="login_submit">
                                            PASSWORD：<input type="text" name="password" value="">
                                        </div>
                                        <div class="login_submit">
                                            <input type="submit" value="ログイン">
                                            <input type="hidden" name="type" value="login">
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="center">
                                    <a href="./user_create.php">新規アカウント登録</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>