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


if ($user['type'] === 1){
    $history = get_all_history_list($db);
} else if ($user['type'] === 2){
    $history = get_private_history_list($db, $user['user_id']);
}



include_once '../view/order_history_view.php';