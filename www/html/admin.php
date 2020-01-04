<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//アイテムデータ操作に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';

//セッション開始(ログインチェック)
session_start();

//is_logined関数でログイン済みか判断
if(is_logined() === false){
  //ログインしていない場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();

//get_login_user関数でユーザー登録情報を取得し$userへ格納
$user = get_login_user($db);

//is_admin関数で管理者かどうかチェック
if(is_admin($user) === false){
  //管理者でなかった場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//get_all_items関数でitemsテーブルから商品のデータを配列で取得
$items = get_all_items($db);

//viewファイル読み込み
include_once '../view/admin_view.php';
