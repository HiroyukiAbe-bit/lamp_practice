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
                                <input type="text" name="search" value="<?php if(isset($search)){if($search !== '') { echo $search;}}?>">
                                <input type="submit" value="商品検索">
                                <input type="hidden" name="type" value="search">
                            </form>
                            <?php 
                                if (count($err_msg) > 0) {
                                    foreach ($err_msg as $value): ?>
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
                                <?php if(isset($user_id)){
                                    print 'ようこそ!' . $user_id . 'さん';
                                }
                                ?>
                            </div>
                            <div class="right">
                                <?php if(isset($user_id)){
                                        if($user_id === 'ユーザー') {?>
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
                                <a class="noline" href="./category.php?category=ギター">ギター</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/bass.png">
                                <a class="noline" href="./category.php?category=ベース">ベース</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/piano.png">
                                <a class="noline" href="./category.php?category=キーボード">キーボード</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/drum.png">
                                <a class="noline" href="./category.php?category=ドラム">ドラム</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/speaker.png">
                                <a class="noline" href="./category.php?category=スピーカー">スピーカー</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/mente.png">
                                <a class="noline" href="./category.php?category=メンテ用品">メンテ用品</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="menu_item">
                            <div class="menu_item_center">
                                <img width="30px" height="30px" src="./img/other.png">
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
                                    if($_POST['type'] === 'search') {
                                        print '【検索結果】';
                                        if(count($items) === 0) { ?>
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
                            foreach ($items as $value) : 
                                if ($value['status'] === 1) {?>
                                    <table>
                                        <tr>
                                            <td><p class="center"><?php print $value['name']; ?></p>
                                                <div class="center2">
                                                    <a class="center" href="<?php print './item.php?item_id=' . $value['id']; ?>">
                                                        <img class="img_size" src="<?php print IMG_DIR . $value['img']; ?>">
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                            <?php } 
                            endforeach; ?>
                        <?php } else {
                            foreach ($items as $value) : ?>
                                    <table>
                                        <tr>
                                            <td><p class="center"><?php print $value['name']; ?></p>
                                                <div class="center2">
                                                    <a class="center" href="<?php print './item.php?item_id=' . $value['id']; ?>">
                                                        <img class="img_size" src="<?php print IMG_DIR . $value['img']; ?>">
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                           <?php endforeach; ?>
                        <?php  } ?>
                        <div class="center">
                        <?php if($now -1 <= 0) { ?>
                          <span style='padding: 5px;'>前のページへ</span>
                        <?php } else { ?>
                          <a href="?page_id=<?php print($now-1); ?>">前のページへ</a>
                        <?php } ?>  
                          <?php for($n = 1; $n <= $page_data['total_pages']; $n ++){
                            if($n == $now) { ?>
                              <a href='?page_id=<?php print $now; ?>' style='padding: 5px; color:red;'><?php print $now; ?></a>
                            <?php } else { ?>
                              <a href='?page_id=<?php print $n; ?>' style='padding: 5px;'><?php print $n; ?></a>
                            <?php } ?>
                          <?php } ?>
                          <?php if($now +1 > $page_data['total_pages']){ ?>
                            <span style='padding: 5px;'>次のページへ</span>
                          <?php } else { ?>
                            <a href="?page_id=<?php print($now+1); ?>">次のページへ</a>
                          <?php } ?>
                        </div> 
                        <div class="center">
                          <?php print $page_data['total_count']; ?>件中 
                            <?php print $start_item_number ?>件目 - <?php print $end_item_number; ?>件目
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>