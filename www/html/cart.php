<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';
//カート機能に関する関数ファイル読み込み
require_once MODEL_PATH . 'cart.php';

//セッション開始(ログインチェック)
session_start();

//$_SESSIONセット済みでなければ(ログイン済みでなければ)
if(is_logined() === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続(データベースへのリンク取得)
$db = get_db_connect();
//ユーザー登録情報を取得
$user = get_login_user($db);

//ユーザーのカートの中身情報を全行配列で取得し$cartsに代入
$carts = get_user_carts($db, $user['user_id']);

//カート内合計金額を計算・取得し$total_priceへ代入
$total_price = sum_carts($carts);

//ビューファイル読み込み
include_once '../view/cart_view.php';