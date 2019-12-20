<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';

//セッション開始(ログインチェック)
session_start();

//$_SESSION['user_id]セット済み(ログイン済み)かどうかチェック
if(is_logined() === false){
  //ログインしていない場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}


//データベース接続
$db = get_db_connect();


//get_login_user関数でデータベースからユーザー情報を取得し$userへ格納
$user = get_login_user($db);


//is_admin関数でこのユーザーが管理者かどうかを確認
if(is_admin($user) === false){
  //管理者でなかった場合、ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}


//get_post関数で、name属性「name」でPOST送信されてきたデータを取得し$nameに格納
$name = get_post('name');
//get_post関数で、name属性「price」でPOST送信されてきたデータを取得し$priceに格納
$price = get_post('price');
//get_post関数で、name属性「status」でPOST送信されてきたデータを取得し$statusに格納
$status = get_post('status');
//get_post関数で、name属性「stock」でPOST送信されてきたデータを取得し$stockに格納
$stock = get_post('stock');


//get_file関数に引数「image」を送り、$_FILES[$name]の値を連想配列で取得
$image = get_file('image');

//regist_item関数で入力値のバリデーション・データベースインサート処理まで完了すれば(trueであれば)
if(regist_item($db, $name, $price, $stock, $status, $image)){
  //set_message関数で$_SESSIONへ引数のメッセージを代入
  set_message('商品を登録しました。');
}else {
  //falseであればset_error関数で$_SESSIONに引数のエラーメッセージ代入
  set_error('商品の登録に失敗しました。');
}

//管理者用トップページへリダイレクト
redirect_to(ADMIN_URL);