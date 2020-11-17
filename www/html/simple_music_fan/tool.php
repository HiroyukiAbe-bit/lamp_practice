<?php 
$host = 'localhost'; //DBH取得用文言ローカルホスト
$username = 'codecamp37046'; //MySQLのユーザ名
$password = 'codecamp37046'; //MySQLのパスワード
$dbname = 'codecamp37046'; //MYSQLのDB名
$charset = 'utf8'; //データベースの文字コード

//MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir = './img/'; //アップロードした画像の保存ディレクトリ
$err_msg = array(); //エラーメッセージを入れる配列
$new_img_filename = ''; //アップロードした新しい画像ファイル名

$name = '';
$price = '';
$stock = '';
$st = 2;
$ca = '';
$comment = '';

$message ='';

$item_id = 0;

//在庫数の変更処理の場合に整数か浮動小数点型か判断する。
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'update_stock') {
    $update_stock = $_POST['update_stock'];
    
    //$priceに小数点が含まれているか確認、含まれていればfloat型、なければint型にキャスト
    if(preg_match('/\./',$update_stock) == TRUE) {
        $update_stock = (float)$update_stock;
    } else if (preg_match('/\./',$update_stock) == FALSE) {
        $update_stock = (int)$update_stock;
    }
    
    //float型の場合はエラーメッセージを代入する。
    if(is_float($update_stock) === TRUE) {
        $err_msg[] = '在庫の更新は0以上の整数でお願いします';
    }
    
}


//新規アイテム追加までの流れ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'insert_item'){
    //$nameにPOSTされた商品名を代入、その際半角、全角空白を置換
    $name = str_replace([' ','　'],'',$_POST['name']);
    
    if(mb_strlen($name) === 0) {
        $err_msg[] ='名前を入力してください';
    }
    
    //$priceにPOSTされた商品代金を代入
    $price = $_POST['price'];
    
    //$priceに小数点が含まれているか確認、含まれていればfloat型、なければint型にキャスト
    if(preg_match('/\./',$price) == TRUE) {
        $price = (float)$price;
    } else if (preg_match('/\./',$price) == FALSE) {
        $price = (int)$price;
    }
    
    //float型の場合はエラーメッセージを代入する。
    if(is_float($price) === TRUE) {
        $err_msg[] = '値段は0以上の整数で入力願います';
    }
    
    $stock = $_POST['stock'];
    
    if(preg_match('/\./',$stock) == TRUE) {
        $stock = (float)$stock;
    } else if (preg_match('/\./',$stock) == FALSE) {
        $stock = (int)$stock;
    }
    if(is_float($stock) === TRUE) {
        $err_msg[] = '在庫は0以上の整数で入力願います';
    }
    
    $st = (int)$_POST['status'];
    
    //HTTP POSTでファイルがアップロードされたかどうかチェック
    if(is_uploaded_file($_FILES['img']['tmp_name']) === TRUE) {
        //$_FILE['img']['tmp_name']の拡張子だけを変数に入れる。
        $extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
        //指定の拡張子かどうか調べる
        if($extension === 'jpg' || $extension === 'jpeg' || $extension ==='png'){
        //保存するファイル名を生成する。
            $new_img_filename = sha1(uniqid(mt_rand(),true)). '.' . $extension;
            //同名ファイルが存在するかどうかチェック
            if(is_file($img_dir . $new_img_filename) !==TRUE) {
            //ファイルをディレクトリに移動させる。失敗したらエラーメッセージに代入。
                if(move_uploaded_file($_FILES['img']['tmp_name'], $img_dir.$new_img_filename) !== TRUE){
                    $err_msg[] = 'ファイルアップロードに失敗しました';
                }
            } else {
            $err_msg[] = 'ファイルのアップロードに失敗しました、再度お試しください';
            }
        } else {
        $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPG、JPEG、PNGのいずれかで登録ください';
        }
    } else {
    $err_msg[] = 'ファイルを選択してください';
    }
    
    if($st === 0 || $st === 1){
        $status = $st;
    } else {
        $err_msg[] = 'ステータスは、公開または非公開で選択ください';
    }
    
    $ca = $_POST['category'];
    
    if(mb_strlen($ca) > 0) {
        $category = $ca;
    } else {
        $err_msg[] = 'カテゴリーはプルダウンの中より選択ください';
    }
    
    $comment = str_replace([' ','　'],'',$_POST['comment']);
    if ($comment < 0 || $comment > 100) {
        $err_msg[] = '商品説明は1文字以上100文字以内で登録ください';
    }
    
    if(mb_strlen($comment) === 0) {
        $err_msg[] ='商品説明を入力してください';
    }
}


