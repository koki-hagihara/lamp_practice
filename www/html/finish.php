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
require_once MODEL_PATH . 'history.php';

//セッション開始(ログインチェック)
session_start();

//$_SESSIONセット済みかチェック
if(is_logined() === false){
  //ログインしていない場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続(データベースへのリンク取得)
$db = get_db_connect();
//ユーザーの登録情報を取得
$user = get_login_user($db);

//該当ユーザーのカートの中身を取得
$carts = get_user_carts($db, $user['user_id']);

//purchase_carts関数実行(カート内チェック後update文とdelete文)
//結果リターンがfalseであれば...
if(purchase_carts($db, $carts) === false){
  //$_SESSIOにエラーメッセージ代入
  set_error('商品が購入できませんでした。');
  //カートページへリダイレクト
  redirect_to(CART_URL);
} 

//カートの中身の合計金額を計算
$total_price = sum_carts($carts);

//ビューファイル読み込み
include_once '../view/finish_view.php';