<!DOCTYPE html>
<html lang ="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入明細</title>
    <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'order_details.css'); ?>">
</head>
<body>
    <?php 
    include VIEW_PATH . 'templates/header_logined.php'; 
    ?>

    <div class="container">
        <h1>購入明細</h1>
        <?php if ($user['type'] === 1 || $user['user_id'] === $order_details[0]['user_id']) { ?>
            <p>注文番号：<?php print entity_str($order_number);?></p>
            <p>購入日：<?php print entity_str($order_date);?></p>
            <p>合計金額：<?php print entity_str($total_price);?></p>
        <?php } ?>

        <?php include VIEW_PATH . 'templates/messages.php'; ?>

        <table class="table table-bordered text-center">
            <thead class="thead-light">
                <tr>
                    <th>商品名</th>
                    <th>商品価格</th>
                    <th>購入数</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($user['type'] === 1 || $user['user_id'] === $order_details[0]['user_id']) { ?>
                <?php foreach ($order_details as $value) { ?>
                    <tr>
                        <td><?php print entity_str($value['name']);?></td>
                        <td><?php print entity_str($value['price']);?>円</td>
                        <td><?php print entity_str($value['amount']);?></td>
                        <td><?php print entity_str($value['sub_total_price']);?>円</td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>