try {
    //データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    
    //エラーがなければ、アップロードした情報をDBへ登録。
    if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] ==='POST' && $_POST['type'] === 'insert_item'){
        $create_datetime = date('Y-m-d H:i:s');
        try {
            //SQL文を作成
            $sql = 'INSERT INTO items(name,price,stock,status,category,img,comment,createdate) VALUES (:name,:price,:stock,:status,:category,:img,:comment,:createdate)';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':price', $price, PDO::PARAM_INT);
            $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':img', $new_img_filename);
            $stmt->bindValue(':comment', $comment);
            $stmt->bindValue(':createdate', $create_datetime);
            //SQLを実行
            $stmt->execute();
            
            $message = '商品を追加しました';
            //最後にインサートしたIDを取得
            $id = $dbh->LastInsertID();
        } catch (PDOException $e){
            throw $e;
        }
    }
    
    //在庫の数量変更
    if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'update_stock'){
        $update_datetime = date('Y-m-d H:i:s');
        $message = '在庫を更新しました';
        $item_id = $_POST['id'];
        
        try {
            //SQL文を作成
            $sql = 'UPDATE items SET stock = :stock, updatedate = :update WHERE id = :id';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':stock',$update_stock, PDO::PARAM_INT);
            $stmt->bindValue(':update', $update_datetime);
            $stmt->bindValue(':id',$item_id, PDO::PARAM_INT);
            //$SQlを実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    //値段更新処理
    if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'update_price'){
        $update_datetime = date('Y-m-d H:i:s');
        $update_price = (int)$_POST['update_price'];
        $message = '値段を更新しました';
        $item_id = $_POST['id'];
        try {
            //SQL文を作成
            $sql = 'UPDATE items SET price = :price WHERE id = :id';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':price',$update_price, PDO::PARAM_INT);
            $stmt->bindValue(':id',$item_id, PDO::PARAM_INT);
            //$SQlを実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    //ステータス更新処理
    if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'change_status'){
        $update_datetime = date('Y-m-d H:i:s');
        $status = (int)$_POST['change_status'];
        $message = 'ステータスを変更しました';
        $item_id = $_POST['id'];
        
        try {
            //SQL文を作成
            $sql = 'UPDATE items SET status = :status, updatedate = :update WHERE id = :id';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':status',$status, PDO::PARAM_INT);
            $stmt->bindValue(':update', $update_datetime);
            $stmt->bindValue(':id',$item_id, PDO::PARAM_INT);
            //$SQlを実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    //商品の削除
    if(count($err_msg) === 0 & $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'item_delete'){
        $update_datetime = date('Y-m-d H:i:s');
        $item_id = $_POST['id'];
        $message = 'アイテムを削除しました';
        
        try {
            //SQL文を作成
            $sql = 'DELETE FROM items WHERE id = :id';
            //SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            //SQL文のプレースホルダに値をバインド
            $stmt->bindValue(':id',$item_id, PDO::PARAM_INT);
            //$SQlを実行
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    
    
    //既存のアップロードされた画像ファイル名の取得
    try {
        //SQL文を作成
        $sql = 'SELECT id,name,price,stock,status,category,img,comment FROM items';
        //SQL文を実行するための準備
        $stmt = $dbh->prepare($sql);
        //SQLを実行
        $stmt->execute();
        //レコードの取得
        $rows = $stmt->fetchAll();
        //配列の情報の順番を逆にする。
        $reverse = array_reverse($rows);
    } catch(PDOException $e) {
        throw $e;
    }
} catch(PDOException $e) {
    $err_msg['db_connect'] = 'DBエラー:'. $e->getMessage();
} 
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/tool.css">
    </head>
    <body>
        <header>
            <div class="top_menu">
                <div><a href=""><img src="./img/logo.png"></a></div> 
                <h1>商品管理ページ</h1>
                <div class="back"><a href="/simple_music_fan/menu.html">戻る</a></div>
                <div class="message">
                    <div><?php if(mb_strlen($message) > 0) {
                        print '処理結果:' . $message;
                    }
                    ?></div>
                </div>
            </div>
        </header>
        <main>
            <div class="form">
                <form method="post" enctype="multipart/form-data">
                    <p>商品名：<input type="text" name="name" value=""></p>
                    <p>値段：<input type="text" name="price" value="0"></p>
                    <p>在庫数：<input type="text" name="stock" value="0"></p>
                    <p><input type="file" name="img"></p>
                    <p><select name="status">
                        <option value="2">--ステータスを選択ください--</option>
                        <option value="0">非公開</option>
                        <option value="1">公開</option>
                    </select></p>
                    <p><select name="category">
                        <option value="">---カテゴリーを選択してください---</option>
                        <option value="ギター">ギター</option>
                        <option value="ベース">ベース</option>
                        <option value="キーボード">キーボード</option>
                        <option value="ドラム">ドラム</option>
                        <option value="スピーカー">スピーカー</option>
                        <option value="メンテナンス">メンテナンス</option>
                        <option value="その他">その他</option>
                    </select></p>
                    <p>商品説明：</p>
                    <p><textarea name="comment" rows="4" cols="40" value=""></textarea></p>
                    <p><input type="submit" value="商品追加"></p>
                    <input type="hidden" name="type" value="insert_item">
                </form>
            </div>
            <div class="err_msg">
                <?php 
                if(count($err_msg) > 0):
                    foreach($err_msg as $value){ ?>
                        <ul>
                            <li>
                                <?php print $value; ?>
                            </li>
                        </ul>
                <?php } endif;?>
            </div>
        </main>
        <div class="border"></div>
            <div class="center">
                <div>
                <?php foreach($reverse as $value) : ?>
                <div class="table">
                    <div>商品名:<?php print $value['name']; ?>/アイテムID：<?php print $value['id']; ?></div>
                    <div class="data">
                        <div>
                            <div><img class="img_size" src="<?php print $img_dir . $value['img']; ?>"></div>
                        </div>
                        <div class="list">
                            <div>カテゴリー:<?php print $value['category']; ?></div>
                            <div>
                                <form method="post">
                                    <input class="stock_textbox" type="text" name="update_price" value="<?php print $value['price'] ?>">円
                                    <input class="submit" type="submit" value="値段修正">
                                    <input type="hidden" name="type" value="update_price">
                                    <input type="hidden" name="id" value="<?php print $value['id'] ?>">
                                </form>
                            </div>
                            <div>
                                <form method="post">
                                    <input class="stock_textbox" type="text" name="update_stock" value="<?php print $value['stock'] ?>">個
                                    <input class="submit" type="submit" value="在庫数変更">
                                    <input type="hidden" name="type" value="update_stock">
                                    <input type="hidden" name="id" value="<?php print $value['id'] ?>">
                                </form>
                            </div>
                            <div><?php if($value['status'] === 0) { ?>
                                <form method="post">
                                    <input class="submit_status" type="submit" value="非公開から公開へステータス変更">
                                    <input type="hidden" name="type" value="change_status">
                                    <input type="hidden" name="change_status" value="1">
                                    <input type="hidden" name="id" value="<?php print $value['id'] ?>">
                                </form>
                            <?php } else if ($value['status'] === 1) { ?>
                                <form method="post">
                                    <input class="submit_status" type="submit"value="公開から非公開へステータス変更">
                                    <input type="hidden" name="type" value="change_status">
                                    <input type="hidden" name="change_status" value="0">
                                    <input type="hidden" name="id" value="<?php print $value['id'] ?>">
                                </form>
                            <?php }
                            ?></div>
                            <div>
                                <p>
                                    <form method="post">
                                        <input class="submit_delete" type="submit" value="商品削除">
                                        <input type="hidden" name="type" value="item_delete">
                                        <input type="hidden" name="id" value="<?php print $value['id']; ?>">
                                    </form>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="comment">商品説明:<?php print $value['comment'] ?></div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        
    </body>
</html>