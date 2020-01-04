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



function record_order_details($db, $order_number, $item_id, $price, $amount) {
    $sql = "
        INSERT INTO
        order_details (order_number, item_id, price, amount)
        VALUES(:order_number, :item_id, :price, :amount)
        ";
    $params = array(':order_number' => $order_number, ':item_id' => $item_id, ':price' => $price, ':amount' => $amount);

    return execute_query($db, $sql, $params);
}



function get_all_history_list($db){
    $sql = "
        SELECT
        order_history.order_number,
        DATE_FORMAT(order_history.order_date, \"%Y-%m-%d\") AS order_date,
        SUM(items.price * order_details.amount) AS total_price
        FROM order_history JOIN order_details
        ON order_history.order_number = order_details.order_number
        JOIN items
        ON order_details.item_id = items.item_id   
        GROUP BY order_details.order_number
        ORDER BY order_history.order_date desc
        ";
    return fetch_all_query($db, $sql);
}



function get_private_history_list($db, $user_id){
    $sql = "
        SELECT
        order_history.order_number,
        DATE_FORMAT(order_history.order_date, \"%Y-%m-%d\") AS order_date,
        SUM(items.price * order_details.amount) AS total_price
        FROM order_history JOIN order_details
        ON order_history.order_number = order_details.order_number
        JOIN items
        ON order_details.item_id = items.item_id
        WHERE
        user_id = :user_id
        GROUP BY order_details.order_number
        ORDER BY order_history.order_date desc
        ";
    $params = array(':user_id' => $user_id);

    return fetch_all_query($db, $sql, $params);
}


function get_order_details($db, $order_number) {
    $sql = "
        SELECT
        items.name,
        order_details.price,
        order_details.amount,
        order_details.price * order_details.amount AS sub_total_price,
        order_history.user_id
        FROM items JOIN order_details
        ON items.item_id = order_details.item_id
        JOIN order_history
        ON order_details.order_number = order_history.order_number
        WHERE
        order_details.order_number = :order_number
        ";
    $params = array(':order_number' => $order_number);

    return fetch_all_query($db, $sql, $params);
}