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

<!--card-deck:隣り合うカード型デザインの高さを自動的に揃えてくれる -->
    <div class="card-deck">
    <!--row:行に与えるクラス-->
      <div class="row">
      <?php foreach($items as $item){ ?>
      <!--col:列を表すクラス-->
        <div class="col-6 item">
        <!--card:画像やタイトル等をくくる、h-100:height100%、text-center:テキスト中央寄せ-->
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print(entity_str($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . entity_str($item['image'])); ?>">
              <figcaption>
                <?php print(number_format(entity_str($item['price']))); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                    <input type="hidden" name="token" value="<?php print $token; ?>">
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
    <h1>人気ランキング</h1>
    <div class="card-deck">
      <div class="row">
      <?php foreach ($ranking as $value) { ?>
        <div class="col-4 item">  
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print $rank;?>位
              <?php $rank++;?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . entity_str($value['image'])); ?>">
              <figcaption>
                <div class="card-title"><?php print(entity_str($value['name'])); ?></div>
                <div class="card-text"><?php print(number_format(entity_str($value['price']))); ?>円</div>
                <?php if($value['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($value['item_id']); ?>">
                    <input type="hidden" name="token" value="<?php print $token; ?>">
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
  </div>
  
<pre>
<?php print_r($token);?>
</pre>
<pre>
<?php print_r($_SESSION['csrf_token']);?>
</pre>

</body>
</html>