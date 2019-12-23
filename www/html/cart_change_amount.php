<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー情報に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';
//カート機能に関する関数ファイル読み込み
require_once MODEL_PATH . 'cart.php';

//セッション開始(ログインチェック)
session_start();

//セッション変数セット済みかどうかチェック
if(is_logined() === false){
  //ログインしていなければログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();
//登録済み$_SESSIONをもとにユーザー情報取得
$user = get_login_user($db);

//数量変更する対象のカートid取得
$cart_id = get_post('cart_id');
//変更する数量取得
$amount = get_post('amount');

//cartsテーブルの購入数変更
if(update_cart_amount($db, $cart_id, $amount)){
  //成功すれば$_SESSIONに成功メッセージ代入
  set_message('購入数を更新しました。');
} else {
  //失敗すれば$_SESSIONにエラーメッセージ代入
  set_error('購入数の更新に失敗しました。');
}
//カートページにリダイレクト
redirect_to(CART_URL);