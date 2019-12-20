<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録情報に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';

//セッション開始(ログインチェック)
session_start();

//$_SESSION変数が空文字あれば
if(is_logined() === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();

//セット済$_SESSION変数の値をもとにデータベースからユーザー情報を取得し$userへ代入
$user = get_login_user($db);

//ユーザーが管理者でなければ
if(is_admin($user) === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//削除対象の商品idを取得し$item_idへ代入
$item_id = get_post('item_id');


//対象の商品レコードと画像ファイルの削除に成功
if(destroy_item($db, $item_id) === true){
  //$_SESSIONに成功メッセージ代入
  set_message('商品を削除しました。');
  //失敗
} else {
  //$_SESSIONにエラーメッセージ代入
  set_error('商品削除に失敗しました。');
}



//管理者トップページへリダイレクト
redirect_to(ADMIN_URL);