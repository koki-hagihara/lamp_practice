<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'history.php';

session_start();

if (is_logined() === false) {
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

$order_number = get_get('order_number');
$order_date = get_get('order_date');
$total_price = get_get('total_price');

$order_details = get_order_details($db, $order_number);

if ($user['type'] !== 1 && $user['user_id'] !== $order_details[0]['user_id']) {
    set_error('ページを表示できません');
}



include_once '../view/order_details_view.php';