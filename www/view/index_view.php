<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print($item['name']); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <div class="text-center">
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
    <div class="text-center">
      <?php print $page_data['total_count']; ?>件中 
        <?php print $start_item_number ?>件目 - <?php print $end_item_number; ?>件目
    </div>
  </div>
</body>
</html>