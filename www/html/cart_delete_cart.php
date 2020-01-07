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

//$_SESSIONセット済みでなければ(ログインしていなければ)
if(is_logined() === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}


//データベース接続(データベースへのリンクを取得)
$db = get_db_connect();
//ユーザーの登録情報を取得し$userへ代入
$user = get_login_user($db);

//商品削除対象のカートIDを取得
$cart_id = get_post('cart_id');

//カートの中身から該当cart_idのレコードを削除
if(delete_cart($db, $cart_id)){
  //$_SESSIONに成功メッセージを代入
  set_message('カートを削除しました。');
  //削除失敗(リターンがfalseの場合)
} else {
  //$_SESSIONにエラーメッセージ代入
  set_error('カートの削除に失敗しました。');
}

//カートページへリダイレクト
redirect_to(CART_URL);