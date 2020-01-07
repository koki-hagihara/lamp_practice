<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
//セッション開始(ログインチェック)
session_start();
//ログインしていれば
if(is_logined() === true){
  //商品一覧ページへリダイレクト
  redirect_to(HOME_URL);
}
//POST送信されてきた「名前」を$nameに格納
$name = get_post('name');
//POST送信されてきた「パスワード」を$passwordに格納
$password = get_post('password');
//POST送信されてきた「確認用パスワード」を$password_confirmationに格納
$password_confirmation = get_post('password_confirmation');
//データベースに接続
$db = get_db_connect();

try{
  //regist_user関数の返り値を$resultへ格納
  $result = regist_user($db, $name, $password, $password_confirmation);
  //regist_user関数の返り値がfalseであれば...
  if( $result=== false){
    //set_error関数でエラーメッセージ格納
    set_error('ユーザー登録に失敗しました。');
    //サインアップ画面へリダイレクト
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  //データベース接続に失敗した際、set_error関数でエラーメッセージ格納
  set_error('ユーザー登録に失敗しました。');
  //サインアップ画面へリダイレクト
  redirect_to(SIGNUP_URL);
}
//regisst_user関数内でinsert_user関数が成功すればset_message関数でメッセージ格納
set_message('ユーザー登録が完了しました。');
//login_as関数でデータベースから登録情報の取得・$_SESSIONへの格納
login_as($db, $name, $password);
//商品一覧ページへリダイレクト
redirect_to(HOME_URL);