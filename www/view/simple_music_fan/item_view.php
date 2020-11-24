<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Simple Music Fan</title>
        <link rel="stylesheet" href="/simple_music_fan/css/item.css">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
                        <div class="center">【<?php print $item['name']; ?>】</div>
                            <table>
                                <tr>
                                    <td>
                                        <div class="details">
                                            <div>
                                                <img class="img_size" src="<?php print IMG_DIR . $item['img']; ?>">
                                            </div>
                                            <div>
                                                <div>カテゴリー：<?php print $item['category']; ?></div>
                                                <div>商品金額：<?php print $item['price'];?>円</div>
                                                <div>
                                                    <form method="post" action="./carts.php">
                                                        <div>数量：<input type="text" name="amount" value=""></div>
                                                        <div>
                                                            <?php if ($item['stock'] === 0) : ?>
                                                                <p class ="red">売り切れ</p>
                                                            <?php elseif ($item['stock'] > 0) : ?>
                                                                <input type="submit" value="カートに追加">
                                                            <?php endif; ?>
                                                        </div>
                                                        <input type="hidden" name="type" value="cart_insert">
                                                        <input type="hidden" name="item_number" value="<?php print $item['id']; ?>">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div><?php print $item['comment']; ?></div>
                                        <?php if(count($comments) > 0) : ?>
                                          <div> 【お客様からの評価レビュー】</div>
                                        <?php endif; ?>
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
                                        <?php if(isset($_SESSION['userid']) && $is_purchase_history !== 0): ?>
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
                </div>
            </main>
        </div>
        <footer>
            Copyright 2020 Simple music Fan All Rights Reserved.
        </footer>
    </body>
</html>