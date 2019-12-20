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

//is_logined関数でセッション変数登録済(ログイン済)かどうかチェック
if(is_logined() === false){
  //ログイン済でない場合(返り値がfalse)ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();


//get_login_user関数でユーザー情報をfetchメソッドで取得まで行い結果を$userへ代入
$user = get_login_user($db);


//is_admin関数でユーザーが管理者かどうかの確認を行い、管理者でなかったら...
if(is_admin($user) === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}


//get_post関数で在庫変更を行う商品のitem_idを取得し$item_idへ代入
$item_id = get_post('item_id');
//get_post関数で入力された在庫変更数を取得し$stockへ代入
$stock = get_post('stock');


//update_item_stock関数で在庫数を変更するSQLを実行
if(update_item_stock($db, $item_id, $stock)){
  //成功であればset_message関数で$_SESSIONに引数のメッセージ代入
  set_message('在庫数を変更しました。');
} else {
  //失敗ならset_error関数で$_SESSIONに引数のエラーメッセージ代入
  set_error('在庫数の変更に失敗しました。');
}

//管理者用トップページヘリダイレクト
redirect_to(ADMIN_URL);