<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';

//セッション開始(ログインチェック)
session_start();

//is_logined関数でセッション変数セット済(ログイン済)かどうかをチェック
if(is_logined() === true){
  //ログイン済の場合redirect_to関数により商品一覧ページへリダイレクトし、以下の処理はしない
  redirect_to(HOME_URL);
}

//以下、ログインしていなかった場合...
//ユーザーがログイン画面で入力してきたユーザー名を$nameに格納
$name = get_post('name');
//ユーザーがログイン画面で入力してきたパスワードを$passwordに格納
$password = get_post('password');

//データベース接続
$db = get_db_connect();

//login_as関数で入力された$nameと$passwordをもとにデータ照合
$user = login_as($db, $name, $password);
//照合結果がfalseであった場合
if( $user === false){
  //set_error関数で$_SESSION変数にエラーメッセージ格納
  set_error('ログインに失敗しました。');
  //ログイン画面へリダイレクト
  redirect_to(LOGIN_URL);
}

//照合成功
set_message('ログインしました。');
//そのユーザーが管理者として登録されていた場合
if ($user['type'] === USER_TYPE_ADMIN){
  //商品管理ページへリダイレクト
  redirect_to(ADMIN_URL);
}
//管理者でなければ商品一覧ページへリダイレクト
redirect_to(HOME_URL);