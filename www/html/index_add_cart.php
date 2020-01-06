<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録情報に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';
//カート機能に関する関数ファイル読み込み
require_once MODEL_PATH . 'cart.php';
require_once '../model/csrf.php';


//セッション開始(ログインチェック)
session_start();

//$_SESSION変数セット済みでなければ(ログイン済みでなければ)
if(is_logined() === false){
  //ログイン画面へリダイレクト
  redirect_to(LOGIN_URL);
}

$token = get_post('token');

if (is_valid_csrf_token($token) === false) {
  set_error('不正なリクエストです');
  redirect_to(HOME_URL);
}

//データベース接続(データベースへのリンクを取得)
$db = get_db_connect();
//セット済み$_SESSION変数をもとにユーザー情報を取得
$user = get_login_user($db);


//カートに追加する商品のIDを取得
$item_id = get_post('item_id');

//add_cart関数実行(カートテーブルに新しくインサート or カート内の数量更新)
if(add_cart($db,$user['user_id'], $item_id)){
  //$_SESSIONに成功メッセージ代入
  set_message('カートに商品を追加しました。');
  //関数実行失敗で$_SESSIONにエラーメッセージ代入
} else {
  set_error('カートの更新に失敗しました。');
}

$token = token_destroy($token);


//商品一覧ページへリダイレクト
redirect_to(HOME_URL);