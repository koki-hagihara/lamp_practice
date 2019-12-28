<?php 
require_once 'functions.php';
require_once 'db.php';

function record_order_history($db, $user_id) {
    $sql = "
        INSERT INTO
        order_history (user_id)
        VALUES(:user_id)
        ";
    $params = array(':user_id' => $user_id);

    return execute_query($db, $sql, $params);
}

function record_order_details($db, $order_number, $item_id, $amount) {
    $sql = "
        INSERT INTO
        order_details (order_number, item_id, amount)
        VALUES(:order_number, :item_id, :amount)
        ";
    $params = array(':order_number' => $order_number, ':item_id' => $item_id, ':amount' => $amount);

    return execute_query($db, $sql, $params);
}