<?php
require_once 'functions.php';
require_once 'db.php';


function get_ranking($db) {
    $sql = "
        SELECT
        items.name,
        items.price,
        items.image,
        items.stock,
        order_details.item_id,
        SUM(order_details.amount) AS buy_amount
        FROM items JOIN order_details
        ON items.item_id = order_details.item_id
        GROUP BY order_details.item_id
        ORDER BY buy_amount desc
        LIMIT 3
        ";
    return fetch_all_query($db, $sql);
}