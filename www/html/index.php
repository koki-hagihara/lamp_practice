<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once '../model/functions.php';
//ユーザー情報登録に関する関数ファイル読み込み
require_once '../model/user.php';
//商品情報登録に関する関数ファイル読み込み
require_once '../model/item.php';
require_once '../model/ranking.php';

//セッション開始(ログインチェック)
session_start();

//$_SESSION変数セット済み(ログイン済み)かチェック
if(is_logined() === false){
  //ログイン済みでない場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続(データベースへのリンク取得)
$db = get_db_connect();
//セット済み$_SESSIONをもとにユーザー情報を取得
$user = get_login_user($db);

//公開ステータスになっている商品全てのデータを取得
$items = get_open_items($db);

$ranking = get_ranking($db);

$rank = 1;

//ビューファイル読み込み
include_once '../view/index_view.php';