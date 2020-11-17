<?php
session_start();

//セッション変数を全て解除
$_SESSION = array();

//セッションクッキーを削除
if(isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '',time() - 1800, '/');
}

//セッションを破棄する
session_destroy();

header('Location: index.php');
exit;

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ログアウト画面</title>
    </head>
    <body>
        <p>ログアウトしています...</p>
    </body>
</html>