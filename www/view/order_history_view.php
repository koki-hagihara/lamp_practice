<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
    <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'order_history.css'); ?>">
</head>
<body>
    <?php 
    include VIEW_PATH . 'templates/header_logined.php'; 
    ?>

    <div class="container">
        <h1>購入履歴</h1>

        <?php include VIEW_PATH . 'templates/messages.php'; ?>

        <?php if (count($history)>0) { ?>
            <table class="table table-bordered text-center"> 
                <thead class="thead-light">
                    <tr>
                        <th>注文番号</th>
                        <th>購入日時</th>
                        <th>合計金額</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($history as $value) { ?>
                    <tr>
                        <td><?php print entity_str($value['order_number']); ?> </td>
                        <td><?php print entity_str($value['order_date']); ?> </td>
                        <td>
                            <?php print entity_str($value['total_price']); ?>円 
                            <a href = "order_details.php?order_number=<?php print $value['order_number'];?>&order_date=<?php print $value['order_date'];?>&total_price=<?php print $value['total_price'];?>">購入明細表示</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>   
        <?php } else {?>
            <p>購入履歴はありません</p>
        <?php } ?>
    </div>

</body>
</html>